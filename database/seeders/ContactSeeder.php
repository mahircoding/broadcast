<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contacts = [
            [
                'name' => 'Ahmad Wijaya',
                'phone_number' => '+6281234567890',
                'email' => 'ahmad@email.com',
                'group' => 'Keluarga',
                'notes' => 'Kakak kandung',
                'is_active' => true
            ],
            [
                'name' => 'Siti Nurhaliza',
                'phone_number' => '+6281234567891',
                'email' => 'siti@email.com',
                'group' => 'Kerja',
                'notes' => 'Manager divisi marketing',
                'is_active' => true
            ],
            [
                'name' => 'Budi Santoso',
                'phone_number' => '+6281234567892',
                'email' => null,
                'group' => 'Teman',
                'notes' => 'Teman SMA',
                'is_active' => true
            ],
            [
                'name' => 'Maria Gonzales',
                'phone_number' => '+6281234567893',
                'email' => 'maria@email.com',
                'group' => 'Klien',
                'notes' => 'Klien VIP dari Jakarta',
                'is_active' => true
            ],
            [
                'name' => 'Andi Pratama',
                'phone_number' => '+6281234567894',
                'email' => 'andi@email.com',
                'group' => 'Keluarga',
                'notes' => 'Sepupu',
                'is_active' => true
            ],
            [
                'name' => 'Lisa Susanti',
                'phone_number' => '+6281234567895',
                'email' => 'lisa@email.com',
                'group' => 'Kerja',
                'notes' => 'Rekan kerja tim development',
                'is_active' => true
            ],
            [
                'name' => 'Rizki Maulana',
                'phone_number' => '+6281234567896',
                'email' => null,
                'group' => 'Teman',
                'notes' => 'Teman kuliah',
                'is_active' => true
            ],
            [
                'name' => 'Dewi Lestari',
                'phone_number' => '+6281234567897',
                'email' => 'dewi@email.com',
                'group' => 'Klien',
                'notes' => 'Klien potensial dari Surabaya',
                'is_active' => false
            ],
            [
                'name' => 'Fahmi Rahman',
                'phone_number' => '+6281234567898',
                'email' => 'fahmi@email.com',
                'group' => 'Komunitas',
                'notes' => 'Anggota komunitas programmer',
                'is_active' => true
            ],
            [
                'name' => 'Indah Permata',
                'phone_number' => '+6281234567899',
                'email' => 'indah@email.com',
                'group' => 'Komunitas',
                'notes' => 'Koordinator event',
                'is_active' => true
            ]
        ];

        foreach ($contacts as $contact) {
            Contact::create($contact);
        }
    }
}
