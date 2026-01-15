<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder
{
    public function run()
    {
        DB::table('tasks')->insert([
            ['title' => 'Complete internship assignment', 'is_completed' => false],
            ['title' => 'Learn Flutter state management', 'is_completed' => true],
            ['title' => 'Prepare for technical interview', 'is_completed' => false],
        ]);
    }
}