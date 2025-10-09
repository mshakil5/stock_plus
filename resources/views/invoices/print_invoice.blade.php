<!DOCTYPE html>
<html lang="en">
<head>
  @php
    $company = \App\Models\CompanyDetails::first();
    // Controls how many rows fit per page without spilling
    $rowsPerPage = 17; // adjust to 16/18/19 if needed
    $items = $order->orderdetails->values(); // ensure zero-based indexing
    $pages = $items->chunk($rowsPerPage);
  @endphp
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $company->company_name }} - Invoice - {{$order->id}}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body { font-family: Arial, sans-serif; font-size: 14px; color:#000; }
    .page { page-break-after: always; }
    .page:last-child { page-break-after: auto; }

    .invoice-box {
      position: relative;
      padding: 20px;
      margin: 0 auto;
      width: 950px;     /* tune if needed */
      background: #fff;
      padding-bottom: 120px; /* reserve space for bottom area (signature/totals) */
    }

    .invoice-title { font-weight: bold; font-size: 20px; text-align: center; }
    .invoice-subtitle { font-weight: bold; font-size: 16px; text-align: center; padding-right: 200px; }

    .custom-table {
      width: 100%;
      border-collapse: collapse;
      border: 1px solid #000;
      table-layout: fixed; /* helps consistent layout */
    }
    .custom-table th, .custom-table td {
      padding: 6px;
      text-align: center;
      border-left: 1px solid #000;
      border-right: 1px solid #000;
      vertical-align: middle;
      word-wrap: break-word;
      overflow-wrap: anywhere;
    }
    .custom-table th { border-bottom: 1px solid #000; }
    .custom-table thead th { font-weight: 700; }

    /* Row/border logic: only last row gets the bottom border */
    .custom-table tbody tr td { border-top: none; border-bottom: none; height: 28px; }
    .custom-table tbody tr:last-child td { border-bottom: 1px solid #000; }
    .custom-table tbody tr.filler td { height: 28px; }

    .outer-border {
      border: 1px solid #000;
      padding: 8px;
      margin: 15px 0;
    }

    .signature {
      position: absolute;
      right: 20px;
      bottom: 60px;
      text-align: right;
      font-size: 14px;
    }

    .footer-note {
      position: absolute;
      bottom: 10px; /* adjust space from bottom */
      left: 10px;
      right: 10px;
      font-size: 14px;
      text-align: center;
      page-break-inside: avoid;
      color: #F74B00;
      font-weight: bold;
    }


    .invoice-header {
      padding: 10px; /* optional */
      margin-left: -15px; /* adjust based on bootstrap container */
      margin-right: -15px;
      
    }

    .invoice-header p {
      font-size: 18px;
      color: black;
      font-weight: bold;

    }

    /* image center align */
    .invoice-header img{
      display: block;
      margin-left: 14px;
      margin-right: auto;
    }

    @media print {
      @page { size: A4 portrait; margin: 6mm; }
      thead { display: table-header-group; } /* safe repeat (also we chunk pages) */
      .outer-border, .totals, .footer, .no-break { page-break-inside: avoid; }
      tr, img { page-break-inside: avoid; }
      .invoice-box { margin: 0 auto; }
    }
  </style>
</head>
<body>

@php $totalQtyAll = 0; @endphp

@foreach ($pages as $pageIndex => $pageItems)
  @php
    $isLast = ($pageIndex === $pages->count() - 1);
    $renderedRows = $pageItems->count();
    $padRows = max(0, $rowsPerPage - $renderedRows);
  @endphp

  <div class="page">
    <div class="invoice-box">
      {{-- Header --}}
      <div class="row mb-3">
        <div class="col-12 text-center  invoice-header">
          <img src="{{asset('touch.png')}}" alt="" width="100%" height="120">
          <p class="mb-0">{{ $company->address1 }}</p>
          <p class="mb-0">
            @if ($company->phone1)
              Tel: {{ $company->phone1 }}@if ($company->phone2),@endif
            @endif
            @if ($company->phone2)
              {{ $company->phone2 }}
            @endif
          </p>

          @if ($company->website)
            <p class="mb-0">Website: {{ $company->website }}</p>
          @endif
          @if ($company->email1)
            <p>Email: {{ $company->email1 }}</p>
          @endif
        </div>

        <div class="col-12 text-center">

          <br>
          @if ($order->quotation == 1)
            <h3 class="invoice-title">QUOTATION</h3>
          @elseif ($order->delivery_note == 1)
            <h3 class="invoice-title">DELIVERY NOTE</h3>
          @else
            <h3 class="invoice-title">TAX INVOICE</h3>
          @endif
          <h3 class="invoice-subtitle">TRN:</h3>
        </div>
      </div>

      {{-- Customer --}}
      <div class="row mb-3">
        <div class="col-12"><strong>To:</strong> {{ $customerdtl->name ?? '' }}</div>
        <div class="col-12"><strong>Address:</strong> {{ $customerdtl->address ?? '' }}</div>
        <div class="col-12"><strong>Tel:</strong> {{ $customerdtl->phone ?? '' }}</div>
      </div>

      {{-- Invoice meta --}}
      <div class="outer-border no-break">
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

      {{-- Table --}}
      <table class="custom-table">
        <thead>
          <tr>
            <th style="width: 6%">Sl No</th>
            @if ($order->partnoshow == 1)
              <th>Part No</th>
            @endif
            <th class="text-center">Description</th>
            <th style="width: 7%">Qty</th>
            <th style="width: 10%">Unit Price</th>
            <th style="width: 10%">Total (Excl. vat)</th>
            <th style="width: 7%">Vat%</th>
            <th style="width: 7%">Vat Amt</th>
            <th>Total (Incl. vat)</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($pageItems as $i => $item)
            @php
              $globalIndex = $pageIndex * $rowsPerPage + $i; // zero-based
              $totalQtyAll += $item->quantity;
            @endphp
            <tr>
              <td style="border-bottom: 1px solid #000;">{{ $globalIndex + 1 }}</td>
              @if ($order->partnoshow == 1)
                <td style="border-bottom: 1px solid #000;" class="text-start">{{ $item->product->part_no }}</td>
              @endif
              <td style="border-bottom: 1px solid #000;" class="text-start">{{ $item->product->productname }}</td>
              <td style="border-bottom: 1px solid #000;" class="text-end">{{ $item->quantity }}</td>
              <td style="border-bottom: 1px solid #000;" class="text-end">{{ number_format($item->sellingprice, 2) }}</td>
              <td style="border-bottom: 1px solid #000;" class="text-end">{{ number_format($item->subtotal_excl_vat, 2) }}</td>
              <td style="border-bottom: 1px solid #000;" class="text-center">{{ number_format($item->vat_percent, 0) }}</td>
              <td style="border-bottom: 1px solid #000;" class="text-end">{{ number_format($item->vat_amount, 2) }}</td>
              <td style="border-bottom: 1px solid #000;" class="text-end">{{ number_format($item->total_amount, 2) }}</td>
            </tr>
          @endforeach

          {{-- Filler rows to push bottom border down on every page --}}
          @for ($j = 0; $j < $padRows; $j++)
            <tr class="filler">
              <td>&nbsp;</td>
              @if ($order->partnoshow == 1)
                <td>&nbsp;</td>
              @endif
              <td class="text-start">&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td class="text-end">&nbsp;</td>
            </tr>
          @endfor
        </tbody>
      </table>

      {{-- Totals + amount in words (ONLY on last page) --}}
      @if ($isLast)
        <div class="outer-border mt-2 p-2 no-break">
          <div class="row">
            <div class="col-3"></div>
            <div class="col-3 text-end">Total Qty: {{ $totalQtyAll }}</div>
            <div class="col-3"></div>
            <div class="col-2 text-start"><strong>Gross Amount:</strong></div>
            <div class="col-1 text-end" style="padding-right: 6px;">
              <strong>{{ number_format($order->grand_total, 2) }}</strong>
            </div>
          </div>

          <div class="row">
            <div class="col-9"></div>
            <div class="col-2 text-start"><strong>VAT Amount:</strong></div>
            <div class="col-1 text-end" style="padding-right: 6px;">
              <strong>{{ is_numeric($order->vatamount) ? number_format($order->vatamount, 2) : '0.00' }}</strong>
            </div>
          </div>

          @php
            $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
            $whole = floor($order->net_total);
            $decimal = round(($order->net_total - $whole) * 100);
            $amountInWords = ucfirst(str_replace('-', ' ', $f->format($whole)));
            $decimalInWords = str_replace('-', ' ', $f->format($decimal));
            if($decimal > 0){
                $amountInWords .= ' and ' . $decimalInWords . ' Fils';
            }
          @endphp

          <div class="row">
            <div class="col-9">{{ $amountInWords }} Only</div>
            <div class="col-2 text-start"><strong>Net Amount:</strong></div>
            <div class="col-1 text-end" style="padding-right: 6px;">
              <strong>{{ number_format($order->net_total, 2) }}</strong>
            </div>
          </div>
        </div>

        {{-- Signature (right-aligned, no "Authorised Dealer") --}}
        <div class="signature">
          Received By: ___________________
        </div>
      @endif

    </div> <!-- /.invoice-box -->
    <div class="footer-note">
      <br>
      <p>Our Services: Mechanics | Electrical Repair | Ac Working | Computer Checking | Oil Change | Suspension Work</p>
    </div>
  </div> <!-- /.page -->
@endforeach

<script>
  window.onload = function() { window.print(); };
</script>
</body>
</html>
