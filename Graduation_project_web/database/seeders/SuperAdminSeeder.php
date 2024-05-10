<?php

namespace Database\Seeders;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Joud Al Bakkour',
            'email' => 'joud@gmail.com',
            'password' => Hash::make('123456789'),
            'type' =>'super_admin',
            'created_at'=>'2024-04-27 08:46:30',
            
        ]);
}
}
