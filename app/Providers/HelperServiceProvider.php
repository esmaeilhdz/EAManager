<?php

namespace App\Providers;

use App\Helpers\AccessoryBuyHelper;
use App\Helpers\AccessoryHelper;
use App\Helpers\AccessoryWarehouseHelper;
use App\Helpers\AccountHelper;
use App\Helpers\AddressHelper;
use App\Helpers\AttachmentHelper;
use App\Helpers\BillHelper;
use App\Helpers\CityHelper;
use App\Helpers\ClothSellHelper;
use App\Helpers\ClothWarehouseHelper;
use App\Helpers\EnumerationHelper;
use App\Helpers\FactorPaymentHelper;
use App\Helpers\FactorItemHelper;
use App\Helpers\GroupConversationHelper;
use App\Helpers\ChatGroupPersonHelper;
use App\Helpers\ChatHelper;
use App\Helpers\ClothBuyHelper;
use App\Helpers\ClothHelper;
use App\Helpers\CompanyHelper;
use App\Helpers\CustomerHelper;
use App\Helpers\CuttingHelper;
use App\Helpers\DesignModelHelper;
use App\Helpers\FactorHelper;
use App\Helpers\InvoiceHelper;
use App\Helpers\MenuHelper;
use App\Helpers\NotifHelper;
use App\Helpers\PaymentHelper;
use App\Helpers\PermissionHelper;
use App\Helpers\PersonCompanyHelper;
use App\Helpers\ProductAccessoryHelper;
use App\Helpers\ProductModelHelper;
use App\Helpers\ProvinceHelper;
use App\Helpers\ReportHelper;
use App\Helpers\RoleHelper;
use App\Helpers\SalaryDeductionHelper;
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
use App\Helpers\UserHelper;
use App\Helpers\WarehouseHelper;
use App\Helpers\WarehouseItemHelper;
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
        App::alias(ClothSellHelper::class, 'cloth_sell_helper');
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
        App::alias(FactorItemHelper::class, 'factor_item_helper');
        App::alias(FactorPaymentHelper::class, 'factor_payment_helper');
        App::alias(AttachmentHelper::class, 'attachment_helper');
        App::alias(PaymentHelper::class, 'payment_helper');
        App::alias(BillHelper::class, 'bill_helper');
        App::alias(CompanyHelper::class, 'company_helper');
        App::alias(SalaryHelper::class, 'salary_helper');
        App::alias(DesignModelHelper::class, 'design_model_helper');
        App::alias(GroupConversationHelper::class, 'group_conversation_helper');
        App::alias(ChatGroupPersonHelper::class, 'chat_group_person_helper');
        App::alias(ChatHelper::class, 'chat_helper');
        App::alias(ReportHelper::class, 'report_helper');
        App::alias(MenuHelper::class, 'menu_helper');
        App::alias(UserHelper::class, 'user_helper');
        App::alias(RoleHelper::class, 'role_helper');
        App::alias(PermissionHelper::class, 'permission_helper');
        App::alias(PersonCompanyHelper::class, 'person_company_helper');
        App::alias(EnumerationHelper::class, 'enumeration_helper');
        App::alias(ClothWarehouseHelper::class, 'cloth_warehouse_helper');
        App::alias(SalaryDeductionHelper::class, 'salary_deduction_helper');
        App::alias(ProvinceHelper::class, 'province_helper');
        App::alias(CityHelper::class, 'city_helper');
        App::alias(ProductModelHelper::class, 'product_model_helper');
        App::alias(ProductAccessoryHelper::class, 'product_accessory_helper');
        App::alias(AccessoryWarehouseHelper::class, 'accessory_warehouse_helper');
        App::alias(WarehouseHelper::class, 'warehouse_helper');
        App::alias(WarehouseItemHelper::class, 'warehouse_item_helper');
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
