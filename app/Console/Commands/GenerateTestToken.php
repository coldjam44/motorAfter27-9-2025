<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Userauth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class GenerateTestToken extends Command
{
    protected $signature = 'generate:test-token {--email=test_bid_user@example.com} {--password=secret123}';

    protected $description = 'Create (or reuse) a test user and output a JWT token as JSON';

    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');

        $user = Userauth::where('email', $email)->first();
        $created = false;
        if (!$user) {
            // generate a unique phone number
            $phone = '7' . rand(10000000, 99999999);
            $user = Userauth::create([
                'first_name' => 'Test',
                'last_name' => 'Bidder',
                'email' => $email,
                'phone_number' => $phone,
                'password' => Hash::make($password),
                'role' => 'user',
            ]);
            $created = true;
        } else {
            // ensure known password for testing
            $user->password = Hash::make($password);
            $user->save();
        }

        $token = JWTAuth::fromUser($user);

        $payload = [
            'created_new_user' => $created,
            'email' => $user->email,
            'password' => $password,
            'user_id' => $user->id,
            'token' => $token,
            'note' => 'Use Authorization: Bearer <token>'
        ];

        $this->line(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return Command::SUCCESS;
    }
}
