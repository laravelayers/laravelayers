<?php

namespace Tests\Feature\Auth;

use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravelayers\Admin\Services\Auth\UserRoleService;
use Laravelayers\Auth\Models\User;
use Laravelayers\Auth\Models\UserAction;
use Laravelayers\Auth\Services\UserService;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user authorization.
     */
    public function testAuthUser()
    {
        $user = $this->authUser();

        $this->assertTrue($this->get(route('home'))->status() == 200);
        $this->assertTrue($this->get(route('admin.index'))->status() == 403);
    }

    /**
     * Test the authorization of user actions.
     */
    public function testAuthActions()
    {
        $user = $this->authUser([
            'admin.auth.users.edit',
            ['action' => 'admin.auth.users.show', 'allowed' => 1, 'ip' => '::1'],
            ['action' => 'admin.auth.roles'],
            ['action' => 'admin.auth.roles.create', 'allowed' => 0, 'ip' => '127.0.0.1']
        ]);

        $this->assertTrue($this->get(route('admin.index'))->status() == 200);
        $this->assertTrue($this->get(route('admin.auth.users.index'))->status() == 200);
        $this->assertTrue($this->get(route('admin.auth.users.edit', [$user->id]))->status() == 200);
        $this->assertTrue($this->get(route('admin.auth.users.create'))->status() == 403);
        $this->assertTrue($this->get(route('admin.auth.users.show', [$user->id]))->status() == 403);

        $this->withSession(['_token' => ($token = Str::random(40))])
            ->post(route('admin.auth.users.store'), [
                'action' => 'destroy_multiple',
                '_token' => $token
            ])
            ->assertStatus(403);
        
        $this->assertTrue($this->get(route('admin.auth.users.actions.index', [$user->id]))->status() == 403);
        $this->assertTrue($this->get(route('admin.auth.roles.index'))->status() == 200);
        $this->assertTrue($this->get(route('admin.auth.roles.create'))->status() == 403);
    }

    /**
     * Test the authorization of user roles.
     */
    public function testAuthRoles()
    {
        $user = $this->authUser([
            'admin.auth.roles',
            'admin.auth.users.actions',
        ]);

        $this->assertTrue($this->get(route('admin.auth.users.create'))->status() == 403);
        
        $this->withSession(['_token' => ($token = Str::random(40))])
            ->post(route('admin.auth.roles.store'), [
                '_token' => $token,
                'role' => 'role.administrator'
            ]);
        
        $role = app(UserRoleService::class)->paginate(request())->first();

        $this->withSession(['_token' => ($token = Str::random(40))])
            ->post(route('admin.auth.roles.actions.store', [$role->id]), [
                '_token' => $token,
                'action' => 'admin',
                'allowed' => 1
            ]);
        
        $this->withSession(['_token' => ($token = Str::random(40))])->post(route('logout'), [
            '_token' => $token
        ]);

        $this->authUser([
            'role.administrator',
            ['action' => 'admin.auth.users.edit', 'allowed' => 0],
            ['action' => 'admin.auth.roles.users', 'allowed' => 0, 'ip' => '127.0.0.1']
        ]);

        $this->assertTrue($this->get(route('admin.auth.users.create'))->status() == 200);
        $this->assertTrue($this->get(route('admin.auth.users.edit', [$user->id]))->status() == 403);
        $this->assertTrue($this->get(route('admin.auth.roles.users.index', [$role->id]))->status() == 403);
    }

    /**
     * User authentication.
     *
     * @param array $actions
     * @return DataDecorator
     */
    protected function authUser($actions = [])
    {
        $user = $this->getUser($actions);

        $this->withSession(['_token' => ($token = Str::random(40))])
            ->post(route('login'), [
                'email' => $user->email,
                'password' => '12345678',
                '_token' => $token
            ])
            ->assertRedirect(route('home'));

        return $user;
    }
    
    /**
     * Get the user.
     *
     * @param array $actions
     * @return DataDecorator
     */
    protected function getUser($actions = [])
    {
        $service = app(UserService::class);

        $userActions = [];

        foreach ($actions as $key => $value) {
            $actions[$key] = [
                'action' => $value['action'] ?? $value,
                'allowed' => $value['allowed'] ?? 1,
                'ip' => $value['ip'] ?? null
            ];
        }

        User::factory()
            ->has(
                UserAction::factory()->count(count($actions))
                    ->state(new Sequence(...$actions))
                    ->state(function (array $attributes, User $user) {
                        return ['user_id' => $user->id];
                    })
            )
            ->create();

        $user = $service->withActionsAndRoles()->get()->last();

        $this->assertTrue($user->isNotEmpty());

        return $user;
    }
}
