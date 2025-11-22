@extends('admin.layouts.app')

@section('title', 'Tambah Data Produksi')

@push('styles')
<style>
    body {
      background: linear-gradient(135deg, #f0f5ff, #ffffff);
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
    }

    .card {
      border: none;
      border-radius: 1rem;
      background: #fff;
      box-shadow: 0 6px 25px rgba(0, 0, 0, 0.06);
    }

    .card-header {
      background: white;
      color: #333;
      border-radius: 0;
      padding: 1rem 1.5rem 16px;
      border-bottom: 1px solid #e9ecef;
      margin-bottom: 24px;
    }

    .card-header h1 {
      font-size: 1.5rem;
      font-weight: 700;
      color: #333;
      line-height: 1.2;
    }

    .card-header p {
      margin-bottom: 0;
      font-size: 0.9rem;
      opacity: 0.9;
    }

    .form-label {
      font-weight: 600;
      color: #333;
    }

    .form-control, .form-select {
      border-radius: 0.5rem;
      border: 1px solid #ced4da;
      transition: 0.2s;
    }

    .form-control:focus, .form-select:focus {
      border-color: #007bff;
      box-shadow: 0 0 0 0.15rem rgba(0,123,255,0.2);
    }

    .section-box {
      background: #f8fbff;
      border: 1px solid #e0e6f0;
      border-radius: 0.75rem;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
    }

    .section-title {
      font-weight: 600;
      color: #0077b6;
      border-left: 4px solid #00b4d8;
      padding-left: 0.75rem;
      margin-bottom: 1rem;
    }

    .section-title.manual {
      color: #007bff;
      border-left-color: #007bff;
    }

    .section-title.pembesaran {
      color: #28a745;
      border-left-color: #28a745;
    }

    .section-title.penetasan {
      color: #fd7e14;
      border-left-color: #fd7e14;
    }

    .btn-primary {
      background: linear-gradient(90deg, #007bff, #0096c7);
      border: none;
      border-radius: 0.5rem;
      transition: 0.3s;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      background: linear-gradient(90deg, #0069d9, #0084b4);
    }

    .btn-secondary {
      border-radius: 0.5rem;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
    }

    .btn-secondary:hover {
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    @media (max-width: 768px) {
      .card-header h1 {
        font-size: 1.2rem;
      }
      .jenis-input-radios {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
      }
      .btn-secondary {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        white-space: nowrap;
      }
      .btn-secondary i {
        font-size: 0.8rem;
      }
    }

    .field-auto-fill {
      transition: all 0.3s ease;
    }

    .field-auto-fill.auto-filled {
      background-color: #e8f5e8 !important;
      border-color: #28a745 !important;
      color: #155724 !important;
    }

    .detail-subsection {
      margin-bottom: 2rem;
      padding: 1.5rem;
      background: #f8f9fa;
      border-radius: 0.5rem;
      border-left: 4px solid #007bff;
    }

    .detail-subsection h6 {
      margin-bottom: 1rem;
      font-weight: 600;
    }

    .field-hint-manual, .field-hint-pembesaran, .field-hint-penetasan {
      transition: opacity 0.3s ease;
    }

    .required {
      color: #dc3545;
      font-weight: bold;
    }

    .date-input-wrapper {
      position: relative;
    }

    .date-input-wrapper .placeholder-label {
      position: absolute;
      inset-inline-start: 0.85rem;
      inset-block-start: 50%;
      transform: translateY(-50%);
      color: #94a3b8;
      pointer-events: none;
      font-size: 0.95rem;
      line-height: 1;
      transition: opacity 0.2s ease;
    }

    .date-input-wrapper input.date-input {
      caret-color: #1e293b;
    }

    .date-input-wrapper:not(.has-value) input.date-input:not(:focus) {
      color: transparent;
    }

    .date-input-wrapper:not(.has-value) input.date-input:not(:focus)::placeholder {
      color: transparent;
    }

    .date-input-wrapper input.date-input:focus + .placeholder-label,
    .date-input-wrapper.has-value .placeholder-label {
      opacity: 0;
    }
</style>
@endpush

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-12">
      <div class="card">
  @include('admin.pages.produksi.partials.create-form._form-header')

        <div class="card-body p-4">
          <form id="produksiForm" action="{{ route('admin.produksi.store') }}" method="POST" onsubmit="return validateForm()">
            @csrf

            @include('admin.pages.produksi.partials.create-form._form-errors')

            @include('admin.pages.produksi.partials.create-form._form-basic-info')

            @include('admin.pages.produksi.partials.create-form._form-manual-section')

            @include('admin.pages.produksi.partials.create-form._form-pembesaran-section')

            @include('admin.pages.produksi.partials.create-form._form-penetasan-section')

            @include('admin.pages.produksi.partials.create-form._form-production-details')

            @include('admin.pages.produksi.partials.create-form._form-notes')

            @include('admin.pages.produksi.partials.create-form._form-actions')
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
@include('admin.pages.produksi.partials.create-form._form-scripts')
@endpush

@endsection




