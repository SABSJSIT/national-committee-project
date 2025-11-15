@extends('includes.layouts.super_admin')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12 mb-4 text-center">
      <img src="{{ asset('images/mslogo.png') }}" alt="Mahila Samiti Logo" class="img-fluid" style="max-width: 200px;">
      <h2 class="fw-bold mt-3" style="font-family: 'Amita', cursive;">श्री अखिल भारतवर्षीय साधुमार्गी जैन महिला समिति</h2>
    </div>
  </div>

  <div class="row justify-content-center">
    <!-- View Members Button -->
    <div class="col-md-4 mb-4">
      <a href="{{ route('mahila-samiti-members') }}" class="btn btn-lg btn-primary w-100 py-3">
        <i class="bi bi-list-ul me-2"></i> View Members
      </a>
    </div>

    <!-- Add Members Button -->
    <div class="col-md-4 mb-4">
      <a href="{{ route('mahila-samiti-members.add') }}" class="btn btn-lg btn-success w-100 py-3">
        <i class="bi bi-person-plus me-2"></i> Add Members
      </a>
    </div>
  </div>
</div>

<!-- Include Amita font -->
@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Amita:wght@400;700&display=swap" rel="stylesheet">
@endpush
@endsection

<style>
    .role-greeting {
        background: #f7f7f7;
        border-left: 6px solid #ff6a00;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .shramnopasak-box {
        background: #fff8e1;
        border: 1px solid #ff9800;
        color: #333;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
</style>
