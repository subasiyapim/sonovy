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

class LabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $diskName = 'tenant_'.tenant('domain').'_labels';

        Label::factory(150)->create()->each(function (Label $row) use ($diskName) {

            $imageUrl = 'https://picsum.photos/id/'.rand(1, 1000).'/500/500';

            if (!@getimagesize($imageUrl)) {
                $imageUrl = 'https://picsum.photos/500/500';
            }

            $row->addMediaFromUrl($imageUrl)
                ->usingFileName(Str::slug($row->name).'.jpg')
                ->usingName($row->name)
                ->toMediaCollection('labels', $diskName);
        });

    }
}
