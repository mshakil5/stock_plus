@extends('admin.layouts.master')

@section('content')

<section class="content pt-3">
  <div class="container-fluid">
    <div class="row" style="display: flex; justify-content: center; align-items: center; margin-top: 30px;">
      <div class="col-md-10">

        <div class="card card-secondary">
          <div class="card-header">
          <div class="mb-3">
              <a href="{{ route('view_branch') }}" class="btn btn-primary">Back</a>
          </div>
          </div>

          <form id="createThisForm">
            @csrf
            <div class="card-body">

              <div class="ermsg"></div>

              <div class="text-center mb-4 company-name-container">
                <h2>{{ $branchName }}</h2>
                <h4>{{ $branchEmail }}</h4>
              </div>

              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label>Subject</label>
                    <input type="text" class="form-control" id="subject" name="subject">
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="form-group">
                    <label>Body</label>
                    <textarea name="body" id="body" cols="30" rows="5" class="form-control"></textarea>
                  </div>
                </div>
              </div>

            </div>

            <div class="card-footer">
              <button type="submit" class="btn btn-lg btn-success" id="sendEmailButton">Send</button>
              <div id="loader" style="display: none;">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Loading...
              </div>
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
  $(function() {
    $('#body').summernote({
      height: 300,
    });

    $('#createThisForm').on('submit', function(event) {
      event.preventDefault();

      var subject = $('#subject').val();
      var body = $('#body').val();
      var sendButton = $('#sendEmailButton');
      var loader = $('#loader');
      var branchEmail = "{{ $branchEmail }}";

      if (!subject || !body) {
        swal("Error", "Please fill all required fields", "error");
        return;
      }

      sendButton.prop('disabled', true);
      loader.show();

      $.ajax({
        url: "{{ route('sendBranchEmail') }}",
        method: 'POST',
        data: {
          _token: "{{ csrf_token() }}",
          subject: subject,
          body: body,
          email: branchEmail
        },
        success: function(response) {
          if (response.status === 'success') {
              $(".ermsg").html("<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Email sent successfully!</b></div>");
          } else {
              $(".ermsg").html("<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Error sending email. Please try again!</b></div>");
          }
          $('#createThisForm')[0].reset();
          $('#body').summernote('code', '');
          $('#subject').val('');
        },
        error: function(xhr) {
          console.error(xhr.responseText);
          $(".ermsg").html("<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Error sending email. Please try again!</b></div>");
        },
        complete: function() {
          sendButton.prop('disabled', false);
          loader.hide();
        }
      });
    });
  });
</script>

@endsection