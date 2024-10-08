<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\Author\AuthorStoreRequest;
use App\Http\Requests\Author\AuthorUpdateRequest;
use App\Models\Author;
use App\Models\Contract;
use App\Services\AuthorService;
use App\Services\CountryServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;

class AuthorController extends Controller
{

    public function index()
    {
        //abort_if(Gate::denies('contract_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        $authors = Author::advancedFilter();
        return inertia('Control/Authors/Index', compact('authors'));
    }


    public function create()
    {
        abort_if(Gate::denies('author_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $performing_rights_organizations = DB::table('performing_rights_organizations')->get();

        return inertia('Control/Authors/Create', compact('performing_rights_organizations'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(AuthorStoreRequest $request)
    {
        $author = AuthorService::create($request->validated());

        return redirect()->back()->with([
            'notification' => [
                'title' => 'Success',
                'message' => 'Author created successfully',
                'data' => $author
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author)
    {
        abort_if(Gate::denies('author_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response()->json($author, Response::HTTP_OK);
    }

    public function edit(Author $author)
    {
        abort_if(Gate::denies('author_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $performing_rights_organizations = DB::table('performing_rights_organizations')->get();

        return inertia('Control/Authors/Edit', compact('author', 'performing_rights_organizations'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AuthorUpdateRequest $request, Author $author)
    {
        AuthorService::update($author, $request->validated());

        return redirect()->back()->with([
            'notification' => [
                'title' => 'Success',
                'message' => 'Author updated successfully',
                'data' => $author
            ]
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:3|max:255'
        ]);


        $search = $request->input('search');

        $authors = AuthorService::search($search);


        return response()->json($authors, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author)
    {
        abort_if(Gate::denies('author_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $author->delete();

        return redirect()->back();
    }

}
