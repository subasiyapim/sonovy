<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\MediaServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MediaController extends Controller
{
    public function upload(Request $request)
    {
        $max_file_size = 1024 * 1024 * 1024;
        $allowed_file_types = Setting::where('key', 'allowed_song_formats')->first()->value;
        $validate = Validator::make($request->all(), [
            'type' => ['in:1,2'],
            'file' => ['required', 'mimes:'.$allowed_file_types, 'max:'.$max_file_size, 'file'],
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }

        $song = MediaServices::mediaUpload($request->file('file'), $request->input('type'));

        return response()->json(['song' => $song]);

    }

    public function destroy($ids)
    {
        //
    }


}
