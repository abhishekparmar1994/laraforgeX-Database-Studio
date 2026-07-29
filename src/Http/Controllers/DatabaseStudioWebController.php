<?php

declare(strict_types=1);

namespace Laraforge\DatabaseStudio\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DatabaseStudioWebController extends Controller
{
    /**
     * Display the main Database Studio Table Explorer Dashboard.
     */
    public function index(): View
    {
        return view('database-studio::index');
    }

    /**
     * Display the Visual Table Creator Wizard.
     */
    public function create(): View
    {
        return view('database-studio::create');
    }

    /**
     * Display the Interactive SQL Console Page.
     */
    public function console(): View
    {
        return view('database-studio::console');
    }

    /**
     * Display the Table Inspector, Schema Details & Data Browser for a single table.
     */
    public function manage(string $table): View
    {
        return view('database-studio::manage', [
            'tableName' => $table,
        ]);
    }

    /**
     * Display the Security Gateway Login page.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (session('database_studio_authenticated') === true) {
            return redirect()->to(url(config('database-studio.path', 'database-studio')));
        }

        return view('database-studio::login');
    }

    /**
     * Authenticate security credentials against package configuration or .env.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Fallback to env() if published config file in host app is missing the 'auth' key
        $expectedUsername = config('database-studio.auth.username') ?? env('DB_STUDIO_AUTH_USERNAME', 'admin@admin.com');
        $expectedPassword = config('database-studio.auth.password') ?? env('DB_STUDIO_AUTH_PASSWORD', 'admin123');

        $inputUsername = trim((string) $request->input('username'));
        $inputPassword = (string) $request->input('password');

        if ($inputUsername === $expectedUsername && $inputPassword === $expectedPassword) {
            session(['database_studio_authenticated' => true]);

            return redirect()->to(url(config('database-studio.path', 'database-studio')));
        }

        return redirect()->back()
            ->withInput($request->only('username'))
            ->with('error', 'Invalid username or password credentials. Please try again.');
    }

    /**
     * Log out from Database Studio session.
     */
    public function logout(): RedirectResponse
    {
        session()->forget('database_studio_authenticated');

        return redirect()->to(url(config('database-studio.path', 'database-studio') . '/login'));
    }
}
