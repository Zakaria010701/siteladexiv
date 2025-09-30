<?php

namespace App\Http\Controllers;

class CustomPageController extends Controller
{
    public function provider()
    {
        return view('filament.admin.resources.user-resource.pages.provider');
    }
}
