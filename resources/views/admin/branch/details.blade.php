@extends('admin.layouts.master')

@section('content')

<section class="content pt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">




                @if(session()->has('success'))
                <div class="alert alert-success pt-3 mb-3" id="successMessage">{{ session()->get('success') }}</div>
                @endif

                @if(session()->has('error'))
                <div class="alert alert-danger pt-3 mb-3" id="errMessage">{{ session()->get('error') }}</div>
                @endif

                <div class="card card-secondary">
                    <div class="card-header">

                    <div class="mb-3">
                        <a href="{{ route('view_branch') }}" class="btn btn-primary">Back</a>
                    </div>

                        <h3 class="card-title">Branch Information</h3>
                    </div>

                    <form id="createThisForm" action="{{ isset($branchDetails) ? route('admin.branchDetails.update', $branchDetails->id) : route('admin.branchDetails.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if (isset($branchDetails))
                        @method('PUT') <!-- Add this for updating the data -->
                        <input type="hidden" name="codeid" value="{{ $branchDetails->id }}">
                        @endif

                        <input type="hidden" name="branch_id" id="branch_id" value="{{ $branch->id ?? '' }}">

                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Branch Name*</label>
                                        <input type="text" class="form-control @error('branch_name') is-invalid @enderror" id="branch_name" name="branch_name" value="{{ $branchDetails->branch_name ?? old('branch_name') }}">
                                        @error('branch_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Email (1)</label>
                                        <input type="email" class="form-control @error('email1') is-invalid @enderror" id="email1" name="email1" value="{{ $branchDetails->email1 ?? old('email1') }}">
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Email (2)</label>
                                        <input type="email" class="form-control @error('email2') is-invalid @enderror" id="email2" name="email2" value="{{ $branchDetails->email2 ?? old('email2') }}">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Phone (1)</label>
                                        <input type="text" class="form-control @error('phone1') is-invalid @enderror" id="phone1" name="phone1" value="{{ $branchDetails->phone1 ?? old('phone1') }}">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Phone (2)</label>
                                        <input type="text" class="form-control @error('phone2') is-invalid @enderror" id="phone2" name="phone2" value="{{ $branchDetails->phone2 ?? old('phone2') }}">
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Address</label>
                                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ $branchDetails->address ?? old('address') }}">
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Google Map source code</label>
                                        <textarea name="google_map" id="google_map" class="form-control @error('google_map') is-invalid @enderror" cols="30" rows="3">{{ $branchDetails->google_map ?? old('google_map') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Fav Icon*</label>
                                        <input type="file" class="form-control @error('fav_icon') is-invalid @enderror" id="fav_icon" name="fav_icon" onchange="previewImage(event, 'fav_icon_preview')">
                                        @error('fav_icon')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="card card-outline card-info">
                                        <div class="card-body">
                                            <img id="fav_icon_preview" src="{{ isset($branchDetails->fav_icon) ? asset('images/branch/'.$branchDetails->fav_icon) : '' }}" alt="" style="width: 230px">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Branch Logo*</label>
                                        <input type="file" class="form-control @error('branch_logo') is-invalid @enderror" id="branch_logo" name="branch_logo" onchange="previewImage(event, 'branch_logo_preview')">
                                        @error('branch_logo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="card card-outline card-info">
                                        <div class="card-body">
                                            <img id="branch_logo_preview" src="{{ isset($branchDetails->branch_logo) ? asset('images/branch/'.$branchDetails->branch_logo) : '' }}" alt="" style="width: 230px">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Invoice Header</label>
                                        <input type="file" class="form-control @error('invoice_header') is-invalid @enderror" id="invoice_header" name="invoice_header" onchange="previewImage(event, 'invoice_header_preview')">
                                        @error('invoice_header')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="card card-outline card-info">
                                        <div class="card-body">
                                            <img id="invoice_header_preview" src="{{ isset($branchDetails->invoice_header) ? asset('images/branch/'.$branchDetails->invoice_header) : '' }}" alt="" style="width: 230px">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-lg btn-success" value="{{ isset($branchDetails) ? 'Update' : 'Save' }}">{{ isset($branchDetails) ? 'Update' : 'Save' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')
<script>
    function previewImage(event, previewId) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById(previewId);
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection