<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $gov_amount = $this->faker->numberBetween(1500, 4000);
        $service_charges = $this->faker->numberBetween(800, 1200);
        $gst = round(($service_charges * 0.18), 2);
        $total_amount = $gov_amount + $service_charges + $gst;

        return [
            'service_name' => $this->faker->unique()->word . ' Passport',
            'service_code' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{3}'),
            'service_gov_amount' => $gov_amount,
            'service_charges' => $service_charges,
            'service_gst' => $gst,
            'service_total_amount' => $total_amount,
        ];
    }
}
