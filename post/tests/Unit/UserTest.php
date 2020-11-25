<?php

namespace Tests\Unit;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use App\Services\UserService;
use DateTime;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Tests\TestCase;
use function PHPUnit\Framework\assertJson;

class UserTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    public $mockConsoleOutput = false;
    protected $userService;

    public function setUp():void
    {
        parent::setUp();
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
        $this->userService = $this->app->make(UserService::class);
    }

    public function testIt_tests_create_method()
    {
        $user=User::factory()->create();

        $this->assertNotEmpty($user);
        $this->assertInstanceOf(User::class,$user);
        $this->assertDatabaseHas('users',['email'=>$user->email]);

        $response = $this->post('api/users/login', [
            'email' => $user->email,
            'password' => $user->password
        ]);

        $response->assertOk();
    }
    public function testMail_sent_test()
    {
        $user=User::factory()->create();

        Mail::fake();

        $response1=$this->post('api/password/email', [
            'email' => $user->email,
        ]);
        $response1->assertOk();
        Mail::assertSent(ResetPasswordMail::class,1);

        $this->assertDatabaseHas('reset_passwords', ['user_id' => $user->id]);
    }
    public function testUser_update_test()
    {
        $user=User::factory()->create();
        $data= [
            'email' => 'test@test.com',
            'name' => 'Ben'
        ];

        $this->userService->updateUser($data,$user->id);

        $this->assertDatabaseHas('users', ['id' => $user->id,'email' => $data['email']]);
    }

    public function testGet_auth_user_data()
    {
        $user=User::factory()->create();
        $user2= User::factory()->create();

        Passport::actingAs($user);
        $this->get('api/users/'.$user->id)->assertOk()
            ->assertJsonStructure(['data' =>
                [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->get('api/users/'.rand(4,7))->assertStatus(404);
        $this->get('api/users/'.$user2->id)->assertStatus(403)->assertJsonStructure(['message']);
    }
}
