<?php

namespace App\Http\Resources\User;

use App\Enums\PaymentStatusEnum;
use App\Enums\ProductStatusEnum;
use App\Models\Label;
use App\Models\Order;
use App\Models\Payment;
use App\Models\System\User;
use App\Services\EarningService;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class UserShowResource extends JsonResource
{
    protected mixed $tab;

    public function __construct($resource, $tab)
    {
        parent::__construct($resource);
        $this->tab = $tab;
    }

    private function getTabContent()
    {
        return match ($this->tab) {
            'pricing' => $this->pricing(),
            'contracts' => $this->contracts(),
            'balances' => $this->balances(),
            'invoices' => $this->invoices(),
            'activities' => $this->activities(),
            'flags' => $this->flags(),
            'relations' => $this->relations(),
            'authorisations' => $this->authorisations(),
            default => $this->profile()
        };
    }


    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'country_id' => $this->country_id,
            'language_id' => $this->language_id,
            'city_id' => $this->city_id,
            'parent' => $this->parent,
            'parent_id' => $this->parent_id,
            'district_id' => $this->district_id,
            'commission_rate' => $this->commission_rate,
            'address' => $this->address,
            'roles' => $this->roles,
            'status' => $this->status->value,
            'email' => $this->email,
            'last_login' => $this->last_login,
            'email_verified_at' => $this->email_verified_at,
            'is_verified' => $this->is_verified,
            'tab' => $this->getTabContent()
        ];
    }

    private function pricing(): array
    {
        return [
            'commission_rate' => $this->commission_rate,
            'payment_threshold' => $this->payment_threshold,
            'currency' => $this->currency,
            'available_items' => $this->availablePlanItemsCount(),
            'history' => $this->orders
        ];
    }

    private function contracts()
    {
        return null;
    }

    private function balances(): array
    {
        return [
            'current_balance' => $this->calculateBalance(),
            'pending_out_payments' => $this->pendingOutPayments(),
            'pending_invoices' => $this->pendingInvoices(),
            'confirmed_total' => $this->confirmedTotal(),
        ];
    }

    private function calculateBalance(): float
    {
        return EarningService::balance($this->id);
    }

    private function pendingInvoices()
    {
        return Payment::where('user_id', $this->id)
            ->where('status', PaymentStatusEnum::PENDING->value)
            ->sum('amount');
    }

    private function confirmedTotal()
    {
        return Payment::where('user_id', $this->id)
            ->where('status', PaymentStatusEnum::APPROVED->value)
            ->sum('amount');
    }

    private function pendingOutPayments(): float
    {
        return Payment::where('status', PaymentStatusEnum::PENDING->value)
            ->where('user_id', $this->id)
            ->sum('amount');
    }

    private function invoices()
    {
        return $this->orders->toArray();
    }

    private function activities()
    {
        return $this->activities->toArray();
    }

    private function relations()
    {
        return [
            'relations' => $this->children->toArray(),
            'products' => $this->getProducts(),
            'labels' => $this->getLabels(),
            'participants' => $this->getParticipants(),
        ];
    }

    private function flags()
    {
        return $this->flags;
    }

    private function authorisations()
    {
        return PermissionService::getGroupedPermissions($this->resource);
    }

    private function profile()
    {
        return [];
    }

    private function getParticipants()
    {
        $participants = [];

        $this->products->each(function ($product) use (&$participants) {
            $product->songs->each(function ($song) use (&$participants) {
                $song->participants->load('user');
                $participants = array_merge($participants, $song->participants->map(function ($participant) {
                    $s = $participant->song;
                    $s->loadMissing('mainArtists', 'products');
                    return [
                        'id' => $participant->user->id,
                        'song' => $s,
                        'roles' => $participant->user->roles,
                        'name' => $participant->user->name,
                        'email' => $participant->user->email,
                        'branch_names' => $participant->branch_names,
                        'commission_rate' => $participant->rate,
                        'realization' => 100 - $participant->rate,
                        'status' => $participant->user->status->title(),
                        'song_name' => $participant->song->name,
                        'song_id' => $participant->song->id,
                        'song_isrc' => $participant->song->isrc,
                    ];
                })->toArray());
            });
        });
        return $participants;
    }

    private function getLabels()
    {
        return $this->labels->map(function ($label) {
            $label->load('country', 'user');

            // Correcting the product count
            $products_count = $label->products()->count();

            // Correcting the song count within products
            $song_count = $label->products()
                ->join('product_song', 'products.id', '=', 'product_song.product_id')
                ->join('songs', 'product_song.song_id', '=', 'songs.id')
                ->count();

            return [
                'id' => $label->id,
                'name' => $label->name,
                'products_count' => $products_count,
                'song_count' => $song_count,
                'country' => $label->country->name,
                'country_emoji' => $label->country->emoji,
                'phone' => $label->phone,
                'email' => $label->email,
                'commission_rate' => $label->user->commission_rate,
                'status' => $label->hasActive() ? 'Aktif şirket' : 'Pasif şirket',
                'user' => $label->user,
            ];
        })->toArray();
    }


    private function getProducts()
    {
        return $this->products->where('status', '!=', ProductStatusEnum::DRAFT->value)
            ->map(function ($product) {
                $product->load('label', 'songs.participants', 'artists');
                return [
                    'id' => $product->id,
                    'type' => $product->type->value,
                    'status' => $product->status->value,

                    'version' => $product->version,
                    'status_name' => $product->status->title(),
                    'image' => $product->image ? $product->image->getUrl('thumb') : null,
                    'album_name' => $product->album_name,
                    'artists' => $product->artists->map(function ($artist) {
                        return $artist->name;
                    })->implode(', '),
                    'label' => $product?->label?->name,
                    'physical_release_date' => Carbon::parse($product->physical_release_date)->format('d.m.Y'),
                    'song_count' => $product->songs->count(),
                    'upc' => $product->upc_code,
                    'catalog_number' => $product->catalog_number,
                    'isrc' => $product->isrc_code,
                ];
            })->toArray();
    }
}
