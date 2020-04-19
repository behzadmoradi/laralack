<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'email' => 'me.behzad.moradi@gmail.com',
            'password' => Hash::make('ffffffff'),
            'api_token' => Str::random(80),
        ]);

        DB::table('users')->insert([
            'email' => 'behzad@surgelearning.ca',
            'password' => Hash::make('ffffffff'),
            'api_token' => Str::random(80),
        ]);

        DB::table('users')->insert([
            'email' => 'user3@gmail.com',
            'password' => Hash::make('ffffffff'),
            'api_token' => Str::random(80),
        ]);
    }
}
