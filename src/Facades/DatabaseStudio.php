<?php

declare(strict_types=1);

namespace Laraforge\DatabaseStudio\Facades;

use Illuminate\Support\Facades\Facade;
use Laraforge\DatabaseStudio\Agents\DatabaseManagerAgent;

/**
 * @see \Laraforge\DatabaseStudio\Agents\DatabaseManagerAgent
 */
class DatabaseStudio extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return DatabaseManagerAgent::class;
    }
}
