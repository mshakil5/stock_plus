<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\PurchaseHistory;
use App\Models\Stock;
use App\Models\Transaction;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;

class SalesController extends Controller
{
    public function sales()
    {
        return view('admin.sales.create');
    }

    public function getAllQuoation()
    {
        return view('admin.quotation.index');
    }

    public function filterQuotation(Request $request)
    {
        
        $allInvoice = Order::with('customer','orderdetails')
                    ->where('quotation','1')
                    ->where('branch_id', auth()->user()->branch_id)->get();

        return Datatables::of($allInvoice)
            ->addIndexColumn()
            ->editColumn('customer_id', function ($invoice) {
                return $invoice->customer->name;
            })
            ->addColumn('total', function ($invoice) {
                $total = $invoice->net_total;
                return $total;
            })
            ->addColumn('created_at', function ($invoice) {
                return "<span data-title='" . Carbon::parse($invoice->created_at)->format('h:m A') . "'>" . Carbon::parse($invoice->created_at)->format('d M Y') . "</span>";
            })
            ->addColumn('action', function ($invoice) {
                $btn = '<div class="table-actions text-right">';

                    $btn = '<a href="' . route('sales.return', $invoice->id) . '" class="btn btn-info btn-xs ms-1">
                                <i class="fa fa-undo" aria-hidden="true"></i><span title="Return">Return</span>
                            </a>';

                    $btn .= '<a href="' . route('sales.edit', $invoice->id) . '" class="btn btn-warning btn-xs ms-1">
                        <i class="fa fa-pencil" aria-hidden="true"></i><span title="Edit">Edit</span>
                    </a>';

                    $btn .= '<a href="' . route('customer.invoice.print', $invoice->id) . '" class="btn btn-success btn-xs print-window" target="_blank">
                        <span title="Print Invoice">Print</span>
                    </a>';

                
                    $btn .= '<a href="' . route('admin.get_invoice', $invoice->id) . '" class="btn btn-primary btn-xs">
                        <span title="Download Invoice">
                            <i class="fa fa-download" aria-hidden="true"></i> Download
                        </span>
                    </a>
                    <button type="button" class="btn btn-primary btn-xs view-btn" data-toggle="modal" data-target="#product-details" value="' . $invoice->id . '">
                        <i class="fa fa-eye" aria-hidden="true"></i> View
                    </button>';
                
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['created_at', 'action'])
            ->make(true);
    }

    public function getAllDeliveryNote()
    {
        return view('admin.delivery_note.index');
    }

    public function filterDeliveryNote(Request $request)
    {      
        $allInvoice = Order::with('customer','orderdetails')
                    ->where('delivery_note','1')
                    ->where('branch_id', auth()->user()->branch_id)->get();

        return Datatables::of($allInvoice)
            ->addIndexColumn()
            ->editColumn('customer_id', function ($invoice) {
                return $invoice->customer->name;
            })
            ->addColumn('total', function ($invoice) {
                $total = $invoice->net_total;
                return $total;
            })
            ->addColumn('created_at', function ($invoice) {
                return "<span data-title='" . Carbon::parse($invoice->created_at)->format('h:m A') . "'>" . Carbon::parse($invoice->created_at)->format('d M Y') . "</span>";
            })
            ->addColumn('action', function ($invoice) {
                $btn = '<div class="table-actions text-right">';

                    $btn = '<a href="' . route('sales.return', $invoice->id) . '" class="btn btn-info btn-xs ms-1">
                                <i class="fa fa-undo" aria-hidden="true"></i><span title="Return">Return</span>
                            </a>';

                    $btn .= '<a href="' . route('sales.edit', $invoice->id) . '" class="btn btn-warning btn-xs ms-1">
                        <i class="fa fa-pencil" aria-hidden="true"></i><span title="Edit">Edit</span>
                    </a>';

                    $btn .= '<a href="' . route('customer.invoice.print', $invoice->id) . '" class="btn btn-success btn-xs print-window" target="_blank">
                        <span title="Print Invoice">Print</span>
                    </a>';

                
                    $btn .= '<a href="' . route('admin.get_invoice', $invoice->id) . '" class="btn btn-primary btn-xs">
                        <span title="Download Invoice">
                            <i class="fa fa-download" aria-hidden="true"></i> Download
                        </span>
                    </a>
                    <button type="button" class="btn btn-primary btn-xs view-btn" data-toggle="modal" data-target="#product-details" value="' . $invoice->id . '">
                        <i class="fa fa-eye" aria-hidden="true"></i> View
                    </button>';
                
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['created_at', 'action'])
            ->make(true);
    }

    public function getAllReturnInvoice()
    {
        $invoices  = SalesReturn::with('salesreturndetail')->where('branch_id', Auth::user()->branch_id)->orderby('id','DESC')->get();
        return view('user.invoice.managereturninvoice', compact('invoices'));
    }  

    public function saveCustomer(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'nullable|email',
            'phone' => 'nullable|numeric',
            'address' => 'nullable|string',
            'vehicleno' => 'nullable|string',
            'vat_number' => 'nullable|string',
            'type' => 'required|in:0,1',
            'member_id' => 'nullable|string|unique:customers',
        ]);

        $customer = new Customer();
        $customer->name = $request->name;
        $customer->branch_id = auth()->user()->branch_id;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->address = $request->address;
        $customer->vehicleno = $request->vehicleno;
        $customer->vat_number = $request->vat_number;
        $customer->member_id = $request->member_id;
        $customer->type = $request->type;
        $customer->save();

        return response()->json([
            'success' => true,
            'customer' => $customer
        ]);
    }

    public function salesStore(Request $request)
    {
        $productIDs = $request->input('product_id');

        if (empty($productIDs)) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Product field.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        if (empty($request->total_amount)) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please provide a total amount.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        if ($request->salestype == "Cash" && empty($request->customer_id) && $request->due_amount > 0) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please pay the full amount.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        if ($request->salestype == "Credit" && empty($request->customer_id)) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select a customer.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        if (!empty($request->customer_id)) {
            $customer = Customer::find($request->customer_id);
            if (($customer->amount + $request->due_amount) > $customer->limitation) {
                $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Customer credit limitation exceeded. Please pay the full amount.</b></div>";
                return response()->json(['status' => 303, 'message' => $message]);
            }
        }

        $order = new Order();
        $order->invoiceno = $request->invoiceno;
        $order->orderdate = $request->date;
        $order->salestype = $request->salestype;
        $order->customer_id = $request->customer_id;
        $order->branch_id = Auth::user()->branch_id;
        $order->ref = $request->ref; 
        $order->qn_no = $request->order_id ?: "0";
        $order->dn_no = $request->delivery_note_id ?: "0";
        $order->vatpercentage = $request->vat_percent;
        $order->vatamount = $request->total_vat_amount;
        $order->discount_amount = $request->discount;
        $order->grand_total = $request->grand_total; 
        $order->net_total = $request->net_amount;
        $order->customer_paid = $request->paid_amount;
        $order->due = $request->due_amount;
        $order->partnoshow = $request->partnoshow;
        $order->sales_status = "1";
        $order->return_amount = $request->return_amount;
        $order->created_by = Auth::user()->id;
        $order->status = 0;

        if ($order->save()) {

            $transaction = new Transaction();
            $transaction->date = $request->date;
            $transaction->table_type = 'Income';
            $transaction->description = 'Sales';
            $transaction->amount = $request->grand_total;
            $transaction->vat_amount = $request->total_vat_amount;
            $transaction->at_amount = $request->net_amount;
            $transaction->transaction_type = 'Current';
            if ($request->salestype == "Credit") {
                $transaction->payment_type = "Account Receivable";
            } else {
                $transaction->payment_type = $request->salestype;
            }

            // $transaction->supplier_id = $request->vendor_id;
            $transaction->branch_id = Auth::user()->branch_id;
            $transaction->created_by = Auth()->user()->id;
            $transaction->created_ip = request()->ip();
            $transaction->order_id = $order->id;
            $transaction->save();
            $transaction->tran_id = 'SL' . date('Ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
            $transaction->save();

            foreach ($request->input('product_id') as $key => $value) {
                $orderDtl = new OrderDetail();
                $orderDtl->invoiceno = $order->invoiceno;
                $orderDtl->order_id = $order->id;
                $orderDtl->product_id = $request->get('product_id')[$key];
                $orderDtl->quantity = $request->get('quantity')[$key];
                $orderDtl->sellingprice = $request->get('unit_price')[$key];
                $orderDtl->total_amount = $request->get('quantity')[$key] * $request->get('unit_price')[$key];
                $orderDtl->created_by = Auth::user()->id;
                $orderDtl->save();

                $purchaseHistory = PurchaseHistory::where('product_id', $orderDtl->product_id)
                    ->where('branch_id', Auth::user()->branch_id)
                    ->where('available_stock', '>', 0)
                    ->orderBy('id', 'asc')
                    ->first();

                if ($purchaseHistory) {
                    $orderDtl->purchase_history_id = $purchaseHistory->id;
                    $orderDtl->save();

                    $purchaseHistory->sold += $orderDtl->quantity;
                    $purchaseHistory->available_stock -= $orderDtl->quantity;
                    $purchaseHistory->updated_by = Auth::user()->id;
                    $purchaseHistory->save();
                }

                $stockid = Stock::where('product_id', '=', $request->get('product_id')[$key])
                    ->where('branch_id', '=', Auth::user()->branch_id)
                    ->first();

                if ($request->delivery_note_id == "") {
                    if (isset($stockid->id)) {
                        $dstock = Stock::find($stockid->id);
                        $dstock->quantity -= $request->get('quantity')[$key];
                        $dstock->save();
                    } else {
                        $newstock = new Stock();
                        $newstock->branch_id = Auth::user()->branch_id;
                        $newstock->product_id = $request->get('product_id')[$key];
                        $newstock->quantity = 0 - $request->get('quantity')[$key];
                        $newstock->created_by = Auth::user()->id;
                        $newstock->save();
                    }
                } else {
                    $oldDNqty = OrderDetail::where('order_id', $request->delivery_note_id)
                        ->where('product_id', $request->get('product_id')[$key])
                        ->first();

                    if (isset($oldDNqty)) {
                        $amend_stock = $oldDNqty->quantity - $request->get('quantity')[$key];
                        $dstock = Stock::find($stockid->id);
                        $dstock->quantity += $amend_stock;
                        $dstock->save();
                    } else {
                        if (isset($stockid->id)) {
                            $dstock = Stock::find($stockid->id);
                            $dstock ->quantity -= $request->get('quantity')[$key];
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

            $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Thank you for this order.</b></div>";
            return response()->json(['status' => 300, 'message' => $message, 'id' => $order->id]);
        }

        return response()->json(['status' => 303, 'message' => 'Failed to save the order.']);
    }

    public function quotationStore(Request $request)
    {
        $productIDs = $request->input('product_id');

        if (empty($productIDs)) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Product field.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        if (empty($request->total_amount)) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please provide a total amount.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        $order = new Order();
        $order->invoiceno = $request->invoiceno;
        $order->orderdate = $request->date;
        $order->salestype = $request->salestype;
        $order->customer_id = $request->customer_id;
        $order->branch_id = Auth::user()->branch_id;
        $order->ref = $request->ref; 
        $order->qn_no = $request->order_id ?: "0";
        $order->dn_no = $request->delivery_note_id ?: "0";
        $order->vatpercentage = $request->vat_percent;
        $order->vatamount = $request->total_vat_amount;
        $order->discount_amount = $request->discount;
        $order->grand_total = $request->grand_total; 
        $order->net_total = $request->net_amount;
        $order->customer_paid = $request->paid_amount;
        $order->due = $request->due_amount;
        $order->partnoshow = $request->partnoshow;
        $order->quotation = "1";
        $order->return_amount = $request->return_amount;
        $order->created_by = Auth::user()->id;
        $order->status = 0;

        if ($order->save()) {

            foreach ($request->input('product_id') as $key => $value) {
                $orderDtl = new OrderDetail();
                $orderDtl->invoiceno = $order->invoiceno;
                $orderDtl->order_id = $order->id;
                $orderDtl->product_id = $request->get('product_id')[$key];
                $orderDtl->quantity = $request->get('quantity')[$key];
                $orderDtl->sellingprice = $request->get('unit_price')[$key];
                $orderDtl->total_amount = $request->get('quantity')[$key] * $request->get('unit_price')[$key];
                $orderDtl->created_by = Auth::user()->id;
                $orderDtl->save();

            }

            $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Thank you for quotation.</b></div>";
            return response()->json(['status' => 300, 'message' => $message, 'id' => $order->id]);
        }

        return response()->json(['status' => 303, 'message' => 'Failed to save the order.']);
    }
    
    public function deliveryNoteStore(Request $request)
    {
        $productIDs = $request->input('product_id');

        if (empty($productIDs)) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Product field.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        if (empty($request->total_amount)) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please provide a total amount.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        $order = new Order();
        $order->invoiceno = $request->invoiceno;
        $order->orderdate = $request->date;
        $order->salestype = $request->salestype;
        $order->customer_id = $request->customer_id;
        $order->branch_id = Auth::user()->branch_id;
        $order->ref = $request->ref; 
        $order->qn_no = $request->order_id ?: "0";
        $order->dn_no = $request->delivery_note_id ?: "0";
        $order->vatpercentage = $request->vat_percent;
        $order->vatamount = $request->total_vat_amount;
        $order->discount_amount = $request->discount;
        $order->grand_total = $request->grand_total; 
        $order->net_total = $request->net_amount;
        $order->customer_paid = $request->paid_amount;
        $order->due = $request->due_amount;
        $order->partnoshow = $request->partnoshow;
        $order->delivery_note = "1";
        $order->return_amount = $request->return_amount;
        $order->created_by = Auth::user()->id;
        $order->status = 0;

        if ($order->save()) {

            foreach ($request->input('product_id') as $key => $value) {
                $orderDtl = new OrderDetail();
                $orderDtl->invoiceno = $order->invoiceno;
                $orderDtl->order_id = $order->id;
                $orderDtl->product_id = $request->get('product_id')[$key];
                $orderDtl->quantity = $request->get('quantity')[$key];
                $orderDtl->sellingprice = $request->get('unit_price')[$key];
                $orderDtl->total_amount = $request->get('quantity')[$key] * $request->get('unit_price')[$key];
                $orderDtl->created_by = Auth::user()->id;
                $orderDtl->save();


                $stockid = Stock::where('product_id', '=', $request->get('product_id')[$key])
                    ->where('branch_id', '=', Auth::user()->branch_id)
                    ->first();

                if ($request->delivery_note_id == "") {
                    if (isset($stockid->id)) {
                        $dstock = Stock::find($stockid->id);
                        $dstock->quantity -= $request->get('quantity')[$key];
                        $dstock->save();
                    } else {
                        $newstock = new Stock();
                        $newstock->branch_id = Auth::user()->branch_id;
                        $newstock->product_id = $request->get('product_id')[$key];
                        $newstock->quantity = 0 - $request->get('quantity')[$key];
                        $newstock->created_by = Auth::user()->id;
                        $newstock->save();
                    }
                } else {
                    $oldDNqty = OrderDetail::where('order_id', $request->delivery_note_id)
                        ->where('product_id', $request->get('product_id')[$key])
                        ->first();

                    if (isset($oldDNqty)) {
                        $amend_stock = $oldDNqty->quantity - $request->get('quantity')[$key];
                        $dstock = Stock::find($stockid->id);
                        $dstock->quantity += $amend_stock;
                        $dstock->save();
                    } else {
                        if (isset($stockid->id)) {
                            $dstock = Stock::find($stockid->id);
                            $dstock ->quantity -= $request->get('quantity')[$key];
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

            $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Thank you for delievery note.</b></div>";
            return response()->json(['status' => 300, 'message' => $message, 'id' => $order->id]);
        }

        return response()->json(['status' => 303, 'message' => 'Failed to save the order.']);
    }
}