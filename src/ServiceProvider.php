<?php

declare(strict_types=1);

namespace TPG\PayFast;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use TPG\PayFast\Contracts\LaravelPayFastInterface;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LaravelPayFastInterface::class, fn () => new LaravelPayFast(config('payfast')));
        $this->app->bind('payfast.facade', fn () => app(LaravelPayFastInterface::class));
    }

    public function boot(): void
    {

    }
}
