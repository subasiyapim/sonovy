<?php

namespace Database\Seeders;

use App\Models\Label;
use App\Models\System\Tenant;
use App\Services\MediaServices;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class LabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $diskName = 'tenant_'.tenant('domain').'_labels';

        Label::factory(10)->create()->each(function (Label $row) use ($diskName) {
            try {
                // Dummyimage.com servisini kullan
                $imageUrl = 'https://dummyimage.com/500x500/3498db/ffffff&text=' . urlencode($row->name);
                
                $row->addMediaFromUrl($imageUrl)
                    ->usingFileName(Str::slug($row->name).'.jpg')
                    ->usingName($row->name)
                    ->toMediaCollection('labels', $diskName);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        });
    }
}
