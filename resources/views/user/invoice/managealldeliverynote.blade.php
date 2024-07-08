
@extends('user.layouts.master')
@section('content')

    <!-- main wrapper -->
    <section class="main ">
        <div class="content-container">
            
            <div class="inner">
                <div class="content w-90 mx-auto">
                    <div class="container">
                        <div class="row mx-auto"> 
                            <div class="box">
                                <p class="box-title">Manage Delivery Notes</p>
                                <table class="table table-striped table-hover" id="example" style="width:100%"> 
                                    <thead>
                                        <tr>
                                            <th style="text-align: center">ID</th>
                                            <th style="text-align: center">InvoiceID</th>
                                            <th style="text-align: center">Date</th>
                                            <th style="text-align: center">Customer</th>
                                            <th style="text-align: center">Ref</th>
                                            <th style="text-align: center">QN</th>
                                            <th style="text-align: center">Due</th>
                                            <th style="text-align: center">Total</th>
                                            <th style="text-align: center">Part No status</th>
                                            <th style="text-align: center">Created</th>
                                            <th style="text-align: center">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div> 
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </section>

    <!-- Modal -->
<div class="modal modal-xl fade" id="view" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">
                    Sales Details
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <table class="table table-hover orderdtl" id="salesdetails" style="width: 100%" >
                        <thead>
                            <tr>
                                <td>Product</td>
                                <td>Part No#</td>
                                <td>Qty</td>
                                <td>Amount</td>
                                <td>Total Amount</td>
                            </tr>
                        </thead>                                   
                        <tbody>
                            
                        </tbody>
                    </table>

                </div>
                        
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal End -->

@endsection
    
@section('script')

<script>
    
    $(document).ready( function () {
        $('#example').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('user.filteralldnotes') !!}',
            order: [[0, "desc"]],
            columns: [
                { data: 'id', name: 'id' },
                { data: 'invoiceno', name: 'invoiceno' },
                { data: 'orderdate', name: 'orderdate' },
                { data: 'customername', name: 'customername' },
                { data: 'ref', name: 'ref' },
                { data: 'qn_no', name: 'qn_no' },
                { data: 'due', name: 'due' },
                { data: 'net_total', name: 'net_total' },
                {
                    data: 'partnoshow', name: 'partnoshow', render: function (data, type, row, meta) {

                        
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
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action' },
            ]
        });

 

        var orderdtlurl = "{{URL::to('/sales-detail')}}";
        $("#example").on('click','.viewThis', function(){

                orderid = $(this).attr('oid');
                info_url = orderdtlurl + '/'+ orderid;

                var table = $(".orderdtl tbody");
                $.get(info_url,{},function(d){
                    console.log(d);
                    table.empty();
                    $.each(d, function (a, b) {
                        // table.empty();
                        table.append("<tr><td class='text-left'>" + b.product.productname + "</td>" +
                            "<td class='text-success text-left'>" + b.product.part_no + "</td>" +
                            "<td class='text-center'>" + b.quantity + "</td>" +
                            "<td class='text-center'>" + b.sellingprice + "</td>" +
                            "<td class='text-center'>" + b.total_amount + "</td>" +
                            "</tr>");
                    });
                });
            }); 

    } );

</script>

<script>
    $(document).ready( function () {
        $('#salesdetails').DataTable();
    } );
</script>

<script>
    var stsurl = "{{URL::to('/user')}}";
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