<?php

namespace App\Providers;

use App\Models\AccessoryBuy;
use App\Models\ClothBuyItem;
use App\Models\ClothSellItem;
use App\Models\Notif;
use App\Models\ProductToStore;
use App\Models\Sewing;
use App\Observers\AccessoryBuyObserver;
use App\Observers\ClothBuyObserver;
use App\Observers\ClothSellObserver;
use App\Observers\NotifObserver;
use App\Observers\ProductToStoreObserver;
use App\Observers\SewingObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        ClothBuyItem::observe(ClothBuyObserver::class);
        ClothSellItem::observe(ClothSellObserver::class);
        AccessoryBuy::observe(AccessoryBuyObserver::class);
        ProductToStore::observe(ProductToStoreObserver::class);
        Notif::observe(NotifObserver::class);
        Sewing::observe(SewingObserver::class);
    }
}
