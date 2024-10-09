<?php

namespace App\Http\Controllers\Control;

use App\Enums\AlbumTypeEnum;
use App\Enums\AnnouncementReceiversEnum;
use App\Enums\AnnouncementTypeEnum;
use App\Enums\ProductPublishedCountryTypeEnum;
use App\Enums\ProductStatusEnum;
use App\Enums\ProductTypeEnum;
use App\Enums\PlatformTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductChangeStatusRequest;
use App\Http\Requests\Product\ProductCorrectionRequest;
use App\Jobs\DDEXXmlJob;
use App\Models\Artist;
use App\Models\Product;
use App\Models\Country;
use App\Models\Genre;
use App\Models\Label;
use App\Models\Platform;
use App\Models\Upc;
use App\Services\AnnouncementServices;
use App\Services\ProductApplyServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ProductApplyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('broadcast_apply_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $products = Product::when($request->status, function ($query) use ($request) {
            $query->where('status', $request->status);
        })
            ->when($request->type, function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->with('artists', 'label', 'publishedCountries')->advancedFilter();

        $statuses = ProductStatusEnum::getTitles();
        $statusTitles = ProductStatusEnum::getTitles();

        return inertia('Control/ProductsApply/Index', compact('broadcasts', 'statusTitles', 'statuses'));
    }


    public function edit(Product $product)
    {
        abort_if(Gate::denies('broadcast_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $props = [
            'product' => $product->load('artists', 'label', 'publishedCountries', 'genre', 'subGenre', 'language',
                'label', 'hashtags', 'downloadPlatforms', 'songs', 'addedBy'),
            'genres' => Genre::pluck('name', 'id'),
            'artists' => Artist::pluck('name', 'id'),
            'labels' => Label::pluck('name', 'id'),
            'counties' => Country::pluck('name', 'id'),
            'languages' => Country::pluck('name', 'id'),
            'platforms' => Platform::get()->groupBy('type')->map(function ($platforms) {
                return $platforms->pluck('name', 'id');
            }),
            'album_types' => AlbumTypeEnum::getTitles(),
            'platform_types' => PlatformTypeEnum::getTitles(),
            'broadcast_types' => ProductTypeEnum::getTitles(),
            'publish_country_types' => ProductPublishedCountryTypeEnum::getTitles(),
        ];

        return inertia('Control/ProductsApply/Edit', compact('props'));

    }


    public function changeStatus(ProductChangeStatusRequest $request)
    {

        abort_if(Gate::denies('broadcast_change_status'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product = Product::where('id', $request->id)->firstOrFail();

        $newUpc = Upc::notUsedUpcs()->first();
        if ($newUpc) {
            $product->upc_code = $product->upc_code ?? $newUpc->upc;
            $newUpc->use_by_date = now();
            $newUpc->product_id = $product->id;
            $newUpc->save();
            $product->upc_code = $newUpc->upc;
            $product->save();
        }

        ProductApplyServices::update($product, $request->validated());


        $data = [
            'name' => 'Product Approved',
            'type' => [
                AnnouncementTypeEnum::EMAIl->value,
                AnnouncementTypeEnum::SMS->value,
                AnnouncementTypeEnum::SITE->value
            ],
            'content_type' => 'success',
            'content' => $product->name.' '.__('control.product.successfully_published'),
            'receivers' => AnnouncementReceiversEnum::SELECTED->value,
            'selected' => [$product->added_by],
            'url' => route('dashboard.broadcasts.show', $product->id)
        ];

        AnnouncementServices::create($data);

        return redirect()->back()->with(['message' => 'Product updated successfully'], Response::HTTP_OK);
    }

    public function correction(ProductCorrectionRequest $request)
    {

        abort_if(Gate::denies('broadcast_apply_correction'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product = Product::where('id', $request->id)->firstOrFail();


        $data = $request->validated();
        $data['status'] = '3';
        ProductApplyServices::update($product, $data);

        $data = [
            'name' => 'Product Correction need',
            'type' => [
                AnnouncementTypeEnum::EMAIl->value,
                AnnouncementTypeEnum::SMS->value,
                AnnouncementTypeEnum::SITE->value
            ],
            'content_type' => 'warning',
            'content' => $product->name.' '.$data['correction'],
            'receivers' => AnnouncementReceiversEnum::SELECTED->value,
            'selected' => [$product->added_by],
            'url' => route('dashboard.broadcasts.show', $product->id)
        ];

        AnnouncementServices::create($data);

        return redirect()->back()->with(['message' => 'Product updated successfully'], Response::HTTP_OK);
    }

    public function makeDdexXml(Product $product)
    {
        abort_if(Gate::denies('broadcast_make_xml'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        DDEXXmlJob::dispatch($product);

        return redirect()->back()->with([
            'notification' => [
                'type' => 'success',
                'message' => 'DDEX XML is being created. You will be notified when it is ready.'
            ]
        ]);
    }

    public function downloadDdexXml(Product $product)
    {
        abort_if(Gate::denies('broadcast_download_xml'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $path = $product->xml_path;
        $file = Storage::disk('xml_files')->get($path);

        return response($file, 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'attachment; filename="'.$path.'"'
        ]);

    }
}
