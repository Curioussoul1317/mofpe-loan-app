<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanAuditResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'action' => $this->action,
            'old_values' => $this->old_values,
            'new_values' => $this->new_values,

            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],

            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}