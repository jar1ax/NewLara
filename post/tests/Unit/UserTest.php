<?php

namespace Tests\Unit;

use App\Services\UserService;
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
    protected $userService;

    public function setUp():void
    {
        parent::setUp();
        $this->userService = $this->app->make(UserService::class);
    }

    public function testIt_tests_create_method()
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
            'name' => 'John Doe',
            'email'=>'onetest211223@tes.com',
            'password'=>'123456',
            'password_confirmation'=>'123456'
        ];

        $user=$this->userService->createUser($data);
        $this->assertNotEmpty($user);
        $this->assertInstanceOf(UserService::class,$this->userService);
        $this->assertDatabaseHas('users',['email'=>'onetest211223@tes.com']);
    }
}
