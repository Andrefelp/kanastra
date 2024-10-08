<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class CreateUserAndGenerateToken extends Command
{
    protected $signature = 'create:user';
    protected $description = 'Cria um novo usuário com Faker e gera um token de API';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $faker = Faker::create();

        $name = $faker->name;
        $email = $faker->unique()->safeEmail;
        $password = $faker->password;

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $token = $user->createToken('API Token')->plainTextToken;

        $this->info("Usuário criado com sucesso!");
        $this->info("Token de API: " . $token);

        return 0;
    }
}
