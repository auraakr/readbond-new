<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Membuat akun Admin/Utama untuk testing login
        User::create([
            'name'              => 'Anne',
            'username'          => 'annewithane',
            'email'             => 'anne@readbond.com',
            'email_verified_at' => now(),
            'password'          => Hash::make('1234567890'), // Ganti sesuai kebutuhan
            'role'              => 'admin',
            'avatar'            => 'avatars/default-admin.jpg',
            'bio'               => 'Membaca untuk membangun kebiasaan dan merajut koneksi.',
            'reading_streak'    => 5,
            'last_diary_date'   => null,
            'remember_token'    => Str::random(10),
        ]);

        // 2. Membuat beberapa user dummy sebagai member Book Club (mengikuti desain avatar tumpuk)
        $dummyUsers = [
            [
                'name'     => 'Ahmad Fauzi',
                'username' => 'ahmadfz',
                'email'    => 'ahmad@example.com',
                'avatar'   => 'avatars/avatar-1.jpg',
                'bio'      => 'Penikmat buku fiksi dan klasik modern.',
            ],
            [
                'name'     => 'Citra Lestari',
                'username' => 'citralst',
                'email'    => 'citra@example.com',
                'avatar'   => 'avatars/avatar-2.jpg',
                'bio'      => 'Suka baca buku self-improvement di malam hari.',
            ],
            [
                'name'     => 'Rian Hidayat',
                'username' => 'rianhdy',
                'email'    => 'rian@example.com',
                'avatar'   => 'avatars/avatar-3.jpg',
                'bio'      => 'Tech enthusiast yang hobi baca buku sci-fi.',
            ],
            [
                'name'     => 'Nabila Putri',
                'username' => 'nabilapt',
                'email'    => 'nabila@example.com',
                'avatar'   => 'avatars/avatar-4.jpg',
                'bio'      => 'Membaca adalah petualangan tanpa batas.',
            ],
            [
                'name'     => 'Dimas Pratama',
                'username' => 'dimasprtm',
                'email'    => 'dimas@example.com',
                'avatar'   => 'avatars/avatar-5.jpg',
                'bio'      => 'Penggemar buku biografi dan sejarah.',
            ],
            [
                'name'     => 'Sari Wulandari',
                'username' => 'sariwlnd',
                'email'    => 'sari@example.com',
                'avatar'   => 'avatars/avatar-6.jpg',
                'bio'      => 'Suka baca novel romantis dan drama.',
            ],
            [
                'name'     => 'Fajar Nugroho',
                'username' => 'fajarnug',
                'email'    => 'fajar@example.com',
                'avatar'   => 'avatars/avatar-7.jpg',
                'bio'      => 'Membaca untuk mencari inspirasi dan ide baru.',
            ]
        ];

        foreach ($dummyUsers as $data) {
            User::create([
                'name'              => $data['name'],
                'username'          => $data['username'],
                'email'             => $data['email'],
                'email_verified_at' => now(),
                'password'          => Hash::make('1234567890'),
                'role'              => 'user', // Atau sesuaikan enum role kamu
                'avatar'            => $data['avatar'],
                'bio'               => $data['bio'],
                'reading_streak'    => rand(0, 10),
                'last_diary_date'   => null,
                'remember_token'    => Str::random(10),
            ]);
        }
    }
}
