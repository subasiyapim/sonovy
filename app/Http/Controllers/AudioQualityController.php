<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Services\FFMpegServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AudioQualityController extends Controller
{
    public function analyze(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'audio_file' => 'required|file|mimes:mp3,wav,aac,m4a|max:50000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Geçersiz dosya formatı veya boyutu',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('audio_file');
            $path = $file->store('temp');
            $fullPath = Storage::path($path);

            // Ses kalitesi kontrolü
            $analysis = FFMpegServices::checkAudioQuality($fullPath);

            // Geçici dosyayı sil
            Storage::delete($path);

            if (!$analysis['status']) {
                return response()->json([
                    'status' => 'error',
                    'message' => $analysis['error'] ?? 'Ses dosyası analiz edilirken bir hata oluştu'
                ], 500);
            }

            return response()->json([
                'status' => $analysis['is_valid'] ? 'success' : 'warning',
                'message' => $analysis['is_valid'] ? 'Ses dosyası kalite kontrolünden geçti' : 'Ses dosyasında bazı sorunlar tespit edildi',
                'data' => [
                    'specs' => $analysis['specs'] ?? null,
                    'issues' => $analysis['issues'] ?? [],
                    'silence_analysis' => $analysis['silence_analysis'] ?? null,
                    'level_analysis' => $analysis['level_analysis'] ?? null
                ]
            ]);

        } catch (\Exception $e) {
            if (isset($path)) {
                Storage::delete($path);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Ses dosyası analiz edilirken bir hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Belirli bir şarkı için kalite analizi verilerini kaydet
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeQualityData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'song_id' => 'required|exists:songs,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Geçersiz şarkı ID',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Şarkı modelini al
            $song = Song::findOrFail($request->song_id);

            // Şarkı dosyasının tam yolunu al
            $filePath = str_replace('storage/', '', parse_url($song->path, PHP_URL_PATH));
            $fullPath = Storage::path($filePath);

            Log::info('Ses kalite analizi başlatılıyor', [
                'song_id' => $song->id,
                'file_path' => $fullPath
            ]);

            if (!file_exists($fullPath)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Şarkı dosyası bulunamadı'
                ], 404);
            }

            // Ses kalitesi kontrolü
            $analysis = FFMpegServices::checkAudioQuality($fullPath);

            if (!$analysis['status']) {
                return response()->json([
                    'status' => 'error',
                    'message' => $analysis['error'] ?? 'Ses dosyası analiz edilirken bir hata oluştu'
                ], 500);
            }

            // Mevcut details alanını güncelle
            $details = $song->details ?? [];
            $details['quality_analysis'] = [
                'analyzed_at' => now()->toIso8601String(),
                'specs' => $analysis['specs'] ?? null,
                'issues' => $analysis['issues'] ?? [],
                'silence_analysis' => $analysis['silence_analysis'] ?? null,
                'level_analysis' => $analysis['level_analysis'] ?? null,
                'is_valid' => $analysis['is_valid'] ?? false
            ];

            // Şarkı modelini güncelle
            $song->details = $details;
            $song->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Kalite analizi başarıyla kaydedildi',
                'data' => $details['quality_analysis']
            ]);

        } catch (\Exception $e) {
            Log::error('Kalite analizi kaydedilirken hata', [
                'song_id' => $request->song_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Kalite analizi kaydedilirken bir hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
