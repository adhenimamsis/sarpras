<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    /**
     * Redirects the legacy /dashboard route to the Filament admin dashboard.
     */
    public function index(): RedirectResponse
    {
        return redirect('/admin');
    }
}
