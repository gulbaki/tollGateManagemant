<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        DB::table('users')->insert([
            'id' => 1, // Assuming 'id' is not an auto-incrementing field anymore
            'name' => 'baki',
            'email' => 'bakigul@gmail.com',
            'password' => Hash::make('baki'), // You should encrypt the password
            'wallet' => 500.0, // Assuming this is the wallet balance
            'created_at' => '2024-01-28 10:51:32', // Use the same date-time format as your DB
            'updated_at' => '2024-01-28 10:51:32', // Use the current date-time or copy 'created_at'
        ]);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
