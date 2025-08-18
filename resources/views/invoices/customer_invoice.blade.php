<!DOCTYPE html>
<html lang="en">
<head>
  @php
    $company = \App\Models\CompanyDetails::first();
  @endphp
  <meta charset="UTF-8">
  <title>{{ $company->company_name }} Invoice</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    .invoice-box { padding: 15px; width: 100%; }
    .invoice-title { font-weight: bold; font-size: 18px; text-align: right; }

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

    .outer-border { border: 1px solid #000; padding: 6px; margin: 10px 0; }
    .text-end { text-align: right; }
    .text-start { text-align: left; }
    .fw-bold { font-weight: bold; }
    .footer {
      position: fixed;
      bottom: 20px;
      left: 0;
      width: 100%;
      text-align: left;
      font-size: 12px;
    }
  </style>
</head>
<body>
  <div class="invoice-box">
    <div style="width:100%;">
      <div style="float:left; width:50%;">
        <h4 class="fw-bold">{{ $company->company_name }}</h4>
        <p>{{ $company->address1 }}</p>
        @if ($company->phone1) <p>Tel: {{ $company->phone1 }}</p> @endif
        @if ($company->phone2) <p>Office: {{ $company->phone2 }}</p> @endif
        @if ($company->email1) <p>Email: {{ $company->email1 }}</p> @endif
      </div>
      <div style="float:right; width:50%; text-align:right;">
        @if ($order->quotation == 1)
          <h3 class="invoice-title">QUOTATION</h3>
        @elseif ($order->delivery_note == 1)
          <h3 class="invoice-title">DELIVERY NOTE</h3>
        @else
          <h3 class="invoice-title">TAX CASH INVOICE</h3>
        @endif
        <p>TRN: 100474976600003</p>
      </div>
    </div>

    <div style="clear:both; margin-top:10px;">
      <p><strong>To:</strong> {{ $customerdtl->name ?? '' }}</p>
      <p><strong>Address:</strong> {{ $customerdtl->address ?? '' }}</p>
      <p><strong>Tel:</strong> {{ $customerdtl->phone ?? '' }}</p>
    </div>

    <div class="outer-border">
      <table style="width:100%;">
        <tr>
          <td><strong>Invoice No:</strong> {{ $order->invoiceno }}</td>
          <td><strong>Date:</strong> {{ \Carbon\Carbon::parse($order->orderdate)->format('d-m-Y') }}</td>
          <td class="text-end"><strong>Salesman:</strong> {{ \App\Models\User::where('id', $order->created_by)->value('name') }}</td>
        </tr>
      </table>
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
      <table style="width:100%; border-collapse: collapse;">
        <tr>
          <td style="width:40%;"></td>
          <td style="width:30%; text-align:left;">
            Total Qty: {{ $totalQty }}
          </td>
          <td style="width:20%; text-align:left;">
            <strong>Gross Amount:</strong>
          </td>
          <td style="width:10%; text-align:right;">
            {{ number_format($order->grand_total, 2) }}
          </td>
        </tr>
        <tr>
          <td colspan="2"></td>
          <td style="text-align:left;">
            <strong>VAT {{ is_numeric($order->vatpercentage) ? $order->vatpercentage : 0 }}%:</strong>
          </td>
          <td style="text-align:right;">
            {{ is_numeric($order->vatamount) ? number_format($order->vatamount, 2) : '0.00' }}
          </td>
        </tr>
        @php
          $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
          $whole = floor($order->net_total);
          $decimal = round(($order->net_total - $whole) * 100);
          $amountInWords = ucfirst($f->format($whole));
          if($decimal > 0){
              $amountInWords .= ' and ' . $decimal . '/100';
          }
        @endphp
        <tr>
          <td colspan="2">{{ $amountInWords }} Only</td>
          <td style="text-align:left;"><strong>Net Amount:</strong></td>
          <td style="text-align:right;">{{ number_format($order->net_total, 2) }}</td>
        </tr>
      </table>
    </div>

    <div class="footer">
      <p>Received By: ___________________</p>
      <p>Authorised Dealer</p>
    </div>
  </div>
</body>
</html>