<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Services\FFMpegServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AudioQualityController extends Controller
{
    /**
     * Ses dosyasını analiz et
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
        try {
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

            // Şarkı modelini al
            $song = Song::findOrFail($request->song_id);

            Log::info('Kalite analizi başlatılıyor - Şarkı bulundu', [
                'song_id' => $song->id,
                'song_name' => $song->name,
                'path_attribute' => $song->path ?? 'path attribute mevcut değil',
                'attributes' => array_keys($song->getAttributes())
            ]);

            // Değer özelliğine doğrudan erişelim - path() Attribute tanımından dolayı özel işlem yapmak gerekiyor
            $songPathValue = $song->getAttributes()['path'] ?? null;

            if (!$songPathValue) {
                Log::error('Şarkının path değeri bulunamadı', [
                    'song_id' => $song->id,
                    'attributes' => $song->getAttributes()
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Şarkının dosya yolu bilgisi bulunamadı'
                ], 404);
            }

            Log::info('Dosya yolu bulundu, tam yol oluşturuluyor', [
                'song_path_value' => $songPathValue,
                'tenant_id' => tenant('id'),
                'tenant_domain' => tenant('domain')
            ]);

            // Farklı yaklaşımları deneyelim (birden fazla yol oluşturup kontrol edelim)
            $filePaths = [];

            // 1. Yaklaşım: Temel tenant dizin yapısı
            $tenantDirectory = 'tenant_' . tenant('domain') . '_songs';
            $filePath1 = storage_path('app/public/' . $tenantDirectory . '/' . $songPathValue);
            $filePaths[] = ['path' => $filePath1, 'description' => 'storage_path/app/public/tenant_domain_songs'];

            // 2. Yaklaşım: Storage::disk('public')->path kullanarak
            $filePath2 = Storage::disk('public')->path($tenantDirectory . '/' . $songPathValue);
            $filePaths[] = ['path' => $filePath2, 'description' => 'Storage::disk(public)->path/tenant_domain_songs'];

            // 3. Yaklaşım: Doğrudan dosya yolu kullanımı
            $filePath3 = storage_path('app/public/songs/' . $songPathValue);
            $filePaths[] = ['path' => $filePath3, 'description' => 'storage_path/app/public/songs'];

            // 4. Yaklaşım: Path bilgisini direkt kullanma
            $filePath4 = $songPathValue;
            $filePaths[] = ['path' => $filePath4, 'description' => 'Direct path value'];

            Log::info('Oluşturulan dosya yolları kontrol ediliyor', [
                'file_paths' => $filePaths
            ]);

            // Dosya varlığını kontrol et ve geçerli olan ilk yolu kullan
            $validPath = null;
            foreach ($filePaths as $pathInfo) {
                if (file_exists($pathInfo['path'])) {
                    $validPath = $pathInfo['path'];
                    Log::info('Geçerli dosya yolu bulundu', [
                        'path' => $validPath,
                        'description' => $pathInfo['description']
                    ]);
                    break;
                }
            }

            if (!$validPath) {
                Log::error('Şarkı için geçerli dosya yolu bulunamadı', [
                    'song_id' => $song->id,
                    'song_path_value' => $songPathValue,
                    'attempted_paths' => $filePaths
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Şarkı dosyası bulunamadı. Kontrol edilen tüm yollarda dosya mevcut değil.'
                ], 404);
            }

            Log::info('Kalite analizi başlatılıyor', [
                'song_id' => $song->id,
                'file_path' => $validPath
            ]);

            // Ses kalitesi kontrolü
            $analysis = FFMpegServices::checkAudioQuality($validPath);

            if (!$analysis['status']) {
                Log::error('Kalite analizi başarısız oldu', [
                    'song_id' => $song->id,
                    'error' => $analysis['error'] ?? 'Bilinmeyen hata'
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => $analysis['error'] ?? 'Ses dosyası analiz edilirken bir hata oluştu'
                ], 500);
            }

            Log::info('Kalite analizi tamamlandı, veriler kaydediliyor', [
                'song_id' => $song->id,
                'is_valid' => $analysis['is_valid'] ?? false
            ]);

            // Mevcut details alanını güncelle
            $details = $song->details;

            // Null ise, boş array oluştur
            if ($details === null) {
                $details = [];
            }

            // Array değilse, JSON decode et
            if (!is_array($details)) {
                try {
                    $details = json_decode($details, true) ?? [];
                } catch (\Exception $e) {
                    $details = [];
                }
            }

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

            Log::info('Kalite analizi başarıyla kaydedildi', [
                'song_id' => $song->id
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Kalite analizi başarıyla kaydedildi',
                'data' => $details['quality_analysis']
            ]);

        } catch (\Exception $e) {
            Log::error('Kalite analizi kaydedilirken kritik hata', [
                'song_id' => $request->song_id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Kalite analizi kaydedilirken bir hata oluştu: ' . $e->getMessage(),
                'error_details' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
    }
}
