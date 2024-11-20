<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\MediaServices;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    public function songUpload(Request $request)
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

    public function destroy(Media $media)
    {
        $media->delete();

        return response()->json(['message' => 'Media deleted successfully']);
    }

    /**
     * @throws ValidationException
     */
    public function mediaUpload(Request $request, $model, $id): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:jpeg,png,jpg|max:4086',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $class = 'App\Models\\'.$model;
        if (!class_exists($class)) {
            return response()->json(['errors' => 'Model not found: '.$class], 422);
        }

        $modelInstance = $class::find($id);
        if (!$modelInstance) {
            return response()->json(['errors' => 'Record not found for the given id'], 422);
        }

        $file = $validated['file'];
        $collection_name = $modelInstance->getTable();

        MediaServices::upload($modelInstance, $file, $collection_name, $collection_name);

        return response()->json(
            [
                'message' => 'File uploaded successfully',
                'image' => $modelInstance->image,
            ]
        );
    }
}
