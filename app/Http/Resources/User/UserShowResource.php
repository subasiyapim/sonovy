<?php

namespace App\Http\Resources\User;

use App\Enums\PaymentStatusEnum;
use App\Models\Label;
use App\Models\Order;
use App\Models\Payment;
use App\Models\System\User;
use App\Services\EarningService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserShowResource extends JsonResource
{
    protected mixed $tab;

    public function __construct($resource, $tab)
    {
        parent::__construct($resource);
        $this->tab = $tab;
    }

    private function getTabContent()
    {
        return match ($this->tab) {
            'pricing' => $this->pricing(),
            'contracts' => $this->contracts(),
            'balances' => $this->balances(),
            'invoices' => $this->invoices(),
            'activities' => $this->activities(),
            'flags' => $this->flags(),
            'relations' => $this->relations(),
            'authorisations' => $this->authorisations(),
            default => $this->profile()
        };
    }


    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'roles' => $this->roles,
            'status' => $this->status->value,
            'email' => $this->email,
            'last_login' => $this->last_login,
            'tab' => $this->getTabContent()
        ];
    }

    private function pricing(): array
    {
        return [
            'commission_rate' => $this->commission_rate,
            'payment_threshold' => $this->payment_threshold,
            'currency' => $this->currency,
            'available_items' => $this->availablePlanItemsCount(),
            'history' => $this->orders
        ];
    }

    private function contracts()
    {
        return null;
    }

    private function balances(): array
    {
        return [
            'current_balance' => $this->calculateBalance(),
            'pending_out_payments' => $this->pendingOutPayments(),
            'pending_invoices' => $this->pendingInvoices(),
            'confirmed_total' => $this->confirmedTotal(),
        ];
    }

    private function calculateBalance(): float
    {
        return EarningService::balance($this->id);
    }

    private function pendingInvoices()
    {
        return Payment::where('user_id', $this->id)->get();
    }

    private function confirmedTotal()
    {
        return [];
    }

    private function pendingOutPayments(): float
    {
        return Payment::where('status', PaymentStatusEnum::PENDING->value)
            ->where('user_id', $this->id)
            ->sum('amount');
    }

    private function invoices()
    {
        return $this->orders->toArray();
    }

    private function activities()
    {
        return $this->activities->toArray();
    }

    private function relations()
    {
        return [
            'relations' => $this->children->toArray(),
            'products' => $this->getProducts(),
            'labels' => $this->getLabels(),
            'participants' => $this->getParticipants(),
        ];
    }

    private function flags()
    {
        return $this->flags?->toArray();
    }

    private function authorisations()
    {
        return null;
    }

    private function profile()
    {
        return [

        ];
    }

    private function getParticipants()
    {
        $participants = [];

        $this->products->each(function ($product) use (&$participants) {
            $product->songs->each(function ($song) use (&$participants) {
                $song->participants->load('user');
                $participants = array_merge($participants, $song->participants->map(function ($participant) {
                    return [
                        'name' => $participant->user->name,
                        'email' => $participant->user->email,
                        'branch_names' => $participant->branch_names,
                        'commission_rate' => $participant->user->commission_rate,
                        'realization' => 100 - $participant->user->commission_rate,
                        'status' => $participant->user->status->title()
                    ];
                })->toArray());
            });
        });

        return $participants;
    }

    private function getLabels()
    {
        return $this->labels->map(function ($label) {
            $label->load('country', 'user');
            return [
                'name' => $label->name,
                'country' => $label->country->name,
                'country_emoji' => $label->country->emoji,
                'phone' => $label->phone,
                'email' => $label->email,
                'commission_rate' => $label->user->commission_rate,
                'status' => $label->hasActive() ? 'Aktif şirket' : 'Pasif şirket'
            ];
        })->toArray();
    }

    private function getProducts()
    {
        return $this->products->map(function ($product) {
            $product->load('label', 'songs', 'artists');
            return [
                'type' => $product->type->value,
                'status' => $product->status->value,
                'status_name' => $product->status->title(),
                'image' => $product->image ? $product->image->getUrl('thumb') : null,
                'name' => $product->name,
                'artists' => $product->artists->map(function ($artist) {
                    return $artist->name;
                })->implode(', '),
                'label' => $product->label->name,
                'physical_release_date' => $product->physical_release_date,
                'song_count' => $product->songs->count(),
                'upc' => $product->upc_code,
            ];
        })->toArray();
    }
}
