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
            'headerType' => 1,
            'footerType' => 1,
            'title1' => 'default-title1',
            'title2' => '',
            'headerBgColor' => '#64748B',
            'headerTextColor' => '#000000',
            'footer1' => 'default-footer1',
            'footer2' => '',
            'footerBgColor' => '#64748B',
            'footerTextColor' => '#FFFFFF',
            'avaPath' => '/images/default-ava.png',
        ]);
        $user = User::create([
            'username' => 'test01',
            'password' => Hash::make('123456'),
        ]);
        Show::create([
            'template_id' => 1,
        ]);
        ;
        Section::create([
            'type' => 1,
            'title' => 'default-title',
            'content1' => 'default-content1',
            'content2' => '',
            'bgColor' => '#F3F4F6',
            'textColor' => '#000000',
            'template_id' => 1,
        ]);
        $role = Role::create(['name' => 'admin']);

        $user->roles()->attach($role->id);
    }
}
