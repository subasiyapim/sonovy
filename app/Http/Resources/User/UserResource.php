<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray($request)
    {
        // Kullanıcının kazancı bir sayı mı, yoksa collection mı? Emin olalım.
        $total_earnings = is_numeric($this->earnings) ? $this->earnings : 0;

        // Komisyon oranını doğrula ve hesapla
        $commission_rate = is_numeric($this->commission_rate) ? ($this->commission_rate / 100) : 0;

        // Reel hakediş oranı: komisyonlar dağıtıldıktan sonra kalan oran
        $real_commission_rate = (1 - $commission_rate) * 100; // % olarak hesapla

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'commission_rate' => number_format($commission_rate * 100, 2), // Yüzdelik değer olarak
            'real_commission_rate' => number_format($real_commission_rate, 2), // Yüzdelik değer olarak
            //'earnings' => $total_earnings,
            //'earnings_after_commission' => number_format($total_earnings - ($total_earnings * $commission_rate), 2),
            // Kazanç üzerinden komisyon sonrası miktar
            'roles' => $this->whenLoaded('roles', $this->roles),
            'sub_users' => $this->whenLoaded('sub_users', $this->sub_users),
            'status' => $this->status->value,
            'country_id' => $this->country_id,
            'city_id' => $this->city_id,
            'language_id' => $this->language_id,
            'district_id' => $this->district_id,
            'address' => $this->address,
            'phone' => $this->phone,
            'status_text' => $this->status->title(),
            'created_at' => Carbon::parse($this->created_at)->locale(App::currentLocale())->translatedFormat('d F Y H:i'),
            'children' => UserResource::collection($this->whenLoaded('children')),
        ];
    }
}
