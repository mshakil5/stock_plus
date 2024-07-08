
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html">
    <title>Customer invoice</title>
</head>

<body>
    <section class="invoice">
        <div class="container-fluid p-0">
            <div class="invoice-body py-5">
                <div style="  max-width: 1170px; margin: 70px auto;">
                    @if ( $order->quotation == 1 )
                    <div class="col-lg-2" style="flex: 2; text-align: center;">
                        <h3 style="font-size: 1.5rem; margin-bottom: 5px;">QUOTATION</h3>
                    </div>

                        <table style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td colspan="4" class="" style="border :0px solid #dee2e6 ;width:80%;">
                                        <div class="col-lg-5" style="flex: 2;">
                                            <p>Customer Name : {{ $customerdtl->name }} </p>
                                            <span style="padding-left: 118px">{{ $customerdtl->address }}</span> 
                                        </div>
                                    </td>
                                    <td colspan="2" class="" style="border :0px solid #dee2e6 ;">
                                        <div class="col-lg-2 text-end" style="flex: 2; text-align: right;">
                                            <h5 style="font-size: .90rem; margin : 5px;text-align: left;">TRN: 100474976600003</h5>
                                            <h5 style="font-size: .90rem; margin : 5px;text-align: left;">INV NO: {{ $order->invoiceno }}3</h5>
                                            <h5 style="font-size: .90rem; margin : 5px;text-align: left;">QTN No: 000{{ $order->id }}</h5>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="" style="border :0px solid #dee2e6 ;">
                                        <div class="col-lg-5" style="flex: 2;">
                                            <i>Dear Sir, </i>
                                            <p>We are pleased to quote our best prices as follows </p>
                                        </div>
                                    </td>
                                    <td colspan="2" class="" style="border :0px solid #dee2e6 ;">
                                        <div class="col-lg-2 text-end" style="">
                                            <h5 style="font-size: .90rem; margin : 5px;">Date: {{ $order->orderdate }}</h5>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            
                        </table>


                    @elseif ($order->delivery_note == 1)

                    <div class="col-lg-2" style="flex: 2; text-align: center;">
                        <h3 style="font-size: 1.5rem; margin-bottom: 5px;">DELIVERY NOTE</h3>
                    </div>

                        <table style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 75%"></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="border :0px solid #ffffff ; width:75%;">
                                        <div class="col-lg-5" style="flex: 2;">
                                            <p> <span style="text-transform: uppercase;text-align: left;">{{ $customerdtl->name }}</span> </p>
                                            <p> <span style="text-transform: uppercase;text-align: left;">{{ $customerdtl->address }}</span> </p>
                                        </div>
                                    </td>
                                    <td style="border :0px solid #ffffff ;">
                                        <div class="col-lg-2 text-end" style="flex: 2; text-align: right;">
                                            <h5 style="font-size: .90rem; margin : 5px;text-align: left;">INV NO: {{ $order->invoiceno }}</h5>
                                            <h5 style="font-size: .90rem; margin : 5px;text-align: left;">Q/N NO: {{ $order->qn_no }}</h5>
                                            <h5 style="font-size: .90rem; margin : 5px;text-align: left;">D/N NO: {{ $order->id }}</h5>
                                            <h5 style="font-size: .90rem; margin : 5px;text-align: left;">Date: {{ $order->orderdate }}</h5>
                                            <h5 style="font-size: .90rem; margin : 5px;text-align: left;">Ref: {{ $order->ref }}</h5>
                                            <h5 style="font-size: .90rem; margin : 5px;text-align: left;">Page: </h5>
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>


                    @else

                        <div class="row text-center" style="text-align: center; margin: 5px 0;">
                            <h3 style="font-size: 1.1rem; margin-bottom: 5px;">TAX CASH INVOICE</h3>
                            <h5 style="font-size: .70rem; margin : 5px;">TRN: 100474976600003</h5>
                        </div>
                    @endif



                    @if ( $order->quotation == 1 )

                    @elseif ($order->delivery_note == 1)

                    @else
                        <div class=" " style="display: flex; flex-wrap: wrap; margin: 5px 0;">
                            <table style="width: 100%">
                                <tbody>
                                    <tr>
                                        <td colspan="2" style="width: 25%">
                                            <div class="col-lg-12" style="flex:2;">
                                                <div class="card border box-round" style="border: 1px solid #dee2e6!important; padding: 5px 0 75px 15px; border-radius:5px;">
                                                    @if ($customerdtl->id == 1)
                                                        CASH SALES
                                                    @else
                                                        <p> <span style="text-transform: uppercase;text-align: left;">{{ $customerdtl->name }}</span> </p>
                                                        <p> <span style="text-transform: uppercase;text-align: left;">{{ $customerdtl->address }}</span> </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td style="width: 30%">
                                            <div class="col-lg-12" style="flex:1;width: 5%"> </div>
                                        </td>
                                        <td colspan="2" style="width: 29%">
                                            <div class="col-lg-12"style="flex:2; border: 1px solid #dee2e6!important; padding-left: 10px; border-radius:5px;">
                                                <div class="card border box-round" style="padding: 5px" >
                                                    <span><b>Invoice No :</b> {{ $order->invoiceno }}</span><br>
                                                    <span><b>Invoice Date:</b> {{ $order->orderdate }}</span><br>
                                                    <span><b>DO No:</b>{{ $order->dn_no }}</span><br>
                                                    <span><b>LPO No:</b></span><br>
                                                    <span><b>Salesman:</b> {{\App\Models\User::where('id', $order->created_by)->first()->name}}-{{\App\Models\User::where('id', $order->created_by)->first()->phone}}</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif

                    


                    
                    <div class="row overflow">
                        <table style="width: 100%;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th  style="border: 1px solid #dee2e6!important; padding: 0 15px;">#</th>
                                    <th  style="border: 1px solid #dee2e6!important; padding: 0 15px;">Part No</th>
                                    <th  style="border: 1px solid #dee2e6!important; padding: 0 15px;">Product Number</th>
                                    <th  style="border: 1px solid #dee2e6!important; padding: 0 15px;">Qty</th>
                                    <th  style="border: 1px solid #dee2e6!important; padding: 0 15px;">Price</th>
                                    <th  style="border: 1px solid #dee2e6!important; padding: 0 15px;">Total Amount</th>

                                </tr>
                            </thead>
                            <tbody>


                                @foreach ($order->orderdetails as $key => $orderdetail)
                                <tr style="border-bottom:1px solid #dee2e6 ; border-right:1px solid #dee2e6 ; border-left:1px solid #dee2e6 ;">
                                    <td style="border: 1px solid #dee2e6!important; padding: 1px 10px;text-align:center">{{ $key + 1 }}</td>
                                    <td style="border: 1px solid #dee2e6!important; padding: 1px 10px;">@if ($order->partnoshow == 1){{ $orderdetail->product->part_no }} @endif  </td>
                                    <td style="border: 1px solid #dee2e6!important; padding: 1px 10px;">{{ $orderdetail->product->productname }} </td>
                                    <td style="border: 1px solid #dee2e6!important; padding: 1px 10px;text-align:center">{{ $orderdetail->quantity }}</td>
                                    <td style="border: 1px solid #dee2e6!important; padding: 1px 10px;text-align:right">{{ number_format($orderdetail->sellingprice, 2) }}</td>
                                    <td style="border: 1px solid #dee2e6!important; padding: 1px 10px;text-align:right">{{ number_format($orderdetail->total_amount, 2) }}</td>
                                </tr>
                                @endforeach

                            </tbody>
                            <tfoot  style="border :1px solid #dee2e6 ; width: 100%; ">
                                <tr>
                                    <td colspan="3" rowspan="7" style="padding-left: 25px;">
                                      <b>  </i></b>
                                    </td>
                                    <td colspan="2" class="" style="border :1px solid #dee2e6 ;">
                                        <span class="float-start"><b>Total w/o discount:</b></span>
                                    </td>
                                    <td colspan="1" class="" style="border :1px solid #dee2e6; text-align:right">
                                        <span class="float-end">{{ number_format($order->grand_total, 2) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="" style="border :1px solid #dee2e6 ;">
                                        <span class="float-start"><b>Discount:</b></span>
                                    </td>
                                    
                                    <td colspan="1" class="" style="border :1px solid #dee2e6; text-align:right">
                                        <span class="float-end">{{ number_format($order->discount_amount, 2) }}</span>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td colspan="2" class="" style="border :1px solid #dee2e6 ;">
                                        <span class="float-start"><b>Vat:{{ $order->vatpercentage }}%</b></span>
                                    </td>
                                    
                                    <td colspan="1" class="" style="border :1px solid #dee2e6; text-align:right">
                                        <span class="float-end">{{ number_format($order->vatamount, 2) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="" style="border :1px solid #dee2e6 ;">
                                        <span class="float-start"> <b>Total with vat:</b> </span>
                                    </td>
                                    
                                    <td colspan="1" class="" style="border :1px solid #dee2e6; text-align:right">
                                        <span class="float-end">{{ number_format($order->net_total, 2) }}</span>
                                    </td>
                                </tr>

                                @if ($order->sales_status == 1)
                                <tr>
                                    <td colspan="2" class="" style="border :1px solid #dee2e6 ;">
                                        <span class="float-start"> <b>Customer paid:</b> </span>
                                    </td>
                                    
                                    <td colspan="1" class="" style="border :1px solid #dee2e6; text-align:right">
                                        <span class="float-end">{{ number_format($order->customer_paid, 2) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="" style="border :1px solid #dee2e6 ;">
                                        <span class="float-start"> <b>Due:</b> </span>
                                    </td>
                                    
                                    <td colspan="1" class="" style="border :1px solid #dee2e6; text-align:right">
                                        <span class="float-end">{{ number_format($order->due, 2) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="" style="border :1px solid #dee2e6 ;">
                                        <span class="float-start"> <b>Return Amount:</b> </span>
                                    </td>
                                    
                                    <td colspan="1" class="" style="border :1px solid #dee2e6; text-align:right">
                                        <span class="float-end">{{ number_format($order->return_amount, 2) }}</span>
                                    </td>
                                </tr>
                                @endif

                                



                            </tfoot>
                        </table>
                    </div>
                    <br>
                    <br>
                    <div class="row my-5" style="display: flex;">


                        @if ( $order->quotation == 1 )

                            <table style="width: 100%;">
                                <tr>
                                    <td class="" style="border :0px solid #dee2e6 ;">
                                        <div class="col-lg-5" style="flex: 2;">
                                            <u>Terms and conditions </u><br>
                                            <i>Validity : </i><br>
                                            <i>Delivery : </i><br>
                                            <i>Payment : </i><br>
                                            <i>Other terms and conditions : </i>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                        @elseif ($order->delivery_note == 1)
                            <table style="width: 100%;">
                                <tr>
                                    <td colspan="2" class="" style="border :0px solid #dee2e6 ;">
                                        <div class="col-lg-5" style="flex: 2;">
                                            <i>Received by : Signature & stamp</i>
                                        </div>
                                    </td>
                                    <td colspan="2" class="" style="border :0px solid #dee2e6 ;">
                                        <div class="col-lg-5" style="flex: 1;"></div>
                                    </td>
                                    <td colspan="2" class="" style="border :0px solid #dee2e6 ;">
                                        <div class="col-lg-2 text-end" style="flex: 2; text-align: right;">
                                            <span for="" style="padding-right: 30px">{{\App\Models\User::where('id', $order->created_by)->first()->name}}</span><br>
                                            Salesman Signature
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        @else
                            <table style="width: 100%;">
                                <tr>
                                    <td colspan="2" class="" style="border :0px solid #dee2e6 ;">
                                        <div class="col-lg-5" style="flex: 2;">
                                            <i>Received by : Signature & stamp</i>
                                        </div>
                                    </td>
                                    <td colspan="2" class="" style="border :0px solid #dee2e6 ;">
                                        <div class="col-lg-5" style="flex: 1;"></div>
                                    </td>
                                    <td colspan="2" class="" style="border :0px solid #dee2e6 ;">
                                        <div class="col-lg-2 text-end" style="flex: 2; text-align: right;">
                                            <span for="" style="padding-right: 30px">{{\App\Models\User::where('id', $order->created_by)->first()->name}}</span><br>
                                            Salesman Signature
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        @endif

                        

                    </div>
                </div>
            </div>
            {{-- <div  style="margin-top: 15px; display: flex;align-items: center;justify-content: center;background-color: #FF9A38;">
                <h4 class="mb-0 text-white" style="color: white; text-align: center;">Musaffa M-9 Abudhabi UAE</h4>
            </div> --}}
        </div>
    </section>


</body>
</html>

