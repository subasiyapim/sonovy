<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerformingRightsOrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seeds = [
            'ALBAUTOR',
            'SADAIC',
            'ARMAUTHOR',
            'APRA',
            'AKM',
            'COSCAP',
            'SABAM',
            'SOBODAYCOM',
            'AMUS',
            'ABRAMUS*',
            'AMAR*',
            'SADEMBRA*',
            'SBACEM*',
            'SOCINPRO*',
            'UBC',
            'MUSICAUTOR',
            'SOCAN',
            'SCD',
            'MCSC',
            'SAYCO',
            'ACAM',
            'HDS',
            'OSA',
            'KODA',
            'SGACEDOM',
            'SAYCE',
            'ECCO',
            'SACIM',
            'EAU',
            'TEOSTO',
            'SACEM',
            'GCA',
            'GEMA',
            'AUTODIA',
            'EDEM*',
            'AEI',
            'AACIMH',
            'CASH',
            'ARTISJUS',
            'STEF',
            'IPRS',
            'WAMI',
            'IMRO',
            'ACUM',
            'SIAE',
            'JACAP',
            'JASRAC',
            'KazAK',
            'KOMCA',
            'AKKA/LAA',
            'LATGA-A',
            'MACA',
            'ZAMP',
            'MACP',
            'MASA',
            'SACM',
            'PAM CG',
            'BUMA',
            'MRCSN',
            'COSON',
            'TONO',
            'SPAC',
            'APA',
            'APDAYC',
            'FILSCAP',
            'ZAIKS',
            'SPA',
            'UCMR-ADA',
            'RAO',
            'SOKOJ',
            'COMPASS',
            'SOZA',
            'SAZAS',
            'SAMRO',
            'SGAE',
            'STIM',
            'SUISA',
            'MUST',
            'MCT',
            'COTT',
            'MESAM*',
            'MSG',
            'NGO-UACRR',
            'PRS',
            'AGADU',
            'SACVEN',
            'VCPMC',
        ];

        foreach ($seeds as $seed) {
            DB::table('performing_rights_organizations')
                ->upsert([
                    'code' => $seed,
                ], ['code']);
        }
    }
}
