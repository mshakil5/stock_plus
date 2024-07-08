
@extends('user.layouts.master')
@section('content')

    <!-- main wrapper -->
    <section class="main ">
        <div class="content-container">
            
            <div class="inner">
                <div class="content w-90 mx-auto">

                    <div class="row mx-auto"> 
                        <div class="box">
                            <p class="box-title">Manage Sales Invoice</p>
                            <table class="table table-striped table-hover" id="example"> 
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Product</th>
                                        <th>Requested Date</th>
                                        <th>From Branch</th>
                                        <th>Quantity</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>                                  
                                <tbody>
                                    @foreach ($data as $key => $item)
                                        <tr>
                                            <th>{{ $key + 1 }}</th>
                                            <td>{{ $item->product->productname }}</td> 
                                            <td>{{ $item->created_at }}</td>
                                            <td>{{ \App\Models\Branch::where('id','=', $item->from_branch_id)->first()->name }}</td> 
                                            <td>{{ $item->requestqty }}</td> 
                                            <td>
                                                @if ($item->status == "1")
                                                    <button class="btn btn-sm btn-success">Transferred</button>
                                                @else
                                                <a href='#' id='transferBtn' class='btn btn-sm btn-theme ms-1'  data-bs-toggle='modal' data-bs-target='#reqStockModal' >Transfer</a>
                                                @endif
                                                
                                                {{-- <a href="" class="btn btn-primary btn-sm"><span title="Sales Details" ><i class="fa fa-eye" aria-hidden="true"></i>view</span></a> --}}

                                                
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
        </div>

    </section>

    <div class="modal fade" id="reqStockModal" tabindex="-1" aria-labelledby="reqStockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="reqStockModalLabel">Product stock request</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form-custom" id="stockReqForm">
                        {{csrf_field()}}
                    <div class="form-group">
                        <label for="quantity" class="col-sm-3 control-label">Quantity</label>
                        <div class="col-sm-9">
                            <input type="number" name="quantity" class="form-control" id="quantity" required>
                            <input type="hidden" name="productid" class="form-control" id="productid">
                            <input type="hidden" name="stockid" class="form-control" id="stockid">
                            <input type="hidden" name="reqtobranchid" class="form-control" id="reqtobranchid">
                        </div>
                    </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary req-save-btn">Request</button>
                </div>
            </div>
        </div>
    </div>
@endsection
    
@section('script')

<script>
    
    $(document).ready( function () {
        $('#example').DataTable();
    } );

</script>


@endsection