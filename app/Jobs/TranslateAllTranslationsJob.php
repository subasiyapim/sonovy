<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\File;

class TranslateAllTranslationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sourceLang;
    protected $targetLang;
    protected $chunkSize;
    protected $skipExisting;

    /**
     * Create a new job instance.
     */
    public function __construct($sourceLang, $targetLang, $chunkSize = 5, $skipExisting = false)
    {
        $this->sourceLang = $sourceLang;
        $this->targetLang = $targetLang;
        $this->chunkSize = $chunkSize;
        $this->skipExisting = $skipExisting;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $sourceDir = lang_path($this->sourceLang);
        $targetDir = lang_path($this->targetLang);

        // Hedef dil dizinini oluştur (eğer yoksa)
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Source dil dizininde bulunan tüm PHP dosyalarını bul
        $phpFiles = File::allFiles($sourceDir);

        foreach ($phpFiles as $file) {
            $relativePath = $file->getRelativePathname();
            $targetFilePath = "{$targetDir}/{$relativePath}";

            // Source dilindeki çevirileri al
            $sourceTranslations = include $file->getPathname();
            $targetTranslations = is_file($targetFilePath)
                ? include $targetFilePath
                : [];

            $translatedTexts = [];

            foreach (array_chunk($sourceTranslations, $this->chunkSize, true) as $chunk) {
                foreach ($chunk as $key => $text) {
                    if ($this->skipExisting && isset($targetTranslations[$key])) {
                        continue; // Eğer skip_existing true ise ve çeviri mevcutsa atla
                    }

                    // Eğer skip_existing false ise veya çeviri mevcut değilse çeviriyi yap
                    try {
                        if (is_array($text)) {
                            $translatedTexts[$key] = $this->translateArray($text, $this->targetLang);
                        } else {
                            $translatedTexts[$key] = $this->translateWithPlaceholders($text, $this->targetLang);
                        }
                    } catch (\Exception $e) {
                        // Hata durumunu loglayabilirsiniz.
                    }
                }
            }

            // Tüm çevirileri hedef dosyaya kaydet
            $this->saveTranslations(array_merge($targetTranslations, $translatedTexts), $targetFilePath);
        }
    }

    private function translateArray(array $texts, $targetLang): array
    {
        $translateService = new GoogleTranslate();
        $translateService->setSource($this->sourceLang);
        $translateService->setTarget($targetLang);

        $translated = [];
        foreach ($texts as $key => $text) {
            if (is_array($text)) {
                $translated[$key] = $this->translateArray($text, $targetLang);
            } else {
                $translated[$key] = $this->translateWithPlaceholders($text, $targetLang);
            }
        }
        return $translated;
    }

    private function translateWithPlaceholders($text, $targetLang)
    {
        $translateService = new GoogleTranslate();
        $translateService->setSource($this->sourceLang);
        $translateService->setTarget($targetLang);

        preg_match_all('/:([a-zA-Z_]+)/', $text, $placeholders);

        $placeholderReplacements = [];
        foreach ($placeholders[0] as $index => $placeholder) {
            $placeholderReplacements[$index] = "__PLACEHOLDER_{$index}__";
        }
        $textWithPlaceholders = str_replace($placeholders[0], $placeholderReplacements, $text);

        $translatedText = $translateService->translate($textWithPlaceholders);

        foreach ($placeholders[0] as $index => $placeholder) {
            $translatedText = str_replace("__PLACEHOLDER_{$index}__", $placeholder, $translatedText);
        }

        return $translatedText;
    }

    private function saveTranslations(array $translations, $filePath): void
    {
        File::put($filePath, "<?php\n\nreturn " . var_export($translations, true) . ";\n");
    }
}
