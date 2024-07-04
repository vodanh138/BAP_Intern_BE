<?php

namespace Database\Seeders;
use App\Models\Template;
use App\Models\Show;
use App\Models\User;
use App\Models\Role;
use App\Models\Section;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class initial extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Template::create([
            'name' => 'default-name',
            'logo' => 'lg',
            'title' => 'default-title',
            'footer' => 'default-footer',
        ]);
        User::create([
            'username' => 'test01',
            'password' => Hash::make(123456),
        ]);
        Show::create([
            'template_id' => 1,
        ]);;
        Section::create([
            'type' => 1,
            'title' => 'default-title',
            'content1' => 'default-content1',
            'content2' => '',
            'template_id' => 1,
        ]);
        Role::create(['name' => 'admin']);
    }
}
