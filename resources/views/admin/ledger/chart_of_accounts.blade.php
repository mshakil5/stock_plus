@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div id="alert-container"></div>
        @component('components.widget')
            @slot('title')
               Chart of Accounts
            @endslot
            @slot('description')

            @endslot
            @slot('body')
                @component('components.table')
                    @slot('tableID')
                        expenseTBL
                    @endslot
                    @slot('head')
                        <th>ID</th>
                        <th>Account</th>
                    @endslot
                @endcomponent
            @endslot
        @endcomponent
    </div>
</div>

@endsection
    
@section('script')

<!-- Main script -->
<script>

    var charturl = "{{URL::to('/admin/ledger')}}";
    var accountUrl = "{{URL::to('/admin/ledger/account/:id')}}";
    var customerTBL = $('#expenseTBL').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
        url: charturl,
        type: 'GET',
        error: function (xhr, error, thrown) {
            console.log(xhr.responseText);
        }
        },
        deferRender: true,
        columns: [
            { data: 'id', name: 'id' },
            { 
                data: 'account_name', 
                name: 'account_name',
                render: function(data, type, row) {
                    var url = accountUrl.replace(':id', row.id);
                    return '<a href="' + url + '">' + data + '</a>';
                }
            }
        ]
    });

</script>

@endsection