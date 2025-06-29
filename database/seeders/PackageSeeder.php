<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Make sure to import the DB facade

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('packages')->insert([
            // === WEBSITE TYPES ===
            [
                'name' => 'Simple and Basic',
                'description' => 'A clean and professional brochure-style website.',
                'type' => 'website',
                'price' => 500.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Fully Dynamic Wordpress Driven',
                'description' => 'A powerful website with a full content management system.',
                'type' => 'website',
                'price' => 1500.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Ecommerce with Woocommerce',
                'description' => 'A complete online store to sell your products.',
                'type' => 'website',
                'price' => 2500.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // === HOSTING PACKAGES ===
            [
                'name' => 'Basic Hosting',
                'description' => 'Reliable hosting for small to medium websites.',
                'type' => 'hosting',
                'price' => 150.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Business Hosting',
                'description' => 'More power and resources for growing websites.',
                'type' => 'hosting',
                'price' => 300.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Ecommerce Hosting',
                'description' => 'Optimized for speed and security for online stores.',
                'type' => 'hosting',
                'price' => 500.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // === EMAIL HOSTING PACKAGES ===
            [
                'name' => 'Basic Email',
                'description' => 'Professional email addresses for your team (5 mailboxes).',
                'type' => 'email',
                'price' => 50.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Business Email',
                'description' => 'More storage and features (20 mailboxes).',
                'type' => 'email',
                'price' => 100.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Enterprise Email',
                'description' => 'Advanced security and unlimited mailboxes.',
                'type' => 'email',
                'price' => 200.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}  