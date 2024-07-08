<?php
  
use Illuminate\Support\Facades\Route;
  
use App\Http\Controllers\HomeController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\ProductController;
  
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// cache clear
Route::get('/clear', function() {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cleared!";
 });
//  cache clear
  
Route::get('/', function () {
    return view('auth.login');
});
  
Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('invoice/customer/{id}', [InvoiceController::class, 'customer_invoice_download'])->name('customer.invoice.download');
Route::get('invoice/print/{id}', [InvoiceController::class, 'customer_invoice_print'])->name('customer.invoice.print');
Route::get('getpayment-method', [PaymentMethodController::class, 'getpaymentmethod']);


    
Route::get('/admin/group-all', [App\Http\Controllers\Admin\GroupController::class, 'get_all_group']);
Route::get('/admin/brand-all', [App\Http\Controllers\Admin\BrandController::class, 'get_all_brand']);
Route::get('/admin/category-all', [App\Http\Controllers\Admin\CategoryController::class, 'get_all_category']);
Route::post('/admin/category', [App\Http\Controllers\Admin\CategoryController::class, 'save_category']);
Route::post('/admin/brand', [App\Http\Controllers\Admin\BrandController::class, 'save_brand']);
Route::post('/admin/group', [App\Http\Controllers\Admin\GroupController::class, 'save_group']);


/*------------------------------------------
--------------------------------------------
All Normal Users Routes List
--------------------------------------------
--------------------------------------------*/
Route::group(['prefix' =>'user/', 'middleware' => ['auth', 'is_user']], function(){
  
    Route::get('home', [HomeController::class, 'userHome'])->name('user.home');
    Route::get('sales', [HomeController::class, 'sales'])->name('user.sales');
    // partno status 
    Route::get('/published-partno/{id}', [OrderController::class, 'published_partno']);
    Route::get('/unpublished-partno/{id}', [OrderController::class, 'unpublished_partno']);

});
  

  
/*------------------------------------------
--------------------------------------------
All Manager Routes List
--------------------------------------------
--------------------------------------------*/
Route::group(['prefix' =>'manager/', 'middleware' => ['auth', 'is_manager']], function(){
  
    Route::get('home', [HomeController::class, 'managerHome'])->name('manager.home');
});

// this is test

Route::group(['middleware' => ['auth']], function(){

    // all product
    Route::get('admin/product/active', [App\Http\Controllers\Admin\ProductController::class, 'getAllProduct']);
    Route::post('admin/product', [ProductController::class, 'storeProduct'])->name('admin.storeproduct');
  
    Route::get('sales', [HomeController::class, 'sales'])->name('sales');
    Route::get('all-invoices', [OrderController::class, 'getAllInvoice'])->name('user.allinvoices');
    Route::get('all-quotation', [OrderController::class, 'getAllQuoation'])->name('user.allquotation');
    Route::get('all-delivery-note', [OrderController::class, 'getAllDeliveryNote'])->name('user.alldeliverynote');

    // filter all invoice by datatables
    Route::get('filter-all-invoices', [OrderController::class, 'filterAllInvoice'])->name('user.filterallinvoices');
    Route::get('filter-all-quotation', [OrderController::class, 'filterAllQuotation'])->name('user.filterallquotation');
    Route::get('filter-all-delivery-notes', [OrderController::class, 'filterAllDeliveryNote'])->name('user.filteralldnotes');



    Route::post('getproduct', [OrderController::class, 'getproduct']);
    Route::post('getcustomer', [OrderController::class, 'getcustomer']);
    Route::get('customer/active', [CustomerController::class, 'activeCustomer']);
    Route::post('customers', [CustomerController::class, 'store']);
    Route::post('stock-request', [OrderController::class, 'stockRequest']);
    Route::get('stock-request', [OrderController::class, 'getStockRequest'])->name('user.stockrequest');

    Route::post('/order-store', [OrderController::class, 'orderStore'])->name('order.store');
    Route::post('/order-update', [OrderController::class, 'orderUpdate'])->name('order.update');
    Route::post('/quotation-update', [OrderController::class, 'quotationUpdate'])->name('quotation.update');
    Route::post('/delivery-note-update', [OrderController::class, 'deliveryNoteUpdate'])->name('deliverynote.update');
    Route::get('/quotation-edit/{id}', [OrderController::class, 'quotationEdit'])->name('quotation.edit');
    Route::get('/delivery-note-edit/{id}', [OrderController::class, 'deliveryNoteEdit'])->name('deliverynote.edit');
    Route::get('/sales-edit/{id}', [OrderController::class, 'salesEdit'])->name('sales.edit');
    Route::get('/sales-return/{id}', [OrderController::class, 'salesReturn'])->name('sales.return');
    Route::get('sales-detail/{id}', [OrderController::class, 'salesDetails'])->name('user.salesdetails');

    // delivery note 
    Route::post('/delivery-note-store', [OrderController::class, 'deliveryNoteStore'])->name('deliverynote.store');
    // quotation
    Route::post('/quotation-store', [OrderController::class, 'quotationNoteStore'])->name('quotationnote.store');

    // sales return
    Route::post('get-product-details', [OrderController::class, 'getproductdetails']);
    Route::post('sales-return', [OrderController::class, 'salesReturnStore'])->name('salesReturn.store');
    Route::get('all-sales-return', [OrderController::class, 'getAllReturnInvoice'])->name('user.allreturninvoices');
    Route::get('sales-return-detail/{id}', [OrderController::class, 'salesReturnDetails'])->name('user.salesreturndetails');

    // partno status 
    Route::get('/published-partno/{id}', [OrderController::class, 'published_partno']);
    Route::get('/unpublished-partno/{id}', [OrderController::class, 'unpublished_partno']);

});
