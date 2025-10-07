<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['client','coach','admin'] as $r) {
            Role::findOrCreate($r);
        }
    }
}
