<?php

namespace App\Modules\Settings\Panel\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Settings/Panel/Index');
    }

    public function profile(): Response
    {
        return Inertia::render('Settings/Panel/Profile');
    }

    public function security(): Response
    {
        return Inertia::render('Settings/Panel/Security');
    }
} 