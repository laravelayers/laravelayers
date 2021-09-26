<?php

namespace Laravelayers\Admin\Controllers\Auth;

use Illuminate\Http\Request;
use Laravelayers\Admin\Controllers\Controller as AdminController;
use Laravelayers\Contracts\Admin\Services\Auth\RoleUserService as RoleUserServiceContract;
use Laravelayers\Previous\PreviousUrl;

class RoleUserController extends AdminController
{
    /**
     * Create a new User2RoleController instance.
     *
     * @param RoleUserServiceContract $user2RoleService
     */
    public function __construct(RoleUserServiceContract $user2RoleService)
    {
        $this->authorizeResource();

        $this->service = $user2RoleService
            ->setSorting('id', 'desc')
            ->setPerPage(25);
    }

    /**
     * Initialize path items for the admin menu.
     *
     * @return array
     */
    protected function initMenuPath()
    {
        return [
            'route' => 'admin.auth.roles.users.index',
            'parent' => 'admin.auth.roles.edit',
            'name' => trans('admin::auth/users.menu.name'),
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $items = $this->service->paginate($request);

        return view("admin::layouts.action.index", compact('items'));
    }

    /**
     * Store a newly created resource in the repository.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        return $this->edit($id);
    }

    /**
     * Remove the specified resource from the repository.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $this->service->destroy($id);

        return back();
    }

    /**
     * Show the form for editing multiple resources.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function editMultiple(Request $request)
    {
        $items = $this->service->paginate($request);

        return view('admin::layouts.action.multiple', compact('items'));
    }

    /**
     * Update multiple resources in the repository.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateMultiple(Request $request)
    {
        $items = $this->service->paginate($request);

        $items->getElements()->validate($items);

        $this->service->updateMultiple($items);

        return back();
    }

    /**
     * Show the form for delete multiple resources.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function deleteMultiple(Request $request)
    {
        $items = $this->service->paginate($request);

        return view('admin::layouts.action.multiple', compact('items'));
    }

    /**
     * Remove multiple resources from the repository.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyMultiple(Request $request)
    {
        $items = $this->service->paginate($request);

        $items->getElements()->validate();

        $this->service->destroyMultiple($items);

        return back();
    }
}
