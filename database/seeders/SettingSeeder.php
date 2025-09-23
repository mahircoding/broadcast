<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'wabox_api_url',
                'value' => 'https://www.waboxapp.com/api',
                'is_encrypted' => false,
            ],
            [
                'key' => 'wabox_token',
                'value' => '',
                'is_encrypted' => false,
            ],
            [
                'key' => 'wabox_uid',
                'value' => '',
                'is_encrypted' => false,
            ],
            [
                'key' => 'wabox_broadcast_delay',
                'value' => '3',
                'is_encrypted' => false,
            ],
            [
                'key' => 'wabox_batch_size',
                'value' => '50',
                'is_encrypted' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'is_encrypted' => $setting['is_encrypted'],
                ]
            );
        }
    }
}
