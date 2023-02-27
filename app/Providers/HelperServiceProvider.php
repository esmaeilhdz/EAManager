<?php

namespace App\Providers;

use App\Helpers\AccessoryBuyHelper;
use App\Helpers\AccessoryHelper;
use App\Helpers\AccountHelper;
use App\Helpers\AddressHelper;
use App\Helpers\AttachmentHelper;
use App\Helpers\BillHelper;
use App\Helpers\ClothBuyHelper;
use App\Helpers\ClothHelper;
use App\Helpers\CompanyHelper;
use App\Helpers\CustomerHelper;
use App\Helpers\CuttingHelper;
use App\Helpers\FactorHelper;
use App\Helpers\InvoiceHelper;
use App\Helpers\NotifHelper;
use App\Helpers\PaymentHelper;
use App\Helpers\SalaryHelper;
use App\Helpers\SalePeriodHelper;
use App\Helpers\PersonHelper;
use App\Helpers\PlaceHelper;
use App\Helpers\ProductHelper;
use App\Helpers\ProductPriceHelper;
use App\Helpers\ProductToStoreHelper;
use App\Helpers\ProductWarehouseHelper;
use App\Helpers\RequestProductWarehouseHelper;
use App\Helpers\SewingHelper;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        App::alias(PersonHelper::class, 'person_helper');
        App::alias(PlaceHelper::class, 'place_helper');
        App::alias(SalePeriodHelper::class, 'sale_period_helper');
        App::alias(AccountHelper::class, 'account_helper');
        App::alias(ClothHelper::class, 'cloth_helper');
        App::alias(ClothBuyHelper::class, 'cloth_buy_helper');
        App::alias(AccessoryHelper::class, 'accessory_helper');
        App::alias(AccessoryBuyHelper::class, 'accessory_buy_helper');
        App::alias(ProductHelper::class, 'product_helper');
        App::alias(ProductToStoreHelper::class, 'product_to_store_helper');
        App::alias(NotifHelper::class, 'notif_helper');
        App::alias(RequestProductWarehouseHelper::class, 'request_product_warehouse_helper');
        App::alias(ProductPriceHelper::class, 'product_price_helper');
        App::alias(ProductWarehouseHelper::class, 'product_warehouse_helper');
        App::alias(SewingHelper::class, 'sewing_helper');
        App::alias(CuttingHelper::class, 'cutting_helper');
        App::alias(CustomerHelper::class, 'customer_helper');
        App::alias(AddressHelper::class, 'address_helper');
        App::alias(InvoiceHelper::class, 'invoice_helper');
        App::alias(FactorHelper::class, 'factor_helper');
        App::alias(AttachmentHelper::class, 'attachment_helper');
        App::alias(PaymentHelper::class, 'payment_helper');
        App::alias(BillHelper::class, 'bill_helper');
        App::alias(CompanyHelper::class, 'company_helper');
        App::alias(SalaryHelper::class, 'salary_helper');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
