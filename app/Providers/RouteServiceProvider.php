<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();


        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

            // روت پرسنل
            $this->mapPerson();
            // روت مکان
            $this->mapPlace();
            // روت دوره فروش
            $this->mapSalePeriod();
            // روت حساب های بانکی
            $this->mapAccount();
            // روت پارچه ها
            $this->mapCloth();
            // روت خرید پارچه
            $this->mapClothBuy();
            // روت خرج کار
            $this->mapAccessory();
            // روت خرید خرج کار
            $this->mapAccessoryBuy();
            // روت کالا
            $this->mapProductBuy();
            // روت ارسال کالا به فروشگاه
            $this->mapProductToStore();
            // روت اعلان ها
            $this->mapNotifs();
            // روت درخواست کالا از انبار
            $this->mapRequestProductFromWarehouse();
            // روت قیمت های کالا
            $this->mapProductPrice();
            // روت انبار های کالا
            $this->mapProductWarehouse();
            // روت دوخت
            $this->mapSewing();
            // روت برش
            $this->mapCutting();
            // روت مشتری
            $this->mapCustomer();
            // روت آدرس
            $this->mapAddress();
            // روت پیش فاکتور
            $this->mapInvoice();
            // روت فاکتور
            $this->mapFactor();
            // روت پیوست
            $this->mapAttachment();

        });
    }

    private function mapPerson()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/person.php'));

    }

    private function mapPlace()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/place.php'));

    }

    private function mapSalePeriod()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/sale_period.php'));

    }

    private function mapAccount()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/account.php'));

    }

    private function mapCloth()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/cloth.php'));

    }

    private function mapClothBuy()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/cloth_buy.php'));

    }

    private function mapAccessory()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/accessories.php'));

    }

    private function mapAccessoryBuy()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/accessory_buy.php'));

    }

    private function mapProductBuy()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/product.php'));

    }

    private function mapProductToStore()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/product_to_store.php'));

    }

    private function mapProductPrice()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/product_price.php'));

    }

    private function mapProductWarehouse()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/product_warehouse.php'));

    }

    private function mapNotifs()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/notifs.php'));

    }

    private function mapRequestProductFromWarehouse()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/request_product_from_warehouse.php'));

    }

    private function mapSewing()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/sewing.php'));

    }

    private function mapCutting()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/cutting.php'));

    }

    private function mapCustomer()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/customer.php'));

    }

    private function mapAddress()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/address.php'));

    }

    private function mapInvoice()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/invoice.php'));

    }

    private function mapFactor()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/factor.php'));

    }

    private function mapAttachment()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/attachment.php'));

    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
