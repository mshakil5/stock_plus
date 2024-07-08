<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\OrderDetail;
use App\Models\Stock;
use App\Models\Branch;
use App\Models\StockTransfer;
use App\Models\StockTransferRequest;
use Illuminate\Support\Facades\Auth;

class StockTransferController extends Controller
{
    public function stock_transfer_request(){
        
        //    $stocks = Stock::with('Products')->get();
    
           $stocks = DB::table('stocks')
                        ->join('products', 'stocks.product_id', '=', 'products.id')
                        ->select('stocks.*', 'products.productname','products.selling_price','products.unit','products.location')
                        ->get();
    
            $data  = StockTransferRequest::orderby('id','DESC')->get();
    
           $branches = Branch::where('status', '=', 1)->get();
            return view('admin.stock.transferRequest', compact('stocks', 'branches','data'));
        }

    

    public function saveStockTransfer(Request $request)
    {


    	$productid = $request->data['productid'];
        $tranReqid = $request->data['tranReqid'];
    	$frombranchid = $request->data['reqfrombranchid'];
    	$tobranchid = $request->data['reqtobranchid'];
    	$transferQty = $request->data['transferQty'];

        if($frombranchid == $tobranchid){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>You Have Selected Same Branch..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
    	//transferring product
    	$transfer = new StockTransfer();
    	$transfer->product_id = $productid;
    	$transfer->stocktransferqty = $transferQty;
    	$transfer->from_branch_id = $tobranchid;
    	$transfer->to_branch_id = $frombranchid;
    	$transfer->status = "1";
    	$transfer->created_by = Auth::user()->id;
    	$transfer->save();
        // from branch stock reduce start

        $givenbranchstockid = Stock::where('branch_id',$tobranchid)->where('product_id',$productid)->first();
        $frmbstock = Stock::find($givenbranchstockid->id);
        $frmbstock->quantity = $frmbstock->quantity - $transferQty;
        $frmbstock->save();

        // from branch stock reduce end

        // to branch stock increase start

        $rcvbranchstockid = Stock::where('branch_id',$frombranchid)->where('product_id',$productid)->first();
        if ($rcvbranchstockid) {
            $updateoldostock = Stock::find($rcvbranchstockid->id);
            $updateoldostock->quantity = $updateoldostock->quantity + $transferQty;
            $updateoldostock->save();
        } else {
            $newStock = new Stock;
            $newStock->quantity = $transferQty;
            $newStock->branch_id = $transfer->to_branch_id;
            $newStock->product_id = $productid;
            $newStock->exp_date = $givenbranchstockid->exp_date;
            $newStock->status = "1";
            $newStock->created_by= Auth::user()->id;
            $newStock->save();
        }

        $tranrequpdatestatus = StockTransferRequest::find($tranReqid);
        $tranrequpdatestatus->status = "1";
        $tranrequpdatestatus->save();

        // to branch stock increase end
        

        $message ="<div class='alert alert-success' style='color:white'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Stock Transfer Successfully.</b></div>";
        return response()->json(['status'=> 300,'message'=>$message]);
    }


    public function adminStockTransfer(Request $request)
    {

    	$productid = $request->data['productid'];
    	$frombranchid = $request->data['frombranchid'];
    	$tobranchid = $request->data['brnachToTransfer'];
    	$transferQty = $request->data['transferQty'];
    	$stockid = $request->data['stockid'];


        if($frombranchid == $tobranchid){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>You Have Selected Same Branch..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }


    	//transferring product
    	$transfer = new StockTransfer();
    	$transfer->product_id = $productid;
    	$transfer->stocktransferqty = $transferQty;
    	$transfer->from_branch_id = $frombranchid;
    	$transfer->to_branch_id = $tobranchid;
    	$transfer->stock_id = $stockid;
    	$transfer->status = "1";
    	$transfer->created_by = Auth::user()->id;
    	$transfer->save();
        // from branch stock reduce start

        
        $frmbstock = Stock::find($stockid);
        $frmbstock->quantity = $frmbstock->quantity - $transferQty;
        $frmbstock->save();

        // from branch stock reduce end

        // to branch stock increase start

        $rcvbranchstockid = Stock::where('branch_id',$tobranchid)->where('product_id',$productid)->first();
        if ($rcvbranchstockid) {
            $updateoldostock = Stock::find($rcvbranchstockid->id);
            $updateoldostock->quantity = $updateoldostock->quantity + $transferQty;
            $updateoldostock->save();
        } else {
            $newStock = new Stock;
            $newStock->quantity = $transferQty;
            $newStock->branch_id = $transfer->to_branch_id;
            $newStock->product_id = $productid;
            $newStock->status = "1";
            $newStock->created_by= Auth::user()->id;
            $newStock->save();
        }
        // to branch stock increase end
        

        $message ="<div class='alert alert-success' style='color:white'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Stock Transfer Successfully.</b></div>";
        return response()->json(['status'=> 300,'message'=>$message]);
    }



    

}
