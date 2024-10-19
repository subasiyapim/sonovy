<?php

namespace Database\Seeders;

use App\Enums\PlatformTypeEnum;
use App\Models\Platform;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            //Amazon
            [
                "type" => PlatformTypeEnum::DOWNLOADABLE->value,
                "name" => 'Amazon',
                "visible_name" => 'Amazon Prime Video',
                "code" => 'amazon',
                "url" => 'https://www.amazon.com',
                "status" => 1,
                "authenticators" => [
                    json_decode('{"key": "password", "value": "34343434"}'),
                    json_decode('{"key": "username", "value": "3847837834"}')
                ],
                'icon' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M6.61202 7.97631C6.25104 8.49675 6.07055 9.12347 6.07055 9.85756H6.06836C6.06836 10.7823 6.33089 11.4978 6.85596 12.0018C7.38102 12.5058 8.05267 12.7578 8.86871 12.7578C9.39924 12.7578 9.84555 12.7052 10.2065 12.5978C10.7786 12.4378 11.3617 12.0346 11.9568 11.386C11.9994 11.4397 12.0738 11.5427 12.181 11.6983C12.286 11.8528 12.3626 11.9547 12.4107 12.0094C12.4578 12.062 12.5376 12.1475 12.6492 12.2636C12.7608 12.3809 12.8898 12.5036 13.0386 12.6307C13.2191 12.7052 13.3733 12.6942 13.5002 12.5978C13.5746 12.5343 14.0515 12.1212 14.9332 11.3542C15.0185 11.2907 15.0601 11.2162 15.0601 11.1318C15.0601 11.0573 15.0284 10.9719 14.9649 10.8765L14.5996 10.3901C14.5252 10.2893 14.4464 10.1249 14.3699 9.89482C14.2889 9.66801 14.2495 9.41601 14.2495 9.13881V5.28208C14.2495 5.24044 14.2452 5.10458 14.2342 4.87668C14.2233 4.64879 14.208 4.49868 14.1861 4.42965C14.1664 4.36063 14.1336 4.23572 14.0898 4.05713C14.0483 3.87525 14.0023 3.7372 13.9476 3.64078C13.8962 3.54436 13.8229 3.43698 13.7321 3.31427C13.6413 3.19265 13.544 3.07761 13.439 2.97133C12.7586 2.34461 11.8145 2.03125 10.6058 2.03125H10.2087C9.26579 2.08384 8.43991 2.33913 7.73435 2.79602C7.02989 3.25291 6.58686 3.95414 6.40637 4.89969C6.39543 4.94242 6.38996 4.97858 6.38996 5.01145C6.38996 5.16046 6.48075 5.25578 6.66015 5.29961L8.49022 5.52312C8.65978 5.49135 8.76588 5.36864 8.80964 5.15608C8.88293 4.81642 9.04701 4.55018 9.30189 4.35844C9.55676 4.1656 9.85977 4.05603 10.2098 4.02316H10.3334C10.8115 4.02316 11.1615 4.18313 11.3836 4.50306C11.5312 4.73534 11.6078 5.19333 11.6078 5.87264V6.14327C10.9602 6.19586 10.492 6.2375 10.2076 6.27037C9.36971 6.37665 8.66415 6.55195 8.09205 6.79628C7.46525 7.06253 6.973 7.45587 6.61202 7.97631ZM9.11702 10.5347C8.91028 10.2827 8.80745 9.95179 8.80745 9.53763L8.80636 9.53873C8.80636 8.62494 9.27235 8.03548 10.2065 7.77033C10.5238 7.68596 10.9908 7.64213 11.6067 7.64213V8.03986C11.6067 8.37952 11.6045 8.62494 11.5991 8.77395C11.5936 8.92296 11.5619 9.1169 11.5039 9.35465C11.4448 9.59351 11.3573 9.81374 11.2413 10.0164C10.9974 10.4733 10.6517 10.7593 10.2076 10.8765C10.1857 10.8765 10.1453 10.882 10.0873 10.893C10.0282 10.9028 9.98447 10.9072 9.95166 10.9072C9.60161 10.9072 9.32486 10.7834 9.11702 10.5347Z" fill="#353E47"/>
<path d="M15.5832 14.5426C15.5427 14.5853 15.5099 14.627 15.488 14.6708V14.673C15.4771 14.6938 15.4727 14.7091 15.4727 14.7201C15.4617 14.742 15.4661 14.7617 15.488 14.7836C15.5099 14.8056 15.5503 14.8165 15.616 14.8165C15.838 14.7847 16.082 14.7541 16.3489 14.7212C16.5917 14.6993 16.8039 14.6883 16.9833 14.6883C17.4723 14.6883 17.7687 14.753 17.8748 14.879C17.9164 14.9316 17.9383 15.017 17.9383 15.1342C17.9383 15.4958 17.7436 16.1379 17.3498 17.0626C17.3181 17.147 17.3323 17.2051 17.3979 17.2379C17.4176 17.2478 17.4395 17.2533 17.4614 17.2533C17.504 17.2533 17.5511 17.2335 17.6058 17.1897C17.9646 16.8818 18.2457 16.4742 18.448 15.9702C18.6493 15.4662 18.75 15.028 18.75 14.6554V14.5437C18.75 14.4166 18.7303 14.3213 18.6865 14.2588C18.5924 14.1405 18.3047 14.0616 17.8267 14.0178C17.7326 13.997 17.6309 13.9904 17.5248 14.0013C17.143 14.0123 16.7503 14.0715 16.3489 14.1766C16.1038 14.2402 15.849 14.3629 15.5832 14.5426Z" fill="#FF9900"/>
<path d="M1.5366 14.3837C1.41955 14.3092 1.33532 14.3146 1.28282 14.4001C1.26094 14.4319 1.25 14.4625 1.25 14.4954C1.25 14.548 1.28282 14.6028 1.34736 14.6543C2.51453 15.7061 3.83376 16.5224 5.31051 17.1009C6.78397 17.6794 8.34823 17.9698 10.0033 17.9698C11.0753 17.9698 12.1725 17.8197 13.2959 17.5238C14.4204 17.2258 15.4388 16.8062 16.35 16.2638C16.6475 16.0841 16.8915 15.9242 17.0829 15.7861C17.2306 15.6798 17.2601 15.5626 17.1704 15.4355C17.0796 15.3073 16.9549 15.2766 16.7952 15.3402C16.757 15.3588 16.6961 15.3853 16.6115 15.4221L16.5808 15.4355L16.3511 15.5308C14.3241 16.3065 12.2709 16.6955 10.1925 16.6955C7.07385 16.6955 4.18818 15.9242 1.5366 14.3837Z" fill="#FF9900"/>
</svg>
'
            ],
            //Spotify
            [
                'type' => PlatformTypeEnum::DOWNLOADABLE->value,
                'name' => 'Spotify',
                'visible_name' => 'Spotify',
                'code' => 'spotify',
                'url' => 'https://www.spotify.com',
                'status' => 1,
                "authenticators" => [
                    json_decode('{"key": "password", "value": "34343434"}'),
                    json_decode('{"key": "username", "value": "3847837834"}')
                ],
                'icon' => '<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M11 21.5C16.799 21.5 21.5 16.799 21.5 11C21.5 5.20101 16.799 0.5 11 0.5C5.20101 0.5 0.5 5.20101 0.5 11C0.5 16.799 5.20101 21.5 11 21.5ZM14.9122 15.4108C15.2112 15.5816 15.5939 15.5019 15.7733 15.2172C15.9526 14.9326 15.857 14.5682 15.558 14.3975C12.927 12.8604 9.65012 12.5074 5.83511 13.3386C5.50025 13.4069 5.29695 13.7257 5.3687 14.0445C5.44046 14.3633 5.77532 14.5568 6.11017 14.4885C9.60228 13.7371 12.5562 14.0445 14.9122 15.4108ZM15.9168 12.8718C16.2875 13.0767 16.7659 12.9742 16.9931 12.6213C17.2203 12.2683 17.1007 11.8129 16.7539 11.608C13.6565 9.79766 9.13587 9.29669 5.50025 10.3442C5.08168 10.458 4.85445 10.8679 4.97405 11.2664C5.09364 11.6535 5.52417 11.8698 5.94275 11.756C9.12391 10.8337 13.2259 11.2892 15.9168 12.8718ZM5.46437 8.80711C8.54986 7.91903 13.8717 8.08981 17.1007 9.91151C17.5432 10.1734 18.1173 10.0254 18.3684 9.59271C18.6315 9.17145 18.488 8.62494 18.0455 8.37445C14.3381 6.27949 8.45419 6.07455 4.91425 7.09926C4.42392 7.24727 4.1369 7.74824 4.29237 8.21505C4.44784 8.69325 4.97405 8.95512 5.46437 8.80711Z" fill="#1ED760"/>
</svg>'
            ],
            //Spotify Premium
            [
                'type' => PlatformTypeEnum::DOWNLOADABLE->value,
                'name' => 'Spotify Premium',
                'visible_name' => 'Spotify Premium',
                'code' => 'spotify_premium',
                'url' => 'https://www.spotify.com',
                'status' => 1,
                "authenticators" => [
                    json_decode('{"key": "password", "value": "34343434"}'),
                    json_decode('{"key": "username", "value": "3847837834"}')
                ],
                'icon' => '<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M11 21.5C16.799 21.5 21.5 16.799 21.5 11C21.5 5.20101 16.799 0.5 11 0.5C5.20101 0.5 0.5 5.20101 0.5 11C0.5 16.799 5.20101 21.5 11 21.5ZM14.9122 15.4108C15.2112 15.5816 15.5939 15.5019 15.7733 15.2172C15.9526 14.9326 15.857 14.5682 15.558 14.3975C12.927 12.8604 9.65012 12.5074 5.83511 13.3386C5.50025 13.4069 5.29695 13.7257 5.3687 14.0445C5.44046 14.3633 5.77532 14.5568 6.11017 14.4885C9.60228 13.7371 12.5562 14.0445 14.9122 15.4108ZM15.9168 12.8718C16.2875 13.0767 16.7659 12.9742 16.9931 12.6213C17.2203 12.2683 17.1007 11.8129 16.7539 11.608C13.6565 9.79766 9.13587 9.29669 5.50025 10.3442C5.08168 10.458 4.85445 10.8679 4.97405 11.2664C5.09364 11.6535 5.52417 11.8698 5.94275 11.756C9.12391 10.8337 13.2259 11.2892 15.9168 12.8718ZM5.46437 8.80711C8.54986 7.91903 13.8717 8.08981 17.1007 9.91151C17.5432 10.1734 18.1173 10.0254 18.3684 9.59271C18.6315 9.17145 18.488 8.62494 18.0455 8.37445C14.3381 6.27949 8.45419 6.07455 4.91425 7.09926C4.42392 7.24727 4.1369 7.74824 4.29237 8.21505C4.44784 8.69325 4.97405 8.95512 5.46437 8.80711Z" fill="#1ED760"/>
</svg>'
            ],
            //Apple Music
            [
                'type' => PlatformTypeEnum::DOWNLOADABLE->value,
                'name' => 'Apple',
                'visible_name' => 'Apple Music',
                'code' => 'apple',
                'url' => 'https://www.apple.com',
                'status' => 1,
                "authenticators" => [
                    json_decode('{"key": "password", "value": "34343434"}'),
                    json_decode('{"key": "username", "value": "3847837834"}')
                ],
                'icon' => '<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M8.86636 5.38296L15.1722 4.1297C15.4595 4.07259 15.7273 4.29243 15.7273 4.58539V14.2471C15.7273 15.0696 15.1521 15.78 14.3476 15.9512L13.7906 16.0697C12.8201 16.2762 11.906 15.5361 11.906 14.5439C11.906 13.845 12.3771 13.2339 13.053 13.056L14.6319 12.6405C14.8462 12.5841 14.9956 12.3903 14.9956 12.1687V7.68295C14.9956 7.47672 14.8061 7.32257 14.6042 7.36454L9.34945 8.4567C9.18779 8.49029 9.0719 8.63274 9.0719 8.79786V15.4966C9.0719 16.3963 8.44242 17.1734 7.5623 17.36L7.18029 17.4411C6.18034 17.6532 5.23894 16.8905 5.23894 15.8683C5.23894 15.2169 5.67739 14.647 6.30708 14.4801L7.95978 14.0418C8.16348 13.9878 8.30531 13.8035 8.30531 13.5928V6.06649C8.30531 5.73398 8.54022 5.44778 8.86636 5.38296Z" fill="url(#paint0_radial_4060_99562)"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M21.5 11C21.5 16.799 16.799 21.5 11 21.5C5.20101 21.5 0.5 16.799 0.5 11C0.5 5.20101 5.20101 0.5 11 0.5C16.799 0.5 21.5 5.20101 21.5 11ZM20.5708 11.0116C20.5708 16.3038 16.2858 20.594 11 20.594C5.7142 20.594 1.4292 16.3038 1.4292 11.0116C1.4292 5.7194 5.7142 1.4292 11 1.4292C16.2858 1.4292 20.5708 5.7194 20.5708 11.0116Z" fill="url(#paint1_radial_4060_99562)"/>
<defs>
<radialGradient id="paint0_radial_4060_99562" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(5.23894 19.8042) rotate(-55.1325) scale(21.0082 16.4975)">
<stop stop-color="#7A66FB"/>
<stop offset="0.440198" stop-color="#52A2F4"/>
<stop offset="0.702" stop-color="#FC5D6D"/>
<stop offset="1" stop-color="#E85E7B"/>
</radialGradient>
<radialGradient id="paint1_radial_4060_99562" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(5.23894 19.8042) rotate(-55.1325) scale(21.0082 16.4975)">
<stop stop-color="#7A66FB"/>
<stop offset="0.440198" stop-color="#52A2F4"/>
<stop offset="0.702" stop-color="#FC5D6D"/>
<stop offset="1" stop-color="#E85E7B"/>
</radialGradient>
</defs>
</svg>'
            ],
            //Youtube
            [
                'type' => PlatformTypeEnum::STREAMING->value,
                'name' => 'Youtube',
                'visible_name' => 'Youtube Premium',
                'code' => 'youtube',
                'url' => 'https://www.youtube.com',
                'status' => 1,
                "authenticators" => [
                    json_decode('{"key": "password", "value": "34343434"}'),
                    json_decode('{"key": "username", "value": "3847837834"}')
                ],
                'icon' => '<svg width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M21.0612 2.84699C20.8197 1.92316 20.1081 1.19559 19.2046 0.948672C17.5669 0.5 11 0.5 11 0.5C11 0.5 4.43315 0.5 2.79544 0.948672C1.89193 1.19563 1.18033 1.92316 0.938815 2.84699C0.5 4.52148 0.5 8.01516 0.5 8.01516C0.5 8.01516 0.5 11.5088 0.938815 13.1833C1.18033 14.1071 1.89193 14.8044 2.79544 15.0513C4.43315 15.5 11 15.5 11 15.5C11 15.5 17.5668 15.5 19.2046 15.0513C20.1081 14.8044 20.8197 14.1071 21.0612 13.1833C21.5 11.5088 21.5 8.01516 21.5 8.01516C21.5 8.01516 21.5 4.52148 21.0612 2.84699ZM8.85226 11.1871V4.84316L14.3409 8.01523L8.85226 11.1871Z" fill="#FF0000"/>
</svg>'
            ],
//Youtube Premium
            [
                'type' => PlatformTypeEnum::STREAMING->value,
                'name' => 'Youtube Premium',
                'visible_name' => 'Youtube Premium',
                'code' => 'youtube_premium',
                'url' => 'https://www.youtube.com',
                'status' => 1,
                "authenticators" => [
                    json_decode('{"key": "password", "value": "34343434"}'),
                    json_decode('{"key": "username", "value": "3847837834"}')
                ],
                'icon' => '<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M11 21.5C16.799 21.5 21.5 16.799 21.5 11C21.5 5.20101 16.799 0.5 11 0.5C5.20101 0.5 0.5 5.20101 0.5 11C0.5 16.799 5.20101 21.5 11 21.5Z" fill="#FF0000"/>
</svg>'
            ],
            //Tiktok
            [
                'type' => PlatformTypeEnum::STREAMING->value,
                'name' => 'Tiktok',
                'visible_name' => 'Tiktok Premium',
                'code' => 'tiktok',
                'url' => 'https://www.tiktok.com',
                'status' => 1,
                "authenticators" => [
                    json_decode('{"key": "password", "value": "34343434"}'),
                    json_decode('{"key": "username", "value": "3847837834"}')
                ],
                'icon' => '<svg width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M21.0612 2.84699C20.8197 1.92316 20.1081 1.19559 19.2046 0.948672C17.5669 0.5 11 0.5 11 0.5C11 0.5 4.43315 0.5 2.79544 0.948672C1.89193 1.19563 1.18033 1.92316 0.938815 2.84699C0.5 4.52148 0.5 8.01516 0.5 8.01516C0.5 8.01516 0.5 11.5088 0.938815 13.1833C1.18033 14.1071 1.89193 14.8044 2.79544 15.0513C4.43315 15.5 11 15.5 11 15.5C11 15.5 17.5668 15.5 19.2046 15.0513C20.1081 14.8044 20.8197 14.1071 21.0612 13.1833C21.5 11.5088 21.5 8.01516 21.5 8.01516C21.5 8.01516 21.5 4.52148 21.0612 2.84699ZM8.85226 11.1871V4.84316L14.3409 8.01523L8.85226 11.1871Z" fill="#FF0000"/>
</svg>'
            ],
            //Tidal
            [
                'type' => PlatformTypeEnum::STREAMING->value,
                'name' => 'Tidal',
                'visible_name' => 'Tidal Premium',
                'code' => 'tidal',
                'url' => 'https://www.tidal.com',
                'status' => 1,
                "authenticators" => [
                    json_decode('{"key": "password", "value": "34343434"}'),
                    json_decode('{"key": "username", "value": "3847837834"}')
                ],
                'icon' => '<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M11 0.5C16.799 0.5 21.5 5.20101 21.5 11C21.5 16.799 16.799 21.5 11 21.5C5.20101 21.5 0.5 16.799 0.5 11C0.5 5.20101 5.20101 0.5 11 0.5ZM13.0665 9.50125L11.0005 11.5677L13.0668 13.6346L11.0004 15.7015L8.93339 13.6346L11.0003 11.5678L8.93339 9.50104L11.0004 7.435L13.0667 9.50092L15.1335 7.43476L17.1999 9.50121L15.1335 11.5681L13.0665 9.50125ZM8.93351 9.50121L6.86665 11.5681L4.80008 9.50121L6.86665 7.43476L8.93351 9.50121Z" fill="black"/>
</svg>'
            ],
            //Deezer
            [
                'type' => PlatformTypeEnum::STREAMING->value,
                'name' => 'Deezer',
                'visible_name' => 'Deezer Premium',
                'code' => 'deezer',
                'url' => 'https://www.deezer.com',
                'status' => 1,
                "authenticators" => [
                    json_decode('{"key": "password", "value": "34343434"}'),
                    json_decode('{"key": "username", "value": "3847837834"}')
                ],
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M12 1.5C17.799 1.5 22.5 6.20101 22.5 12C22.5 17.799 17.799 22.5 12 22.5C6.20101 22.5 1.5 17.799 1.5 12C1.5 6.20101 6.20101 1.5 12 1.5Z" fill="black"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M17.1727 7.90039H14.6992V9.34389H17.1727V7.90039Z" fill="#29AB70"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M14.6992 9.91016H17.1725V11.3542H14.6992V9.91016Z" fill="url(#paint0_linear_155_117539)"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M14.6992 11.9199H17.1723V13.3638H14.6992V11.9199Z" fill="url(#paint1_linear_155_117539)"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M5.73438 13.9277H8.20766V15.3721H5.73438V13.9277Z" fill="url(#paint2_linear_155_117539)"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M8.72266 13.9277H11.196V15.3721H8.72266V13.9277Z" fill="url(#paint3_linear_155_117539)"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M11.7109 13.9277H14.1843V15.3721H11.7109V13.9277Z" fill="url(#paint4_linear_155_117539)"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M14.6992 13.9277H17.1726V15.3722H14.6992V13.9277Z" fill="url(#paint5_linear_155_117539)"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M11.7109 11.918H14.1842V13.3603H11.7109V11.918Z" fill="url(#paint6_linear_155_117539)"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M8.72266 11.9199H11.1966V13.3626H8.72266V11.9199Z" fill="url(#paint7_linear_155_117539)"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M8.72266 9.91016H11.1947V11.3531H8.72266V9.91016Z" fill="url(#paint8_linear_155_117539)"/>
<defs>
<linearGradient id="paint0_linear_155_117539" x1="10.9261" y1="14.0585" x2="10.6043" y2="15.8816" gradientUnits="userSpaceOnUse">
<stop stop-color="#2C8C9D"/>
<stop offset="0.039" stop-color="#298E9A"/>
<stop offset="0.388" stop-color="#129C83"/>
<stop offset="0.722" stop-color="#05A475"/>
<stop offset="1" stop-color="#00A770"/>
</linearGradient>
<linearGradient id="paint1_linear_155_117539" x1="14.784" y1="11.4894" x2="17.3314" y2="12.7875" gradientUnits="userSpaceOnUse">
<stop stop-color="#2839BA"/>
<stop offset="1" stop-color="#148CB3"/>
</linearGradient>
<linearGradient id="paint2_linear_155_117539" x1="5.73438" y1="8.63208" x2="8.20766" y2="8.63208" gradientUnits="userSpaceOnUse">
<stop stop-color="#F6A500"/>
<stop offset="1" stop-color="#F29100"/>
</linearGradient>
<linearGradient id="paint3_linear_155_117539" x1="8.72266" y1="8.63208" x2="11.196" y2="8.63208" gradientUnits="userSpaceOnUse">
<stop stop-color="#F29100"/>
<stop offset="1" stop-color="#D12F5F"/>
</linearGradient>
<linearGradient id="paint4_linear_155_117539" x1="11.7109" y1="8.63208" x2="14.1843" y2="8.63208" gradientUnits="userSpaceOnUse">
<stop stop-color="#B4197C"/>
<stop offset="1" stop-color="#472EAD"/>
</linearGradient>
<linearGradient id="paint5_linear_155_117539" x1="14.6992" y1="8.63204" x2="17.1726" y2="8.63204" gradientUnits="userSpaceOnUse">
<stop stop-color="#2839BA"/>
<stop offset="1" stop-color="#3072B7"/>
</linearGradient>
<linearGradient id="paint6_linear_155_117539" x1="11.8899" y1="10.3231" x2="14.5572" y2="11.1029" gradientUnits="userSpaceOnUse">
<stop stop-color="#B4197C"/>
<stop offset="1" stop-color="#373AAC"/>
</linearGradient>
<linearGradient id="paint7_linear_155_117539" x1="8.46926" y1="12.1786" x2="11.1172" y2="11.2153" gradientUnits="userSpaceOnUse">
<stop stop-color="#FFCB00"/>
<stop offset="1" stop-color="#D12F5F"/>
</linearGradient>
<linearGradient id="paint8_linear_155_117539" x1="10.3835" y1="15.2949" x2="12.4385" y2="13.3983" gradientUnits="userSpaceOnUse">
<stop stop-color="#FFCF00"/>
<stop offset="1" stop-color="#ED743B"/>
</linearGradient>
</defs>
</svg>
'
            ],
            //Netflix
            [
                'type' => PlatformTypeEnum::STREAMING->value,
                'name' => 'Netflix',
                'visible_name' => 'Netflix Premium',
                'code' => 'netflix',
                'url' => 'https://www.netflix.com',
                'status' => 1,
                "authenticators" => [
                    json_decode('{"key": "password", "value": "34343434"}'),
                    json_decode('{"key": "username", "value": "3847837834"}')
                ],
                'icon' => '<svg width="12" height="18" viewBox="0 0 12 18" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M0.375 0.252302L4.41714 10.0698V10.0652L4.73676 10.8387C6.51208 15.1455 7.46554 17.4566 7.47091 17.4612C7.4736 17.4635 7.74218 17.4773 8.06716 17.4911C9.05017 17.5325 10.2695 17.6223 11.1961 17.7213C11.4083 17.7443 11.5936 17.7558 11.6044 17.7466L7.59714 7.99583V7.99813L7.2265 7.1004C6.86392 6.22338 6.62219 5.6364 5.1638 2.10532C4.77167 1.15464 4.43863 0.351282 4.4252 0.314452L4.39834 0.25H2.38667L0.375 0.252302Z" fill="#E30A17"/>
</svg>'
            ]
        ];


        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Platform::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($data as $item) {
            $platform = Platform::updateOrCreate(
                [
                    'name' => $item['name'],
                ],
                $item);
        }


    }
}
