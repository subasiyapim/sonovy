<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\HelpCenter\ArticleStoreRequest;
use App\Http\Requests\HelpCenter\ArticleUpdateRequest;
use App\Models\HelpCenterArticle;
use App\Services\HelpCenterArticleServices;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HelpCenterArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('help_center_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $articles = HelpCenterArticle::advancedFilter();

        return inertia('Control/HelpCenter/Article_List', compact('articles'));

    }


    public function create()
    {

        return inertia('Control/HelpCenter/Article_Create');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(ArticleStoreRequest $request)
    {

        $article = HelpCenterArticleServices::create($request->validated());

        return redirect()->back()->with([
            'notification' => [
                'title' => 'Success',
                'message' => 'HelpCenter created successfully',
                'data' => $article
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(HelpCenterArticle $article)
    {
        abort_if(Gate::denies('help_center_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response()->json($article, Response::HTTP_OK);
    }

    public function edit(HelpCenterArticle $article)
    {

        abort_if(Gate::denies('help_center_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return inertia('Control/HelpCenter/Article_Edit', compact('article'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ArticleUpdateRequest $request, HelpCenterArticle $article)
    {

        HelpCenterArticleServices::update($article, $request->validated());

        return redirect()->back()->with([
            'notification' => [
                'title' => 'Success',
                'message' => 'HelpCenter updated successfully',
                'data' => $article
            ]
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:3|max:255'
        ]);


        $search = $request->input('search');

        $articles = HelpCenterArticleServices::search($search);

        return response()->json($articles, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HelpCenterArticle $article)
    {
        abort_if(Gate::denies('help_center_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $article->delete();

        return redirect()->back();
    }
}
