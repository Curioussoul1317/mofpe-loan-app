<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $officer;
    private Currency $usd;
    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMINISTRATOR,
        ]);

        $this->officer = User::factory()->create([
            'role' => User::ROLE_LOAN_OFFICER,
        ]);

        $this->usd = Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'decimal_places' => 2,
        ]);

        $this->customer = Customer::create([
            'name' => 'Ahmed Asad',
            'email' => 'asad@example.com',
            'phone' => '7777777',
            'status' => 'active',
        ]);
    }

    public function test_loan_can_be_created(): void
    {
        Sanctum::actingAs($this->officer);

        $this->postJson('/api/loans', $this->loanData())
            ->assertSuccessful();

        $loan = Loan::first();

        $this->assertSame(
            'LN-000001',
            $loan->loan_number
        );

        $this->assertDatabaseHas('loan_audits', [
            'loan_id' => $loan->id,
            'action' => 'created',
        ]);
    }

public function test_loan_numbers_are_generated(): void
{
    Sanctum::actingAs($this->officer);

    $this->postJson('/api/loans', $this->loanData())
        ->assertSuccessful();

    $this->postJson('/api/loans', $this->loanData())
        ->assertSuccessful();

    $loans = Loan::orderBy('id')->get();

    foreach ($loans as $loan) {
        $expected = 'LN-' . str_pad(
            $loan->id,
            6,
            '0',
            STR_PAD_LEFT
        );

        $this->assertSame(
            $expected,
            $loan->loan_number
        );
    }

    $this->assertNotSame(
        $loans[0]->loan_number,
        $loans[1]->loan_number
    );
}

    public function test_zero_principal_is_rejected(): void
    {
        Sanctum::actingAs($this->officer);

        $data = $this->loanData();
        $data['principal_amount'] = '0';

        $this->postJson('/api/loans', $data)
            ->assertStatus(422);
    }

    public function test_invalid_customer_is_rejected(): void
    {
        Sanctum::actingAs($this->officer);

        $data = $this->loanData();
        $data['customer_id'] = 99999;

        $this->postJson('/api/loans', $data)
            ->assertStatus(422);
    }

    public function test_invalid_currency_is_rejected(): void
    {
        Sanctum::actingAs($this->officer);

        $data = $this->loanData();
        $data['currency_id'] = 99999;

        $this->postJson('/api/loans', $data)
            ->assertStatus(422);
    }

    public function test_maturity_date_cannot_be_before_start_date(): void
    {
        Sanctum::actingAs($this->officer);

        $data = $this->loanData();
        $data['maturity_date'] = '2025-12-31';

        $this->postJson('/api/loans', $data)
            ->assertStatus(422);
    }

    public function test_admin_can_close_loan(): void
    {
        $loan = $this->makeLoan();

        Sanctum::actingAs($this->admin);

        $this->patchJson(
            "/api/loans/{$loan->id}",
            ['status' => 'closed']
        )->assertSuccessful();

        $this->assertDatabaseHas('loan_audits', [
            'loan_id' => $loan->id,
            'action' => 'updated',
        ]);
    }

    public function test_officer_cannot_edit_loan(): void
    {
        $loan = $this->makeLoan();

        Sanctum::actingAs($this->officer);

        $this->patchJson(
            "/api/loans/{$loan->id}",
            ['status' => 'closed']
        )->assertForbidden();
    }

    private function loanData(): array
    {
        return [
            'customer_id' => $this->customer->id,
            'currency_id' => $this->usd->id,
            'principal_amount' => '1000000.00',
            'start_date' => '2026-01-01',
            'maturity_date' => '2027-01-01',
            'status' => 'active',
        ];
    }

    private function makeLoan(): Loan
    {
        return Loan::create([
            'loan_number' => 'LN-TEST-001',
            'customer_id' => $this->customer->id,
            'currency_id' => $this->usd->id,
            'principal_amount' => '1000000.00',
            'start_date' => '2026-01-01',
            'maturity_date' => '2027-01-01',
            'status' => 'active',
            'created_by' => $this->officer->id,
        ]);
    }
}