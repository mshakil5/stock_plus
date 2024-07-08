<?php


use Illuminate\Support\Facades\Route;


use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\OrderController;


/*------------------------------------------
--------------------------------------------
All Admin Routes List
--------------------------------------------
--------------------------------------------*/
Route::group(['prefix' =>'admin/', 'middleware' => ['auth', 'is_admin']], function(){
  
    Route::get('home', [HomeController::class, 'adminHome'])->name('admin.home');


    // add Branch
    Route::get('/branch', [BranchController::class, 'view_branch'])->name('view_branch');
    Route::get('/branch-all', [BranchController::class, 'get_all_branch']);
    Route::post('/branch', [BranchController::class, 'save_branch']);
    Route::get('/published-branch/{id}', [BranchController::class, 'published_branch']);
    Route::get('/unpublished-branch/{id}', [BranchController::class, 'unpublished_branch']);
    Route::post('/edit-branch/{id}', [BranchController::class, 'edit_branch']);
    
    //System User
    Route::get('/create-user', [UserController::class, 'create_user'])->name('create_user');
    Route::post('/create-user', [UserController::class, 'save_user'])->name('save_user');
    Route::get('/manage-user', [UserController::class, 'manage_user'])->name('manage_user');
    Route::post('/update-user', [UserController::class, 'update_user'])->name('update_user');

    Route::get('/create-admin', [UserController::class, 'create_admin'])->name('create_admin');
    Route::post('/create-admin', [UserController::class, 'save_admin'])->name('save_admin');
    Route::get('/manage-admin', [UserController::class, 'manage_admin'])->name('manage_admin');
    Route::post('/update-admin', [UserController::class, 'update_admin'])->name('update_admin');
    Route::get('/super-admin', [UserController::class, 'super_admin'])->name('super_admin');
    Route::post('/update-super-admin', [UserController::class, 'update_super_admin'])->name('update_super_admin');
    
    Route::get('/published-user/{id}', [UserController::class, 'published_user']);
    Route::get('/unpublished-user/{id}', [UserController::class, 'unpublished_user']);

    // switch branch
    Route::get('/switch-branch', [UserController::class, 'switch_branch'])->name('switch_branch');
    Route::post('/switch-branch', [UserController::class, 'switch_branch_store'])->name('switch_branch_store');

    // add product
    Route::get('add-product', [ProductController::class, 'addProduct'])->name('admin.addproduct');
    Route::get('product-edit/{id}', [ProductController::class, 'editProduct'])->name('admin.editproduct');
    Route::get('manage-product', [ProductController::class, 'view_manage_product'])->name('admin.manage_product');
    Route::get('filter-all', [ProductController::class, 'filter_product'])->name('admin.filter_product');
    Route::get('product-info/{product}', [ProductController::class, 'get_product']);
    Route::post('update-product-details', [ProductController::class, 'update_product_details']);

    // add category
    Route::get('/product-category', [CategoryController::class, 'view_product_category'])->name('view_product_category');
    // Route::get('/category-all', [CategoryController::class, 'get_all_category']);
    Route::post('/category', [CategoryController::class, 'save_category']);
    Route::get('/published-category/{id}', [CategoryController::class, 'published_category']);
    Route::get('/unpublished-category/{id}', [CategoryController::class, 'unpublished_category']);
    Route::post('/edit-category/{id}', [CategoryController::class, 'edit_category']);

    // add brand
    Route::get('/product-brand', [BrandController::class, 'view_product_brand'])->name('view_product_brand');
    // Route::get('/brand-all', [BrandController::class, 'get_all_brand']);
    Route::post('/brand', [BrandController::class, 'save_brand']);
    Route::get('/published-brand/{id}', [BrandController::class, 'published_brand']);
    Route::get('/unpublished-brand/{id}', [BrandController::class, 'unpublished_brand']);
    Route::post('/edit-brand/{id}', [BrandController::class, 'edit_brand']);

    // add group
    Route::get('/product-group', [GroupController::class, 'view_product_group'])->name('view_product_group');
    // Route::get('/group-all', [GroupController::class, 'get_all_group']);
    Route::post('/group', [GroupController::class, 'save_group']);
    Route::get('/published-group/{id}', [GroupController::class, 'published_group']);
    Route::get('/unpublished-group/{id}', [GroupController::class, 'unpublished_group']);
    Route::post('/edit-group/{id}', [GroupController::class, 'edit_group']);

    // add size
    Route::get('/product-size', [SizeController::class, 'view_product_size'])->name('view_product_size');
    Route::get('/size-all', [SizeController::class, 'get_all_size']);
    Route::post('/size', [SizeController::class, 'save_size']);
    Route::get('/published-size/{id}', [SizeController::class, 'published_size']);
    Route::get('/unpublished-size/{id}', [SizeController::class, 'unpublished_size']);
    Route::post('/edit-size/{id}', [SizeController::class, 'edit_size']);
    
    //Vendor
    Route::get('vendor/add', [VendorController::class, 'add_vendor'])->name('admin.addvendor');
    Route::post('vendor/save', [VendorController::class, 'save_vendor'])->name('admin.savevendor');
    Route::post('vendor/update', [VendorController::class, 'update_vendor'])->name('admin.updatevendor');
    Route::get('vendor/type', [VendorController::class, 'vendor_type'])->name('admin.addtype');
    Route::post('vendor/type', [VendorController::class, 'save_type']);

    // Customer
    Route::get('customers', [CustomerController::class, 'index'])->name('admin.addcustomer');
    Route::post('customers', [CustomerController::class, 'store']);
    Route::get('customers/{id}', [CustomerController::class, 'edit']);
    Route::put('customers/{id}', [CustomerController::class, 'update']);
    Route::get('customers/{id}/change-status', [CustomerController::class, 'changeStatus']);

    // stock
    Route::get('add-stock', [StockController::class, 'addstock'])->name('admin.addstock');
    Route::get('purchase-edit/{id}', [StockController::class, 'editpurchase'])->name('admin.purchaseedit');
    Route::get('purchase-return/{id}', [StockController::class, 'purchaseReturn'])->name('admin.purchasereturn');
    Route::post('purchase-return/{id}', [StockController::class, 'purchaseReturnStore']);
    Route::post('add-stock', [StockController::class, 'stockStore']);
    Route::post('update-purchase', [StockController::class, 'purchaseUpdate']);
    Route::post('update-stock', [StockController::class, 'stockUpdate']);
    Route::get('stock-history/{id}', [StockController::class, 'stockHistory'])->name('admin.stockhistory');
    Route::get('stock-re-entry', [StockController::class, 'stockReEntry'])->name('stock-re-entry');
    Route::get('stock-re-entry-product-push/{id}', [StockController::class, 'pushProduct']);
    Route::get('stock-re-entry-old-purchase-get/{id}', [StockController::class, 'getOldPurchase']);
    Route::get('filter-stock-all', [StockController::class, 'filter_product'])->name('stock.filterall');
    Route::get('manage-stock', [StockController::class, 'managestock'])->name('admin.managestock');
    Route::get('stock-return-history', [StockController::class, 'stockReturnHistory'])->name('admin.stockReturnHistory');

    // stock history 
    Route::get('product-purchase-history', [StockController::class, 'productPurchaseHistory'])->name('admin.product.purchasehistory');

    // stock transfer history
    Route::get('stock-transfer-request', [StockTransferController::class, 'stock_transfer_request'])->name('admin.stock.transferrequest');
    Route::post('save-stock-transfer', [StockTransferController::class, 'saveStockTransfer'])->name('admin.stock.transfer');
    Route::get('stock-transfer-history', [StockController::class, 'stock_transfer_history'])->name('admin.stock.transferhistory');
    // admin stock transfer
    Route::post('admin-stock-transfer', [StockTransferController::class, 'adminStockTransfer'])->name('admin.stock.transfer');

    // product return
    Route::post('save-product-return', [StockController::class, 'saveStockReturn'])->name('admin.stock.return');

    // invoices
    Route::get('all-sellsinvoice', [InvoiceController::class, 'all_sell_invoice'])->name('admin.allsellinvoice');
    Route::get('invoice/{id}', [InvoiceController::class, 'get_invoice'])->name('admin.get_invoice');
    Route::get('filter', [InvoiceController::class, 'filter'])->name('invoice-filter');

    // payment method
    Route::get('payment-method', [PaymentMethodController::class, 'view_payment_method'])->name('view_payment_method');
    Route::get('method-all', [PaymentMethodController::class, 'get_all_method']);
    Route::post('payment-method', [PaymentMethodController::class, 'save_method']);
    Route::get('published-method/{id}', [PaymentMethodController::class, 'published_method']);
    Route::get('unpublished-method/{id}', [PaymentMethodController::class, 'unpublished_method']);
    Route::post('edit-method/{id}', [PaymentMethodController::class, 'edit_method']);

    // for purchase 
    Route::post('getproduct', [ProductController::class, 'getproduct']);

    
    // partno status 
    Route::get('/published-partno/{id}', [OrderController::class, 'published_partno']);
    Route::get('/unpublished-partno/{id}', [OrderController::class, 'unpublished_partno']);

    // roles and permission
    Route::get('role', [RoleController::class, 'index'])->name('admin.role');
    Route::post('role', [RoleController::class, 'store'])->name('admin.rolestore');
    Route::get('role/{id}', [RoleController::class, 'edit'])->name('admin.roleedit');
    Route::post('role-update', [RoleController::class, 'update'])->name('admin.roleupdate');

    // reports
    Route::get('getreport-title', [ReportController::class, 'getReportTitle'])->name('report');
    Route::get('sales-report', [ReportController::class, 'getSalesReport'])->name('salesReport');
    Route::post('sales-report', [ReportController::class, 'getSalesReport'])->name('salesReport.search');

    Route::get('quotation-report', [ReportController::class, 'getQuotationReport'])->name('quotationReport');
    Route::post('quotation-report', [ReportController::class, 'getQuotationReport'])->name('quotationReport.search');

    Route::get('delivery-note-report', [ReportController::class, 'getDeliveryNoteReport'])->name('deliveryNoteReport');
    Route::post('delivery-note-report', [ReportController::class, 'getDeliveryNoteReport'])->name('deliveryNoteReport.search');

    Route::get('purchase-report', [ReportController::class, 'getPurchaseReport'])->name('purchaseReport');
    Route::post('purchase-report', [ReportController::class, 'getPurchaseReport'])->name('purchaseReport.search');

    Route::get('sales-return-report', [ReportController::class, 'getSalesReturnReport'])->name('salesReturnReport');
    Route::post('sales-return-report', [ReportController::class, 'getSalesReturnReport'])->name('salesReturnReport.search');

    Route::get('purchase-return-report', [ReportController::class, 'getPurchaseReturnReport'])->name('purchaseReturnReport');
    Route::post('purchase-return-report', [ReportController::class, 'getPurchaseReturnReport'])->name('purchaseReturnReport.search');

    Route::get('stock-transfer-report', [ReportController::class, 'getStockTransferReport'])->name('stockTransferReport');
    Route::post('stock-transfer-report', [ReportController::class, 'getStockTransferReport'])->name('stockTransferReport.search');
    
    Route::get('profit-loss-report', [ReportController::class, 'getProfitLossReport'])->name('profitLossReport');
    Route::post('profit-loss-report', [ReportController::class, 'getProfitLossReport'])->name('profitLossReport.search');

    

});