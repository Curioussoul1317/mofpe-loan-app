<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanAudit;
use App\Models\User;

class LoanAuditService
{
    public function record(
        Loan $loan,
        User $user,
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null
    ): LoanAudit {
        return LoanAudit::create([
            'loan_id' => $loan->id,
            'user_id' => $user->id,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }
}