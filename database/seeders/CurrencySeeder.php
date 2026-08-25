<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        Currency::updateOrCreate(
            [
                'code' => 'USD',
            ],
            [
                'name' => 'US Dollar',
                'decimal_places' => 2,
            ]
        );

        Currency::updateOrCreate(
            [
                'code' => 'MVR',
            ],
            [
                'name' => 'Maldivian Rufiyaa',
                'decimal_places' => 2,
            ]
        );
    }
}