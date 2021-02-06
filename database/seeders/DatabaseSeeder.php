<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@teamsy.test',
            'role' => 'superadmin',
        ]);

        User::factory()->count(9)->withTenant()->create();

        User::factory()->count(20)->memberTenant()->create();
    }
}
