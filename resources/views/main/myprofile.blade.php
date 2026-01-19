@extends('master.layoutsmaster')
@section('styles')
<style>
    .profile-user-img {
        /* Membuat gambar dan ikon kamera berada dalam satu baris */
        display: inline-block;
        position: relative; /* Diperlukan jika menggunakan posisi absolut untuk ikon kamera */
    }
    .camera-icon {
        position: absolute;
        bottom: 0;
        right: 0;
        transform: translate(-50%, 50%);
        cursor: pointer;
        font-size: 1.5em; /* Atur ukuran ikon kamera */
    }
    .profile-container {
        position: relative;
        display: inline-block;
    }
    #cropperContainer {
        display: none;
        max-height: 400px; /* Atur tinggi maksimum untuk container cropper */
        overflow: hidden; /* Sembunyikan overflow jika gambar terlalu besar */
    }
    #cropperImage {
        max-width: 100%; /* Atur lebar maksimum gambar agar tidak melebihi container */
        max-height: 100%; /* Atur tinggi maksimum gambar agar tidak melebihi container */
    }
    .modal-sm-custom {
        max-width: 400px;
    }
    .modal-body {
        /* Tambahan margin-top untuk menggeser tombol ke bawah */
        margin-top: 20px;
    }
</style>
@endsection

@section('content')
 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        
      </div>
    </div><!-- /.container-fluid -->
  </section>
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-3">

        <div class="modal fade" id="changeImageModal" tabindex="-1" aria-labelledby="changeImageModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-sm"> <!-- Adjust modal size as needed -->
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="changeImageModalLabel">Change Profile Picture</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                      <form id="cropForm" action="{{ route('uploadphoto') }}" method="post" enctype="multipart/form-data">
                          @csrf
                          <div class="form-group">
                              <label for="profileImage">Choose Profile Picture</label>
                              <input type="file" class="form-control-file" id="profileImage" name="profileImage" accept=".jpg,.jpeg" required>
                          </div>
                          <div class="form-group">
                              <label>Preview</label>
                              <div id="imagePreview"></div>
                          </div>
                          <input type="hidden" id="cropData" name="cropData">
                          <button type="submit" class="btn btn-danger">Change Picture</button>
                      </form>
                  </div>
              </div>
          </div>
      </div>

          <!-- Profile Image -->
          <div class="card card-primary card-outline">
            <div class="card-body box-profile">
              <div class="text-center">    
                <div class="text-center position-relative">
                  @if($user->details->photo)
                      <img class="profile-user-img img-fluid img-circle"
                           src="data:image/jpeg;base64,{{ $user->details->photo }}"
                           alt="User profile picture">
                  @else
                      <img class="profile-user-img img-fluid img-circle"
                           src="{{ asset('images/pp.jpg') }}"
                           alt="Default profile picture">
                  @endif
              </div>
              <div class="text-center mt-1 mb-4">
                <a href="#" data-toggle="modal" data-target="#changeImageModal">Edit (maksimal 1MB)</a>
              </div>
              </div>
              <h3 class="profile-username text-center">{{ $user->details->gelar_depan }} {{ $user->name }}@if($user->details->gelar_belakang), {{ $user->details->gelar_belakang }}@endif</h3>
              <hr>
              <strong>Jabatan :</strong>
              <br>
              {{$user->details->jabatan}}
            </div>    
          </div>      
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="card">
          <div class="card-header">
          <h3 class="card-title">My Profile</h3>
          <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
              </button>
          </div>
      </div>
            <div class="card-body">
              <div class="tab-content">
                <div>
                    
                    <div class="form-group row">
                      <label for="inputName" class="col-sm-2 col-form-label">Nama</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $user->name }}" readonly>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                      <div class="col-sm-10">
                        <input type="email" class="form-control" id="email" name="email"  value="{{ $user->email }}" readonly>
                      </div>
                    </div>                     
                    <div class="form-group row">
                      <label for="inputSkills" class="col-sm-2 col-form-label">NIK</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="nik"  name="nik"  value="{{ $user->details->nik }}" readonly>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputSkills" class="col-sm-2 col-form-label">NIDN/NUP</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="nidn" name="nidn" value="{{ $user->details->nidn }}" readonly>
                      </div>
                    </div>
                   
                
                </div>
                <!-- /.tab-pane -->
              </div>
            </div><!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script>
 // Initialize Cropper.js
var cropper;

// When file input changes
document.getElementById('profileImage').addEventListener('change', function(e) {
    var file = e.target.files[0];
    var image = document.getElementById('imagePreview');

    // Ensure it's an image file
    if (file.type.match('image.*')) {
        var reader = new FileReader();

        reader.onload = function(e) {
            // Destroy old cropper instance
            if (cropper) {
                cropper.destroy();
            }

            // Replace image source
            image.innerHTML = '<img src="' + e.target.result + '" id="cropImage" style="max-width: 100%;">';

            // Initialize Cropper.js
            cropper = new Cropper(document.getElementById('cropImage'), {
                aspectRatio: 1, // Adjust as needed
                viewMode: 0,
            });

            console.log('Gambar berhasil dimuat untuk pemangkasan.');
        };

        // Read in the image file as a data URL
        reader.readAsDataURL(file);
    } else {
        image.innerHTML = '<p class="text-danger">File not supported!</p>';
        console.log('File yang dipilih bukan file gambar.');
    }
});

// Handle form submission
document.getElementById('cropForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Get cropped data
    var croppedData = cropper.getData();

    console.log('Data pemangkasan yang akan dikirim:', croppedData);

    // Set cropped data into hidden input field
    document.getElementById('cropData').value = JSON.stringify(croppedData);

    // Submit the form
    this.submit();
});
</script>
@endsection