<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@pds.local'],
            [
                'name' => 'Administrador',
                'cpf' => '11144477735',
                'password' => Hash::make('admin12345'),
                'is_admin' => true,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'cliente@pds.local'],
            [
                'name' => 'Cliente Demo',
                'cpf' => '93541134780',
                'password' => Hash::make('cliente12345'),
                'is_admin' => false,
            ]
        );
    }
}
