<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Product;
use App\Models\Label;
use App\Models\Song;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string'
        ]);

        $artists = Artist::where('name', 'like', '%'.$request->search.'%')->get();
        $songs = Song::where('name', 'like', '%'.$request->search.'%')->get();
        $labels = Label::where('name', 'like', '%'.$request->search.'%')->get();
        $products = Product::where('name', 'like', '%'.$request->search.'%')->get();

        $arr = [];
        foreach ($artists as $artist) {
            $arr[] = [
                'id' => $artist->id,
                'name' => $artist->name,
                'type' => 'sanatçı',
                'slug' => "artists"
            ];
        }

        foreach ($songs as $song) {
            $arr[] = [
                'id' => $song->id,
                'name' => $song->name,
                'artist' => $song->broadcasts()->first()->artists()->first()->name,
                'type' => 'parça',
                'slug' => "songs"

            ];
        }

        foreach ($labels as $label) {
            $arr[] = [
                'id' => $label->id,
                'name' => $label->name,
                'type' => 'label',
                'slug' => "labels"
            ];
        }

        foreach ($products as $product) {
            $arr[] = [
                'id' => $product->id,
                'name' => $product->name,
                'type' => 'yayın',
                'slug' => "product"
            ];
        }

        //dd($arr);
        return $arr;

    }
}
