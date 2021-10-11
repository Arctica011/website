<?php

namespace  Jdlx\Commands\Auth;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Console\ClientCommand;
use Laravel\Passport\Passport;

class LoginPassport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:login:passport {email} {password} {--id=} {--secret=} {--server=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get token for account';

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
    public function handle(ClientRepository $clients)
    {
        //$url = $request->get('url', null);
        $email = $this->argument('email');
        $password = $this->argument('password');
        $id = $this->option('id');
        $server = $this->option('server');
        $secret = $this->option('secret');

        if($id){
            if(!$secret){
                $client = $this->getClientById($id);
                $secret = $client->secret;
            }
        }else{
            $client = $this->getPasswordClient();
            $id = $client->id;
            $secret = $client->secret;
        }

        $body = [
            'grant_type' => 'password',
            'username' => $email,
            'password' => $password,
            'client_id' => $id,
            'client_secret' => $secret
        ];

        $domain = config('app.url');
        if($server){
            $domain = $server;
        }

        $url = "${domain}/oauth/token";
        $this->info("Requesting tokens from ${url}");

        $client = new \GuzzleHttp\Client(['verify' => false ]);
        $response = $client->post($url, ["form_params"=>$body]);
        $json = json_decode($response->getBody());
        dump($json);
    }

    protected function getClientById($id){
        $client = Passport::client();

        return $client
            ->where('id', $id)
            ->first();
    }

    protected function getPasswordClient(){
        $client = Passport::client();

        return $client
            ->where('password_client', 1)
            ->first();
    }
}
