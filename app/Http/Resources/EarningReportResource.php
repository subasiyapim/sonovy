<?php

namespace App\Http\Resources;

use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EarningReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reportFileId' => $this->reportFile->id,
            'platform' => $this->whenLoaded('platform'),
            'platformIcon' => $this->getPlatformIcon($this->platform),
            'report_date' => $this->report_date,
            'report_name' => $this->reportFile->name,
            'errors' => $this->reportFile->errors,
            'total' => $this->getPlatformTotal($this->platform),
            'file_size' => $this->file_size,
            'created_at' => $this->created_at,
            'status' => $this->reportFile->status,
            'file' => $this->reportFile->file,
        ];
    }

    /**
     * Get platform icon
     *
     * @param  string  $platform
     *
     * @return string
     */
    private function getPlatformIcon(string $platform)
    {
        $platform = strtolower($platform);
        $platformModel = Platform::where('name', $platform)->first();
        return $platformModel ? $platformModel?->icon : null;
    }

    /**
     * Belirli bir platforma ait toplam geliri hesaplar
     *
     * @param  string  $platform
     *
     * @return string
     */
    private function getPlatformTotal(string $platform): string
    {
        // Platform adını küçük harfe çeviriyoruz (tutarlılık için)
        $platformName = strtolower($platform);

        // Aynı platforma ait tüm raporların toplamını hesapla
        $total = \App\Models\EarningReport::where('platform', $platformName)
            ->where('earning_report_file_id', $this->reportFile->id)
            ->sum('net_revenue');

        return (string) $total;
    }
}
