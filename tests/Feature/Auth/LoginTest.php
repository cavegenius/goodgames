<?php

namespace Tests\Feature\Auth;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;

class LoginTest extends TestCase {
    use RefreshDatabase;

    // TODO: Move this to a more appropriate location when finished debugging
    public function testCheckEnv() {

        $env = $this->app->environment();

        $this->assertEquals($env, 'testing');
    }

    /**
     * Test a user can view a login form
     * 
     * @return void
     */
    public function testUserCanViewALoginForm() {
        //
        $response = $this->get('/login');

        $response->assertSuccessful();
        $response->assertViewIs('auth.login');
    }

    /**
     * Test a user can not view the login form if already authenticated
     * 
     * @return void
     */
    public function testUserCannotViewALoginFormWhenAuthenticated() {
        $user = factory(User::class)->make();

        $response = $this->actingAs($user)->get('/login');
        $response->assertRedirect('/games');
    }

    /**
     * Test a user can login with correct credentials
     * 
     * @return void
     */
    public function testUserCanLoginWithCorrectCredentials() {
        $user = factory(User::class)->create([
            'password' => bcrypt($password = 'i-love-laravel'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect('/games');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test a user can not login with incorrect password
     * 
     * @return void
     */
    public function testUserCannotLoginWithIncorrectPassword() {
        $user = factory(User::class)->create([
            'password' => bcrypt('i-love-laravel'),
        ]);
        
        $response = $this->from('login')->post('/login', [
            'email' => $user->email,
            'password'=> 'invalid-password',
        ]);

        $response->assertRedirect('login');
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    /**
     * Test the remember me option
     * 
     * @return void
     */
    public function testRememberMeFunctionality() {
        $user = factory(User::class)->create([
            'id' => random_int(1,100),
            'password' => bcrypt($password = 'i-love-laravel'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
            'remember' => 'on',
        ]);

        $response->assertRedirect('/games');
        // Cookie Assertion
        $value = vsprintf('%s|%s|%s', [
            $user->id,
            $user->getRememberToken(),
            $user->password,
        ]);
        $response->assertCookie(Auth::guard()->getRecallerName(), vsprintf('%s|%s|%s', [
            $user->id,
            $user->getRememberToken(),
            $user->password,
        ]));

        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test a user recieves the password reset email
     * 
     * @return void
     */
    public function testUserReceivesAnEmailWithAPasswordResetLink() {
        Notification::fake();

        $user = factory(User::class)->create();

        $response = $this->post('/password/email', [
            'email' => $user->email,
        ]);

        $token = DB::table('password_resets')->first();
        $this->assertNotNull($token);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification, $channels) use ($token) {
            return Hash::check($notification->token, $token->token) === true;
        });
    }
}