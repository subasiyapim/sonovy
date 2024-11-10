<?php

namespace Database\Seeders;

use App\Models\Label;
use App\Models\System\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('labels')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $diskName = 'tenant_' . tenant('id');
        if (!Storage::disk($diskName)->exists('/')) {
            Storage::disk($diskName)->makeDirectory('/');
        }
        Label::factory(15)->create()->each(function (Label $label) {
            $label->addMediaFromUrl('https://picsum.photos/400/400')
                ->usingFileName(Str::slug($label->name) . '.jpg')
                ->usingName($label->name);
            // ->toMediaCollection('labels', 'tenant_' . tenant('id'));
        });
    }
}
