<?php

namespace Database\Seeders;

use App\Models\UserVisit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserVisitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserVisit::factory(300)->create();
    }
}
