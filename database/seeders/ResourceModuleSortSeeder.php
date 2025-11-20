<?php

namespace Database\Seeders;

use App\Models\ResourceCollection;
use Illuminate\Database\Seeder;

class ResourceModuleSortSeeder extends Seeder
{
    public function run(): void
    {
        ResourceCollection::with('modules')->chunkById(100, function ($collections) {
            foreach ($collections as $collection) {
                $i = 1;
                foreach ($collection->modules()->orderBy('created_at')->get() as $module) {
                    $module->update(['sort_order' => $i++]);
                }
            }
        });
    }
}
