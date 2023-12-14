<?php

namespace Database\Seeders;

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
        $users = [
            [
                'name'=>'Client One',
                'email'=>'one@client.tgo',
                'password'=> Hash::make('ClientOne'),
                'email_verified_at' => '2023-01-01 00:00:00',
                'user_type' => 'client',
                'timezone' => ''
            ],
            [
                'name'=>'Client Two',
                'email'=>'two@client.tgo',
                'password'=> Hash::make('ClientTwo'),
                'email_verified_at' => '2023-01-01 00:00:00',
                'user_type' => 'client',
                'timezone' => ''
            ],
            [
                'name'=>'The Therapist',
                'email'=>'the@therapist.tgo',
                'password'=> Hash::make('TheTherapist'),
                'email_verified_at' => '2023-01-01 00:00:00',
                'user_type' => 'therapist',
                'timezone' => ''
            ]
        ];

        foreach($users as $user){
            User::create($user);
        }
    }
}
