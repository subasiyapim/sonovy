<?php

namespace App\Services;

use App\Enums\ProductTypeEnum;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Format\Audio\Mp3;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use FFMpeg\Format\Video\X264;

class FFMpegServices
{
    /**
     * FFMpeg yapılandırma önbelleği
     *
     * @var array|null
     */
    private static $config = null;

    /**
     * FFProbe nesnesi oluştur
     *
     * @return FFProbe
     * @throws \Exception FFProbe oluşturulurken bir hata oluşursa
     */
    private static function ffprobe(): FFProbe
    {
        try {
            return FFProbe::create(self::getConfig());
        } catch (\Throwable $e) {
            Log::error("FFProbe oluşturulurken hata: " . $e->getMessage(), [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw $e;
        }
    }

    /**
     * FFMpeg nesnesi oluştur
     *
     * @return FFMpeg
     * @throws \Exception FFMpeg oluşturulurken bir hata oluşursa
     */
    private static function ffmpeg(): FFMpeg
    {
        try {
            return FFMpeg::create(self::getConfig());
        } catch (\Throwable $e) {
            Log::error("FFMpeg oluşturulurken hata: " . $e->getMessage(), [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw $e;
        }
    }

    /**
     * FFMpeg yapılandırma ayarlarını al
     *
     * @return array
     */
    private static function getConfig(): array
    {
        // Yapılandırma önbellekte yoksa hesapla
        if (self::$config === null) {
            // Yapılandırma cache'i temizle
            Cache::forget('ffmpeg_config');

            // Yapılandırmayı 24 saat boyunca önbellekte tut (nadiren değişir)
            self::$config = Cache::remember('ffmpeg_config', 60 * 60 * 24, function () {
                // Docker ortamında mıyız?
                $isDocker = file_exists('/.dockerenv') || (getenv('DOCKER_CONTAINER') !== false);

                Log::info("FFMpeg yapılandırması oluşturuluyor", [
                    'is_docker' => $isDocker,
                    'env_ffmpeg_path' => env('FFMPEG_PATH'),
                    'env_ffprobe_path' => env('FFPROBE_PATH')
                ]);

                // Docker içinde mi çalışıyoruz?
                if ($isDocker) {
                    $config = [
                        'timeout' => config('ffmpeg.timeout', 3600),
                        'ffmpeg.threads' => config('ffmpeg.threads', 2),
                        'ffmpeg.binaries' => '/usr/bin/ffmpeg',  // Docker içinde sabit yol
                        'ffprobe.binaries' => '/usr/bin/ffprobe'  // Docker içinde sabit yol
                    ];

                    Log::info("Docker ortamı tespit edildi. Sabit yollar kullanılıyor.", [
                        'ffmpeg_path' => $config['ffmpeg.binaries'],
                        'ffprobe_path' => $config['ffprobe.binaries']
                    ]);

                    return $config;
                }

                // Docker dışında çalışıyoruz - normal konfigürasyon
                $config = [
                    'timeout' => config('ffmpeg.timeout', 3600),
                    'ffmpeg.threads' => config('ffmpeg.threads', 4),
                    'ffmpeg.binaries' => config('ffmpeg.ffmpeg_path', env('FFMPEG_PATH', '/usr/bin/ffmpeg')),
                    'ffprobe.binaries' => config('ffmpeg.ffprobe_path', env('FFPROBE_PATH', '/usr/bin/ffprobe'))
                ];

                // FFmpeg ve FFProbe binary'lerinin varlığını kontrol et
                try {
                    // FFmpeg binary
                    $ffmpegPath = $config['ffmpeg.binaries'];
                    if (!file_exists($ffmpegPath)) {
                        // PATH'de aramayı dene
                        $ffmpegPath = trim(shell_exec('which ffmpeg') ?: '');

                        if (empty($ffmpegPath)) {
                            $locations = [
                                '/usr/bin/ffmpeg',
                                '/usr/local/bin/ffmpeg',
                                '/opt/homebrew/bin/ffmpeg',
                                '/usr/local/bin/ffmpeg'
                            ];

                            foreach ($locations as $location) {
                                if (file_exists($location)) {
                                    $ffmpegPath = $location;
                                    break;
                                }
                            }
                        }

                        if (empty($ffmpegPath) || !file_exists($ffmpegPath)) {
                            Log::warning("FFmpeg binary bulunamadı.", [
                                'checked_path' => $config['ffmpeg.binaries'],
                                'is_docker' => $isDocker
                            ]);
                        } else {
                            $config['ffmpeg.binaries'] = $ffmpegPath;
                            Log::info("FFmpeg binary bulundu: " . $ffmpegPath);
                        }
                    } else {
                        Log::info("FFmpeg binary doğrudan bulundu: " . $ffmpegPath);
                    }

                    // FFProbe binary
                    $ffprobePath = $config['ffprobe.binaries'];
                    if (!file_exists($ffprobePath)) {
                        // PATH'de aramayı dene
                        $ffprobePath = trim(shell_exec('which ffprobe') ?: '');

                        if (empty($ffprobePath)) {
                            $locations = [
                                '/usr/bin/ffprobe',
                                '/usr/local/bin/ffprobe',
                                '/opt/homebrew/bin/ffprobe'
                            ];

                            foreach ($locations as $location) {
                                if (file_exists($location)) {
                                    $ffprobePath = $location;
                                    break;
                                }
                            }
                        }

                        if (empty($ffprobePath) || !file_exists($ffprobePath)) {
                            Log::warning("FFProbe binary bulunamadı.", [
                                'checked_path' => $config['ffprobe.binaries'],
                                'is_docker' => $isDocker
                            ]);
                        } else {
                            $config['ffprobe.binaries'] = $ffprobePath;
                            Log::info("FFProbe binary bulundu: " . $ffprobePath);
                        }
                    } else {
                        Log::info("FFProbe binary doğrudan bulundu: " . $ffprobePath);
                    }

                    // FFmpeg ve FFProbe sürümlerini log'la
                    try {
                        $ffmpegVersion = trim(shell_exec("$ffmpegPath -version | head -n1") ?: 'Unknown');
                        $ffprobeVersion = trim(shell_exec("$ffprobePath -version | head -n1") ?: 'Unknown');

                        Log::info("FFMpeg Tools:", [
                            'ffmpeg_version' => $ffmpegVersion,
                            'ffprobe_version' => $ffprobeVersion,
                            'ffmpeg_path' => $ffmpegPath,
                            'ffprobe_path' => $ffprobePath,
                            'is_docker' => $isDocker
                        ]);
                    } catch (\Throwable $e) {
                        Log::warning("FFmpeg sürüm bilgisi alınamadı: " . $e->getMessage());
                    }
                } catch (\Throwable $e) {
                    Log::error("FFmpeg yapılandırma ayarları kontrol edilirken hata: " . $e->getMessage(), [
                        'trace' => $e->getTraceAsString()
                    ]);
                }

                return $config;
            });
        }

        return self::$config;
    }

    /**
     * Süreyi dakika:saniye formatına dönüştür
     *
     * @param float $seconds
     * @return string
     */
    private static function formatDuration($seconds): string
    {
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        return sprintf('%02d:%02d', $minutes, $remainingSeconds);
    }

    /**
     * Medya dosyasının detaylarını al
     *
     * @param string $file Dosya yolu
     * @return array Medya detayları
     */
    public static function getMediaDetails($file)
    {
        // Docker ortamında olup olmadığımızı kontrol et
        $isDocker = file_exists('/.dockerenv') || (getenv('DOCKER_CONTAINER') !== false);

        Log::info("FFMpegServices: Medya dosyası analiz ediliyor", [
            'file' => $file,
            'is_docker' => $isDocker,
            'exists' => File::exists($file)
        ]);

        // Dosyanın var olup olmadığını kontrol et
        if (!File::exists($file)) {
            Log::error("FFMpegServices: Dosya bulunamadı", ['file' => $file, 'is_docker' => $isDocker]);
            return ['status' => false, 'error' => 'Dosya bulunamadı: ' . $file];
        }

        // Dosyanın okunabilir olup olmadığını kontrol et
        if (!is_readable($file)) {
            Log::error("FFMpegServices: Dosya okunabilir değil", [
                'file' => $file,
                'permissions' => File::permissions($file),
                'is_docker' => $isDocker
            ]);
            return ['status' => false, 'error' => 'Dosya okunabilir değil: ' . $file];
        }

        // Dosyanın boyutunu kontrol et (0 byte dosyalar işlenemez)
        $fileSize = File::size($file);
        if ($fileSize <= 0) {
            Log::error("FFMpegServices: Dosya boş veya boyut okunamadı", ['file' => $file, 'size' => $fileSize]);
            return ['status' => false, 'error' => 'Dosya boş veya geçersiz: ' . $file];
        }

        // Önbellek anahtarı oluştur (dosyanın MD5 hash'i ve son değiştirilme zamanı)
        $fileHash = md5($file . File::lastModified($file));
        $cacheKey = 'media_details_' . $fileHash;

        // Önbelleği temizle (her seferinde yeniden analiz et)
        Cache::forget($cacheKey);

        // 24 saat boyunca önbellekte tut (medya detayları genellikle değişmez)
        return Cache::remember($cacheKey, 60 * 60 * 24, function () use ($file, $fileSize, $isDocker) {
            try {
                Log::info("FFMpegServices: Medya detayları alınıyor", [
                    'file' => $file,
                    'size' => $fileSize,
                    'mime' => File::mimeType($file),
                    'is_docker' => $isDocker
                ]);

                // FFProbe nesnesini oluşturmayı dene
                try {
                    $ffprobe = self::ffprobe();

                    Log::info("FFMpegServices: FFProbe oluşturuldu", [
                        'config' => self::getConfig()
                    ]);
                } catch (\Throwable $e) {
                    Log::error("FFMpegServices: FFProbe oluşturulamadı", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return ['status' => false, 'error' => 'FFProbe oluşturulamadı: ' . $e->getMessage()];
                }

                // Medya akışlarını alma
                try {
                    $streams = $ffprobe->streams($file);

                    Log::info("FFMpegServices: Streams alındı", [
                        'count' => count($streams),
                        'file' => $file
                    ]);
                } catch (\Throwable $e) {
                    Log::error("FFMpegServices: Medya akışları alınamadı", [
                        'file' => $file,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return ['status' => false, 'error' => 'Medya akışları alınamadı: ' . $e->getMessage()];
                }

                // Ses ve video akışlarını kontrol et
                try {
                    $audioStream = $streams->audios()->first();
                    $videoStream = $streams->videos()->first();

                    Log::info("FFMpegServices: Medya türleri tespit edildi", [
                        'has_audio' => !is_null($audioStream),
                        'has_video' => !is_null($videoStream),
                        'file' => $file
                    ]);
                } catch (\Throwable $e) {
                    Log::error("FFMpegServices: Ses/video akışları işlenemedi", [
                        'file' => $file,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return ['status' => false, 'error' => 'Ses/video akışları işlenemedi: ' . $e->getMessage()];
                }

                // Geçerli bir medya akışının olup olmadığını kontrol et
                if (!$audioStream && !$videoStream) {
                    Log::warning("FFMpegServices: Desteklenmeyen veya bozuk medya dosyası", ['file' => $file]);
                    return ['status' => false, 'error' => 'Desteklenmeyen veya bozuk medya dosyası'];
                }

                try {
                    // Format bilgilerini al
                    $format = $ffprobe->format($file);

                    if ($audioStream) {
                        // Ses dosyası işleme
                        return [
                            'status' => true,
                            'type' => ProductTypeEnum::SOUND->value,
                            'duration' => self::formatDuration($audioStream->get('duration')),
                            'details' => [
                                'bit_rate' => $audioStream->get('bit_rate'),
                                'sample_rate' => $audioStream->get('sample_rate'),
                                'channels' => $audioStream->get('channels'),
                                'duration' => $audioStream->get('duration'),
                                'format' => $format->get('format_name'),
                                'size' => $format->get('size'),
                                'tags' => $format->get('tags') ?: [],
                            ]
                        ];
                    } elseif ($videoStream) {
                        // Video dosyası işleme
                        return [
                            'status' => true,
                            'type' => ProductTypeEnum::VIDEO->value,
                            'duration' => self::formatDuration($videoStream->get('duration')),
                            'details' => [
                                'width' => $videoStream->get('width'),
                                'height' => $videoStream->get('height'),
                                'bit_rate' => $videoStream->get('bit_rate'),
                                'frame_rate' => $videoStream->get('r_frame_rate'),
                                'duration' => $videoStream->get('duration'),
                                'format' => $format->get('format_name'),
                                'size' => $format->get('size'),
                                'tags' => $format->get('tags') ?: [],
                            ]
                        ];
                    } else {
                        // Bu noktaya gelmemeli ama güvenlik için
                        return ['status' => false, 'error' => 'Desteklenmeyen medya formatı'];
                    }
                } catch (\Throwable $e) {
                    Log::error("FFMpegServices: Format bilgileri alınırken hata oluştu", [
                        'file' => $file,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return ['status' => false, 'error' => 'Format bilgileri alınamadı: ' . $e->getMessage()];
                }
            } catch (\Throwable $e) {
                Log::error("FFMpegServices: Medya detayları alınırken kritik hata oluştu", [
                    'file' => $file,
                    'error' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'file_location' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                return ['status' => false, 'error' => 'Medya işlenirken hata: ' . $e->getMessage()];
            }
        });
    }

    /**
     * Medya dosyasını kırp
     *
     * @param string $file Kırpılacak dosyanın yolu
     * @param string $fileName Çıktı dosyasının adı
     * @param int $start Başlangıç zamanı (saniye)
     * @param int $end Bitiş zamanı (saniye)
     * @param bool $isAudio Ses dosyası mı? (varsayılan: true)
     * @return array İşlem durumu
     */
    public static function trimMedia(string $file, string $fileName, int $start, int $end, bool $isAudio = true): array
    {
        $cacheKey = 'trim_media_' . md5($file . $fileName . $start . $end . $isAudio);

        // İşlem zaten yapıldıysa ve önbellekte varsa, direkt döndür
        if (File::exists($fileName) && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            Log::info("FFMpegServices: Medya kırpma işlemi başlatıldı", [
                'file' => $file,
                'output' => $fileName,
                'start' => $start,
                'end' => $end,
                'type' => $isAudio ? 'audio' : 'video',
            ]);

            $ffmpeg = self::ffmpeg();
            $ffmpegFile = $ffmpeg->open($file);
            $from = TimeCode::fromSeconds($start);
            $to = TimeCode::fromSeconds($end - $start);

            if ($isAudio) {
                $ffmpegFile->filters()->clip($from, $to);
                $ffmpegFile->save(new Mp3(), $fileName);
            } else {
                $ffmpegFile->filters()->clip($from, $to);
                $ffmpegFile->save(new X264(), $fileName);
            }

            $result = ['status' => true];

            // Sonucu önbelleğe al
            Cache::put($cacheKey, $result, 60 * 60 * 24); // 24 saat

            return $result;
        } catch (\Throwable $e) {
            Log::error("FFMpegServices: Medya kırpma işlemi sırasında hata oluştu", [
                'file' => $file,
                'output' => $fileName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $result = ['status' => false, 'error' => $e->getMessage()];

            // Hata durumunu da önbelleğe al (kısa süreli)
            Cache::put($cacheKey, $result, 60 * 10); // 10 dakika

            return $result;
        }
    }

    /**
     * Medya dosyasından önizleme oluştur
     *
     * @param string $file Kaynak dosyası
     * @param string $outputFile Çıktı dosyası
     * @param int $startTime Başlangıç zamanı (saniye)
     * @param int $duration Süre (saniye)
     * @param bool $isAudio Ses dosyası mı?
     * @return array İşlem durumu
     */
    public static function createPreview(string $file, string $outputFile, int $startTime = 0, int $duration = 30, bool $isAudio = true): array
    {
        return self::trimMedia($file, $outputFile, $startTime, $startTime + $duration, $isAudio);
    }

    /**
     * Ses dosyasının kalite kontrolünü yapar
     *
     * @param string $filePath Dosya yolu
     * @return array Kalite kontrol sonuçları
     */
    public static function checkAudioQuality(string $filePath): array
    {
        try {
            $ffprobe = self::ffprobe();

            // Ses akışını al
            $streams = $ffprobe->streams($filePath);
            $audioStream = $streams->audios()->first();

            if (!$audioStream) {
                return [
                    'status' => false,
                    'error' => 'Ses akışı bulunamadı'
                ];
            }

            $format = $ffprobe->format($filePath);

            // Temel özellikler
            $specs = [
                'channels' => $audioStream->get('channels'),
                'sample_rate' => $audioStream->get('sample_rate'),
                'codec_name' => $audioStream->get('codec_name'),
                'bit_rate' => $format->get('bit_rate'),
                'duration' => $format->get('duration'),
                'size' => $format->get('size'),
            ];

            $issues = [];

            // Stereo kontrolü
            if ($specs['channels'] !== 2) {
                $issues[] = 'Ses dosyası stereo değil. Stereo (2 kanal) olmalıdır.';
            }

            // Sample rate kontrolü (44.1kHz veya üstü olmalı)
            if ($specs['sample_rate'] < 44100) {
                $issues[] = 'Sample rate çok düşük. En az 44.1kHz olmalıdır.';
            }

            // Bit rate kontrolü (en az 192kbps)
            if ($specs['bit_rate'] < 192000) {
                $issues[] = 'Bit rate çok düşük. En az 192kbps olmalıdır.';
            }

            // Sessizlik kontrolü
            $silenceAnalysis = self::checkSilence($filePath);
            if ($silenceAnalysis['has_long_silences']) {
                $issues[] = 'Dosyada uzun sessizlik periyotları var';
            }

            // Ses seviyesi kontrolü
            $levelAnalysis = self::checkAudioLevels($filePath);
            if (!empty($levelAnalysis['issues'])) {
                $issues = array_merge($issues, $levelAnalysis['issues']);
            }

            return [
                'status' => true,
                'specs' => $specs,
                'issues' => $issues,
                'silence_analysis' => $silenceAnalysis,
                'level_analysis' => $levelAnalysis,
                'is_valid' => empty($issues)
            ];

        } catch (\Exception $e) {
            Log::error('Ses kalite kontrolü yapılırken hata oluştu: ' . $e->getMessage());
            return [
                'status' => false,
                'error' => 'Ses kalite kontrolü yapılırken bir hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Ses dosyasındaki sessizlik sürelerini kontrol eder
     */
    private static function checkSilence(string $filePath, float $threshold = -50, float $duration = 2): array
    {
        try {
            $silenceOutput = shell_exec("ffmpeg -i {$filePath} -af silencedetect=noise={$threshold}dB:d={$duration} -f null - 2>&1");

            preg_match_all('/silence_start: ([\d\.]+).*?silence_end: ([\d\.]+)/s', $silenceOutput, $matches);

            $silencePeriods = [];
            if (!empty($matches[1]) && !empty($matches[2])) {
                for ($i = 0; $i < count($matches[1]); $i++) {
                    $silencePeriods[] = [
                        'start' => floatval($matches[1][$i]),
                        'end' => floatval($matches[2][$i]),
                        'duration' => floatval($matches[2][$i]) - floatval($matches[1][$i])
                    ];
                }
            }

            $longSilences = array_filter($silencePeriods, fn($period) => $period['duration'] > $duration);

            return [
                'silence_periods' => $silencePeriods,
                'long_silences' => $longSilences,
                'has_long_silences' => !empty($longSilences)
            ];

        } catch (\Exception $e) {
            Log::error('Sessizlik analizi yapılırken hata oluştu: ' . $e->getMessage());
            return [
                'error' => 'Sessizlik analizi yapılırken bir hata oluştu',
                'has_long_silences' => false
            ];
        }
    }

    /**
     * Ses seviyelerini kontrol eder (clipping ve distortion için)
     */
    private static function checkAudioLevels(string $filePath): array
    {
        try {
            $volumeOutput = shell_exec("ffmpeg -i {$filePath} -filter:a volumedetect -f null - 2>&1");

            preg_match('/mean_volume: ([\-\d\.]+) dB/', $volumeOutput, $meanMatch);
            preg_match('/max_volume: ([\-\d\.]+) dB/', $volumeOutput, $maxMatch);

            $meanVolume = $meanMatch[1] ?? null;
            $maxVolume = $maxMatch[1] ?? null;

            $issues = [];

            // Maksimum ses seviyesi kontrolü
            if ($maxVolume !== null && $maxVolume > -1) {
                $issues[] = 'Ses seviyesi çok yüksek, clipping olabilir.';
            }

            // Ortalama ses seviyesi kontrolü
            if ($meanVolume !== null && $meanVolume < -30) {
                $issues[] = 'Ortalama ses seviyesi çok düşük.';
            }

            return [
                'mean_volume' => $meanVolume,
                'max_volume' => $maxVolume,
                'issues' => $issues,
                'is_valid' => empty($issues)
            ];

        } catch (\Exception $e) {
            Log::error('Ses seviyeleri analiz edilirken hata oluştu: ' . $e->getMessage());
            return [
                'error' => 'Ses seviyeleri analiz edilirken bir hata oluştu',
                'is_valid' => false
            ];
        }
    }
}
