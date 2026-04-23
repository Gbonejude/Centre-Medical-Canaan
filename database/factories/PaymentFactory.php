<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $type = $this->faker->randomElement([
            'home_care_clients',
            'residential_clients',
            'private_clients',
        ]);

        return [
            'reference' => strtoupper(
                'PAY-'.now()->format('Ymd').'-'.Str::upper(Str::random(4))
            ),
            'customer_id' => Customer::factory(),
            'type' => $type,
            'co_pay' => $type === 'home_care_clients' ? $this->faker->randomFloat(2, 50, 300) : null,
            'rent' => $type === 'residential_clients' ? $this->faker->randomFloat(2, 300, 1000) : null,
            'private_pay' => $type === 'private_clients' ? $this->faker->randomFloat(2, 100, 500) : null,
            'payment_date' => $this->faker->date(),
            'total' => $this->faker->randomFloat(2, 100, 1000),
            'note' => $this->faker->sentence(),
        ];
    }
}
