
@extends('user.layouts.master')
@section('content')

    <!-- main wrapper -->
    <section class="main ">
        <div class="content-container">
            
            <div class="inner">
                <div class="content w-90 mx-auto">

                    <div class="row mx-auto"> 
                        <div class="box">
                            <p class="box-title">Manage Sales Return Invoice</p>
                            <table class="table table-striped table-hover" id="example"> 
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>InvoiceID</th>
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
                                            <td>{{ $item->returndate }}</td>
                                            <td>{{ $item->customer->name }}</td>
                                            <td>{{ $item->reason }}</td>
                                            <td>
                                            <a href="#" class="btn btn-sm btn-theme ms-1 viewThis" data-bs-toggle="modal" data-bs-target="#view" rid="{{$item->id}}" id="viewThis">View</a>
                                            </td>
                                        </tr>
                                                
                                    @endforeach 
                                </tbody>
                            </table>
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
                    Sales Return Details
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <table class="table table-hover returndtl" >
                        <thead>
                            <tr>
                                <td>Part No</td>
                                <td>Product</td>
                                <td>Qty</td>
                                <td>Unit Price</td>
                                <td>Amount</td>
                            </tr>
                        </thead>                                   
                        <tbody>
                            {{-- @foreach ($item->salesreturndetail  as $detail)
                                <tr>
                                    <td>{{$detail->product_id}}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>{{$detail->quantity}}</td>
                                    <td>{{$detail->total_amount}}</td>
                                </tr>
                            @endforeach --}}
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
        $('#example').DataTable();
    } );
</script>

<script>
    
    $(document).ready( function () {

        var returndtlurl = "{{URL::to('/sales-return-detail')}}";
        $("#example").on('click','.viewThis', function(){

                returnid = $(this).attr('rid');
                info_url = returndtlurl + '/'+ returnid;

                var table = $(".returndtl tbody");
                $.get(info_url,{},function(d){
                    // console.log(d);
                    table.empty();
                    $.each(d, function (a, b) {
                        // table.empty();
                        table.append("<tr><td class='text-left'>" + b.part_no + "</td>" +
                            "<td class='text-success text-left'>" + b.productname + "</td>" +
                            "<td class='text-center'>" + b.quantity + "</td>" +
                            "<td class='text-center'>" + b.selling_price + "</td>" +
                            "<td class='text-center'>" + b.total_amount + "</td>" +
                            "</tr>");
                    });
                });
            }); 
    } );
</script>

@endsection