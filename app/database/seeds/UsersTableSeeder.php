<?php

class UsersTableSeeder extends Seeder {

    public function run()
    {
    	// Uncomment the below to wipe the table clean before populating
    	DB::table('users')->delete();

        $users = array(
            [
                'username'  => 'johnDoe',
                'password'  => \Hash::make('password'),
                'email'     => 'john@example.com',
                'firstName' => 'John',
                'lastName' => 'Doe',
                'birthdate' => new DateTime('1/1/1971'),
                'apiKey'    => Hash::make(\Str::random(8) . 'john@example.com' . \Str::random(8)),
                'createdAt' => new DateTime('now'),
                'updatedAt' => new DateTime('now'),
                'deletedAt' => null
            ],
            [
                'username'  => 'janeDoe',
                'password'  => \Hash::make('password'),
                'email'     => 'jane@example.com',
                'firstName' => 'Jane',
                'lastName' => 'Doe',
                'birthdate' => new DateTime('1/1/1972'),
                'apiKey'    => Hash::make(\Str::random(8) . 'jane@example.com' . \Str::random(8)),
                'createdAt' => new DateTime('now'),
                'updatedAt' => new DateTime('now'),
                'deletedAt' => null
            ],
        );

        // Uncomment the below to run the seeder
        DB::table('users')->insert($users);
    }

}