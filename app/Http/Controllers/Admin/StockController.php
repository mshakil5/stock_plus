<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\PurchaseHistory;
use App\Models\StockTransfer;
use App\Models\Type;
use App\Models\Vendor;
use App\Models\Branch;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class StockController extends Controller
{

    public function addstock(){
        
        $products = Product::orderby('id','DESC')->get();
        $branches = Branch::where('status', 1)->get();
        $producttypes = Type::all();

        //vendors are known as suppliers
        $vendors = Vendor::where('status', 1)->get();
        // dd($products);
        return view('admin.stock.StockreEntry', compact('products', 'branches', 'producttypes', 'vendors'));
    }

    public function filter_product(Request $request)
    {
        
        $products = Product::with('brand','category','size');
        return Datatables::of($products)
            ->addColumn('action', function ($product) {
                $btn = '<div class="table-actions">';
                if (Auth::user()) {
                    $btn .= "<span class='btn btn-success btn-sm addThisStock' id='addThisStock' pid='$product->id' pname='$product->productname'> <i class='fa fa-arrow-right'></i> </span>";
                }
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);

    }


    public function managestock(){

    //    $stocks = DB::table('stocks')
    //                 ->join('products', 'stocks.product_id', '=', 'products.id')
    //                 ->select('stocks.*', 'products.productname','products.selling_price','products.unit','products.location')
    //                 ->get();

        $stocks = DB::table('stocks')
                    ->join('products', 'stocks.product_id', '=', 'products.id')
                    ->select('stocks.id','stocks.branch_id','stocks.product_id','stocks.quantity', 'products.productname','products.selling_price','products.unit','products.location')
                    ->get();
       $branches = Branch::where('status', '=', 1)->get();
        return view('admin.stock.ManageStock', compact('stocks', 'branches'));
    }

    // push selected product to right bar
    public function pushProduct($id)
    {              
        return $pushedProducts = Product::where('id', $id)->with('Sizes')->first();
    }

    // get purchased products
    public function getOldPurchase($id)
    {
        return $oldPurchases = ProductDetail::with('Products')->select('*', DB::raw('(purchaseprice * qty) AS total'), DB::raw('DATE_FORMAT(created_at, "%d-%b-%Y (%h:%i:%s %p)") AS purchase_date'))->orderBy('created_at','DESC')->where('productid', $id)->get();
    }


    public function stockStore(Request $request)
    {
        if(empty($request->invoiceno)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Invoice/Transaction No\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->branch_id)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Branch\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->purchase_type)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Transaction Type\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->vendor_id)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Supplier\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->net_amount)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Product quantity, price, vat percentage field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if($request->purchase_type == "Cash"){
            if($request->due_amount > 0){
                $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Credit Purchase not accepted! Please Paid Full Amount</b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }
        }




            try{
                $purchase = new Purchase();
                $purchase->invoiceno = $request->invoiceno;
                $purchase->date = $request->date;
                $purchase->ref = $request->ref;
                $purchase->vat_reg = $request->vat_reg;
                $purchase->branch_id = $request->branch_id;
                $purchase->vendor_id = $request->vendor_id;
                $purchase->purchase_type = $request->purchase_type;
                $purchase->remarks = $request->remarks;
                $purchase->total_amount = $request->total_amount;
                $purchase->discount = $request->discount;
                $purchase->total_vat_amount = $request->total_vat_amount;
                $purchase->paid_amount = $request->paid_amount;
                $purchase->due_amount = $request->due_amount;
                $purchase->net_amount = $request->net_amount;
                $purchase->created_by= Auth::user()->id;
            if ($purchase->save()) {
                
                foreach($request->input('product_id') as $key => $value)
                    {
                        
                        $pid = $request->get('product_id')[$key];
                        $qty = $request->get('quantity')[$key];

                        $total_vat = $qty * ($request->get('unit_price')[$key] * $request->get('vat_percent')[$key]/100);
                        $total_amount_with_vat = ($request->get('unit_price')[$key] * $qty) + $total_vat;

                        $purchasehistry = new PurchaseHistory();
                        $purchasehistry->branch_id = $request->branch_id;
                        $purchasehistry->purchase_id = $purchase->id;
                        $purchasehistry->product_id = $pid;
                        $purchasehistry->quantity = $qty;
                        $purchasehistry->purchase_price = $request->get('unit_price')[$key];
                        $purchasehistry->vat_percent = $request->get('vat_percent')[$key];
                        $purchasehistry->vat_amount_per_unit = $request->get('unit_price')[$key] * $request->get('vat_percent')[$key]/100;
                        $purchasehistry->total_vat = $total_vat;
                        $purchasehistry->total_amount_per_unit = $request->get('unit_price')[$key] * $qty;
                        $purchasehistry->total_amount_with_vat = $request->get('unit_price')[$key] * $qty +  $total_vat;
                        $purchasehistry->created_by = Auth::user()->id;
                        $purchasehistry->save();

                        // selling price update
                        $pselprice = Product::find($pid);
                        $pselprice->selling_price = $request->get('unit_price')[$key] + ($request->get('unit_price')[$key] * 5/100);
                        $pselprice->vat_amount = $request->get('unit_price')[$key] * 5/100;
                        $pselprice->save();
                        // selling price update end

                        $stockcount = Stock::where('product_id','=', $pid)->where('branch_id','=', $request->branch_id)->count();
                        if ($stockcount == 1) {
                            $stock = Stock::where('product_id','=', $pid)->where('branch_id','=', $request->branch_id)->first();
                            $upstock = Stock::find($stock->id);
                            $upstock->quantity = $upstock->quantity + $qty;
                            $upstock->save();
                            
                        } else {
                            $newstock = new Stock();
                            $newstock->branch_id = $request->branch_id;
                            $newstock->product_id = $pid;
                            $newstock->quantity = $qty;
                            $newstock->exp_date = $request->exp_date;
                            $newstock->created_by = Auth::user()->id;
                            $newstock->save();
                        }



                    }

                $message ="<div class='alert alert-success' style='color:white'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Product Purchase Successfully.</b></div>";
                return response()->json(['status'=> 300,'message'=>$message]);
            }

            }catch (\Exception $e){
                return response()->json(['status'=> 303,'message'=>'Server Error!!']);

            }

    }

    public function editpurchase($id)
    {
        $purchase = Purchase::with('purchasehistory')->where('id',$id)->first();
        return view('admin.stock.purchaseedit', compact('purchase'));

    }

    public function purchaseReturn($id)
    {
        $purchase = Purchase::with('purchasehistory')->where('id',$id)->first();
        // dd($purchase);
        return view('admin.stock.purchasereturn', compact('purchase'));

    }

    public function purchaseUpdate(Request $request)
    {
        $pruchasehisIDs = $request->purchase_his_id;
        if(empty($request->invoiceno)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Invoice/Transaction No\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->branch_id)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Branch\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->purchase_type)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Transaction Type\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->vendor_id)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Supplier\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->net_amount)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Product quantity, price, vat percentage field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

            try{
                $purchase = Purchase::find($request->purchase_id);
                $purchase->invoiceno = $request->invoiceno;
                $purchase->date = $request->date;
                $purchase->ref = $request->ref;
                $purchase->vat_reg = $request->vat_reg;
                $purchase->branch_id = $request->branch_id;
                $purchase->vendor_id = $request->vendor_id;
                $purchase->purchase_type = $request->purchase_type;
                $purchase->remarks = $request->remarks;
                $purchase->total_amount = $request->total_amount;
                $purchase->discount = $request->discount;
                $purchase->total_vat_amount = $request->total_vat_amount;
                $purchase->paid_amount = $request->paid_amount;
                $purchase->due_amount = $request->due_amount;
                $purchase->net_amount = $request->net_amount;
                $purchase->created_by= Auth::user()->id;
            if ($purchase->save()) {

                // $collection = PurchaseHistory::where('purchase_id', $request->purchase_id)->get(['id']);
                // PurchaseHistory::destroy($collection->toArray());
                
                foreach($request->input('product_id') as $key => $value)
                    {
                        
                        $pid = $request->get('product_id')[$key];
                        $qty = $request->get('quantity')[$key];
                        $total_vat = $qty * ($request->get('unit_price')[$key] * $request->get('vat_percent')[$key]/100);
                        $total_amount_with_vat = ($request->get('unit_price')[$key] * $qty) + $total_vat;


                        if (isset($pruchasehisIDs[$key])) {
                            $purchasehistry = PurchaseHistory::findOrFail($pruchasehisIDs[$key]);

                                // stock update
                                $stock = Stock::where('product_id','=', $pid)->where('branch_id','=', $request->branch_id)->first();
                                $upstock = Stock::find($stock->id);
                                $upstock->quantity = $upstock->quantity + $qty - $purchasehistry->quantity;
                                $upstock->updated_by = Auth::user()->id;
                                $upstock->save();
                                // stock update end

                            $purchasehistry->branch_id = $request->branch_id;
                            $purchasehistry->purchase_id = $purchase->id;
                            $purchasehistry->product_id = $pid;
                            $purchasehistry->quantity = $qty;
                            $purchasehistry->purchase_price = $request->get('unit_price')[$key];
                            $purchasehistry->vat_percent = $request->get('vat_percent')[$key];
                            $purchasehistry->vat_amount_per_unit = $request->get('unit_price')[$key] * $request->get('vat_percent')[$key]/100;
                            $purchasehistry->total_vat = $total_vat;
                            $purchasehistry->total_amount_per_unit = $request->get('unit_price')[$key] * $qty;
                            $purchasehistry->total_amount_with_vat = $request->get('unit_price')[$key] * $qty +  $total_vat;
                            $purchasehistry->updated_by = Auth::user()->id;
                            $purchasehistry->save();

                        } else {
                            
                            $purchasehistry = new PurchaseHistory();
                            $purchasehistry->branch_id = $request->branch_id;
                            $purchasehistry->purchase_id = $purchase->id;
                            $purchasehistry->product_id = $pid;
                            $purchasehistry->quantity = $qty;
                            $purchasehistry->purchase_price = $request->get('unit_price')[$key];
                            $purchasehistry->vat_percent = $request->get('vat_percent')[$key];
                            $purchasehistry->vat_amount_per_unit = $request->get('unit_price')[$key] * $request->get('vat_percent')[$key]/100;
                            $purchasehistry->total_vat = $total_vat;
                            $purchasehistry->total_amount_per_unit = $request->get('unit_price')[$key] * $qty;
                            $purchasehistry->total_amount_with_vat = $request->get('unit_price')[$key] * $qty +  $total_vat;
                            $purchasehistry->created_by = Auth::user()->id;
                            $purchasehistry->save();

                            // stock update
                            $stockcount = Stock::where('product_id','=', $pid)->where('branch_id','=', $request->branch_id)->count();
                            if ($stockcount == 1) {
                                $stock = Stock::where('product_id','=', $pid)->where('branch_id','=', $request->branch_id)->first();
                                $upstock = Stock::find($stock->id);
                                $upstock->quantity = $upstock->quantity + $qty;
                                $upstock->updated_by = Auth::user()->id;
                                $upstock->save();
                                
                            } else {
                                $newstock = new Stock();
                                $newstock->branch_id = $request->branch_id;
                                $newstock->product_id = $pid;
                                $newstock->quantity = $qty;
                                $newstock->created_by = Auth::user()->id;
                                $newstock->save();
                            }
                            // stock update end
                        }
                        
                        // selling price update
                        $pselprice = Product::find($pid);
                        $pselprice->selling_price = $request->get('unit_price')[$key] + ($request->get('unit_price')[$key] * 5/100);
                        $pselprice->vat_amount = $request->get('unit_price')[$key] * 5/100;
                        $pselprice->save();
                        // selling price update end

                    }

                $message ="<div class='alert alert-success' style='color:white'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Product Purchase Successfully.</b></div>";
                return response()->json(['status'=> 300,'message'=>$message]);
            }

            }catch (\Exception $e){
                return response()->json(['status'=> 303,'message'=>'Server Error!!']);

            }

    }

    // purchase return start
    public function purchaseReturnStore(Request $request)
    {

        if(empty($request->date)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Date\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->reason)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Reason\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        

            try{
                
                foreach($request->input('product_id') as $key => $value)
                    {
                        
                        $pid = $request->get('product_id')[$key];
                        $qty = $request->get('quantity')[$key];
                        $purchase_history_id  = $request->get('purchase_his_id')[$key];


                        $purchasereturn = new PurchaseReturn;
                        $purchasereturn->date = $request->date;
                        $purchasereturn->product_id = $pid;
                        $purchasereturn->branch_id = $request->branch_id;
                        $purchasereturn->vendor_id = $request->vendor_id;
                        $purchasereturn->returnqty = $qty;
                        $purchasereturn->reason = $request->reason;
                        $purchasereturn->purchase_history_id = $purchase_history_id;
                        $purchasereturn->created_by = Auth::user()->id;
                        $purchasereturn->save();

                        // stock update
                        $stock = Stock::where('product_id','=', $pid)->where('branch_id','=', $request->branch_id)->first();
                        $upstock = Stock::find($stock->id);
                        $upstock->quantity = $upstock->quantity - $qty;
                        $upstock->updated_by = Auth::user()->id;
                        $upstock->save();
                        // stock update end


                    
                    }
                    $message ="<div class='alert alert-success' style='color:white'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Product Purchase Return Successfully.</b></div>";
                    
                    return response()->json(['status'=> 300,'message'=>$message]);

            }catch (\Exception $e){
                return response()->json(['status'=> 303,'message'=>'Server Error!!']);

            }

    }
    // purchase return end





    public function stockStore2(Request $request)
    {

        if(empty($request->product_id)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Product\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->branch)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Branch\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->quantity)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Quantity\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->purchasePrice)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Purchase Price\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }



            try{
                $purchasehistry = new PurchaseHistory();
                $purchasehistry->branch_id = $request->branch;
                $purchasehistry->product_id = $request->product_id;
                $purchasehistry->type = $request->type;
                $purchasehistry->quantity = $request->quantity;
                $purchasehistry->purchase_price = $request->purchasePrice;
                $purchasehistry->total_cost = $request->totalPrice;
                $purchasehistry->exp_date = $request->exp_date;
                $purchasehistry->created_by= Auth::user()->id;
            if ($purchasehistry->save()) {

                $productupdate = Product::find($request->product_id);
                $productupdate->selling_price = $request->sellingprice;
                $productupdate->selling_price_with_vat = $request->sellingprice + ($request->sellingprice * $productupdate->vat_percent/100);
                $productupdate->save();

                $stock = Stock::where('product_id','=', $request->product_id)->where('branch_id','=', $request->branch)->first();

                if ($stock) {
                    $upstock = Stock::find($stock->id);
                    $upstock->quantity = $upstock->quantity + $request->quantity;
                    $upstock->save();
                    
                } else {
                    $newstock = new Stock();
                    $newstock->branch_id = $request->branch;
                    $newstock->product_id = $request->product_id;
                    $newstock->quantity = $request->quantity;
                    $newstock->exp_date = $request->exp_date;
                    $newstock->created_by = Auth::user()->id;
                    $newstock->save();
                }

                $message ="<div class='alert alert-success' style='color:white'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Product Purchase Successfully.</b></div>";
                return response()->json(['status'=> 300,'message'=>$message]);
            }

            }catch (\Exception $e){
                return response()->json(['status'=> 303,'message'=>'Server Error!!']);

            }

    }

    public function stockHistory($id)
    {
        
        $histories = DB::table('purchase_histories')
                    ->join('products', 'purchase_histories.product_id', '=', 'products.id')
                    ->join('branches', 'purchase_histories.branch_id', '=', 'branches.id')
                    ->select('purchase_histories.*', 'products.productname', 'branches.name as branchname')
                    ->where('purchase_histories.product_id','=', $id)
                    ->orderby('purchase_histories.id','DESC')
                    ->get();
        
        return response()->json($histories);
    }

    public function productPurchaseHistory()
    {
        
        $histories = DB::table('purchase_histories')
                    ->join('products', 'purchase_histories.product_id', '=', 'products.id')
                    ->select('purchase_histories.*', 'products.productname', 'products.unit', 'products.location', 'products.selling_price')
                    ->get();
        $purchase = Purchase::with('purchasehistory')->orderby('id','DESC')->get();

                    // dd($histories);
        return view('admin.stock.purchasehistory', compact('histories','purchase'));
    }

    public function stock_transfer_history()
    {
        
        $histories = DB::table('stock_transfers')
                    ->join('products', 'stock_transfers.product_id', '=', 'products.id')
                    ->select('stock_transfers.*', 'products.productname', 'products.part_no', 'products.unit', 'products.location', 'products.selling_price')
                    ->get();

                    // dd($histories);
        return view('admin.stock.transferhistory', compact('histories'));
    }


    public function stockUpdate(Request $request)
    {

        if(empty($request->purchasePrice)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Purchase Price\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->history_id)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"history_id\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->product_id)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Product\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->branch)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Branch\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->quantity)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Quantity\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

            try{
                $stock = PurchaseHistory::find($request->history_id);
                $oldstockhistry = $stock->quantity;
                $stock->branch_id = $request->branch;
                $stock->product_id = $request->product_id;
                // $stock->barcode = $request->name;
                $stock->quantity = $request->quantity;
                $stock->purchase_price = $request->purchasePrice;
                $stock->total_cost = $request->totalPrice;
                $stock->exp_date = $request->exp_date;
                $stock->created_by= Auth::user()->id;

            if ($stock->save()) {

                $product = Stock::where('product_id','=', $request->product_id)->where('branch_id','=', $request->branch)->first();

                if ($product) {
                    $upstock = Stock::find($product->id);
                    $upstock->quantity = $upstock->quantity + $request->quantity - $oldstockhistry;
                    $upstock->save();
                    
                }

            }

            $message ="<div class='alert alert-success' style='color:white'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Product Stock Update Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);

            }catch (\Exception $e){
                return response()->json(['status'=> 303,'message'=>'Server Error!!']);

            }
    }

    public function saveStockReturn(Request $request)
    {
    	$productid = $request->data['productid'];
        $phistryid = $request->data['phistryid'];
    	$branchid = $request->data['branchid'];
    	$purchaseqty = $request->data['purchaseqty'];
    	$returnQty = $request->data['returnQty'];
    	$reason = $request->data['reason'];

    	//transferring product
    	$return = new PurchaseReturn();
    	$return->product_id = $productid;
    	$return->returnqty = $returnQty;
    	$return->branch_id = $branchid;
    	$return->purchase_history_id = $phistryid;
    	$return->reason = $reason;
    	$return->status = "0";
    	$return->created_by = Auth::user()->id;
    	$return->save();

        $stock = Stock::where('branch_id',$branchid)->where('product_id',$productid)->first();
        if ($stock) {
            $updateoldostock = Stock::find($stock->id);
            $updateoldostock->quantity = $updateoldostock->quantity - $returnQty;
            $updateoldostock->save();
        }

        $message ="<div class='alert alert-success' style='color:white'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Stock Return Successfully.</b></div>";
        return response()->json(['status'=> 300,'message'=>$message]);
    }


    public function stockReturnHistory()
    {
        
        $histories = PurchaseReturn::orderby('id','DESC')->get();

                    // dd($histories);
        return view('admin.stock.stockreturnhistory', compact('histories'));
    }


}
