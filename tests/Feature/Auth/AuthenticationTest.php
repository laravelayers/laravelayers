<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Laravelayers\Auth\Decorators\RegisterDecorator;
use Laravelayers\Auth\Services\UserService;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user registration, email verification, authentication, and password reset.
     */
    public function testAuthAdmin()
    {
        $this->authAdmin();

        $this->authUser();

        $this->resetPassword();
    }

    /**
     * Authentication of the first user with the administrator role for the local environment.
     */
    protected function authAdmin()
    {
        $this->assertTrue(App::environment() == 'local');
        
        $this->get(route('register'))->assertStatus(200);

        $response = $this->withSession(['_token' => ($token = Str::random(40))])
            ->post(route('register'), [
                'name' => 'admin',
                'email' => 'admin@test.localhost',
                'password' => '123456',
                'password_confirmation' => '123456',
                '_token' => $token
            ]);

        $response->assertRedirect(route('home'));

        $this->assertTrue($this->get(route('admin.index'))->status() == '200');

        $this->withSession(['_token' => ($token = Str::random(40))])->post(route('logout'), [
            '_token' => $token
        ]);

        $this->assertTrue($this->get(route('admin.index'))->status() == '403');
    }

    /**
     * Test user authentication.
     */
    protected function authUser()
    {
        Auth::guard()->getProvider()->setDecorators(RegisterDecorator::class);

        $response = $this->get(route('register'))->assertStatus(200);
        
        $response = $this->withSession(['_token' => ($token = Str::random(40))])
            ->post(route('register'), [
                'name' => 'user', 
                'email' => 'user@test.localhost', 
                'password' => '123456', 
                'password_confirmation' => '123456', 
                '_token' => $token,
            ]);

        $this->get(route('home'))->assertStatus(302);

        $userService = app(UserService::class);
        
        $user = $userService->searchByName('user')->first();

        $this->assertEmpty($user->getEmailVerifiedAt());
        
        $this->get(route('login'));

        $this->withSession(['_token' => ($token = Str::random(40))])->post(route('login'), [
            'email' => 'user@test.localhost',
            'password' => '123456',
            '_token' => $token
        ]);
        
        $this->get(route('home'))->assertRedirect(route('verification.notice'));

        $this->get(route('verification.notice'));
        
        $this->get(route('home'));

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify', Carbon::now()->addMinutes(60), ['id' => $user->getKey()]
        );
        
        $this->get($verificationUrl)->assertRedirect(route('home'));

        $user = $userService->searchByName('user')->first();
        
        $this->assertNotEmpty($user->getEmailVerifiedAt());
        
        $this->withSession(['_token' => ($token = Str::random(40))])->post(route('logout'), [
            '_token' => $token
        ]);
        
        $this->get(route('home'))->assertStatus(302);
    }

    /**
     * Test reset password.
     */
    protected function resetPassword()
    {
        if (config('mail.username')) {
            $this->get(route('password.request'));

            $response = $this->withSession(['_token' => ($token = Str::random(40))])->post(route('password.email'), [
                'email' => 'admin@test.localhost',
                '_token' => $token
            ]);

            $response->assertRedirect(route('password.request'));
        }

        $user = app(UserService::class)->first();

        $token = Password::broker()->getRepository()->create($user);

        $url = url(config('app.url').route('password.reset', $token, false));

        $this->get($url);

        $response = $this->withSession(['_token' => ($_token = Str::random(40))])
            ->post(route('password.reset', [null]), [
                'email' => 'admin@test.localhost',
                'password' => '12345678',
                'password_confirmation' => '12345678',
                '_token' => $_token,
                'token' => $token
            ]);

        $response->assertRedirect(route('home'));

        $this->get(route('home'))->assertStatus(200);

        $this->withSession(['_token' => ($token = Str::random(40))])->post(route('logout'), [
            '_token' => $token
        ]);

        $this->get(route('home'))->assertStatus(302);
    }
}
