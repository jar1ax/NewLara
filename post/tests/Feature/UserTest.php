<?php

namespace Tests\Feature;

use DateTime;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    public $mockConsoleOutput = false;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testRegister()
    {
        /**
         *  Added Laravel/Passport client token generation code,
         * which i found at laracast.com
         */
        $clientRepository = new ClientRepository();
        $client = $clientRepository->createPersonalAccessClient(
            null, 'Test Personal Access Client', 'http://localhost'
        );

        DB::table('oauth_personal_access_clients')->insert([
            'client_id' => $client->id,
            'created_at' => new DateTime,
            'updated_at' => new DateTime,
        ]);

        $data = [
            'name' => 'JohnDoe',
            'email'=>'onetestaq1121@test.com',
            'password'=>'123456',
            'password_confirmation'=>'123456'
        ];

        $response = $this->json('POST','api/users', $data)->assertStatus(201);
        $response->assertJsonStructure(['token']);
    }
}

