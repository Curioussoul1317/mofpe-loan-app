<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoanManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $administrator;
    private User $officer;
    private Currency $usd;
    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->administrator =
            User::factory()->create([
                'role' =>
                    User::ROLE_ADMINISTRATOR,
            ]);

        $this->officer =
            User::factory()->create([
                'role' =>
                    User::ROLE_LOAN_OFFICER,
            ]);

        $this->usd = Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'decimal_places' => 2,
        ]);

        $this->customer = Customer::create([
            'name' => 'John Smith',
            'email' => 'john@example.test',
            'phone' => '7777777',
            'status' => 'active',
        ]);
    }

    public function test_administrator_can_create_customer(): void
    {
        Sanctum::actingAs(
            $this->administrator
        );

        $response = $this->postJson(
            '/api/customers',
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.test',
                'phone' => '7888888',
                'status' => 'active',
            ]
        );

        $response->assertSuccessful();

        $this->assertDatabaseHas(
            'customers',
            [
                'email' =>
                    'jane@example.test',
            ]
        );
    }

    public function test_loan_officer_cannot_create_customer(): void
    {
        Sanctum::actingAs($this->officer);

        $response = $this->postJson(
            '/api/customers',
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.test',
                'phone' => '7888888',
                'status' => 'active',
            ]
        );

        $response->assertForbidden();
    }

    public function test_valid_loan_can_be_created(): void
    {
        Sanctum::actingAs($this->officer);

        $response = $this->postJson(
            '/api/loans',
            $this->validLoanData()
        );

        $response->assertSuccessful();

        $this->assertDatabaseHas(
            'loans',
            [
                'loan_number' =>
                    'LN-000100',

                'customer_id' =>
                    $this->customer->id,

                'currency_id' =>
                    $this->usd->id,
            ]
        );

        $this->assertDatabaseHas(
            'loan_audits',
            [
                'action' => 'created',
                'user_id' =>
                    $this->officer->id,
            ]
        );
    }

    public function test_zero_principal_is_rejected(): void
    {
        Sanctum::actingAs($this->officer);

        $data = $this->validLoanData();

        $data['principal_amount'] = '0';

        $response = $this->postJson(
            '/api/loans',
            $data
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(
                'principal_amount'
            );

        $this->assertDatabaseMissing(
            'loans',
            [
                'loan_number' =>
                    'LN-000100',
            ]
        );
    }

    public function test_invalid_customer_is_rejected(): void
    {
        Sanctum::actingAs($this->officer);

        $data = $this->validLoanData();

        $data['customer_id'] = 999999;

        $response = $this->postJson(
            '/api/loans',
            $data
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(
                'customer_id'
            );
    }

    public function test_invalid_currency_is_rejected(): void
    {
        Sanctum::actingAs($this->officer);

        $data = $this->validLoanData();

        $data['currency_id'] = 999999;

        $response = $this->postJson(
            '/api/loans',
            $data
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(
                'currency_id'
            );
    }

    public function test_currency_decimal_precision_is_enforced(): void
    {
        Sanctum::actingAs($this->officer);

        $data = $this->validLoanData();

        $data['principal_amount'] =
            '100.999';

        $response = $this->postJson(
            '/api/loans',
            $data
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(
                'principal_amount'
            );
    }

    public function test_maturity_cannot_be_before_start_date(): void
    {
        Sanctum::actingAs($this->officer);

        $data = $this->validLoanData();

        $data['start_date'] =
            '2026-08-25';

        $data['maturity_date'] =
            '2026-08-24';

        $response = $this->postJson(
            '/api/loans',
            $data
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(
                'maturity_date'
            );
    }

    public function test_loan_number_must_be_unique(): void
    {
        Sanctum::actingAs($this->officer);

        $this->postJson(
            '/api/loans',
            $this->validLoanData()
        )->assertSuccessful();

        $this->postJson(
            '/api/loans',
            $this->validLoanData()
        )
            ->assertStatus(422)
            ->assertJsonValidationErrors(
                'loan_number'
            );
    }

    private function validLoanData(): array
    {
        return [
            'loan_number' => 'LN-000100',

            'customer_id' =>
                $this->customer->id,

            'currency_id' =>
                $this->usd->id,

            'principal_amount' =>
                '999999999.99',

            'start_date' =>
                '2026-08-25',

            'maturity_date' =>
                '2027-08-25',

            'status' => 'active',
        ];
    }
}