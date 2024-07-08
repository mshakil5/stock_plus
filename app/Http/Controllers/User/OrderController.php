<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\SalesReturn;
use App\Models\SalesReturnDetail;
use DB;
use App\Models\Customer;
use App\Models\Payment;
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
    public function getproduct(Request $request)
        {

            $productDtl = Product::with('alternativeproduct','replacement')->where('id', '=', $request->product)->first();

            $chkstock = Stock::where('product_id',$request->product)->where('branch_id','=', Auth::user()->branch_id)->first();

            // $stocks = Stock::where('product_id',$request->product)->where('branch_id','!=', Auth::user()->branch_id)->where('quantity','>', 0)->get();

            $stocks = DB::table('stocks')
                    ->join('products', 'stocks.product_id', '=', 'products.id')
                    ->join('branches', 'stocks.branch_id', '=', 'branches.id')
                    ->select('stocks.*', 'products.productname', 'branches.name as branchname')
                    ->where('stocks.product_id',$request->product)
                    ->get();

            $alternatives = DB::table('alternative_products')
                    ->join('products', 'alternative_products.alternative_product_id', '=', 'products.id')
                    ->select('alternative_products.*', 'products.productname', 'products.part_no', 'products.selling_price', 'products.location')
                    ->where('alternative_products.product_id',$request->product)
                    ->get();
            $replacements = DB::table('replacements')
                    ->join('products', 'replacements.product_id', '=', 'products.id')
                    ->select('replacements.*', 'products.productname', 'products.part_no', 'products.selling_price')
                    ->where('replacements.product_id',$request->product)
                    ->get();

            if(empty($productDtl)){
                return response()->json(['status'=> 303,'message'=>"No data found"]);
            }else{
                if (empty($chkstock)) {
                    return response()->json(['status'=> 300,'productname'=>$productDtl->productname,'product_id'=>$productDtl->id,'location'=>$productDtl->location, 'sellingprice'=>$productDtl->selling_price,'selling_price_with_vat'=>$productDtl->selling_price_with_vat, 'part_no'=>$productDtl->part_no, 'vat_percent'=>$productDtl->vat_percent, 'vat_amount'=>$productDtl->vat_amount,'stocks'=>$stocks,'chkstock'=>'0','alternatives'=>$alternatives,'replacements'=>$replacements]);
                } else {
                    return response()->json(['status'=> 300,'productname'=>$productDtl->productname,'product_id'=>$productDtl->id, 'location'=>$productDtl->location,'sellingprice'=>$productDtl->selling_price,'selling_price_with_vat'=>$productDtl->selling_price_with_vat, 'part_no'=>$productDtl->part_no, 'vat_percent'=>$productDtl->vat_percent, 'vat_amount'=>$productDtl->vat_amount,'stocks'=>$stocks,'chkstock'=>$chkstock->quantity,'alternatives'=>$alternatives,'replacements'=>$replacements]);
                }
                
                
            }

        }

    public function orderStore(Request $request){

        $productIDs = $request->input('product_id');
        $paymentmethodIDs = $request->input('paymentmethod');
        if($productIDs == "" ){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Product field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        
        if(empty($request->grand_total)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Product\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if ($request->salestype == "Cash" && $request->customer_id == "") {
            if($request->due > 0){
                $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Paid Full Amount..!</b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }
        }

        if ($request->salestype == "Credit") {
            if(empty($request->customer_id)){
                $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Customer\" field..!</b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }
        }

        // if(empty($request->ref)){
        //     $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Reference\" field..!</b></div>";
        //     return response()->json(['status'=> 303,'message'=>$message]);
        //     exit();
        // }
        
        // if(empty($request->customer_paid)){
        //     $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Customer Paid\" field..!</b></div>";
        //     return response()->json(['status'=> 303,'message'=>$message]);
        //     exit();
        // }

        
        

        if($request->order_id == ""){
            $qn_no = "0";
        }else{
            $qn_no = $request->order_id;
        }

        if($request->delivery_note_id == ""){
            $dn_no = "0";
        }else{
            $dn_no = $request->delivery_note_id;
        }

        if ($request->customer_id != "") {
            $customer = Customer::find($request->customer_id);
            if(($customer->amount + $request->due) > $customer->limitation){
                $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Customer Credit Limitation Over..!Please Paid Full Amount..!</b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }
            $customer->name = $request->customername;
            $customer->address = $request->customeraddress;
            $customer->vat_number = $request->customervat;
            $customer->amount = $customer->amount + $request->due;
            $customer->vehicleno = $request->customervehicleno;
            $customer->updated_by = Auth::user()->id;
            $customer->save();

            $customerdtl = Customer::where('id', '=', $request->customer_id)->first();
        }
        // new code
        $order = new Order();
        $order->invoiceno = date('Ymd-his');
        $order->orderdate = $request->orderdate;
        $order->salestype = $request->salestype;
        if(empty($request->customer_id)){
            $order->customer_id = 1;
        }else{
            $order->customer_id = $request->customer_id;
        }
        $order->branch_id = Auth::user()->branch_id;
        $order->ref = $request->ref;
        $order->qn_no = $qn_no;
        $order->dn_no = $dn_no;
        $order->due_date = $request->due_date;
        $order->vatpercentage = $request->vat_percent;
        $order->vatamount = $request->vat_total;
        $order->discount_amount = $request->discount_amount;
        $order->grand_total = $request->grand_total;
        $order->net_total = $request->net_total;
        $order->customer_paid = $request->customer_paid;
        $order->due = $request->due;
        $order->partnoshow = $request->partnoshow;
        $order->sales_status = "1";
        $order->return_amount = $request->return_amount;
        $order->created_by = Auth::user()->id;
        $order->status = 0;
        if($order->save()){

            foreach($request->input('product_id') as $key => $value)
            {
                $orderDtl = new OrderDetail();
                $orderDtl->invoiceno = $order->invoiceno;
                $orderDtl->order_id = $order->id;
                $orderDtl->product_id = $request->get('product_id')[$key];
                $orderDtl->quantity = $request->get('quantity')[$key];
                $orderDtl->sellingprice = $request->get('sellingprice')[$key];
                $orderDtl->total_amount = $request->get('quantity')[$key] * $request->get('sellingprice')[$key];
                $orderDtl->created_by = Auth::user()->id;
                $orderDtl->save();
                $stockid = Stock::where('product_id','=',$request->get('product_id')[$key])->where('branch_id','=', Auth::user()->branch_id)->first();
                if($request->delivery_note_id == ""){
                    if (isset($stockid->id)) {
                        $dstock = Stock::find($stockid->id);
                        $dstock->quantity = $dstock->quantity - $request->get('quantity')[$key];
                        $dstock->save();
                    } else {
                        $newstock = new Stock();
                        $newstock->branch_id = Auth::user()->branch_id;
                        $newstock->product_id = $request->get('product_id')[$key];
                        $newstock->quantity = 0 - $request->get('quantity')[$key];
                        $newstock->created_by = Auth::user()->id;
                        $newstock->save();
                    }
                }else{

                    $oldDNqty = OrderDetail::where('order_id', $request->delivery_note_id)->where('product_id',$request->get('product_id')[$key])->first();  
                    if (isset($oldDNqty)) {
                        $amend_stock = $oldDNqty->quantity - $request->get('quantity')[$key];
                        $dstock = Stock::find($stockid->id);
                        $dstock->quantity = $dstock->quantity + $amend_stock;
                        $dstock->save();
                    }else{
                        if (isset($stockid->id)) {
                            $dstock = Stock::find($stockid->id);
                            $dstock->quantity = $dstock->quantity - $request->get('quantity')[$key];
                            $dstock->save();
                        } else {
                            $newstock = new Stock();
                            $newstock->branch_id = Auth::user()->branch_id;
                            $newstock->product_id = $request->get('product_id')[$key];
                            $newstock->quantity = 0 - $request->get('quantity')[$key];
                            $newstock->created_by = Auth::user()->id;
                            $newstock->save();
                        }
                    }
                }
            }

            if($paymentmethodIDs){
                foreach($request->input('paymentmethod') as $key => $value){
                    $payment = new Payment();
                    $payment->order_id = $order->id;
                    $payment->payment_method_id  = $request->get('paymentmethod')[$key];
                    $payment->payment_amount = $request->get('payment_amount')[$key];
                    $payment->card_number = $request->get('card_number')[$key];
                    $payment->card_holder_name = $request->get('card_holder_name')[$key];
                    $payment->comment = $request->get('comment')[$key];
                    $payment->created_by = Auth::user()->id;
                    $payment->save();
                }
            }
            
            

            //stores the pdf for invoice
            // $pdf = PDF::loadView('invoices.customer_invoice', compact('order','customerdtl'));
            // $output = $pdf->output();
            // file_put_contents(public_path().'/invoices/'.'Order#'.$order->invoiceno.'.pdf', $output);


            // email start
                // $contactmail = ContactMail::where('id', 1)->first()->name;
                $contactmail = 'matirmanush25@gmail.com';
                $array['subject'] = 'New order';
                $array['from'] = 'info@tevini.co.uk';
                $array['cc'] = 'kazimuhammadullah@gmail.com';
                $array['name'] = 'Order#'.$order->invoiceno.'.pdf';
                $email = 'kmushakil22@gmail.com';
                $array['file'] =  public_path().'/invoices/Order#'.$order->invoiceno.'.pdf';
                $array['file_name'] = 'Order#'.$order->invoiceno.'.pdf';
                $array['subjectsingle'] = 'Order Placed - '.$order->invoiceno;
                // Mail::to($email)
                // ->cc($contactmail)
                // ->queue(new InvoiceEmailManager($array));
            // end
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Thank you for this order.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message,'id'=>$order->id,'paymentmethodIDs'=>$paymentmethodIDs]);
        }

    }

    public function deliveryNoteStore(Request $request){

        $productIDs = $request->input('product_id');
        if($productIDs == "" ){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Product field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->customer_id)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Customer\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        
        if(empty($request->grand_total)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Product\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        // if(empty($request->ref)){
        //     $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Reference\" field..!</b></div>";
        //     return response()->json(['status'=> 303,'message'=>$message]);
        //     exit();
        // }

        if(empty($request->vat_percent)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Vat Percent\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if($request->order_id == ""){
            $qn_no = 0;
        }else{
            $qn_no = $request->order_id;
        }

        
        if ($request->customer_id != "") {
            $customer = Customer::find($request->customer_id);
            $customer->name = $request->customername;
            $customer->address = $request->customeraddress;
            $customer->vat_number = $request->customervat;
            $customer->vehicleno = $request->customervehicleno;
            $customer->updated_by = Auth::user()->id;
            $customer->save();

            $customerdtl = Customer::where('id', '=', $request->customer_id)->first();
        }

        // new code
        $order = new Order();
        $order->invoiceno = date('Ymd-his');
        $order->orderdate = $request->orderdate;
        $order->qn_no = $qn_no;
        $order->customer_id = $request->customer_id;
        $order->branch_id = Auth::user()->branch_id;
        $order->ref = $request->ref;
        $order->due_date = $request->due_date;
        $order->vatpercentage = $request->vat_percent;
        $order->vatamount = $request->vat_total;
        $order->discount_amount = $request->discount_amount;
        $order->grand_total = $request->grand_total;
        $order->net_total = $request->net_total;
        $order->customer_paid = $request->customer_paid;
        $order->due = $request->due;
        $order->delivery_note = "1";
        $order->partnoshow = $request->partnoshow;
        $order->return_amount = $request->return_amount;
        $order->created_by = Auth::user()->id;
        $order->status = 0;
        if($order->save()){

            foreach($request->input('product_id') as $key => $value)
            {
                $orderDtl = new OrderDetail();
                $orderDtl->invoiceno = $order->invoiceno;
                $orderDtl->order_id = $order->id;
                $orderDtl->product_id = $request->get('product_id')[$key];
                $orderDtl->quantity = $request->get('quantity')[$key];
                $orderDtl->sellingprice = $request->get('sellingprice')[$key];
                $orderDtl->total_amount = $request->get('quantity')[$key] * $request->get('sellingprice')[$key];
                $orderDtl->created_by = Auth::user()->id;
                $orderDtl->save();

                $stockid = Stock::where('product_id','=',$request->get('product_id')[$key])->where('branch_id','=', Auth::user()->branch_id)->first();
                if (isset($stockid->id)) {
                    $dstock = Stock::find($stockid->id);
                    $dstock->quantity = $dstock->quantity - $request->get('quantity')[$key];
                    $dstock->save();
                } else {
                    $newstock = new Stock();
                    $newstock->branch_id = Auth::user()->branch_id;
                    $newstock->product_id = $request->get('product_id')[$key];
                    $newstock->quantity = 0 - $request->get('quantity')[$key];
                    $newstock->created_by = Auth::user()->id;
                    $newstock->save();
                }


            }
            //stores the pdf for invoice
            // $pdf = PDF::loadView('invoices.customer_invoice', compact('order','customerdtl'));
            // $output = $pdf->output();
            // file_put_contents(public_path().'/invoices/'.'Order#'.$order->invoiceno.'.pdf', $output);


            // email start
                // $contactmail = ContactMail::where('id', 1)->first()->name;
                $contactmail = 'matirmanush25@gmail.com';
                $array['subject'] = 'New order';
                $array['from'] = 'info@tevini.co.uk';
                $array['cc'] = 'kazimuhammadullah@gmail.com';
                $array['name'] = 'Order#'.$order->invoiceno.'.pdf';
                $email = 'kmushakil22@gmail.com';
                $array['file'] =  public_path().'/invoices/Order#'.$order->invoiceno.'.pdf';
                $array['file_name'] = 'Order#'.$order->invoiceno.'.pdf';
                $array['subjectsingle'] = 'Order Placed - '.$order->invoiceno;
                // Mail::to($email)
                // ->cc($contactmail)
                // ->queue(new InvoiceEmailManager($array));
            // end
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Thank you for this order.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message,'id'=>$order->id]);
        }


    }

    public function quotationNoteStore(Request $request){

        $productIDs = $request->input('product_id');
        if($productIDs == "" ){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Product field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->customer_id)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Customer\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->grand_total)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Product\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        // if(empty($request->ref)){
        //     $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Reference\" field..!</b></div>";
        //     return response()->json(['status'=> 303,'message'=>$message]);
        //     exit();
        // }

        if(empty($request->vat_percent)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Vat Percent\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if ($request->customer_id != "") {
            $customer = Customer::find($request->customer_id);
            $customer->name = $request->customername;
            $customer->address = $request->customeraddress;
            $customer->vat_number = $request->customervat;
            $customer->vehicleno = $request->customervehicleno;
            $customer->updated_by = Auth::user()->id;
            $customer->save();
            $customerdtl = Customer::where('id', '=', $request->customer_id)->first();
        }

        // new code
        $order = new Order();
        $order->invoiceno = date('Ymd-his');
        $order->orderdate = $request->orderdate;
        $order->customer_id = $request->customer_id;
        $order->branch_id = Auth::user()->branch_id;
        $order->ref = $request->ref;
        $order->due_date = $request->due_date;
        $order->vatpercentage = $request->vat_percent;
        $order->vatamount = $request->vat_total;
        $order->discount_amount = $request->discount_amount;
        $order->grand_total = $request->grand_total;
        $order->net_total = $request->net_total;
        $order->customer_paid = $request->customer_paid;
        $order->due = $request->due;
        $order->quotation = "1";
        $order->partnoshow = $request->partnoshow;
        $order->return_amount = $request->return_amount;
        $order->created_by = Auth::user()->id;
        $order->status = 0;
        if($order->save()){

            foreach($request->input('product_id') as $key => $value)
            {
                $orderDtl = new OrderDetail();
                $orderDtl->invoiceno = $order->invoiceno;
                $orderDtl->order_id = $order->id;
                $orderDtl->product_id = $request->get('product_id')[$key];
                $orderDtl->quantity = $request->get('quantity')[$key];
                $orderDtl->sellingprice = $request->get('sellingprice')[$key];
                $orderDtl->total_amount = $request->get('quantity')[$key] * $request->get('sellingprice')[$key];
                $orderDtl->created_by = Auth::user()->id;
                $orderDtl->save();

            }
            //stores the pdf for invoice
            // $pdf = PDF::loadView('invoices.customer_invoice', compact('order','customerdtl'));
            // $output = $pdf->output();
            // file_put_contents(public_path().'/invoices/'.'Order#'.$order->invoiceno.'.pdf', $output);

            // end
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Thank you for this order.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message,'id'=>$order->id]);
        }


    }

    // sales edit start here
    public function salesEdit($id)
    {
        $invoices  = Order::with('orderdetails','customer')->where('id', $id)->first();
        // dd($invoices);
        return view('user.invoice.editsales', compact('invoices'));
    }

    public function quotationEdit($id)
    {
        $invoices  = Order::with('orderdetails','customer')->where('id', $id)->first();
        // dd($invoices);
        return view('user.invoice.editquotation', compact('invoices'));
    }

    public function deliveryNoteEdit($id)
    {
        $invoices  = Order::with('orderdetails','customer')->where('id', $id)->first();
        // dd($invoices);
        return view('user.invoice.editdeliverynote', compact('invoices'));
    }

    public function orderUpdate(Request $request){

        $orderdtlIDs = $request->orderdtl_id;

        $productIDs = $request->input('product_id');
        if($productIDs == "" ){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Product field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        
        if(empty($request->grand_total)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Product\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if ($request->salestype == "Credit") {
            if(empty($request->customer_id)){
                $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Customer\" field..!</b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }
        }

        // if(empty($request->ref)){
        //     $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Reference\" field..!</b></div>";
        //     return response()->json(['status'=> 303,'message'=>$message]);
        //     exit();
        // }
        
        if(empty($request->customer_paid)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Customer Paid\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        
        if ($request->salestype == "Cash" && $request->customer_id == "") {
            if($request->due > 0){
                $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Paid Full Amount..!</b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }
        }

        $customer = Customer::find($request->customer_id);
        if(($customer->amount + $request->due) > $customer->limitation){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Customer Credit Limitation Over..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if ($request->customer_id != "") {
            $customer = Customer::find($request->customer_id);
            if(($customer->amount + $request->due) > $customer->limitation){
                $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Customer Credit Limitation Over..!</b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }
            $customer->name = $request->customername;
            $customer->address = $request->customeraddress;
            $customer->vat_number = $request->customervat;
            $customer->amount = $customer->amount + $request->due;
            $customer->vehicleno = $request->customervehicleno;
            $customer->updated_by = Auth::user()->id;
            $customer->save();

            $customerdtl = Customer::where('id', '=', $request->customer_id)->first();
        }
        // new code
        $order = Order::find($request->order_id);
        $order->orderdate = $request->orderdate;
        if(empty($request->customer_id)){
            $order->customer_id = 1;
        }else{
            $order->customer_id = $request->customer_id;
        }
        $order->branch_id = Auth::user()->branch_id;
        $order->ref = $request->ref;
        $order->due_date = $request->due_date;
        $order->vatpercentage = $request->vat_percent;
        $order->vatamount = $request->vat_total;
        $order->discount_amount = $request->discount_amount;
        $order->grand_total = $request->grand_total;
        $order->net_total = $request->net_total;
        $order->customer_paid = $request->customer_paid;
        $order->due = $request->due;
        $order->partnoshow = $request->partnoshow;
        $order->sales_status = "1";
        $order->return_amount = $request->return_amount;
        $order->updated_by = Auth::user()->id;
        if($order->save()){
            

            // $collection = OrderDetail::where('order_id', $request->order_id)->get(['id']);
            // OrderDetail::destroy($collection->toArray());


            foreach($request->input('product_id') as $key => $value)
            {
                $pid = $request->get('product_id')[$key];
                $qty = $request->get('quantity')[$key];
                $total_amount = ($request->get('sellingprice')[$key] * $qty);

                if (isset($orderdtlIDs[$key])) {

                    $orderDtl = OrderDetail::findOrFail($orderdtlIDs[$key]);

                        // stock update
                        $stock = Stock::where('product_id','=', $pid)->where('branch_id','=', Auth::user()->branch_id)->first();
                        $upstock = Stock::find($stock->id);
                        $upstock->quantity = $upstock->quantity - $qty + $orderDtl->quantity;
                        $upstock->updated_by = Auth::user()->id;
                        $upstock->save();
                        // stock update end
                    
                    $orderDtl->invoiceno = $order->invoiceno;
                    $orderDtl->order_id = $order->id;
                    $orderDtl->product_id = $pid;
                    $orderDtl->quantity = $qty;
                    $orderDtl->sellingprice = $request->get('sellingprice')[$key];
                    $orderDtl->total_amount = $request->get('sellingprice')[$key] * $qty;
                    $orderDtl->updated_by = Auth::user()->id;
                    $orderDtl->save();

                } else {

                    $orderDtl = new OrderDetail();
                    $orderDtl->invoiceno = $order->invoiceno;
                    $orderDtl->order_id = $order->id;
                    $orderDtl->product_id = $request->get('product_id')[$key];
                    $orderDtl->quantity = $request->get('quantity')[$key];
                    $orderDtl->sellingprice = $request->get('sellingprice')[$key];
                    $orderDtl->total_amount = $request->get('total')[$key];
                    $orderDtl->created_by = Auth::user()->id;
                    $orderDtl->save();

                    $stockid = Stock::where('product_id','=',$request->get('product_id')[$key])->where('branch_id','=', Auth::user()->branch_id)->first();
                    if (isset($stockid->id)) {
                        $dstock = Stock::find($stockid->id);
                        $dstock->quantity = $dstock->quantity - $request->get('quantity')[$key];
                        $dstock->save();
                    } else {
                        $newstock = new Stock();
                        $newstock->branch_id = Auth::user()->branch_id;
                        $newstock->product_id = $request->get('product_id')[$key];
                        $newstock->quantity = 0 - $request->get('quantity')[$key];
                        $newstock->created_by = Auth::user()->id;
                        $newstock->save();
                    }

                }
                
            }
            //stores the pdf for invoice

            $pdf_path = public_path('invoices').'/Order#'.$order->invoiceno.'.pdf';
            // if (isset($pdf_path)) {
            //     unlink($pdf_path);
            // }
            

            // $pdf = PDF::loadView('invoices.customer_invoice', compact('order','customerdtl'));
            // $output = $pdf->output();
            // file_put_contents(public_path().'/invoices/'.'Order#'.$order->invoiceno.'.pdf', $output);

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Order updated successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message,'id'=>$order->id]);
        }

    }

    // sales edit end here

    // quotation update here
    public function quotationUpdate(Request $request){

        $productIDs = $request->input('product_id');
        if($productIDs == "" ){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Product field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->customer_id)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Customer\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->grand_total)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Product\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        // if(empty($request->ref)){
        //     $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Reference\" field..!</b></div>";
        //     return response()->json(['status'=> 303,'message'=>$message]);
        //     exit();
        // }

        if(empty($request->vat_percent)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Vat Percent\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if ($request->customer_id != "") {
            $customer = Customer::find($request->customer_id);
            $customer->name = $request->customername;
            $customer->address = $request->customeraddress;
            $customer->vat_number = $request->customervat;
            $customer->vehicleno = $request->customervehicleno;
            $customer->updated_by = Auth::user()->id;
            $customer->save();

            $customerdtl = Customer::where('id', '=', $request->customer_id)->first();
        }

        // quotation update
        $order = Order::find($request->order_id);
        $order->orderdate = $request->orderdate;
        $order->customer_id = $request->customer_id;
        $order->branch_id = Auth::user()->branch_id;
        $order->ref = $request->ref;
        $order->due_date = $request->due_date;
        $order->vatpercentage = $request->vat_percent;
        $order->vatamount = $request->vat_total;
        $order->discount_amount = $request->discount_amount;
        $order->grand_total = $request->grand_total;
        $order->net_total = $request->net_total;
        $order->customer_paid = $request->customer_paid;
        $order->due = $request->due;
        $order->quotation = "1";
        $order->return_amount = $request->return_amount;
        $order->partnoshow = $request->partnoshow;
        $order->updated_by = Auth::user()->id;
        $order->status = 0;
        if($order->save()){


            $collection = OrderDetail::where('order_id', $request->order_id)->get(['id']);
            OrderDetail::destroy($collection->toArray());


            foreach($request->input('product_id') as $key => $value)
            {
                $orderDtl = new OrderDetail();
                $orderDtl->invoiceno = $order->invoiceno;
                $orderDtl->order_id = $order->id;
                $orderDtl->product_id = $request->get('product_id')[$key];
                $orderDtl->quantity = $request->get('quantity')[$key];
                $orderDtl->sellingprice = $request->get('sellingprice')[$key];
                $orderDtl->total_amount = $request->get('total')[$key];
                $orderDtl->created_by = Auth::user()->id;
                $orderDtl->save();
            }
            //stores the pdf for invoice

            $pdf_path = public_path('invoices').'/Order#'.$order->invoiceno.'.pdf';
            // if (isset($pdf_path)) {
            //     unlink($pdf_path);
            // }
            

            // $pdf = PDF::loadView('invoices.customer_invoice', compact('order','customerdtl'));
            // $output = $pdf->output();
            // file_put_contents(public_path().'/invoices/'.'Order#'.$order->invoiceno.'.pdf', $output);

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Quotation updated successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message,'id'=>$order->id]);
        }

    } 
    // quotation update end


    // delivery note update
    public function deliveryNoteUpdate(Request $request){

        $productIDs = $request->input('product_id');
        if($productIDs == "" ){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Product field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if ($request->salestype == "Credit") {
            if(empty($request->customer_id)){
                $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Customer\" field..!</b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }
        }

        
        if(empty($request->grand_total)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Product\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->ref)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Reference\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->vat_percent)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Vat Percent\" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if ($request->customer_id != "") {
            $customer = Customer::find($request->customer_id);
            $customer->name = $request->customername;
            $customer->address = $request->customeraddress;
            $customer->vat_number = $request->customervat;
            $customer->vehicleno = $request->customervehicleno;
            $customer->updated_by = Auth::user()->id;
            $customer->save();

            $customerdtl = Customer::where('id', '=', $request->customer_id)->first();
        }

        // new code
        $order = Order::find($request->order_id);
        $order->orderdate = $request->orderdate;
        $order->customer_id = $request->customer_id;
        $order->branch_id = Auth::user()->branch_id;
        $order->ref = $request->ref;
        $order->vatamount = $request->vat_total;
        $order->discount_amount = $request->discount_amount;
        $order->grand_total = $request->grand_total;
        $order->net_total = $request->net_total;
        $order->customer_paid = $request->customer_paid;
        $order->due = $request->due;
        $order->partnoshow = $request->partnoshow;
        $order->return_amount = $request->return_amount;
        $order->updated_by = Auth::user()->id;
        $order->status = 0;
        if($order->save()){

            $collection = OrderDetail::where('order_id', $request->order_id)->get(['id']);
            OrderDetail::destroy($collection->toArray());


            foreach($request->input('product_id') as $key => $value)
            {
                $orderDtl = new OrderDetail();
                $orderDtl->invoiceno = $order->invoiceno;
                $orderDtl->order_id = $order->id;
                $orderDtl->product_id = $request->get('product_id')[$key];
                $orderDtl->quantity = $request->get('quantity')[$key];
                $orderDtl->sellingprice = $request->get('sellingprice')[$key];
                $orderDtl->total_amount = $request->get('total')[$key];
                $orderDtl->created_by = Auth::user()->id;
                $orderDtl->save();

                $stockid = Stock::where('product_id','=',$request->get('product_id')[$key])->where('branch_id','=', Auth::user()->branch_id)->first();
                if (isset($stockid->id)) {
                    $dstock = Stock::find($stockid->id);
                    $dstock->quantity = $dstock->quantity - $request->get('quantity')[$key];
                    $dstock->save();
                } else {
                    $newstock = new Stock();
                    $newstock->branch_id = Auth::user()->branch_id;
                    $newstock->product_id = $request->get('product_id')[$key];
                    $newstock->quantity = 0 - $request->get('quantity')[$key];
                    $newstock->created_by = Auth::user()->id;
                    $newstock->save();
                }

            }
            //stores the pdf for invoice

            $pdf_path = public_path('invoices').'/Order#'.$order->invoiceno.'.pdf';
            // if (isset($pdf_path)) {
            //     unlink($pdf_path);
            // }
            

            // $pdf = PDF::loadView('invoices.customer_invoice', compact('order','customerdtl'));
            // $output = $pdf->output();
            // file_put_contents(public_path().'/invoices/'.'Order#'.$order->invoiceno.'.pdf', $output);

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Delivery Note updated successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message,'id'=>$order->id]);
        }

    }
    // delivery note update end

    public function getcustomer(Request $request)
    {
        $customerDtl = Customer::where('id', '=', $request->customer_id)->first();
        if(empty($customerDtl)){
            return response()->json(['status'=> 303,'message'=>"No data found"]);
        }else{
            return response()->json(['status'=> 300,'customername'=>$customerDtl->name,'customeremail'=>$customerDtl->email,'customer_id'=>$customerDtl->id,'address'=>$customerDtl->address,'vehicleno'=>$customerDtl->vehicleno,'vat_number'=>$customerDtl->vat_number,'showcustomerdue'=>$customerDtl->amount]);
        }
    }

        public function getAllInvoice()
        {
            return view('user.invoice.manageallinvoice');
        }

        public function filterAllInvoice()
        {
            // if (Auth::user()->id == 1) {
            //     $query = Order::where('sales_status','=','1')->get();
            // } else if(Auth::user()->type == 1 && Auth::user()->id != 1) {
            //     $query = Order::where('sales_status','=','1')->where('branch_id', Auth::user()->branch_id)->get();
            // }else{
            //     $query = Order::where('sales_status','=','1')->where('created_by',Auth::user()->id)->get();
            // }

            if(Auth::user()->type == 1) {
                $query = Order::select('id','invoiceno','orderdate','customer_id','ref','due','dn_no','net_total','partnoshow','created_at','branch_id')->where('sales_status','=','1')->where('branch_id', Auth::user()->branch_id)->get();
            }else{
                $query = Order::select('id','invoiceno','orderdate','customer_id','ref','due','dn_no','net_total','partnoshow','created_at','created_by')->where('sales_status','=','1')->where('created_by',Auth::user()->id)->get();
            }


            return Datatables::of($query)
            ->setRowAttr(['align' => 'center'])
            ->addColumn('customername', function(Order $order) {
                return $order->customer->name;
            })
            ->editColumn('created_at', function(Order $order) {
                return $order->created_at->diffForHumans();
            })
            ->addColumn('action', function($invoice){

                $btn = '<div class="table-actions text-right">';

                        if (Auth::user()->type == '1' && in_array('13', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('13', json_decode(Auth::user()->role->permission))) {
                            $btn .= '<a href="https://www.greenstock.greentechnology.com.bd/sales-return/'.$invoice->id.'" class="btn btn-sm btn-success ms-1"><span title="Return">Return</span></a>';
                        }

                        if (Auth::user()->type == '1' && in_array('4', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('4', json_decode(Auth::user()->role->permission))) {
                            $btn .= '<a href="https://www.greenstock.greentechnology.com.bd/sales-edit/'.$invoice->id.'" class="btn btn-sm btn-theme ms-1"><span title="Edit">Edit</span></a>';
                        }


                    $btn .= '<a href="https://www.greenstock.greentechnology.com.bd/invoice/customer/'.$invoice->id.'" class="btn btn-sm btn-theme ms-1"><span title="Download Invoice">Download</span></a>';

                    $btn .= '<a href="https://www.greenstock.greentechnology.com.bd/invoice/print/'.$invoice->id.'" class="btn btn-sm btn-theme ms-1 print-window"><span title="Print Invoice">Print</span></a>';

                    $btn .= '<a href="#" class="btn btn-sm btn-theme ms-1 viewThis" data-bs-toggle="modal" data-bs-target="#view" oid="'.$invoice->id.'" id="viewThis">View</a>';

                $btn .= '</div>';
                return $btn;

           }) 
            ->toJson();

        }

        public function getAllQuoation()
        {
            return view('user.invoice.manageallquotation');
        }

        public function filterAllQuotation()
        {
            // if (Auth::user()->id == 1) {
            //     $query = Order::where('quotation','=','1')->get();
            // } else if(Auth::user()->type == 1 && Auth::user()->id != 1) {
            //     $query = Order::where('quotation','=','1')->where('branch_id', Auth::user()->branch_id)->get();
            // }else{
            //     $query = Order::where('quotation','=','1')->where('created_by',Auth::user()->id)->get();
            // }

            if(Auth::user()->type == 1) {
                $query = Order::select('id','invoiceno','orderdate','customer_id','ref','due','dn_no','net_total','partnoshow','created_at','branch_id')->where('quotation','=','1')->where('branch_id', Auth::user()->branch_id)->get();
            }else{
                $query = Order::select('id','invoiceno','orderdate','customer_id','ref','due','dn_no','net_total','partnoshow','created_at','created_by')->where('quotation','=','1')->where('created_by',Auth::user()->id)->get();
            }
            
            return Datatables::of($query)
            ->setRowAttr(['align' => 'center'])
            ->addColumn('customername', function(Order $order) {
                return $order->customer->name;
            })
            ->editColumn('created_at', function(Order $order) {
                return $order->created_at->diffForHumans();
            })
            ->addColumn('action', function($invoice){

                $btn = '<div class="table-actions text-right">';

                if (Auth::user()->type == '1' && in_array('10', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('10', json_decode(Auth::user()->role->permission))) {
                    $btn .= '<a href="https://www.greenstock.greentechnology.com.bd/quotation-edit/'.$invoice->id.'" class="btn btn-sm btn-theme ms-1"><span title="Edit">Edit</span></a>';
                }

                $btn .= '<a href="https://www.greenstock.greentechnology.com.bd/invoice/customer/'.$invoice->id.'" class="btn btn-sm btn-theme ms-1"><span title="Download Invoice">Download</span></a>';

                $btn .= '<a href="https://www.greenstock.greentechnology.com.bd/invoice/print/'.$invoice->id.'" class="btn btn-sm btn-theme ms-1 print-window"><span title="Print Invoice">Print</span></a>';

                $btn .= '<a href="#" class="btn btn-sm btn-theme ms-1 viewThis" data-bs-toggle="modal" data-bs-target="#view" oid="'.$invoice->id.'" id="viewThis">View</a>';

                $btn .= '</div>';
                return $btn;

           }) 
            ->toJson();

        }

        public function getAllDeliveryNote()
        {
            return view('user.invoice.managealldeliverynote');

        }

        public function filterAllDeliveryNote()
        {
            // if (Auth::user()->id == 1) {
            //     $query = Order::where('delivery_note','=','1')->get();
            // } else if(Auth::user()->type == 1 && Auth::user()->id != 1) {
            //     $query = Order::where('delivery_note','=','1')->where('branch_id', Auth::user()->branch_id)->get();
            // }else{
            //     $query = Order::where('delivery_note','=','1')->where('created_by',Auth::user()->id)->get();
            // }

            if(Auth::user()->type == 1) {
                $query = Order::select('id','invoiceno','orderdate','customer_id','ref','due','qn_no','net_total','partnoshow','created_at','branch_id')->where('delivery_note','=','1')->where('branch_id', Auth::user()->branch_id)->get();
            }else{
                $query = Order::select('id','invoiceno','orderdate','customer_id','ref','due','qn_no','net_total','partnoshow','created_at','created_by')->where('delivery_note','=','1')->where('created_by',Auth::user()->id)->get();
            }

            return Datatables::of($query)
            ->setRowAttr(['align' => 'center'])
            ->addColumn('customername', function(Order $order) {
                return $order->customer->name;
            })
            ->editColumn('created_at', function(Order $order) {
                return $order->created_at->diffForHumans();
            })
            ->addColumn('action', function($invoice){

                $btn = '<div class="table-actions text-right">';

                if (Auth::user()->type == '1' && in_array('12', json_decode(Auth::user()->role->permission)) || Auth::user()->type == '0' && in_array('12', json_decode(Auth::user()->role->permission))) {
                    $btn .= '<a href="https://www.greenstock.greentechnology.com.bd/delivery-note-edit/'.$invoice->id.'" class="btn btn-sm btn-theme ms-1"><span title="Edit">Edit</span></a>';
                }

                $btn .= '<a href="https://www.greenstock.greentechnology.com.bd/invoice/customer/'.$invoice->id.'" class="btn btn-sm btn-theme ms-1"><span title="Download Invoice">Download</span></a>';

                $btn .= '<a href="https://www.greenstock.greentechnology.com.bd/invoice/print/'.$invoice->id.'" class="btn btn-sm btn-theme ms-1 print-window" target="blank"><span title="Print Invoice">Print</span></a>';

                $btn .= '<a href="#" class="btn btn-sm btn-theme ms-1 viewThis" data-bs-toggle="modal" data-bs-target="#view" oid="'.$invoice->id.'" id="viewThis">View</a>';

                $btn .= '</div>';
                return $btn;

           }) 
            ->toJson();

        }

    public function salesdetails($id)
    {
        
        // $data = DB::table('order_details')
        //             ->join('products', 'order_details.product_id', '=', 'products.id')
        //             ->join('orders', 'order_details.order_id', '=', 'orders.id')
        //             ->select('order_details.*', 'products.productname','products.part_no','products.vat_percent','products.vat_amount','products.selling_price')
        //             ->where('order_details.order_id','=', $id)
        //             ->orderby('order_details.id','ASC')
        //             ->get();

        $data = OrderDetail::with('product','orders')->where('order_id','=', $id)->get();
        
        return response()->json($data);
    }

        public function stockRequest(Request $request)
        {
            
            $data = new StockTransferRequest();
            $data->product_id = $request->productid;
            $data->from_branch_id = Auth::user()->branch_id;
            $data->to_branch_id = $request->reqtobranchid;
            $data->stock_id = $request->stockid;
            $data->requestqty = $request->quantity;
            $data->created_by = Auth::user()->id;
            $data->save();
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Request send Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }

        public function getStockRequest()
        {
            $data  = StockTransferRequest::where('to_branch_id', Auth::user()->branch_id)->orderby('id','DESC')->get();
            // dd($data);
            return view('user.stock.stockrequest', compact('data'));
        }


        // sales return start here
        public function salesReturn($id)
        {
            $invoices  = Order::with('orderdetails','customer')->where('id', $id)->first();
            // dd($invoices);
            return view('user.invoice.salesreturn', compact('invoices'));
        }

        public function getproductdetails(Request $request)
        {
            $productDtl = Product::where('id', '=', $request->product)->first();
            $orderDtl = OrderDetail::where('id', '=', $request->orderdetailid)->first();
            if(empty($orderDtl)){
                return response()->json(['status'=> 303,'message'=>"No data found"]);
            }else{
                return response()->json(['status'=> 300,'productname'=>$orderDtl->product->productname,'product_id'=>$orderDtl->product_id,'order_detail_id'=>$orderDtl->id, 'selling_price_with_vat'=>$orderDtl->sellingprice, 'part_no'=>$orderDtl->product->part_no, 'quantity'=>$orderDtl->quantity, 'vat_amount'=>$orderDtl->vat_amount, 'total_vat'=>$orderDtl->total_vat, 'order_id'=>$orderDtl->order_id ]);
            }

        }

        public function salesReturnStore(Request $request){

            if(empty($request->customer_id)){
                $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Customer\" field..!</b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }

            if(empty($request->reason)){
                $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill  \"Reason\" field..!</b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }
            if(empty($request->input('product_id'))){
                $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Select a \"Product\" field..!</b></div>";
                return response()->json(['status'=> 303,'message'=>$message]);
                exit();
            }

            $invoiceeno = Order::where('id',$request->order_id)->first()->invoiceno;
            
            // new code
            $data = new SalesReturn();
            $data->returndate = $request->returndate;
            $data->customer_id = $request->customer_id;
            $data->order_id = $request->order_id;
            $data->invoiceno = $invoiceeno;
            $data->reason = $request->reason;
            $data->net_total = $request->net_total;
            $data->branch_id = Auth::user()->branch_id;
            $data->created_by = Auth::user()->id;
            $data->status = 1;
            if($data->save()){
                foreach($request->input('product_id') as $key => $value)
                {
                    $orderDtl = new SalesReturnDetail();
                    $orderDtl->sales_return_id = $data->id;
                    $orderDtl->branch_id = Auth::user()->branch_id;
                    $orderDtl->product_id = $request->get('product_id')[$key];
                    $orderDtl->quantity = $request->get('quantity')[$key];
                    $orderDtl->total_amount = $request->get('total')[$key];
                    $orderDtl->created_by = Auth::user()->id;
                    $orderDtl->save();
                }
                $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Product return successfully.</b></div>";
                return response()->json(['status'=> 300,'message'=>$message]);
            }
    
        }

    public function getAllReturnInvoice()
        {
            $invoices  = SalesReturn::with('salesreturndetail')->where('branch_id', Auth::user()->branch_id)->orderby('id','DESC')->get();
            // dd($invoices);
            return view('user.invoice.managereturninvoice', compact('invoices'));
        } 

    public function salesReturnDetails($id)
    {
        
        $histories = DB::table('sales_return_details')
                    ->join('products', 'sales_return_details.product_id', '=', 'products.id')
                    ->join('branches', 'sales_return_details.branch_id', '=', 'branches.id')
                    ->select('sales_return_details.*', 'products.productname','products.selling_price','products.part_no','products.vat_percent','products.vat_amount', 'branches.name as branchname')
                    ->where('sales_return_details.sales_return_id','=', $id)
                    ->orderby('sales_return_details.id','ASC')
                    ->get();
        
        return response()->json($histories);
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
