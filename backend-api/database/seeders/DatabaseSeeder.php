<?php

namespace Database\Seeders;

use App\Models\Annonce;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // On demande à Laravel de fabriquer 25 annonces via la Factory
        Annonce::factory(25)->create();
    }
}