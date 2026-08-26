<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\Repayment;
use App\Models\User;
use App\Services\LoanAuditService;
use App\Services\RepaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use RuntimeException;
use Tests\TestCase;

class RepaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Currency $usd;
    private Currency $mvr;
    private Loan $loan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => User::ROLE_LOAN_OFFICER,
        ]);

        $this->usd = Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'decimal_places' => 2,
        ]);

        $this->mvr = Currency::create([
            'code' => 'MVR',
            'name' => 'Maldivian Rufiyaa',
            'decimal_places' => 2,
        ]);

        $customer = Customer::create([
            'name' => 'Ahmed Asad',
            'email' => 'asad@example.com',
            'phone' => '7777777',
            'status' => 'active',
        ]);

        $this->loan = Loan::create([
            'loan_number' => 'LN-001',
            'customer_id' => $customer->id,
            'currency_id' => $this->usd->id,
            'principal_amount' => '1000000.00',
            'start_date' => '2026-01-01',
            'maturity_date' => '2027-01-01',
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        Sanctum::actingAs($this->user);
    }

    public function test_repayment_can_be_recorded(): void
    {
        $this->postJson(
            "/api/loans/{$this->loan->id}/repayments",
            $this->repaymentData('250000.00')
        )->assertSuccessful();

        $repayment = Repayment::first();

        $this->assertSame(
            'PAY-000001',
            $repayment->reference_number
        );
    }

    public function test_overpayment_is_rejected(): void
    {
        $this->postJson(
            "/api/loans/{$this->loan->id}/repayments",
            $this->repaymentData('1000000.01')
        )->assertStatus(422);

        $this->assertDatabaseCount('repayments', 0);
    }

    public function test_wrong_currency_is_rejected(): void
    {
        $data = $this->repaymentData('100.00');

        $data['currency_id'] = $this->mvr->id;

        $this->postJson(
            "/api/loans/{$this->loan->id}/repayments",
            $data
        )->assertStatus(422);
    }

    public function test_repayments_update_balance(): void
    {
        $this->postJson(
            "/api/loans/{$this->loan->id}/repayments",
            $this->repaymentData('250000.00')
        )->assertSuccessful();

        $this->postJson(
            "/api/loans/{$this->loan->id}/repayments",
            $this->repaymentData('100000.00')
        )->assertSuccessful();

        $this->getJson("/api/loans/{$this->loan->id}")
            ->assertJsonPath(
                'data.outstanding_balance',
                '650000.00'
            );
    }

    public function test_failed_audit_rolls_back_repayment(): void
    {
        $this->mock(LoanAuditService::class)
            ->shouldReceive('record')
            ->once()
            ->andThrow(new RuntimeException('Audit failed'));

        $service = app(RepaymentService::class);

        try {
            $service->record(
                $this->loan,
                $this->repaymentData('100.00'),
                $this->user
            );
        } catch (RuntimeException) {
            //
        }

        $this->assertDatabaseCount('repayments', 0);
    }

    private function repaymentData(string $amount): array
    {
        return [
            'currency_id' => $this->usd->id,
            'amount' => $amount,
            'payment_date' => '2026-08-25',
        ];
    }
}