@extends('admin.layouts.master')
@section('content')


@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    @if ($errors->any())
        <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (Session::has('success'))
        <div class="alert alert-success">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
            <p>{{ Session::get('success') }}</p>
        </div>
        {{ Session::forget('success') }}
    @endif

    <div class="row">

        <div class="col-md-8">
            <div class="row">
                <div class="col-md-12">
                    @component('components.widget')
                        @slot('title')
                            Supplier Details
                        @endslot
                        @slot('description')
                            
                        @endslot
                        @slot('body')

                        <table  class="table table-hover table-responsive " width="100%" id="supplierTBL">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Vat reg</th>
                                    <th>Address</th>
                                    <th>Company</th>
                                    <th><i class=""></i> Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach (\App\Models\Vendor::all() as $data)
                                    <tr>
                                        <td>{{ $data->code}}</td>
                                        <td>{{ $data->name}}</td>
                                        <td>{{ $data->email}}</td>
                                        <td>{{ $data->phone}}</td>
                                        <td>{{ $data->vat_reg}}</td>
                                        <td>{{ $data->address}}</td>
                                        <td>{{ $data->companyinfo}}</td>
                                        <td>
                                            <span class="btn btn-success btn-sm editThis" id="editThis" vid="{{$data->id}}" code="{{$data->code}}" name="{{$data->name}}" email="{{$data->email}}" phone="{{$data->phone}}" vatreg="{{$data->vat_reg}}" address="{{$data->address}}" cinfo="{{$data->companyinfo}}"> <i class='fa fa-pencil'></i> Edit </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                            
                            </tfoot>
                        
                        </table>

                        @endslot
                    @endcomponent
                </div>
            </div>
           
        </div>



        <div class="col-md-4">
            @component('components.widget')
                @slot('title')
                    Vendor/Supplier Information
                @endslot
                @slot('description')
                    Please Provide vendor full information<br>
                    Note: Vendors are also known as Suppliers
                @endslot
                @slot('body')
                    <hr/>
                    <div class="col-sm-12" id="createDiv">
                        <form class="form-horizontal" action="{{ route('admin.savevendor')}}" method="POST">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Code<span
                                            class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="code" class="form-control" id="inputEmail3"
                                           placeholder="V-123">
                                </div>
                                @if ($errors->has('code'))
                                    <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $errors->first('code') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Name<span
                                            class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="name" class="form-control" id="inputEmail3"
                                           placeholder="ex. John Doe">
                                </div>
                                @if ($errors->has('name'))
                                    <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" name="email" class="form-control" id="inputPassword3"
                                           placeholder="ex. test@gmail.com">
                                </div>
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">Phone<span
                                            class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="phone" class="form-control" id="inputPassword3"
                                           placeholder="ex. 0123456789" required>
                                </div>
                                @if ($errors->has('phone'))
                                    <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="vat_reg" class="col-sm-3 control-label">Vat Reg#<span
                                            class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="vat_reg" class="form-control" id="vat_reg"
                                           placeholder="ex. 0123456789" required>
                                </div>
                                @if ($errors->has('vat_reg'))
                                    <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $errors->first('vat_reg') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">Address</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" rows="3" placeholder="1355 Market Street, Suite 900 San Francisco, CA 94103 P: (123) 456-7890" name="address"></textarea>
                                </div>
                                @if ($errors->has('address'))
                                    <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $errors->first('address') }}</strong>
                                    </span>
                                @endif
                            </div>
                            
                            <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">Company Information</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" rows="3" placeholder="Twitter, Inc. 1355 Market Street, Suite 900 San Francisco, CA 94103 P: (123) 456-7890" name="company"></textarea>
                                </div>
                                @if ($errors->has('company'))
                                    <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $errors->first('company') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">Contract Date</label>
                                <div class="col-sm-9">
                                    <div class="input-group date" data-provide="datepicker"
                                         data-date-format="dd-mm-yyyy">
                                        <input name="contract" type="text" class="form-control date2"
                                               name="contractdate"
                                               placeholder="dd-mm-yyyy" value="<?= date('d-m-Y')?>">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                                @if ($errors->has('contract'))
                                    <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $errors->first('contract') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary center-block"><i class="fa fa-save"></i> Save</button>

                        </form>
                    </div>

                    <div class="col-sm-12" id="editDiv">
                        <form class="form-horizontal" action="{{ route('admin.updatevendor')}}" method="POST">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label for="vendorcode" class="col-sm-3 control-label">Code<span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="vendorcode" class="form-control" id="vendorcode" required>
                                    <input type="hidden" name="vendorid" class="form-control" id="vendorid" required>
                                </div>
                                @if ($errors->has('code'))
                                    <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $errors->first('code') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="vendorname" class="col-sm-3 control-label">Name<span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="vendorname" class="form-control" id="vendorname">
                                </div>
                                @if ($errors->has('name'))
                                    <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="vendoremail" class="col-sm-3 control-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" name="vendoremail" class="form-control" id="vendoremail" >
                                </div>
                                @if ($errors->has('vendoremail'))
                                    <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $errors->first('vendoremail') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="vendorphone" class="col-sm-3 control-label">Phone <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="vendorphone" class="form-control" id="vendorphone" required>
                                </div>
                                @if ($errors->has('vendorphone'))
                                    <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $errors->first('vendorphone') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="vendorvatreg" class="col-sm-3 control-label">Vat Reg#<span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="vendorvatreg" class="form-control" id="vendorvatreg" required>
                                </div>
                                @if ($errors->has('vendorvatreg'))
                                    <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $errors->first('vendorvatreg') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="vendoraddress" class="col-sm-3 control-label">Address</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" rows="3" name="vendoraddress" id="vendoraddress"></textarea>
                                </div>
                                @if ($errors->has('vendoraddress'))
                                    <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $errors->first('vendoraddress') }}</strong>
                                    </span>
                                @endif
                            </div>
                            
                            <div class="form-group">
                                <label for="vendorcinfo" class="col-sm-3 control-label">Company Information</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" rows="3" name="vendorcinfo" id="vendorcinfo"></textarea>
                                </div>
                                @if ($errors->has('vendorcinfo'))
                                    <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $errors->first('vendorcinfo') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">Contract Date</label>
                                <div class="col-sm-9">
                                    <div class="input-group date" data-provide="datepicker" data-date-format="dd-mm-yyyy">
                                        <input name="contract" type="text" class="form-control date2" name="contractdate" placeholder="dd-mm-yyyy" value="<?= date('d-m-Y')?>">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                                @if ($errors->has('contract'))
                                    <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $errors->first('contract') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary text-center"><i class="fa fa-save"></i> Update</button>
                            <input type="button" class="btn btn-warning text-center" id="FormCloseBtn" value="Close">

                        </form>
                    </div>
                @endslot
            @endcomponent
        </div>
        
    </div>
    <script>
        $(document).ready(function () {
            $('.select2').select2();
            $('#supplierTBL').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': true
            });

        });
    </script>


@endsection
    
@section('script')
<script>
    $(document).ready(function () {

        
        
        $("#editDiv").hide();
        $("#FormCloseBtn").click(function(){
            $("#editDiv").hide();
            $("#createDiv").show();
        });


        // header for csrf-token is must in laravel
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        // 




        // return stock
        $("#supplierTBL").on('click','#editThis', function(){
            $("#editDiv").show();
            $("#createDiv").hide();
            vendorid = $(this).attr('vid');
            vendorcode = $(this).attr('code');
            vendorname = $(this).attr('name');
            vendoremail = $(this).attr('email');
            vendorphone = $(this).attr('phone');
            vendorvatreg = $(this).attr('vatreg');
            vendoraddress = $(this).attr('address');
            vendorcinfo = $(this).attr('cinfo');
            $('#vendorid').val(vendorid);
            $('#vendorcode').val(vendorcode);
            $('#vendorname').val(vendorname);
            $('#vendoremail').val(vendoremail);
            $('#vendorphone').val(vendorphone);
            $('#vendorvatreg').val(vendorvatreg);
            $('#vendoraddress').val(vendoraddress);
            $('#vendorcinfo').val(vendorcinfo);
                
            });
        // return stock end



        // submit to purchase 
        var purchasereturnurl = "{{URL::to('/admin/purchase-return')}}";

            $("body").delegate("#purchasereturnBtn","click",function(event){
                event.preventDefault();

                
                var branch_id = $("#branch_id").val();
                var purchase_id = $("#purchase_id").val();
                var reason = $("#reason").val();
                var date = $("#date").val();
                
                var product_id = $("input[name='product_id[]']")
                    .map(function(){return $(this).val();}).get();
                    
                var quantity = $("input[name='quantity[]']")
                    .map(function(){return $(this).val();}).get();

                    
                var purchase_his_id = $("input[name='purchase_his_id[]']")
                    .map(function(){return $(this).val();}).get();


                $.ajax({
                    url: purchasereturnurl + '/' + purchase_id,
                    method: "POST",
                    data: {branch_id,date,reason,product_id,purchase_his_id,quantity},

                    success: function (d) {
                        if (d.status == 303) {
                            $(".ermsg").html(d.message);
                            pagetop();
                        }else if(d.status == 300){
                            $(".ermsg").html(d.message);
                            pagetop();
                            window.setTimeout(function(){location.href = "{{ route('admin.product.purchasehistory')}}"},2000)
                            
                        }
                    },
                    error: function (d) {
                        console.log(d);
                    }
                });

        });
        // submit to purchase end

















    });  
    </script>

@endsection
    