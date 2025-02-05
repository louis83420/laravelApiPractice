<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name'=>$this->faker->word(),
            'description'=>$this->faker->paragraph(),
            'price'=>$this->faker->numberbetween(100,500),
            'stock'=>$this->faker->numberbetween(1,100),
            'image'=>$this->faker->image
        ];
    }
}