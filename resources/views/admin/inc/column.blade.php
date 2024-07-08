









<a href="{{ route('sales.return', $order->id)}}" class="btn btn-sm btn-success ms-1"><span title="Return" >Return</span></a>
<a href="{{ route('sales.edit', $order->id)}}" class="btn btn-sm btn-theme ms-1"><span title="Edit" >Edit</span></a>
<a href="{{ route('customer.invoice.download', $order->id)}}" class="btn btn-sm btn-theme ms-1"><span title="Download Invoice" >Download</span></a>
