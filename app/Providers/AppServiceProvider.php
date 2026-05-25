<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domains\Orders\Events\Listeners\SendOrderConfirmationListener;
use App\Domains\Orders\Events\OrderCreatedEvent;
use App\Domains\Orders\Services\CartService;
use App\Domains\Orders\Services\CartServiceInterface;
use App\Domains\Results\Events\Listeners\SendResultNotificationListener;
use App\Domains\Results\Events\MedicalResultVerifiedEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CartServiceInterface::class, CartService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());

        Event::listen(
            OrderCreatedEvent::class,
            SendOrderConfirmationListener::class,
        );

        Event::listen(
            MedicalResultVerifiedEvent::class,
            SendResultNotificationListener::class,
        );
    }
}
