<?php

namespace Guzbyte\Ticket; 

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Guzbyte\Ticket\Http\Middleware\IsUser;
use Guzbyte\Ticket\Http\Middleware\checkUser;
use Guzbyte\Ticket\Http\Middleware\checkAgent;
use Guzbyte\Ticket\Http\Middleware\IsTicketAgent;
use Guzbyte\Ticket\Http\Middleware\IsTicketSuperAdmin;
use Guzbyte\Ticket\Http\Middleware\GrantAgentUserAccess;

class TicketServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/config.php', 'ticket');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        $this->loadViewsFrom(__DIR__.'/views', 'ticket');

        $this->publishes([
            __DIR__.'/assets' => public_path('guz_ticket'),
        ], 'public');

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->publishes([
            __DIR__.'/config/config.php' => config_path('config.php'),
        ]);

        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('is_ticket_super_admin', IsTicketSuperAdmin::class);
        $router->aliasMiddleware('is_ticket_agent', IsTicketAgent::class);
        $router->aliasMiddleware('is_user', IsUser::class);
        $router->aliasMiddleware('is_agent', checkAgent::class);
        $router->aliasMiddleware('checkUser', checkUser::class);
        $router->aliasMiddleware('user_agent_access', GrantAgentUserAccess::class);

    }
}


