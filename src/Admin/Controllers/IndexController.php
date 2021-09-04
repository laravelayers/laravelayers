<?php

namespace Laravelayers\Admin\Controllers;

use Laravelayers\Admin\Controllers\Controller as AdminController;

class IndexController extends AdminController
{
    /**
     * Create a new IndexController instance.
     */
    public function __construct()
    {
        $this->middleware('can:admin.*');
    }

    /**
     * Display main page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $content = app(\Parsedown::class)->text(file_get_contents(__DIR__ . '/../../../README.md'));

        return view('admin::index', compact('content'));
    }
}
