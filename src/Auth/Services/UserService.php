<?php

namespace Laravelayers\Auth\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Laravelayers\Auth\Decorators\UserDecorator;
use Illuminate\Support\Facades\Hash;
use Laravelayers\Contracts\Admin\Services\Auth\UserActionService;
use Laravelayers\Contracts\Admin\Services\Auth\UserRoleActionService;
use Laravelayers\Contracts\Admin\Services\Auth\UserRoleService;
use Laravelayers\Contracts\Auth\UserRepository as UserRepositoryContract;
use Laravelayers\Contracts\Auth\UserService as UserServiceContract;
use Laravelayers\Foundation\Services\Service;

class UserService extends Service implements UserServiceContract
{
    /**
     * Create a new UserService instance.
     *
     * @param UserRepositoryContract $userRepository
     */
    public function __construct(UserRepositoryContract $userRepository)
    {
        $this->repository = $userRepository;

        $this->setDecorators([
            UserDecorator::class,
        ]);
    }

    /**
     * Find the user by the specified ID and load the actions and role actions for him.
     *
     * @param int $id
     * @return \Laravelayers\Foundation\Decorators\Decorator
     */
    public function findWithActionsAndRoles($id)
    {
        return $this->repository
            ->withActionsAndRoles()
            ->find($id);
    }

    /**
     * Update the user to the repository.
     *
     * @param UserDecorator $user
     * @return UserDecorator
     */
    public function save(UserDecorator $user)
    {
        if (method_exists($user, 'getElements')) {
            $elements = (clone $user)->getElements();

            $request = $elements->getRequest();

            if ($request->has('old_password')
                && !Hash::check($elements->get('old_password')->getValue(), $request->user()->getAuthPassword())
            ) {
                $user->getElements()
                    ->setError('name', Lang::get(Lang::has($trans = 'auth.failed') ? $trans : 'auth::' . $trans))
                    ->validate();
            }
        }

        return (!(clone $this->repository)->count() && in_array(App::environment(), ['testing', 'local']))
            ? $this->createAdmin($user)
            : $this->repository->save($user);
    }

    /**
     * Create a user with the administrator role in the repository.
     *
     * @param UserDecorator $user
     * @return UserDecorator
     */
    protected function createAdmin(UserDecorator $user)
    {
        $user = $this->repository->save($user);

        $this->repository->markEmailAsVerified();

        $roleService = resolve(UserRoleService::class);

        $role = $roleService->fill($user->getElements()->getRequest());

        $role->put($role->getRoleColumn(), 'role.administrator');

        $role = $roleService->save($role);

        $roleActionService = resolve(UserRoleActionService::class);

        $roleAction = $roleActionService->fill($user->getElements()->getRequest());

        $roleAction->put($roleAction->getActionColumn(), 'admin');
        $roleAction->setRole($role->getKey());
        $roleAction->setAllowed(true);

        $roleActionService->save($roleAction);

        $actionService = resolve(UserActionService::class);

        $action = $actionService->fill($user->getElements()->getRequest());

        $action->put($action->getActionColumn(), $role->getRole());
        $action->setUser($user->getKey());
        $action->setAllowed(true);
        $action->setIp('');

        $actionService->save($action);

        return $this->repository->find($user->getKey());
    }
}
