<?php

namespace App\Providers;

use App\Prism\Providers\LmStudio\LmStudio;
use Illuminate\Support\ServiceProvider;

class PrismLmStudioServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app['prism-manager']->extend('lmstudio', fn ($app, $config) => new LmStudio(
            url: $config['url'],
        ));
    }
}
