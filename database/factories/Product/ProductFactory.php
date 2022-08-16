<?php

namespace Database\Factories\Product;

use App\Models\Category\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $categories = Category::query()
            ->whereDoesntHave('children')
            ->get();

        $categoryIds = [];

        /** @var Category $category */
        foreach ($categories as $category) {
            $categoryIds[] = $category->id;
        }

        for ($i = 1; $i < 11; $i++) {
            $weight[] = $i . ' lbs';
        }

        return $phones =  [
            'sku' => $this->faker->uuid(),
            'name' => $this->faker->sentence(),
            'category_id' => $this->faker->randomElement($categoryIds),
            'price' => $this->faker->randomFloat(2, 100, 2000),
            'quantity' => 0,
            'details' => [
                'Model' => [
                    'Brand' => $this->faker->sentence(2),
                    'Series' => $this->faker->sentence(3),
                    'Model' => $this->faker->sentence(5),
                ],
                'Quick Info' => [
                    'Colour' => $this->faker->randomElement([
                        'Black',
                        'White',
                        'Green',
                        'Blue',
                        'Yellow',
                        'Grey',
                    ]),
                    'Weight' => $this->faker->randomElement($weight),
                    'Screen, inches' => $this->faker->randomFloat(1, 10, 20),
                    'CPU' => $this->faker->sentence(3),
                ]
            ]
        ];
    }
}
