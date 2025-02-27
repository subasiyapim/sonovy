<?php

namespace App\Http\Resources;

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
            'platform' => $this->platform,
            'report_date' => $this->report_date,
            'report_name' => $this->reportFile->name,
            'errors' => $this->errors,
            'total' => $this->sum('net_revenue'),
            'file_size' => $this->file_size,
            'created_at' => $this->created_at,
            'status' => $this->reportFile->status,
            'file' => $this->reportFile->file,
        ];
    }
}
