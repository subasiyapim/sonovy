<?php

namespace App\Http\Resources\Panel;

use App\Models\Country;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'model_type' => $this->model_type,
            'invoice_date' => $this->invoice_date,
            'user' => $this->user,
            'payment_info' => $this->payment_info,
            'amount' => $this->amount,
            'payment_method' => $this->payment_service,
            'status' => $this->status,
            'plan' => $this->plan,
        ];
    }


}
