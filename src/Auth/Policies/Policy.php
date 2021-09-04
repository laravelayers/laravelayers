<?php

namespace Laravelayers\Auth\Policies;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravelayers\Auth\Decorators\UserDecorator;
use Laravelayers\Auth\Services\UserService;
use Laravelayers\Contracts\Auth\Policy as PolicyContract;

class Policy implements PolicyContract
{
    /**
     * UserService instance.
     *
     * @var object
     */
    protected $service;

    /**
     * User actions.
     *
     * @var array
     */
    private static $actions;

    /**
     * Map of actions to aliases.
     *
     * @var array
     */
    protected static $aliases = [
        'view' => 'show',
        'viewAny' => 'index',
        'create' => 'add',
        'update' => 'edit',
        'delete' => 'destroy'
    ];

    /**
     * Checked user actions.
     *
     * @var array
     */
    public static $checked = [];

    /**
     * Create a new Policy instance.
     *
     * @param \Laravelayers\Auth\Services\UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->service = $userService;
    }

    /**
     * Determine if the given action should be granted for the specified user.
     *
     * @param UserDecorator $user
     * @param string $action
     * @param array $arguments
     * @return bool
     */
    public function check(UserDecorator $user, $action, $arguments = [])
    {
        if ($arguments || !$action) {
            return null;
        }

        $userActions = $this->getUserActions($user->getKey());

        if ($userActions->isNotEmpty()) {
            if ($action == '*') {
                $action = '.*';
            }

            $like = Str::endsWith($action, ['.*']);

            $action = $this->getRouteByAction($action);

            $alias = $this->getActionAlias($action);

            if (isset(static::$checked[$user->getKey()][$action])) {
                return static::$checked[$user->getKey()][$action];
            }

            $actions = $alias ? [$alias, $action] : [$action];

            $parentActions = explode('.', $action);

            foreach($parentActions as $item) {
                array_pop($parentActions);

                if (!$parentActions) {
                    break;
                }

                $actions[] = implode('.', $parentActions);
            }

            $deniedAction = $userActions->where('allowed', 0)
                ->whereIn('action', $actions);

            if ($deniedAction->isEmpty()
                || ($deniedAction->first()['ip'] && $deniedAction->first()['ip'] != Request::ip())
            ) {
                foreach ($userActions as $userAction) {
                    $userActionName = strtolower($userAction['action']);
                    $actionName = $action . '.';
                    $aliasName = $alias ? $alias . '.' : '';

                    if (($like && Str::startsWith($userActionName, [$actionName, $aliasName]))
                        || Str::startsWith($actionName, $userActionName . '.')
                        || ($aliasName && Str::startsWith($aliasName, $userActionName . '.'))
                    ) {
                        if ($userAction['allowed']
                            && (!$userAction['ip'] || $userAction['ip'] == Request::ip())
                        ) {
                            return (static::$checked[$user->getKey()][$action] = true);
                        } elseif (!$userAction['ip'] || $userAction['ip'] == Request::ip()) {
                            if (!$like) {
                                return (static::$checked[$user->getKey()][$action] = false);
                            }
                        }
                    }
                }
            }

            return (static::$checked[$user->getKey()][$action] = false);
        }

        return false;
    }

    /**
     * Get a route by action.
     *
     * @param string $name
     * @return string
     */
    protected function getRouteByAction($name)
    {
        $names = explode('.', $name, 2);

        $name = rtrim($name, '.*');

        if (empty($names[1]) || $names[1] == '*') {
            $name = $names[0];

            $route = Request::route() ? Request::route()->getName() : '';

            if (!Str::startsWith($route, $name)) {
                $routes = explode('.', $route);

                array_pop($routes);

                if ($name) {
                    $routes[] = $name;
                }

                $name = implode('.', $routes);

                if (!Route::has($name) && $names[0]) {
                    $alias = $this->getActionAlias($name);

                    $name = Route::has($alias) ? $alias : $names[0];
                }
            }
        }

        return trim($name, '.');
    }

    /**
     * Get the action alias.
     *
     * @param string $action
     * @return mixed|string
     */
    protected function getActionAlias($action)
    {
        $end = substr(strrchr($action, '.'), 1);

        $alias = $this->getActionAliasMap()[$end] ?? array_search($end, $this->getActionAliasMap());

        if ($alias) {
            return preg_replace("/\.[^\.]*$/i", '.' . $alias, $action);
        }

        return '';
    }

    /**
     * Get the map of actions to aliases.
     *
     * @return array
     */
    public static function getActionAliasMap()
    {
        return static::$aliases;
    }

    /**
     * Set the map of actions to aliases.
     *
     * @param string|array $action
     * @param string $alias
     */
    public static function setActionAliasMap($action, $alias = '')
    {
        if (!$alias) {
            static::$aliases = $action;
        } else {
            static::$aliases = array_merge(static::$aliases, [$action => $alias]);
        }
    }

    /**
     * Get user actions.
     *
     * @param int $id
     * @return \Illuminate\Support\Collection
     */
    protected function getUserActions($id)
    {
        if (!isset(static::$actions[$id])) {
            $actions = collect();

            $userActions = $this->service->findWithActionsAndRoles($id)->userActions ?: [];

            foreach ($userActions as $key => $action) {
                if ($actions->where($action->getActionColumn(), $action->getAction())->isEmpty()) {
                    $actions->push([
                        'action' => $action->getAction(),
                        'allowed' => $action->getAllowed(),
                        'ip' => $action->getIp(),
                        'role_id' => $action->userRole->getKey(),
                        'role' => $action->userRole->getRole()
                    ]);
                }

                if ($action->userRole->isNotEmpty()) {
                    foreach ($action->userRole->userRoleActions as $roleAction) {
                        if ($actions->where($roleAction->action_name, $roleAction->name)->isEmpty()) {
                            $actions->push(array_merge($actions->last(), [
                                'role_id' => $roleAction->getKey(),
                                'action' => $roleAction->getAction(),
                                'allowed' => $action->getAllowed() ? $roleAction->getAllowed() : 0,
                            ]));
                        }
                    }
                }
            }

            static::$actions[$id] = $actions;
        }

        return static::$actions[$id];
    }
}
