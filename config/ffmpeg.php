<?php

return [
    /*
    |--------------------------------------------------------------------------
    | FFmpeg ve FFProbe Yolları
    |--------------------------------------------------------------------------
    |
    | Bu yapılandırma dosyası, FFMpegServices sınıfı için gerekli olan
    | FFmpeg ve FFProbe araçlarının yollarını belirtir.
    |
    | Yollar belirtilmezse, sistem otomatik olarak aramayı dener:
    | 1. which ffmpeg/ffprobe komutu ile PATH'de
    | 2. Bilinen yaygın lokasyonlarda
    |
    */
    'ffmpeg_path' => env('FFMPEG_PATH', '/opt/homebrew/bin/ffmpeg'),
    'ffprobe_path' => env('FFPROBE_PATH', '/opt/homebrew/bin/ffprobe'),

    /*
    |--------------------------------------------------------------------------
    | FFmpeg Performans Ayarları
    |--------------------------------------------------------------------------
    |
    | FFmpeg işlemleri için performans ayarlarını yapılandırma
    |
    */
    'threads' => env('FFMPEG_THREADS', 2),  // İşlemci çekirdek sayısı
    'timeout' => env('FFMPEG_TIMEOUT', 3600),  // İşlem zaman aşımı (saniye)

    /*
    |--------------------------------------------------------------------------
    | Depolama Ayarları
    |--------------------------------------------------------------------------
    |
    | İşlenen dosyaların kaydedileceği dizin
    |
    */
    'temp_directory' => env('FFMPEG_TEMP_DIR', storage_path('app/temp')),
    'output_directory' => env('FFMPEG_OUTPUT_DIR', storage_path('app/public/media')),
];
