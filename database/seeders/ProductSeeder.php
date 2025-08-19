<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Living Room (category_id: 1)
            [
                'name' => 'Syltherine',
                'description' => 'Stylish cafe chair',
                'price' => 2500000,
                'category_id' => 1,
                'stock' => 15, // Available
            ],
            [
                'name' => 'Leviosa',
                'description' => 'Stylish cafe chair',
                'price' => 2500000,
                'category_id' => 1,
                'stock' => 8, // Available
            ],
            [
                'name' => 'Lolito',
                'description' => 'Luxury big sofa',
                'price' => 7000000,
                'category_id' => 1,
                'stock' => 3, // Low stock
            ],
            [
                'name' => 'Respira',
                'description' => 'Outdoor bar table and stool',
                'price' => 500000,
                'category_id' => 1,
                'stock' => 0, // Out of stock
            ],
            [
                'name' => 'Grifo',
                'description' => 'Night lamp',
                'price' => 1500000,
                'category_id' => 1,
                'stock' => 25, // In stock
            ],
            [
                'name' => 'Muggo',
                'description' => 'Small mug',
                'price' => 150000,
                'category_id' => 1,
                'stock' => 50, // High stock
            ],

            // Bedroom (category_id: 2)
            [
                'name' => 'Pingky',
                'description' => 'Cute bed set',
                'price' => 7000000,
                'category_id' => 2,
                'stock' => 5, // Available
            ],
            [
                'name' => 'Potty',
                'description' => 'Minimalist flower pot',
                'price' => 500000,
                'category_id' => 2,
                'stock' => 30, // In stock
            ],
            [
                'name' => 'King Bed',
                'description' => 'Luxury king size bed with premium mattress',
                'price' => 15000000,
                'category_id' => 2,
                'stock' => 2, // Limited stock
            ],
            [
                'name' => 'Wardrobe Classic',
                'description' => 'Classic wooden wardrobe with 4 doors',
                'price' => 8500000,
                'category_id' => 2,
                'stock' => 0, // Out of stock
            ],

            // Dining Room (category_id: 3)
            [
                'name' => 'Dining Table Set',
                'description' => 'Elegant dining table for 6 people',
                'price' => 12000000,
                'category_id' => 3,
                'stock' => 4, // Available
            ],
            [
                'name' => 'Dining Chair Oak',
                'description' => 'Comfortable oak dining chair',
                'price' => 1200000,
                'category_id' => 3,
                'stock' => 20, // In stock
            ],
            [
                'name' => 'Buffet Cabinet',
                'description' => 'Modern buffet cabinet for dining room',
                'price' => 6500000,
                'category_id' => 3,
                'stock' => 7, // Available
            ],

            // Office (category_id: 4)
            [
                'name' => 'Executive Desk',
                'description' => 'Large executive desk with drawers',
                'price' => 9500000,
                'category_id' => 4,
                'stock' => 6, // Available
            ],
            [
                'name' => 'Office Chair Ergonomic',
                'description' => 'Ergonomic office chair with lumbar support',
                'price' => 3500000,
                'category_id' => 4,
                'stock' => 12, // In stock
            ],
            [
                'name' => 'Bookshelf Modern',
                'description' => 'Modern 5-tier bookshelf',
                'price' => 2800000,
                'category_id' => 4,
                'stock' => 0, // Out of stock
            ],

            // Kitchen (category_id: 5)
            [
                'name' => 'Kitchen Island',
                'description' => 'Multi-functional kitchen island with storage',
                'price' => 8000000,
                'category_id' => 5,
                'stock' => 3, // Low stock
            ],
            [
                'name' => 'Bar Stool Set',
                'description' => 'Set of 2 adjustable bar stools',
                'price' => 1800000,
                'category_id' => 5,
                'stock' => 18, // In stock
            ],

            // Bathroom (category_id: 6)
            [
                'name' => 'Bathroom Vanity',
                'description' => 'Modern bathroom vanity with mirror',
                'price' => 4500000,
                'category_id' => 6,
                'stock' => 9, // Available
            ],
            [
                'name' => 'Storage Cabinet',
                'description' => 'Bathroom storage cabinet',
                'price' => 1500000,
                'category_id' => 6,
                'stock' => 0, // Out of stock
            ],

            // Outdoor (category_id: 7)
            [
                'name' => 'Garden Table Set',
                'description' => 'Weather-resistant outdoor dining set',
                'price' => 5500000,
                'category_id' => 7,
                'stock' => 8, // Available
            ],
            [
                'name' => 'Outdoor Lounge Chair',
                'description' => 'Comfortable outdoor lounge chair',
                'price' => 2200000,
                'category_id' => 7,
                'stock' => 15, // In stock
            ],

            // Storage (category_id: 8)
            [
                'name' => 'Storage Chest',
                'description' => 'Large wooden storage chest',
                'price' => 3200000,
                'category_id' => 8,
                'stock' => 1, // Last item
            ],
            [
                'name' => 'Shelving Unit',
                'description' => 'Multi-purpose shelving unit',
                'price' => 1800000,
                'category_id' => 8,
                'stock' => 22, // In stock
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
