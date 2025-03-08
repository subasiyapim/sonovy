<?php

namespace App\Http\Resources;

use App\Models\Platform;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class EarningReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Platform bilgisini doğrudan ilişkiden al
        $platform = $this->platform;

        // Toplam net geliri hesapla
        $totalNetRevenue = $this->earningReports()->sum('net_revenue');

        return [
            'id' => $this->id,
            'reportFileId' => $this->id,
            'platform' => $platform,
            'platform_name' => $platform?->name,
            'platformIcon' => $platform?->icon,
            'report_date' => $this->report_date ? Carbon::parse($this->report_date)->format('m Y') : null,
            'report_name' => $this->name,
            'errors' => $this->errors,
            'total' => Number::format($totalNetRevenue ?? 0, 4),
            'file_size' => $this->file?->size,
            'created_at' => $this->created_at,
            'status' => $this->status,
            'file' => $this->file,
        ];
    }
}
