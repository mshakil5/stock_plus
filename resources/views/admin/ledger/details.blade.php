@extends('admin.layouts.master')

@section('content')

<div class="page-header"><a href="{{ url()->previous() }}" class="btn btn-primary">Back</a></div>

<div class="row">
    <div class="col-md-12">
        <div id="alert-container"></div>
        @component('components.widget')
            @slot('title')
               Account Transactions for <strong>{{ $account->account_name }}</strong>
            @endslot
            @slot('description')
            @endslot
            @slot('body')
                @component('components.table')
                    @slot('tableID')
                        transactionsTBL
                    @endslot
                    @slot('head')
                        <th>Date</th>
                        <th>Description</th>
                        <th>Payment Type</th>
                        <th>Ref</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Balance</th>
                    @endslot
                @endcomponent
            @endslot
        @endcomponent
    </div>
</div>
@endsection

@section('script')
<script>
    var transactionsUrl = "{{ route('account.ledger.show', $account->id) }}";
    var transactionsTBL = $('#transactionsTBL').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: transactionsUrl,
            type: 'GET',
            error: function (xhr, error, thrown) {
                console.log(xhr.responseText);
            }
        },
        deferRender: true,
        columns: [
            { data: 'date', name: 'date' },
            { data: 'description', name: 'description' },
            { data: 'payment_type', name: 'payment_type' },
            { data: 'ref', name: 'ref' },
            { data: 'debit', name: 'debit' },
            { data: 'credit', name: 'credit' },
            { data: 'balance', name: 'balance' },
        ]
    });
</script>
@endsection
