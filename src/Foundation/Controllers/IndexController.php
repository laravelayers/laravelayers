<?php

namespace Laravelayers\Foundation\Controllers;

class IndexController extends Controller
{
    /**
     * Create a new IndexController instance.
     */
    public function __construct()
    {
        $this->service = null;
    }

    /**
     * Display main page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('foundation::welcome');
    }
}
