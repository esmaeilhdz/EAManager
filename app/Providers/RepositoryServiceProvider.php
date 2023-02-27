<?php

namespace App\Providers;

use App\Repositories\AccessoryBuyRepository;
use App\Repositories\AccessoryRepository;
use App\Repositories\AccessoryWarehouseRepository;
use App\Repositories\AccountRepository;
use App\Repositories\AddressRepository;
use App\Repositories\AttachmentRepository;
use App\Repositories\BillRepository;
use App\Repositories\ClothBuyRepository;
use App\Repositories\ClothRepository;
use App\Repositories\ClothWarehouseRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\CuttingRepository;
use App\Repositories\FactorPaymentRepository;
use App\Repositories\FactorProductRepository;
use App\Repositories\FactorRepository;
use App\Repositories\Interfaces\iAccessory;
use App\Repositories\Interfaces\iAccessoryBuy;
use App\Repositories\Interfaces\iAccessoryWarehouse;
use App\Repositories\Interfaces\iAccount;
use App\Repositories\Interfaces\iAddress;
use App\Repositories\Interfaces\iAttachment;
use App\Repositories\Interfaces\iBill;
use App\Repositories\Interfaces\iCloth;
use App\Repositories\Interfaces\iClothBuy;
use App\Repositories\Interfaces\iClothWarehouse;
use App\Repositories\Interfaces\iCompany;
use App\Repositories\Interfaces\iCustomer;
use App\Repositories\Interfaces\iCutting;
use App\Repositories\Interfaces\iFactor;
use App\Repositories\Interfaces\iFactorPayment;
use App\Repositories\Interfaces\iFactorProduct;
use App\Repositories\Interfaces\iInvoice;
use App\Repositories\Interfaces\iInvoiceProduct;
use App\Repositories\Interfaces\iNotif;
use App\Repositories\Interfaces\iPayment;
use App\Repositories\Interfaces\iSalePeriod;
use App\Repositories\Interfaces\iPerson;
use App\Repositories\Interfaces\iPlace;
use App\Repositories\Interfaces\iProduct;
use App\Repositories\Interfaces\iProductPrice;
use App\Repositories\Interfaces\iProductToStore;
use App\Repositories\Interfaces\iProductWarehouse;
use App\Repositories\Interfaces\iRequestProductWarehouse;
use App\Repositories\Interfaces\iSewing;
use App\Repositories\InvoiceProductRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\NotifRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\SalePeriodRepository;
use App\Repositories\PersonRepository;
use App\Repositories\PlaceRepository;
use App\Repositories\ProductPriceRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductToStoreRepository;
use App\Repositories\ProductWarehouseRepository;
use App\Repositories\RequestProductWarehouseRepository;
use App\Repositories\SewingRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(iPerson::class, PersonRepository::class);
        $this->app->bind(iPlace::class, PlaceRepository::class);
        $this->app->bind(iSalePeriod::class, SalePeriodRepository::class);
        $this->app->bind(iAccount::class, AccountRepository::class);
        $this->app->bind(iCloth::class, ClothRepository::class);
        $this->app->bind(iClothBuy::class, ClothBuyRepository::class);
        $this->app->bind(iClothWarehouse::class, ClothWarehouseRepository::class);
        $this->app->bind(iAccessory::class, AccessoryRepository::class);
        $this->app->bind(iAccessoryBuy::class, AccessoryBuyRepository::class);
        $this->app->bind(iAccessoryWarehouse::class, AccessoryWarehouseRepository::class);
        $this->app->bind(iProduct::class, ProductRepository::class);
        $this->app->bind(iProductToStore::class, ProductToStoreRepository::class);
        $this->app->bind(iProductWarehouse::class, ProductWarehouseRepository::class);
        $this->app->bind(iProductPrice::class, ProductPriceRepository::class);
        $this->app->bind(iNotif::class, NotifRepository::class);
        $this->app->bind(iRequestProductWarehouse::class, RequestProductWarehouseRepository::class);
        $this->app->bind(iSewing::class, SewingRepository::class);
        $this->app->bind(iCutting::class, CuttingRepository::class);
        $this->app->bind(iCustomer::class, CustomerRepository::class);
        $this->app->bind(iAddress::class, AddressRepository::class);
        $this->app->bind(iInvoice::class, InvoiceRepository::class);
        $this->app->bind(iInvoiceProduct::class, InvoiceProductRepository::class);
        $this->app->bind(iFactor::class, FactorRepository::class);
        $this->app->bind(iFactorProduct::class, FactorProductRepository::class);
        $this->app->bind(iFactorPayment::class, FactorPaymentRepository::class);
        $this->app->bind(iAttachment::class, AttachmentRepository::class);
        $this->app->bind(iPayment::class, PaymentRepository::class);
        $this->app->bind(iBill::class, BillRepository::class);
        $this->app->bind(iCompany::class, CompanyRepository::class);
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
