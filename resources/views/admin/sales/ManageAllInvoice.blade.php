
@extends('admin.layouts.master')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}"/>
    <?php
    $user_id = Session::get('categoryEmployId');
    ?>
    {{-- <div id="loader"></div> --}}
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
    <?php
    echo Session::put('message', '');
    ?>
    @if (session('info'))
        <div class="alert alert-danger">
            {{ session('info') }}
        </div>
    @endif
    <?php
    // echo Session::put('info', '');
    ?>
    <div class="">
        <div class="container-fluid">
            <div class="display-item">
                <div class="row">
                    <div class="col-md-12">
                        @component('components.widget')
                            @slot('title')
                            All Invoices
                            @endslot
                            @slot('description')

                            @endslot
                            @slot('body')
                                @component('components.table')
                                    @slot('tableID')
                                        allinvoiceTBL
                                    @endslot
                                    @slot('head')
                                        <th>Id</th>
                                        <th>Invoice Id</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Ref</th>
                                        <th>Due</th>
                                        <th>Total</th>
                                        <th>Part No Status</th>
                                        <th><i class=""></i> Action</th>
                                    @endslot
                                @endcomponent
                            @endslot
                        @endcomponent
                    </div>

                    
                    <div class="col-md-4" style="display: none">
                        <div class="box-inner">
                            <div class="alert alert-success">
                                <strong>Filter Data: </strong>
                            </div>
                            <div class="box-content">
                                <label for="brand"> Select Branch</label>
                                <select name="" class="custom-select select2" id="branchdropdown">

                                </select>
                            </div>
                            <div class="box-content">
                                <label for="brand"> Paid Status</label>
                                <select name="" class="custom-select select2" id="statusdropdown">
                                    <option value="">Any</option>
                                    <option value="1">Full</option>
                                    <option value="2">Due</option>
                                </select>
                            </div>
                            <div class="box-content">
                                <label for="brand"> Choose Date</label>
                                <div class="input-group date" data-provide="datepicker" data-date-format="dd M yyyy">
                                    <input type="text" class="form-control date2">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                </div>
            </div>

        </div>
    </div>


    <!-- product details Modal -->
<div class="modal fade" id="product-details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Invoice Details </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
                            <ul id="myTabs" class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#details" id="details-tab" role="tab" data-toggle="tab" aria-controls="details" aria-expanded="true">Details</a></li>
                            </ul>
                            <div id="myTabContent" class="tab-content">
                                <div role="tabpanel" class="tab-pane fade active in" id="details"
                                     aria-labelledby="details-tab">
                                    &nbsp;
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
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-close"></i>
                    Close
                </button>
            </div>
        </div>
    </div>
</div>


@endsection
@section('script')

<script>
    $(document).ready(function () {
        $('.select2').select2();
        $(".date input").val('');

        

        $(document).on('keyup change', '#branchdropdown,#statusdropdown,.date input', function () {
            allinvoiceTBL.draw();
        });

// ======================================================================
        $('[data-toggle="popover"]').popover();

        var allinvoiceTBL = $('#allinvoiceTBL').DataTable({
            processing: true,
            serverSide: true,
             dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<"dt-filter-spacer"f><ip>',
             buttons: [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
            ajax: {
                url: '{{ route("invoice-filter") }}',
                data: function (d) {
                    // console.log(d);
                    // d.branch = $("#branchdropdown").val();
                    // d.status = $("#statusdropdown").val();
                    // d.date = $(".date input").val();
                }
            },
            deferRender: true,
            order: [[0, "desc"]],
            // searching:false,
            columns: [
                // {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'id', name: 'id'},
                {data: 'invoiceno', name: 'invoiceno'},
                {data: 'orderdate', name: 'orderdate'},
                {data: 'customer_id', name: 'customer_id'},
                {data: 'ref', name: 'ref'},
                {data: 'due', name: 'due'},
                {data: 'partnoshow', name: 'partnoshow', render: function (data, type, row, meta) {

                    let pub_partno = `<div class="form-check form-switch"><label class="form-check-label" for="partnosts"><input class="form-check-input" type="checkbox" id="partnosts" onclick='partno_status("unpublished-partno","${row.id}")'  checked></label></div>`;

                    let unpub_partno = `<div class="form-check form-switch"><label class="form-check-label" for="partnosts"><input class="form-check-input" type="checkbox" id="partnosts" onclick='partno_status("published-partno","${row.id}")' ></label></div>`;

                        if (row.partnoshow == 1)
                            partnoshow = pub_partno;
                        else {
                            partnoshow = unpub_partno;
                        }
                        return partnoshow
                    }
                },
                {data: 'net_total', name: 'net_total'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        $('#productsDetails').DataTable({

            'paging': true,
            'lengthChange': false,
            'searching': false,
            'ordering': false,
            'info': false,
            'autoWidth': true
        });


        $('#serviceDetils').DataTable({

            'paging': true,
            'lengthChange': false,
            'searching': true,
            'ordering': false,
            'info': false,
            'autoWidth': true
        });

        $('#serviceDetilsOne').DataTable({
            'paging': true,
            'lengthChange': false,
            'searching': true,
            'ordering': false,
            'info': false,
            'autoWidth': true
        });


        //History
        
var invoiceurl = "{{URL::to('/admin/invoice')}}";
$('#product-details').on('show.bs.modal', function (event) {
    $(this).css("zIndex", 99999999);
    let id = $(event.relatedTarget).val();
    var modal = $(this);
    $.ajax({
        type: "GET",
        url: invoiceurl + "/" + id,
        success: function (data) {
            // console.log(data);
            modal.find('.invoice-details tbody').empty();
            ctp.clear().draw(true);
            $.each(data.orderdetails, function (i, orderdetail) {
                ctp.row.add([
                    orderdetail.id,
                    orderdetail.product.part_no,
                    orderdetail.product.productname,
                    orderdetail.quantity,
                    orderdetail.sellingprice,
                    orderdetail.total_amount
                ]).draw(true);
            });
            modal.find('.total').text(data.grand_total);
            modal.find('.vat-amount').text(data.vatamount);
            modal.find('.discount-amount').text(data.discount_amount);
            modal.find('.grand-total').text(data.net_total);
            modal.find('.paid-amount').text(data.customer_paid);
                            
        },
        error: function (err) {
            console.log(err);
        }
    });
});


let ctp = $('.invoice-details').DataTable(
    {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'print',
                text: 'Print',
                autoPrint: true,
                exportOptions: {
                    columns: [0,1,2,3,4],
                },

                customize: function (win) {
                    $(win.document.body).find('table').addClass('display').css('font-size', '14px');
                    $(win.document.body).find('tr:nth-child(odd) td').each(function(index){
                        $(this).css('background-color','#D0D0D0');
                    });
                    $(win.document.body).find('h1').css('text-align','center');
                }
            },
            {
                extend:    'csvHtml5',
                text:      '<i class="fa fa-file-text-o"></i>',
                titleAttr: 'CSV',
                exportOptions: {
                    columns: [0,1,2,3,4],
                },
            }
        ]
    }
);

});

</script>


<script>
var stsurl = "{{URL::to('/admin')}}";
function partno_status(route, id) {
    $.ajax({
        url: stsurl + "/" + route + "/" + id,
        type: 'GET',
        beforeSend: function (request) {
            return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
        },
        success: function (response) {
            
            $(".ermsg").html(response.message);
            pagetop();
        },
        error: function (err) {
            console.log(err);
            alert("Something Went Wrong, Please check again");
        }
    });
}
</script>

@endsection