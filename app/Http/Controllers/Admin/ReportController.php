<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Purchase;
use App\Models\PurchaseHistory;
use App\Models\SalesReturn;
use App\Models\SalesReturnDetail;
use App\Models\PurchaseReturn;
use App\Models\StockTransfer;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    public function getReportTitle()
    {
        return view("admin.report.index");
    }
    
    public function getSalesReport(Request $request)
    {
        $sales = Order::with('orderdetails')->where('sales_status','=','1')
                ->when($request->input('fromdate'), function ($query) use ($request) {
                    $query->whereBetween('orderdate', [$request->input('fromdate'), $request->input('todate')]);
                })
                ->when($request->input('branch_id'), function ($query) use ($request) {
                    $query->where("branch_id",$request->input('branch_id'));
                })
                ->when($request->input('salestype'), function ($query) use ($request) {
                    $query->where("salestype",$request->input('salestype'));
                })
                ->when($request->input('customer_id'), function ($query) use ($request) {
                    $query->where("customer_id",$request->input('customer_id'));
                })
        ->get();

        $cashsales = Order::with('orderdetails')->where('salestype','=','Cash')->where('sales_status','=','1')->sum('net_total');
        $creditsales = Order::with('orderdetails')->where('salestype','=','Credit')->where('sales_status','=','1')->sum('net_total');
        return view("admin.report.sales",compact('sales','cashsales','creditsales'));
    }

    public function getQuotationReport(Request $request)
    {
        $sales = Order::with('orderdetails')->where('quotation','=','1')
                ->when($request->input('fromdate'), function ($query) use ($request) {
                    $query->whereBetween('orderdate', [$request->input('fromdate'), $request->input('todate')]);
                })
                ->when($request->input('branch_id'), function ($query) use ($request) {
                    $query->where("branch_id",$request->input('branch_id'));
                })
                ->when($request->input('customer_id'), function ($query) use ($request) {
                    $query->where("customer_id",$request->input('customer_id'));
                })
        ->get();

        return view("admin.report.quotation",compact('sales'));
    }

    public function getDeliveryNoteReport(Request $request)
    {
        $sales = Order::with('orderdetails')->where('delivery_note','=','1')
                ->when($request->input('fromdate'), function ($query) use ($request) {
                    $query->whereBetween('orderdate', [$request->input('fromdate'), $request->input('todate')]);
                })
                ->when($request->input('branch_id'), function ($query) use ($request) {
                    $query->where("branch_id",$request->input('branch_id'));
                })
                ->when($request->input('customer_id'), function ($query) use ($request) {
                    $query->where("customer_id",$request->input('customer_id'));
                })
        ->get();

        return view("admin.report.deliverynote",compact('sales'));
    }






    public function getPurchaseReport(Request $request)
    {
        
            $purchase = Purchase::with('purchasehistory')
                    ->when($request->input('fromdate'), function ($query) use ($request) {
                        $query->whereBetween('date', [$request->input('fromdate'), $request->input('todate')]);
                    })
                    ->when($request->input('branch_id'), function ($query) use ($request) {
                        $query->where("branch_id",$request->input('branch_id'));
                    })
                    ->when($request->input('purchase_type'), function ($query) use ($request) {
                        $query->where("purchase_type",$request->input('purchase_type'));
                    })
                    ->when($request->input('vendor_id'), function ($query) use ($request) {
                        $query->where("vendor_id",$request->input('vendor_id'));
                    })
            ->get();

            // dd($purchase);

            $cashpurchase = Purchase::with('purchasehistory')->where('purchase_type','=','Cash')->sum('net_amount');
            $creditpurchase = Purchase::with('purchasehistory')->where('purchase_type','=','Credit')->sum('net_amount');
        
        return view("admin.report.purchase",compact('purchase','cashpurchase','creditpurchase'));
    }

    public function getSalesReturnReport(Request $request)
    {
        $salesreturns = SalesReturn::with('salesreturndetail')
                ->when($request->input('fromdate'), function ($query) use ($request) {
                    $query->whereBetween('returndate', [$request->input('fromdate'), $request->input('todate')]);
                })
                ->when($request->input('branch_id'), function ($query) use ($request) {
                    $query->where("branch_id",$request->input('branch_id'));
                })
                ->when($request->input('customer_id'), function ($query) use ($request) {
                    $query->where("customer_id",$request->input('customer_id'));
                })
        ->get();


        return view("admin.report.salesreturn",compact('salesreturns'));
    }

    public function getPurchaseReturnReport(Request $request)
    {
        
        $purchase = PurchaseReturn::
                when($request->input('fromdate'), function ($query) use ($request) {
                    $query->whereBetween('date', [$request->input('fromdate'), $request->input('todate')]);
                })
                ->when($request->input('branch_id'), function ($query) use ($request) {
                    $query->where("branch_id",$request->input('branch_id'));
                })
                ->when($request->input('vendor_id'), function ($query) use ($request) {
                    $query->where("vendor_id",$request->input('vendor_id'));
                })
        ->get();
        return view("admin.report.purchasereturn",compact('purchase'));
    }

    public function getStockTransferReport(Request $request)
    {
        $data = StockTransfer::
                when($request->input('fromdate'), function ($query) use ($request) {
                    $query->whereBetween('created_at', [$request->input('fromdate'), $request->input('todate').' 23:59:59']);
                })
                ->get();
        return view("admin.report.stocktransfer",compact('data'));
    }

    public function getProfitLossReport(Request $request)
    {
        
        $sales = Order::with('orderdetails')->where('sales_status','=','1')
                ->when($request->input('fromdate'), function ($query) use ($request) {
                    $query->whereBetween('orderdate', [$request->input('fromdate'), $request->input('todate')]);
                })
                ->when($request->input('branch_id'), function ($query) use ($request) {
                    $query->where("branch_id",$request->input('branch_id'));
                })
                ->when($request->input('salestype'), function ($query) use ($request) {
                    $query->where("salestype",$request->input('salestype'));
                })
                ->when($request->input('customer_id'), function ($query) use ($request) {
                    $query->where("customer_id",$request->input('customer_id'));
                })
        ->get();

        if ($request->input('fromdate')) {
            $from = $request->input('fromdate');
            $to = $request->input('todate');
        } else {
            $from = "";
            $to = "";
        }
        
        

        $cashsales = Order::with('orderdetails')->where('salestype','=','Cash')->where('sales_status','=','1')->sum('net_total');
        $creditsales = Order::with('orderdetails')->where('salestype','=','Credit')->where('sales_status','=','1')->sum('net_total');
        return view("admin.report.profitlossreport",compact('sales','cashsales','creditsales','from','to'));
    }
}
