<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $user = User::create([
        'name' => 'hamza issa',
        'role' => 'User',
        'email' => 'hamza@gmail.com',
        'password' => 'hamza123@'
       ]);
       $admin = User::create([
        'name' => 'mhamd issa',
        'role' => 'Admin',
        'email' => 'mhamd@gmail.com',
        'password' => 'mhamd123@'
       ]);

       $manager = User::create([
        'name' => 'dani issa',
        'role' => 'Manager',
        'email' => 'dani@gmail.com',
        'password' => 'dani123@'
       ]);

    }
}
