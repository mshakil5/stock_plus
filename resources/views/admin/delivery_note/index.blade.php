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
                Manage Delivery Note
                @endslot
                @slot('body')
                @component('components.table')
                @slot('tableID')
                allinvoiceTBL
                @endslot
                @slot('head')
                <th>ID</th>
                <th>Invoice ID</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Ref</th>
                <th>Due</th>
                <th>Part No Status</th>
                <th>Total</th>
                <th>Action</th>
                @endslot
                @endcomponent
                @endslot
                @endcomponent
            </div>
        </div>
    </div>
</div>

<!-- Modal for Product Details -->
<div class="modal fade" id="product-details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Invoice Details </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-hover table-condensed invoice-details">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Part No</th>
                                    <th scope="col">Product</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
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
    $(document).ready(function() {
        var allinvoiceTBL = $('#allinvoiceTBL').DataTable({
            processing: true,
            serverSide: true,
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<"dt-filter-spacer"f><ip>',
            buttons: [
                {
                    extend: 'copy',
                    text: 'Copy',
                    titleAttr: 'Copy',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'csvHtml5',
                    text: 'CSV',
                    titleAttr: 'Export to CSV',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    titleAttr: 'Export to Excel',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: 'PDF',
                    titleAttr: 'Export to PDF',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'print',
                    text: 'Print',
                    titleAttr: 'Print',
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function(win) {
                        $(win.document.body).find('table').addClass('display').css('font-size', '14px');
                        $(win.document.body).find('tr:nth-child(odd) td').each(function(index) {
                            $(this).css('background-color', '#D0D0D0');
                        });
                        $(win.document.body).find('h1').css('text-align', 'center');
                    }
                }
            ],
            ajax: {
                url: '{{ route("delivery-note-filter.admin") }}',
                data: function(d) {
                    // Add any additional filters here if necessary
                }
            },
            order: [[0, "desc"]],
            columns: [
                { data: 'id', name: 'id' },
                { data: 'invoiceno', name: 'invoiceno' },
                { data: 'orderdate', name: 'orderdate' },
                { data: 'customer_id', name: 'customer_id' },
                { data: 'ref', name: 'ref' },
                { data: 'due', name: 'due' },
                { data: 'partnoshow', name: 'partnoshow', render: function(data, type, row) {
                    let pub_partno = `<div class="form-check form-switch"><label class="form-check-label"><input class="form-check-input" type="checkbox" onclick='partno_status("unpublished-partno","${row.id}")' checked></label></div>`;
                    let unpub_partno = `<div class="form-check form-switch"><label class="form-check-label"><input class="form-check-input" type="checkbox" onclick='partno_status("published-partno","${row.id}")'></label></div>`;
                    return row.partnoshow == 1 ? pub_partno : unpub_partno;
                }},
                { data: 'net_total', name: 'net_total' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $('#product-details').on('show.bs.modal', function(event) {
            let id = $(event.relatedTarget).val();
            var modal = $(this);
            $.ajax({
                type: "GET",
                url: "{{URL::to('/admin/invoice')}}" + "/" + id,
                success: function(data) {
                    modal.find('.invoice-details tbody').empty();
                    $.each(data.orderdetails, function(i, orderdetail) {
                        modal.find('.invoice-details tbody').append(`
                            <tr>
                                <td>${orderdetail.id}</td>
                                <td>${orderdetail.product.part_no}</td>
                                <td>${orderdetail.product.productname}</td>
                                <td>${orderdetail.quantity}</td>
                                <td>${orderdetail.sellingprice}</td>
                                <td>${orderdetail.total_amount}</td>
                            </tr>
                        `);
                    });
                },
                error: function(err) {
                    console.log(err);
                }
            });
        });
    });

    var stsurl = "{{URL::to('/admin')}}";
    function partno_status(route, id) {
        $.ajax({
            url: stsurl + "/" + route + "/" + id,
            type: 'GET',
            beforeSend: function(request) {
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function(response) {
                $(".ermsg").html(response.message);
                pagetop();
            },
            error: function(err) {
                console.log(err);
                alert("Something Went Wrong, Please check again");
            }
        });
    }
</script>

@endsection