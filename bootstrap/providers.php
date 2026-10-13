<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\CustomerPanelProvider;
use App\Providers\TenancyServiceProvider;
use Webkul\Account\AccountServiceProvider;
use Webkul\Accounting\AccountingServiceProvider;
use Webkul\Analytic\AnalyticServiceProvider;
use Webkul\Barcode\BarcodeServiceProvider;
use Webkul\Blog\BlogServiceProvider;
use Webkul\Chatter\ChatterServiceProvider;
use Webkul\Contact\ContactServiceProvider;
use Webkul\Delivery\DeliveryServiceProvider;
use Webkul\Employee\EmployeeServiceProvider;
use Webkul\Field\FieldServiceProvider;
use Webkul\FullCalendar\FullCalendarServiceProvider;
use Webkul\Inventory\InventoryServiceProvider;
use Webkul\Invoice\InvoiceServiceProvider;
use Webkul\Maintenance\MaintenanceServiceProvider;
use Webkul\Manufacturing\ManufacturingServiceProvider;
use Webkul\Partner\PartnerServiceProvider;
use Webkul\Payment\PaymentServiceProvider;
use Webkul\PluginManager\PluginManagerServiceProvider;
use Webkul\Product\ProductServiceProvider;
use Webkul\Project\ProjectServiceProvider;
use Webkul\Purchase\PurchaseServiceProvider;
use Webkul\QrOrdering\QrOrderingServiceProvider;
use Webkul\Recruitment\RecruitmentServiceProvider;
use Webkul\RestaurantKds\RestaurantKdsServiceProvider;
use Webkul\RestaurantPos\RestaurantPosServiceProvider;
use Webkul\RestaurantReports\RestaurantReportsServiceProvider;
use Webkul\RestaurantTables\RestaurantTablesServiceProvider;
use Webkul\Sale\SaleServiceProvider;
use Webkul\Security\SecurityServiceProvider;
use Webkul\Support\SupportServiceProvider;
use Webkul\TableViews\TableViewsServiceProvider;
use Webkul\TimeOff\TimeOffServiceProvider;
use Webkul\Timesheet\TimesheetServiceProvider;
use Webkul\Website\WebsiteServiceProvider;

return [
    AppServiceProvider::class,
    TenancyServiceProvider::class,
    AdminPanelProvider::class,
    CustomerPanelProvider::class,
    AccountingServiceProvider::class,
    AccountServiceProvider::class,
    AnalyticServiceProvider::class,
    BarcodeServiceProvider::class,
    BlogServiceProvider::class,
    ChatterServiceProvider::class,
    ContactServiceProvider::class,
    EmployeeServiceProvider::class,
    DeliveryServiceProvider::class,
    FieldServiceProvider::class,
    InventoryServiceProvider::class,
    InvoiceServiceProvider::class,
    MaintenanceServiceProvider::class,
    ManufacturingServiceProvider::class,
    PartnerServiceProvider::class,
    PaymentServiceProvider::class,
    ProductServiceProvider::class,
    ProjectServiceProvider::class,
    PurchaseServiceProvider::class,
    QrOrderingServiceProvider::class,
    RecruitmentServiceProvider::class,
    RestaurantKdsServiceProvider::class,
    RestaurantPosServiceProvider::class,
    RestaurantReportsServiceProvider::class,
    RestaurantTablesServiceProvider::class,
    SaleServiceProvider::class,
    SecurityServiceProvider::class,
    SupportServiceProvider::class,
    TableViewsServiceProvider::class,
    TimeOffServiceProvider::class,
    FullCalendarServiceProvider::class,
    TimesheetServiceProvider::class,
    WebsiteServiceProvider::class,
    PluginManagerServiceProvider::class,
];
