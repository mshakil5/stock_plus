@extends('admin.layouts.master')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}" />

@if (session('message'))
<div class="alert alert-success">
    {{ session('message') }}
</div>
@endif

@if (session('info'))
<div class="alert alert-danger">
    {{ session('info') }}
</div>
@endif

<div class="container-fluid">
    <div class="display-item">
        <div class="row">
            <div class="col-md-12">
                @component('components.widget')
                @slot('title')
                Manage Sales Return Invoice
                @endslot
                @slot('body')
                <table class="table table-striped table-hover" id="returnInvoiceTable"> 
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Invoice ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Reason</th>
                            <th>Action</th>
                        </tr>
                    </thead>                                  
                    <tbody>
                        @foreach ($invoices as $key => $item)
                            <tr>
                                <th>{{ $key + 1 }}</th>
                                <td>{{ $item->invoiceno }}</td> 
                                <td>{{ \Carbon\Carbon::parse($item->returndate)->format('d-m-Y') }}</td>
                                <td>{{ $item->customer->name }}</td>
                                <td>{{ $item->reason }}</td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#salesReturnDetailsModal" data-id="{{ $item->id }}">View Details</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @endslot
                @endcomponent
            </div>
        </div>
    </div>
</div>

<!-- Sales Return Details Modal -->
<div class="modal fade" id="salesReturnDetailsModal" tabindex="-1" role="dialog" aria-labelledby="salesReturnDetailsModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="salesReturnDetailsModalLabel">Sales Return Details</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-hover table-condensed">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                            <tbody id="salesReturnDetailsBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    $(document).ready( function () {
        $('#returnInvoiceTable').DataTable();
    } );
</script>

<script>
    $(document).ready(function() {
        $('#salesReturnDetailsModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var salesReturnId = button.data('id');

            var modal = $(this);

            $.ajax({
                type: "GET",
                url: '{{ url("sales-return-detail") }}/' + salesReturnId,
                
                success: function(data) {
                    var tableBody = modal.find('#salesReturnDetailsBody');
                    tableBody.empty();

                    $.each(data, function(index, item) {
                        tableBody.append(
                            '<tr>' +
                                '<td>' + item.id + '</td>' +
                                '<td>' + item.productname + '</td>' +
                                '<td>' + item.quantity + '</td>' +
                                '<td>' + item.selling_price + '</td>' +
                                '<td>' + item.total_amount + '</td>' +
                            '</tr>'
                        );
                    });
                },
                error: function(err) {
                    console.error("Error fetching sales return details", err);
                }
            });
        });
    });
</script>

@endsection