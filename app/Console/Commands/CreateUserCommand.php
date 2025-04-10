<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password as RulesPassword;

class CreateUserCommand extends Command
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
    protected $description = 'Create new users.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $user['name'] = $this->ask('Name : ');
        $user['email'] = $this->ask('Email : ');

        $user['password'] = $this->secret('Password : ');

        $role_name = $this->choice('Role : ', ['admin', 'editor'], 1);

        //
        $role = Role::where('name', $role_name)->first();
        if (! $role) {
            $this->error('Role not found !!!');

            return -1;
        }

        //
        $validator = Validator::make($user, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email|unique:users,email',
            'password' => ['required', RulesPassword::default()],
        ]);

        //
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return -1;
        }

        // To ensure that if role attaching failed, don't inject the user into DB
        DB::transaction(function () use ($user, $role) {
            //
            $user['password'] = Hash::make($user['password']);
            //
            $new_user = User::create($user);
            $new_user->roles()->attach($role->id);
        });

        //
        $this->info('User created successfully 😎 ');

        return 0;
    }
}
