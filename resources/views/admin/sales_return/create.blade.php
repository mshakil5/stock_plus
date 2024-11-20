@extends('admin.layouts.master')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <div class="row">
        <div class="container-fluid">
            <div class="col-md-12">
                <div class="box box-default box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Sales Return</h3>
                    </div>
                    <div class="ermsg"></div>

                    <div class="box-body ir-table">
                        <form>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="returndate">Return Date *</label>
                                    <input type="date" class="form-control" id="returndate" name="returndate" value="{{ date('Y-m-d') }}">
                                    <input type="hidden" class="form-control" id="order_id" name="order_id" value="{{ $invoices->id }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="customer_id">Customer</label>
                                    <input type="text" class="form-control" value="{{ $invoices->customer->name }}" readonly>
                                    <input type="hidden" class="form-control" id="customer_id" name="customer_id" value="{{ $invoices->customer_id }}" readonly>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="ref">Reference</label>
                                    <input type="text" class="form-control" id="ref" name="ref" value="{{ $invoices->ref }}" readonly>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="reason">Reason *</label>
                                    <input type="text" class="form-control" id="reason" name="reason" required>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="box box-default box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Sales Items</h3>
                    </div>

                    <table class="table table-hover" id="productsTBL">
                        <thead>
                            <tr>
                                <th class="text-center">Product Name</th>
                                <th class="text-center">Part No</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices->orderdetails as $salesdetail)
                            <tr>
                                <td class="text-center">
                                    <input type="text" value="{{ $salesdetail->product->productname }}" class="form-control" readonly>
                                </td>
                                <td class="text-center">
                                    <input type="text" value="{{ $salesdetail->product->part_no }}" class="form-control" readonly>
                                </td>
                                <td class="text-center">
                                    <input type="number" value="{{ $salesdetail->quantity }}" class="form-control" readonly>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <span class="btn btn-success btn-sm returnThisProduct" 
                                            id="returnThisProduct" 
                                            ordid="{{ $salesdetail->id }}" 
                                            product_id="{{ $salesdetail->product_id }}" 
                                            product_name="{{ $salesdetail->product->productname }}" 
                                            unit_price="{{ $salesdetail->sellingprice }}" 
                                            quantity="{{ $salesdetail->quantity }}"
                                            purchase_history_id="{{$salesdetail->purchase_history_id}}" style="margin-right: 10px;">Return to Stock</span>
                                        <span class="btn btn-danger btn-sm returnToDamage" 
                                            id="returnToDamage" 
                                            ordid="{{ $salesdetail->id }}" 
                                            product_id="{{ $salesdetail->product_id }}" 
                                            product_name="{{ $salesdetail->product->productname }}" 
                                            unit_price="{{ $salesdetail->sellingprice }}" 
                                            quantity="{{ $salesdetail->quantity }}">Return to Damage</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-6">
                <div class="box box-default box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Return Items</h3>
                    </div>

                    <div id="returnbox" style="display: none">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="returninner"></tbody>
                        </table>
                        <div class="form-group col-md-4">
                            <label for="net_total">Total Return Amount</label>
                            <input type="text" id="net_total" class="form-control" readonly>
                        </div>
                        <div class="box-header with-border">
                            <label for="" style="visibility: hidden;"></label>
                            <button class="btn btn-success btn-md center-block" id="returnOrderBtn" type="button">Submit Return to Stock</button>
                        </div>
                    </div>

                    <div id="damagebox" style="display: none">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="damageinner"></tbody>
                        </table>
                        <div class="box-header with-border">
                            <button class="btn btn-danger btn-md center-block" id="damageReturnBtn" type="button">Submit Return to Damage</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        $("#productsTBL").on('click', '.returnThisProduct', function(event) {
            event.preventDefault();
            $('#damagebox').hide();
            $('#returnbox').show();
            
            var product_id = $(this).attr('product_id');
            var product_name = $(this).attr('product_name');
            var unit_price = parseFloat($(this).attr('unit_price'));
            var available_quantity = parseInt($(this).attr('quantity'));
            var quantity = parseInt($(this).closest('tr').find('input[type="number"]').val());
            var purchase_history_id = $(this).attr('purchase_history_id');

            if (quantity > available_quantity) {
                alert('The quantity cannot exceed the available quantity of ' + available_quantity + '.');
                return;
            }

            var total_price = unit_price * quantity;

            var existingProduct = $("input[name='productname[]']").filter(function() {
                return $(this).val() === product_name;
            });

            if (existingProduct.length > 0) {
                alert('This product has already been added to the return list.');
                return;
            }

            var markup = 
                '<tr>' +
                    '<td><input name="productname[]" type="text" value="' + product_name + '" class="form-control" readonly></td>' +
                    '<td><input type="number" class="form-control quantity" name="quantity[]" value="' + quantity + '" min="1" max="' + available_quantity + '"></td>' +
                    '<td>' +
                        '<input name="unit_price[]" type="text" value="' + unit_price.toFixed(2) + '" class="form-control" readonly>' +
                        '<input type="hidden" name="return_product_id[]" value="' + product_id + '">' +
                        '<input type="hidden" name="purchase_history_id[]" value="' + purchase_history_id + '">' +
                    '</td>' +
                    '<td><input name="total[]" type="text" value="' + total_price.toFixed(2) + '" class="form-control total" readonly></td>' +
                    '<td><button class="btn btn-danger btn-sm removeRowBtn">Remove</button></td>' +
                '</tr>';

            $("#returninner").append(markup);
            updateTotals();
        });

        $("#returninner").on('input', '.quantity', function() {
            var $row = $(this).closest('tr');
            var unit_price = parseFloat($row.find('input[name="unit_price[]"]').val());
            var quantity = parseInt($(this).val());
            
            var total_price = unit_price * quantity;
            $row.find('input[name="total[]"]').val(total_price.toFixed(2));

            updateTotals();
        });

        function updateTotals() {
            var grand_total = 0;
            $('.total').each(function() {
                grand_total += parseFloat($(this).val()) || 0;
            });
            $('#net_total').val(grand_total.toFixed(2));
        }

        $("#productsTBL").on('click', '.returnToDamage', function(event) {
            event.preventDefault();
            $('#returnbox').hide();
            $('#damagebox').show();

            var product_id = $(this).attr('product_id');
            var product_name = $(this).attr('product_name');
            var unit_price = parseFloat($(this).attr('unit_price'));
            var quantity = parseInt($(this).closest('tr').find('input[type="number"]').val());

            if (quantity > $(this).attr('quantity')) {
                alert('The quantity cannot be greater than the quantity in the sales detail.');
                return;
            }

            var total_price = unit_price * quantity;

            var existingProduct = $("input[name='damage_productname[]']").filter(function() {
                return $(this).val() === product_name;
            });

            if (existingProduct.length > 0) {
                alert('This product has already been added to the damage return list.');
                return;
            }

            var markup = 
                '<tr>' +
                    '<td><input name="damage_productname[]" type="text" value="' + product_name + '" class="form-control" readonly></td>' +
                    '<td><input type="number" class="form-control" name="damage_quantity[]" value="' + quantity + '" min="1" max="' + quantity + '"></td>' +
                    '<td><input name="damage_unit_price[]" type="text" value="' + unit_price.toFixed(2) + '" class="form-control" readonly></td>' +
                    '<td><input name="damage_total[]" type="text" value="' + total_price.toFixed(2) + '" class="form-control damage_total" readonly><input name="damage_product_id[]" type="hidden" value="' + product_id + '"></td>' +
                    '<td><button class="btn btn-danger btn-sm removeRowBtn">Remove</button></td>' +
                '</tr>';

            $("#damageinner").append(markup);
        });

        $(document).on('click', '.removeRowBtn', function() {
            $(this).closest('tr').remove();
            updateTotals();
        });

        $("#returnOrderBtn").on('click', function() {
            var returndate = $("#returndate").val();
            var customer_id = $("#customer_id").val();
            var reason = $("#reason").val();
            var net_total = $("#net_total").val();
            var order_id = $("#order_id").val();
            var product_id = $("input[name='return_product_id[]']")
              .map(function(){return $(this).val();}).get();
            var quantity = $("input[name='quantity[]']").map(function() { return $(this).val(); }).get();
            var total = $("input[name='total[]']").map(function() { return $(this).val(); }).get();
            var purchase_history_id = $("input[name='purchase_history_id[]']")
                .map(function(){return $(this).val();}).get();

                console.log(product_id, order_id, quantity, total, net_total, returndate, customer_id, reason, purchase_history_id);
            $.ajax({
                url: "{{ URL::to('/sales-return') }}",
                method: "POST",
                data: { product_id, order_id, quantity, total, net_total, returndate, customer_id, reason, purchase_history_id },
                success: function(response) {
                    if (response.status == 200) {
                        $(".ermsg").html('<div class="alert alert-success">' + response.message + '</div>');
                        setTimeout(function() { location.reload(); }, 2000);
                    } else {
                        $(".ermsg").html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });

        $("#damageReturnBtn").on('click', function() {
            var customer_id = $("#customer_id").val();
            var product_id = $("input[name='damage_product_id[]']").map(function() { return $(this).val(); }).get();
            var quantity = $("input[name='damage_quantity[]']").map(function() { return $(this).val(); }).get();
            // console.log(product_id, quantity, customer_id);

            $.ajax({
                url: "{{ URL::to('/damage-return') }}",
                method: "POST",
                data: {product_id,quantity,customer_id},
                success: function(response) {
                    if (response.status == 200) {
                        $(".ermsg").html('<div class="alert alert-success">' + response.message + '</div>');
                        setTimeout(function() { location.reload(); }, 2000);
                    } else {
                        $(".ermsg").html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>
@endsection