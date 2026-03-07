<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\User;

class CreateAdminUser extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-admin-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates an admin user.';

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
     * @return mixed
     */
    public function handle()
    {
        $this->info('Creating new admin user. Please answer the following questions');

        $email = $this->ask('Email Address');
        $name = $this->ask('Name');

        $password = $this->ask('Password');
        $confirm_password = $this->ask('Confirm Password');

        if($password != $confirm_password) {
            $this->error('Passwords do not match');
            return;
        }

        $user = User::where('email', $email)->first();
        if($user) {
            $this->error("A user with the email address {$email} already exists");
            return;
        }

        $user = new User;
        $user->email = $email;
        $user->name = $name;
        $user->password = Hash::make($password);
        $user->is_admin = true;
        $user->save();

        $this->info("Admin user {$user->email} successfully created");

    }
}
