<!DOCTYPE html>
<html lang="en">
<head>

    @php
      $company = \App\Models\CompanyDetails::first();
    @endphp
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $company->company_name }}Invoice</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { font-family: Arial, sans-serif; font-size: 14px; }
    .invoice-box {
      padding: 20px;
      margin: 20px auto;
      width: 900px;
      background: #fff;
    }
    .invoice-title { font-weight: bold; font-size: 20px; text-align: right; }
    
    .custom-table {
      width: 100%;
      border-collapse: collapse;
      border: 1px solid #000;
    }
    .custom-table th, .custom-table td {
      padding: 6px;
      text-align: center;
      border-left: 1px solid #000;
      border-right: 1px solid #000;
    }
    .custom-table th {
      border-bottom: 1px solid #000;
    }
    .custom-table tbody tr td {
      border-top: none;
      border-bottom: none;
    }
    .custom-table tbody tr:last-child td {
      border-bottom: 1px solid #000;
    }

    .outer-border {
      border: 1px solid #000;
      padding: 8px;
      margin: 15px 0;
    }
    .totals p { margin: 0; }
    .footer { font-size: 12px; margin-top: 20px; text-align: center; }
  </style>
</head>
<body>
  <div class="invoice-box">
    <div class="row">
      <div class="col-6">
        <h6 class="fw-bold mb-1">{{ $company->company_name }} <br>
          تاتش فيمس إصلاح كهرباء السيارات - ذ.م.م - ش.ش.و
        </h6>
        <p class="mb-0">{{ $company->address1 }}</p>
        @if ($company->phone1) 
        <p class="mb-0">Tel: {{ $company->phone1 ?? '' }}</p>
        @endif
        @if ($company->phone2) 
        <p class="mb-0">Office: {{ $company->phone2 ?? '' }}</p>
        @endif
        @if ($company->email1)
        <p>Email: {{ $company->email1 ?? '' }}</p>
        @endif
      </div>
      <div class="col-6 text-end">
        @if ($order->quotation == 1)
        <h3 class="invoice-title">QUOTATION</h3>
        @elseif ($order->delivery_note == 1)
        <h3 class="invoice-title">DELIVERY NOTE</h3>
        @else
        <h3 class="invoice-title">TAX CASH INVOICE</h3>
        @endif
        <p class="mb-0">TRN: 100474976600003</p>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-12"><strong>To:</strong> {{ $customerdtl->name ?? '' }}</div>
      <div class="col-12"><strong>Address:</strong> {{ $customerdtl->address ?? '' }}</div>
      <div class="col-12"><strong>Tel:</strong> {{ $customerdtl->phone ?? '' }}</div>
    </div>

    <div class="outer-border">
      <div class="row">
        @if ($order->quotation == 1 || $order->delivery_note == 1)
          <div class="col-3">
            @if ($order->quotation == 1)
              <strong>QTN No:</strong> 000{{ $order->id }}
            @elseif ($order->delivery_note == 1)
              <strong>D/N No:</strong> {{ $order->id }}
            @endif
          </div>
          <div class="col-3"><strong>Invoice No:</strong> {{ $order->invoiceno }}</div>
          <div class="col-3"><strong>Date:</strong> {{ \Carbon\Carbon::parse($order->orderdate)->format('d-m-Y') }}</div>
          <div class="col-3 text-end"><strong>Salesman:</strong> {{ \App\Models\User::where('id', $order->created_by)->value('name') }}</div>
        @else
          <div class="col-4"><strong>Invoice No:</strong> {{ $order->invoiceno }}</div>
          <div class="col-4"><strong>Date:</strong> {{ \Carbon\Carbon::parse($order->orderdate)->format('d-m-Y') }}</div>
          <div class="col-4 text-end"><strong>Salesman:</strong> {{ \App\Models\User::where('id', $order->created_by)->value('name') }}</div>
        @endif
      </div>
    </div>

    <table class="custom-table">
      <thead>
        <tr>
          <th>Sl No</th>
          @if ($order->partnoshow == 1)
            <th>Part No</th>
          @endif
          <th class="text-start">Description</th>
          <th>Qty</th>
          <th>Unit Price</th>
          <th>TotalExcl</th>
          <th>Vat%</th>
          <th>Vat Amt</th>
          <th>TotalIncl</th>
        </tr>
      </thead>
      <tbody>
        @php
          $totalQty = 0;
        @endphp
        @foreach ($order->orderdetails as $index => $item)
        @php
          $totalQty += $item->quantity;
        @endphp
          <tr>
            <td>{{ $index + 1 }}</td>
            @if ($order->partnoshow == 1)
              <td>{{ $item->product->part_no }}</td>
            @endif
            <td class="text-start">{{ $item->product->productname }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format($item->sellingprice, 2) }}</td>
            <td>{{ number_format($item->subtotal_excl_vat, 2) }}</td>
            <td>{{ number_format($item->vat_percent, 0) }}</td>
            <td>{{ number_format($item->vat_amount, 2) }}</td>
            <td>{{ number_format($item->total_amount, 2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div class="outer-border mt-2 p-2">
      <div class="row">
        <div class="col-4">
        </div>
        <div class="col-3 text-end">
          Total Qty: {{ $totalQty }}
        </div>
        <div class="col-2 text-start">
          <strong>Gross Amount:</strong> 
        </div>
        <div class="col-2 text-end">
          {{ number_format($order->grand_total, 2) }}
        </div>
      </div>
      <div class="row">
        <div class="col-9">
        </div>
        <div class="col-2 text-start">
          <strong>VAT {{ is_numeric($order->vatpercentage) ? $order->vatpercentage : 0 }}%:</strong> 
        </div>
        <div class="col-1 text-end">
            {{ is_numeric($order->vatamount) ? number_format($order->vatamount, 2) : '0.00' }}
        </div>
      </div>
      @php
        $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);

        $whole = floor($order->net_total);
        $decimal = round(($order->net_total - $whole) * 100);

        $amountInWords = ucfirst($f->format($whole));

        if($decimal > 0){
            $amountInWords .= ' and ' . $decimal . '/100';
        }
      @endphp
      <div class="row">
        <div class="col-9">{{ $amountInWords }} Only</div>
        <div class="col-2 text-start"><strong>Net Amount:</strong> </div>
        <div class="col-1 text-end">{{ number_format($order->net_total, 2) }}</div>
      </div>
    </div>

    <div class="row" style="position: absolute; bottom: 0; width: 100%;">
      <div class="col-1"></div>
      <div class="col-4 text start">
        Received By:  ___________________<br> <br> <br>
        Authorised Dealer
      </div>
    </div>
  </div>

  <script>
    window.onload = function() {
      window.print();
    };
  </script>

</body>
</html>