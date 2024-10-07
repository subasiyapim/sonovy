<?php

namespace Database\Seeders;

use App\Models\Label;
use App\Models\System\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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

        $disks = config('filesystems.disks');

        Tenant::chunk(100, function ($tenants) use (&$disks) {
            foreach ($tenants as $tenant) {
                $disks['tenant_'.$tenant->id] = [
                    'driver' => 'local',
                    'root' => storage_path('app/public/tenant_'.$tenant->id),
                    'url' => env('APP_URL').'/storage/tenant_'.$tenant->id,
                    'visibility' => 'public',
                ];
            }
        });

        config(['filesystems.disks' => $disks]);

        Label::factory(15)->create()->each(function (Label $label) use ($tenant) {
            $label->addMediaFromUrl('https://picsum.photos/400/400')
                ->usingFileName(Str::slug($label->name).'.jpg')
                ->usingName($label->name)
                ->toMediaCollection('labels', 'tenant_'.$tenant->id);
        });
    }
}
