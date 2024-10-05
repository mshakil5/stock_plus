@extends('admin.layouts.master')

@section('content')

<div class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header text-center">
                        <h5 class="mb-0">Select Start Date</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.getStartDate') }}">
                            @csrf
                            <div class="form-group">
                                <label for="startDate" class="mb-1">Start Date</label>
                                <input type="date" id="startDate" name="start_date" class="form-control" value="{{ request('start_date') }}" required>
                            </div>
                            <div class="text-center">
                                <button type="submit" id="searchButton" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg> Search
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

@endsection