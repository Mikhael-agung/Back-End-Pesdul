<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array { return ['status' => $this->status, 'transaction_id' => $this->transaction_id, 'gross_amount' => $this->gross_amount, 'paid_at' => $this->paid_at]; }
}
