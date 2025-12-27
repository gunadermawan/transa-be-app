<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuickServiceOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'type' => $this->type,
            'status' => $this->status,
            'pax' => $this->pax,
            'table_number' => $this->table_number,
            'customer_name' => $this->customer_name,
            'notes' => $this->notes,
            'items_count' => $this->items->count(),
            'items' => QuickServiceOrderItemResource::collection($this->whenLoaded('items')),
            'subtotal' => (float) $this->subtotal,
            'tax' => (float) $this->tax,
            'tax_percentage' => (float) $this->tax_percentage,
            'service_charge' => (float) $this->service_charge,
            'service_charge_percentage' => (float) $this->service_charge_percentage,
            'discount' => (float) $this->discount,
            'discount_percentage' => (float) $this->discount_percentage,
            'total' => (float) $this->total,
            'invoice_number' => $this->invoice_number,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'paid_amount' => (float) $this->paid_amount,
            'change' => (float) $this->change,
            'cashier' => [
                'id' => $this->cashier->id,
                'name' => $this->cashier->name,
                'email' => $this->cashier->email ?? null,
            ],
            'saved_at' => $this->saved_at?->toIso8601String(),
            'cancelled_at' => $this->cancelled_at?->toIso8601String(),
            'cancel_reason' => $this->cancel_reason,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
