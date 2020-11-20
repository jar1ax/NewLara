<?php

namespace Tests\Unit;

use App\Mail\ResetPasswordMail;
use App\Services\UserService;
use DateTime;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\Models\ResetPassword;

class MailTest extends TestCase
{


    use RefreshDatabase;
    use DatabaseMigrations;

    public $mockConsoleOutput = false;
    protected $userService;

    public function setUp(): void
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
            'email' => 'onetest211223@tes.com',
            'password' => bcrypt($password = '123456'),
            'password_confirmation' => '123456'
        ];

        $user = $this->userService->createUser($data);
        $this->assertNotEmpty($user);
        $this->assertInstanceOf(UserService::class, $this->userService);
        $this->assertDatabaseHas('users', ['email' => 'onetest211223@tes.com']);

        $response = $this->post('api/users/login', [
            'email' => $user->email,
            'password' => $user->password
        ]);

        $response->assertOk();
    }

    public function mail_sent_test($user)
    {
        Mail::fake();

        $response1=$this->post('api/password/email', [
            'email' => $user->email,
        ]);
        $response1->assertOk();
        Mail::assertSent(ResetPasswordMail::class,2);

        $this->assertDatabaseHas('reset_passwords', ['user_id' => $user->id]);
    }

    public function reset_password_test($user,$mail)
    {
        $resetPassword = ResetPassword::where(['token' => $mail->token])->first();
        $data=[
            'password'=> 7777777
        ];
        $this->assertTrue($resetPassword);

        $old_password= $user->password;
        $user->password = bcrypt($data['password']);
        $user->save();

        $this->assertNotEquals($old_password,$user->password);

        $resetPassword->delete();

        $this->assertDatabaseMissing('reset_passwords', ['user_id' => $user->id]);
    }
}
