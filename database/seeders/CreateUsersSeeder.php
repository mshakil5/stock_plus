<?php
  
namespace Database\Seeders;
  
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
  
class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
               'name'=>'Admin User',
               'username'=>'admin',
               'email'=>'admin@gmail.com',
               'type'=>1,
               'branch_id'=>1,
               'branchaccess'=>'["6"]',
               'role_id'=>1,
               'status'=>1,
               'password'=> bcrypt('123456'),
            ],
            [
                'name'=>'Admin User',
                'username'=>'admin2',
                'email'=>'admin2@gmail.com',
                'type'=>1,
                'branch_id'=>1,
                'branchaccess'=>'["6"]',
                'role_id'=>1,
                'status'=>1,
                'password'=> bcrypt('123456'),
             ],
             [
                'name'=>'Admin User',
                'username'=>'admin3',
                'email'=>'admin3@gmail.com',
                'type'=>1,
                'branch_id'=>1,
                'branchaccess'=>'["6"]',
                'role_id'=>1,
                'status'=>1,
                'password'=> bcrypt('123456'),
             ],
             [
                'name'=>'Admin User',
                'username'=>'admin4',
                'email'=>'admin4@gmail.com',
                'type'=>1,
                'branch_id'=>1,
                'branchaccess'=>'["6"]',
                'role_id'=>1,
                'status'=>1,
                'password'=> bcrypt('123456'),
             ],
             [
                'name'=>'Admin User',
                'username'=>'admin5',
                'email'=>'admin5@gmail.com',
                'type'=>1,
                'branch_id'=>1,
                'branchaccess'=>'["6"]',
                'role_id'=>1,
                'status'=>1,
                'password'=> bcrypt('123456'),
             ],
            [
               'name'=>'Manager User',
               'username'=>'manager',
               'email'=>'manager@gmail.com',
               'type'=> 2,
               'branch_id'=>1,
               'branchaccess'=>'["6"]',
               'role_id'=>4,
               'status'=>1,
               'password'=> bcrypt('123456'),
            ],
            [
               'name'=>'User',
               'username'=>'user',
               'email'=>'user@gmail.com',
               'type'=>0,
               'branch_id'=>1,
               'branchaccess'=>'["6"]',
               'role_id'=>4,
               'status'=>1,
               'password'=> bcrypt('123456'),
            ],
        ];
    
        foreach ($users as $key => $user) {
            User::create($user);
        }
    }
}