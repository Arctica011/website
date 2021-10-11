<?php

namespace Jdlx\Commands\Account;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class AccountChangePassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account:change_password {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change a users password';

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
        //$url = $request->get('url', null);
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = User::where('email', $email)->first();

        if(!$user){
            $this->error("can't find user");
            return;
        }

        $user->password = Hash::make($password);
        $user->save();
        $this->info("password changed");
    }
}
