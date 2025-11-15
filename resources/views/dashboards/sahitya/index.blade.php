@extends('includes.layouts.sahitya')

@section('content')
<div class="container mt-4">
    <h1 class="text-center mb-4">🏛️ Welcome to Sahitya Dashboard</h1>

    <div class="row justify-content-center">
        <!-- Card 1: Upload & Manage Shramnopasak -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-primary h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">📤 Upload & Manage</h5>
                    <p class="card-text">Shramnopasak file upload, edit & delete panel.</p>
                    <a href="{{ route('shramnopasak.view') }}" class="btn btn-primary">Go to Upload Panel</a>
                </div>
            </div>
        </div>

        <!-- Card 2: View All Shramnopasak Records -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-success h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">📚 All Records</h5>
                    <p class="card-text">See all published Shramnopasak entries without pagination.</p>
                    <a href="{{ url('/shramnopasak/all-view') }}" class="btn btn-success">View All</a>
                </div>
            </div>
        </div>
        <!-- Card 3: Chaturmas Suchi -->
         <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-info h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">📅 Chaturmas Suchi </h5>
                    <p class="card-text">Upload and manage Chaturmas Suchi PDF files.</p>
                    <a href="{{ route('chaturmas_suchi.view') }}" class="btn btn-info">Go to Chaturmas Suchi</a>
            
    </div>
</div>
</div>

  <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-success h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">📚 Pakhi Ka paana</h5>
                    <p class="card-text">Add Edit Or Delete Pakhi Ka Panna.</p>
                    <a href="{{ route('pakhi.view') }}" class="btn btn-info">Pakhi</a>
                </div>
            </div>
        </div>
@endsection
