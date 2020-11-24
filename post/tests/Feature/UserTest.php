<?php

namespace Tests\Feature;

use App\Models\ResetPassword;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
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
    }
    public function testRegister()
    {
        $data=[
            'name'=>'Ilon',
            'email'=>'test@test.com',
            'password'=>'777777',
            'password_confirmation'=>'777777',
        ];

        $response = $this->json('POST','api/users', $data)->assertStatus(201);
        $response->assertJsonStructure(['token']);

        $response = $this->post('api/users/login', [
            'email' => $data['email'],
            'password'=>$data['password'] ,
        ]);

        $this->assertAuthenticated();
    }
    public function reset_password_test()
    {
        $user=User::factory()->create();

        $resetPassword = ResetPassword::create([
            'user_id' => $user->id,
            'created_at'=> Carbon::now(),
            'updated_at'=> Carbon::now(),
            'token'=>md5($user->email.Carbon::now()),
        ]);
        $data=[
            'password' => '7777777',
            'token' => $resetPassword->token
        ];

        $response1=$this->post('api/password/reset', [
            'token' => $resetPassword->token,
            'password' => $data['password'],
            'password_confirmation' => $data['password']
        ])->assertOk();
    }
    public function testUser_update_test()
    {
        $user=User::factory()->create();
        $data= [
            'id' => $user->id,
            'email'=>'test@test.com',
            'name'=>'Ben'
        ];

        Passport::actingAs($user);
        $this->put('api/users/'.$data['id'],$data)->assertOk();

    }
}

