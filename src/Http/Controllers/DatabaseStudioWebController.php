<?php

declare(strict_types=1);

namespace Laraforge\DatabaseStudio\Http\Controllers;

use Illuminate\Contracts\View\View;
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
}
