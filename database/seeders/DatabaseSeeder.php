<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BPRSeeder::class,
            PredikatKinerjaSeeder::class,
            KebijakanSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}