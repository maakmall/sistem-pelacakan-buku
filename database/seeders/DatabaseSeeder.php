<?php

namespace Database\Seeders;

use App\Models\Pustakawan;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Pustakawan::create([
            'username' => 'admin',
            'realname' => 'Admin',
            'passwd' => bcrypt('123'),
            'user_image' => '3.jpg',
        ]);

        $publishers = [
            'PT Elex Media Komputindo', 'Guepedia', 'Informatika', 'Bumi Aksara', 'Gramedia'
        ];

        foreach ($publishers as $publisher) {
            DB::table('mst_publisher')->insert(['publisher_name' => $publisher]);
        }

        $authors = [
            'Tata Sutabri', 'Wardana', 'Abdul Kadir', 'Ade Rahmat Iskandar', 'Dedik Irawan'
        ];

        foreach ($authors as $author) {
            DB::table('mst_author')->insert(['author_name' => $author]);
        }

        $publishPlaces = ['Jakarta', 'Bandung', 'Yogyakarta'];

        foreach ($publishPlaces as $publishPlace) {
            DB::table('mst_place')->insert(['place_name' => $publishPlace]);
        }

        DB::table('mst_language')->insert([
            ['language_id' => 'id', 'language_name' => 'Indonesia'],
            ['language_id' => 'en', 'language_name' => 'Inggris'],
        ]);

        DB::table('mst_gmd')->insert([
            ['gmd_code' => 'BS', 'gmd_name' => 'Bisnis'],
            ['gmd_code' => 'PJ', 'gmd_name' => 'Perpajakan'],
            ['gmd_code' => 'ALG', 'gmd_name' => 'Algoritma'],
            ['gmd_code' => 'MTK', 'gmd_name' => 'Matematika'],
        ]);
    }
}
