<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['Pending', 'In Progress', 'Completed'];

        foreach ($statuses as $status) {
            \App\Models\Status::firstOrCreate(['name' => $status]);
        }
    }
}
