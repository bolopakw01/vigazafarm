<!-- Display validation errors -->
@if ($errors->any())
  <div class="alert alert-danger">
    <h6 class="alert-heading">
      <i class="fa-solid fa-exclamation-triangle me-2"></i>
      Terjadi Kesalahan Validasi
    </h6>
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif