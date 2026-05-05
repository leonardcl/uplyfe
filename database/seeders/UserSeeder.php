<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Nico',
            'last_name' => 'Victorio',
            'email' => 'nico@gmail.com',
            'password' => Hash::make('nico'),
        ]);

        User::create([
            'first_name' => 'Alvin',
            'last_name' => 'Santoso',
            'email' => 'alvin@gmail.com',
            'password' => Hash::make('alvin'),
        ]);

        User::create([
            'first_name' => 'Yosua',
            'last_name' => 'Soekamto',
            'email' => 'yosua@gmail.com',
            'password' => Hash::make('yosua'),
        ]);

        User::create([
            'first_name' => 'Leonardo',
            'last_name' => 'Limanjaya',
            'email' => 'leo@gmail.com',
            'password' => Hash::make('leo'),
        ]);

        User::create([
            'first_name' => 'Catherine',
            'last_name' => 'Citra',
            'email' => 'carin@gmail.com',
            'password' => Hash::make('carin'),
        ]);

        User::create([
            'first_name' => 'Sally',
            'last_name' => 'Angela',
            'email' => 'sally@gmail.com',
            'password' => Hash::make('sally'),
        ]);

    }
}
