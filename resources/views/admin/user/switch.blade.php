
@extends('admin.layouts.master')
@section('content')
<style>
    .px-2 {
    margin-top: 2px !important;
    margin-bottom: 2px !important;
    }

    .block{
        width:180px; 
        }
</style>

@if (Auth::user()->id > 5)
    
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-navicon"></i> Switch Branch</div>
            @if (\Session::has('success'))
                <div class="alert alert-success">
                    {!! \Session::get('success') !!}
                </div>
            @endif
            <div class="panel-body">
                <form class="form-horizontal" action="{{ route('switch_branch_store')}}" method="POST">
                    {{csrf_field()}}
                <div class="row">
                    <div class="col-md-8">
                        <label class="label label-primary">Branch</label>
                        <select class="form-control select2" name="branch_id">
                            <option value="">Select Branch..</option>
                            @php
                            
                                $branches = json_decode(Auth::user()->branchaccess, true);
                            @endphp

                            @foreach ($branches as $item)
                                @php
                                    $branchNames = \App\Models\Branch::where('status','1')->where('id', $item)->get();
                                @endphp
                                @foreach ($branchNames as $branchName)
                                    <option value="{{$branchName->id}}">{{$branchName->name}}</option>
                                @endforeach
                            @endforeach
                                
                        </select>
                    </div>

                    <div class="col-md-4">
                        <br>
                        <button type="submit" class="btn btn-primary btn-sm block">Switch</button>
                    </div>


                </div>
                
            </form>
            </div>
        </div>

    </div>
</div>

@endif

@if (Auth::user()->id < 6)
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-navicon"></i> Switch Branch</div>
                @if (\Session::has('success'))
                    <div class="alert alert-success">
                        {!! \Session::get('success') !!}
                    </div>
                @endif
            <div class="panel-body">

                
                <form class="form-horizontal" action="{{ route('switch_branch_store')}}" method="POST">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="col-md-8">
                            <label class="label label-primary">Branch</label>
                            <select class="form-control select2" name="branch_id">
                                <option value="">Select Branch..</option>
                                    @php
                                        $branchNames = \App\Models\Branch::where('status','1')->get();
                                    @endphp
                                    @foreach ($branchNames as $branchName)
                                        <option value="{{$branchName->id}}">{{$branchName->name}}</option>
                                    @endforeach
                                    
                            </select>
                        </div>

                        <div class="col-md-4">
                            <br>
                            <button type="submit" class="btn btn-primary btn-sm block">Switch</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>

@endif


@endsection
    
@section('script')


 
@endsection
    