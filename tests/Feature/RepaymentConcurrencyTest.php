<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\Repayment;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RepaymentConcurrencyTest extends TestCase
{
    use DatabaseMigrations;

    public function test_second_repayment_waits_for_locked_loan(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            $this->markTestSkipped('This test requires MySQL.');
        }

        $user = User::factory()->create([
            'role' => User::ROLE_LOAN_OFFICER,
        ]);

        $currency = Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'decimal_places' => 2,
        ]);

        $customer = Customer::create([
            'name' => 'Concurrency Customer',
            'email' => 'concurrency@example.test',
            'phone' => '7000000',
            'status' => 'active',
        ]);

        $loan = Loan::create([
            'loan_number' => 'LN-LOCK-001',
            'customer_id' => $customer->id,
            'currency_id' => $currency->id,
            'principal_amount' => '100.00',
            'start_date' => '2026-01-01',
            'maturity_date' => '2027-01-01',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        config([
            'database.connections.lock_one'
                => config('database.connections.mysql'),

            'database.connections.lock_two'
                => config('database.connections.mysql'),
        ]);

        DB::purge('lock_one');
        DB::purge('lock_two');

        $first = DB::connection('lock_one');
        $second = DB::connection('lock_two');

        $first->beginTransaction();

        $first->table('loans')
            ->where('id', $loan->id)
            ->lockForUpdate()
            ->first();

        $first->table('repayments')->insert([
            'loan_id' => $loan->id,
            'currency_id' => $currency->id,
            'amount' => '80.00',
            'payment_date' => '2026-08-25',
            'reference_number' => 'PAY-LOCK-001',
            'created_by' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $second->statement(
            'SET SESSION innodb_lock_wait_timeout = 1'
        );

        $second->beginTransaction();

        $blocked = false;

        try {
            $second->table('loans')
                ->where('id', $loan->id)
                ->lockForUpdate()
                ->first();
        } catch (QueryException $e) {
            $blocked = true;

            $this->assertStringContainsString(
                'Lock wait timeout',
                $e->getMessage()
            );
        }

        if ($second->transactionLevel() > 0) {
            $second->rollBack();
        }

        $this->assertTrue($blocked);

        $first->commit();

        Sanctum::actingAs($user);

        $this->postJson(
            "/api/loans/{$loan->id}/repayments",
            [
                'currency_id' => $currency->id,
                'amount' => '30.00',
                'payment_date' => '2026-08-25',
            ]
        )
            ->assertStatus(422)
            ->assertJsonValidationErrors('amount');

        $totalPaid = (string) Repayment::where(
            'loan_id',
            $loan->id
        )->sum('amount');

        $this->assertSame(
            0,
            bccomp($totalPaid, '80.00', 8)
        );
    }
}