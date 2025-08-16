<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 14px; background: #f8f9fa; }
        .invoice-container {
            width: 900px;
            margin: auto;
            background: white;
            padding: 20px;
            border: 1px solid black;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        table, th, td {
            border: 1px solid black !important;
        }
        table td {
            min-width: 140px;
            padding: 4px 8px;
        }
        th, td {
            padding: 5px;
            vertical-align: middle;
        }
        .work-list td {
            height: 35px;
        }
        .bordered {
            border: 1px solid black;
        }
        @media print {
            @page {
                margin: 0;
            }
            body {
                margin: 1.6cm;
            }
            .invoice-container {
                border: none;
                padding: 0;
                width: 100%;
            }
        }
    </style>
    <script>
        setTimeout(function () {
            window.print();
        }, 800);
    </script>
</head>
<body>

  @php
    $company = \App\Models\CompanyDetails::first();
  @endphp

<div class="invoice-container">

    <div class="row text-center align-items-center">
        <div class="col-4 text-start fw-bold">
            {{ $company->company_name }} <br>
            <small>{{ $company->address1 }}</small>
        </div>
        <div class="col-4">
            @if ($order->quotation == 1)
                <h3 style="font-size: 1.5rem; margin-bottom: 5px;">QUOTATION</h3>
            @elseif ($order->delivery_note == 1)
                <h3 style="font-size: 1.5rem; margin-bottom: 5px;">DELIVERY NOTE</h3>
            @else
                <h3 style="font-size: 1.5rem; margin-bottom: 5px;">TAX CASH INVOICE</h3>
            @endif
        </div>
        <div class="col-4 text-start fw-bold">
            TRN: 100474976600003<br>
            @if ($order->quotation == 1)
                QTN No: 000{{ $order->id }}<br>
            @elseif ($order->delivery_note == 1)
                D/N NO: {{ $order->id }}<br>
            @endif
            INV NO: {{ $order->invoiceno }}<br>
            Date: {{ \Carbon\Carbon::parse($order->orderdate)->format('d-m-Y') }}
        </div>
    </div>

    <table class="mt-2">
        <tbody>
            <tr>
                <th colspan="2">CUSTOMER INFORMATION</th>
                <th colspan="4">ORDER INFORMATION</th>
            </tr>
            <tr>
                <td class="fw-bold">NAME</td>
                <td>{{ $customerdtl->name }}</td>
                <td class="fw-bold">REFERENCE</td>
                <td>{{ $order->ref }}</td>
                <td class="fw-bold">SALESMAN</td>
                <td>{{\App\Models\User::where('id', $order->created_by)->first()->name}}</td>
            </tr>
            <tr>
                <td class="fw-bold">ADDRESS</td>
                <td>{{ $customerdtl->address }}</td>
                <td class="fw-bold">Q/N NO</td>
                <td>{{ $order->qn_no }}</td>
                <td class="fw-bold">CONTACT</td>
                <td>{{\App\Models\User::where('id', $order->created_by)->first()->phone}}</td>
            </tr>
            <tr>
                <td class="fw-bold">MOBILE</td>
                <td>{{ $customerdtl->phone }}</td>
                <td class="fw-bold">D/N NO</td>
                <td>{{ $order->dn_no }}</td>
                <td class="fw-bold">LPO NO</td>
                <td>{{ $order->lpo_no ?? 'N/A' }}</td>
            </tr>
        </tbody>
    </table>

    <table class="mt-4 work-list">
        <thead>
            <tr>
                <th style="width: 5%;">NO.</th>
                <th>DESCRIPTION</th>
                @if ($order->partnoshow == 1)
                <th style="width: 15%;">PART NO</th>
                @endif
                <th style="width: 10%;">QTY.</th>
                <th style="width: 15%;">PRICE</th>
                <th style="width: 15%;">AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->orderdetails as $key => $orderdetail)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $orderdetail->product->productname }}</td>
                @if ($order->partnoshow == 1)
                <td>{{ $orderdetail->product->part_no }}</td>
                @endif
                <td>{{ $orderdetail->quantity }}</td>
                <td>{{ number_format($orderdetail->sellingprice, 2) }}</td>
                <td>{{ number_format($orderdetail->total_amount, 2) }}</td>
            </tr>
            @endforeach
            
            @for ($i = count($order->orderdetails); $i < 8; $i++)
            <tr><td></td><td></td>@if ($order->partnoshow == 1)<td></td>@endif<td></td><td></td><td></td></tr>
            @endfor
        </tbody>
    </table>

    <table class="mt-2">
        <tr>
            <td colspan="{{ $order->partnoshow == 1 ? 4 : 3 }}" class="fw-bold">Total w/o discount:</td>
            <td class="text-end">{{ number_format($order->grand_total, 2) }}</td>
        </tr>
        <tr>
            <td colspan="{{ $order->partnoshow == 1 ? 4 : 3 }}" class="fw-bold">Discount:</td>
            <td class="text-end">{{ number_format($order->discount_amount, 2) }}</td>
        </tr>
        <tr>
            <td colspan="{{ $order->partnoshow == 1 ? 4 : 3 }}" class="fw-bold">Vat ({{ $order->vatpercentage }}%):</td>
            <td class="text-end">{{ number_format($order->vatamount, 2) }}</td>
        </tr>
        <tr>
            <td colspan="{{ $order->partnoshow == 1 ? 4 : 3 }}" class="fw-bold">Total with vat:</td>
            <td class="text-end">{{ number_format($order->net_total, 2) }}</td>
        </tr>
        @if ($order->sales_status == 1)
        <tr>
            <td colspan="{{ $order->partnoshow == 1 ? 4 : 3 }}" class="fw-bold">Customer paid:</td>
            <td class="text-end">{{ number_format($order->customer_paid, 2) }}</td>
        </tr>
        <tr>
            <td colspan="{{ $order->partnoshow == 1 ? 4 : 3 }}" class="fw-bold">Due:</td>
            <td class="text-end">{{ number_format($order->due, 2) }}</td>
        </tr>
        <tr>
            <td colspan="{{ $order->partnoshow == 1 ? 4 : 3 }}" class="fw-bold">Return Amount:</td>
            <td class="text-end">{{ number_format($order->return_amount, 2) }}</td>
        </tr>
        @endif
    </table>

    @if ($order->quotation == 1)
    <div class="mt-2">
        <strong>TERMS AND CONDITIONS:</strong>
        <div class="bordered p-2" style="min-height:50px;">
            <i>Validity : </i><br>
            <i>Delivery : </i><br>
            <i>Payment : </i><br>
            <i>Other terms and conditions : </i>
        </div>
    </div>
    @endif

    <table class="mt-4">
        <tr>
            <td style="width: 40%;">
                <strong>Received by :</strong><br>
                <div style="height: 40px;"></div>
                <span>Signature & stamp</span>
            </td>
            <td style="width: 20%;"></td>
            <td style="width: 40%; text-align: right;">
                <strong>Salesman Signature:</strong><br>
                <div style="height: 40px;"></div>
                <span>{{\App\Models\User::where('id', $order->created_by)->first()->name}}</span>
            </td>
        </tr>
    </table>

</div>

</body>
</html>