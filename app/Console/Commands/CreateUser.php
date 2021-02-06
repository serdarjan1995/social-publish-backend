<?php

namespace App\Console\Commands;

use App\Model\Role;
use App\UserRole;
use Illuminate\Console\Command;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->anticipate('Enter name?', ['Admin', 'John']);

        $email = $this->anticipate('Enter email', ['admin@admin.com']);
        $validator = Validator::make(
            ['email' => $email],
            ['email' => ['required', 'email', 'unique:users,email']]
        );
        if ($validator->fails()) {
            $this->info('User has not been created. See error messages below:');
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        $password = $this->secret('Enter password:');

        $roles = Role::all('*');
        $role_names = array();
        foreach ($roles as $role) {
            array_push($role_names,$role->name);
        }
        $role_choice_id = 4; //default role_id
        $role_choice = $this->choice('Choose role', $role_names, $role_choice_id);
        foreach ($roles as $role) {
            if($role->name == $role_choice){
                $role_choice_id = $role->id; //chosen role_id
                break;
            }
        }
        if ($this->confirm('Are you sure to add user to Database?')) {
            $user = User::create([
                'email'=>$email,
                'password'=>$password,
                'name'=>$name
            ]);
            $user->markEmailAsVerified();
            $user->assignRole($user, $role_choice_id);
            $this->info('User created.');
        }
        else{
            $this->info('Aborted');
        }
    }
}
