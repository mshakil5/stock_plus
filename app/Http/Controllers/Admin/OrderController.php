<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\SalesReturn;
use App\Models\SalesReturnDetail;
use DB;
use App\Models\Customer;
use App\Models\Product;
use PDF;
use Mail;
use App\Mail\InvoiceEmailManager;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\StockTransferRequest;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function getAllInvoice()
    {
        return view('admin.invoice.manageallinvoice');
    }

    // change partno sts 
    public function published_partno($ID) {

        Order::where('id', $ID)
        ->update(['partnoshow' => 1]);
    
        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Active successfully.</b></div>";
        return response()->json(['status'=> 300,'message'=>$message]);
      }
    
    
      public function unpublished_partno($ID) {
        Order::where('id', $ID)
          ->update(['partnoshow' => 0]);
    
        $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Deactive successfully.</b></div>";
        return response()->json(['status'=> 300,'message'=>$message]);
      }
}
