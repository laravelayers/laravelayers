<?php

namespace Laravelayers\Admin\Controllers\Auth;

use Illuminate\Http\Request;
use Laravelayers\Admin\Controllers\Controller as AdminController;
use Laravelayers\Contracts\Admin\Services\Auth\UserActionService as UserActionServiceContract;
use Laravelayers\Previous\PreviousUrl;

class UserActionController extends AdminController
{
    /**
     * Create a new UserActionController instance.
     *
     * @param UserActionServiceContract $userActionService
     */
    public function __construct(UserActionServiceContract $userActionService)
    {
        $this->authorizeResource();

        $this->service = $userActionService
            ->setSorting('action')
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
            'route' => 'admin.auth.users.actions.index',
            'parent' => 'admin.auth.users.edit',
            'name' => trans('admin::auth/users/actions.menu.name'),
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
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $item = $this->service->fill($request);

        return view('admin::layouts.action.create', compact('item'));
    }

    /**
     * Store a newly created resource in the repository.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $item = $this->service->fill($request);

        $item->getElements()->validate();

        $item = $this->service->save($item);

        return redirect()->route('admin.auth.users.actions.edit', array_merge([
            $request->user,
            $item->getKey()
        ], PreviousUrl::getQuery()));
    }

    /**
     * Display the specified resource.
     *
     * @param int $user
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($user, $id)
    {
        return $this->edit($user, $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $user
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($user, $id)
    {
        $item = $this->service->find($id);

        return view('admin::layouts.action.edit', compact('item'));
    }

    /**
     * Update the specified resource in the repository.
     *
     * @param int $user
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($user, $id)
    {
        $item = $this->service->find($id);

        $item->getElements()->validate();

        $this->service->save($item);

        return redirect()->route('admin.auth.users.actions.edit', array_merge([
            $user, $item->getAction()
        ], PreviousUrl::getQuery()));
    }

    /**
     * Remove the specified resource from the repository.
     *
     * @param int $user
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($user, $id)
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
