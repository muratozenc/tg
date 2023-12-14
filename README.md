# Laravel 10 - Therapist Calendar

Project is focused the appointment system therapists and their clients who has the possibility to live in different time zones.

## Introduction
This project is developed using Laravel 10 and includes Laravel Breeze for the user interface. It's designed to handle user management efficiently with a pre-configured database seeder.

## Features
- **Laravel Breeze Integration**: Simplifies UI concerns.
- **Pre-configured User Seeder**: Streamlines the process of setting up initial user data.

## Installation
1. Clone the repository to your local machine.
2. Run `composer install` to install the required dependencies.
3. Set up your `.env` file with your database credentials.
4. Run `php artisan migrate` to create the database tables.
5. Run `php artisan db:seed --class=UserSeeder` to seed the database with initial user data.
6. Run `npm install && npm run dev` to install and make node depended services up.

## Database Seeding
The `UserSeeder` class is used to seed the database with initial user data. The seeder adds three types of users:
- Client One
- Client Two
- The Therapist

Each user is created with unique credentials and assigned roles (`client` or `therapist`). The user data is as follows:

```php
class UserSeeder extends Seeder
{
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
            ],[
                'name'=>'Client Two',
                'email'=>'two@client.tgo',
                'password'=> Hash::make('ClientTwo'),
                'email_verified_at' => '2023-01-01 00:00:00',
                'user_type' => 'client',
                'timezone' => ''
            ],[
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
```

## Usage

Go to your virtual host domain `eg. www.therapist.test` or run `php artisan serve` to see the action.

