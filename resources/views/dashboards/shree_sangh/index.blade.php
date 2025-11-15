@extends('includes.layouts.shree_sangh')

@section('content')
<div class="container py-4">

    <h4 class="mb-4 text-center text-white py-3 px-4 rounded-4"
        style="font-family: 'Bebas Neue', sans-serif; font-size: 28px; background: linear-gradient(to right, #36D1DC, #5B86E5); box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        📘 Dashboard Features
    </h4>

    <div class="row row-cols-1 row-cols-md-3 g-4">

       

        <!-- 🚶‍♂️ Card 2: विहार जानकारी -->
        <div class="col">
            <div class="card h-100 border-0 shadow-lg rounded-4">
                <div class="card-body text-center" style="background: linear-gradient(to right, #ff9966, #ff5e62); color: white; border-radius: 1rem 1rem 0 0;">
                    <img src="{{ asset('images/vihar_seva.jpg') }}" alt="Vihar Logo" class="mb-3" style="width: 60px; height: auto; border-radius: 8px;">
                    <h5 class="card-title fs-5">🚶‍♂️ विहार जानकारी</h5>
                </div>
                <div class="card-footer bg-white text-center rounded-bottom-4 py-3">
                    <a href="{{ route('vihar.sewa') }}" class="btn btn-outline-danger fw-semibold">👁️ देखें</a>
                </div>
            </div>
        </div>

   <!-- 🔍 Card 3: Vir Pariwar -->
<!-- <div class="col">
    <div class="card h-100 border-0 shadow-lg rounded-4">
        <div class="card-body text-center" style="background: linear-gradient(to right, #56ccf2, #2f80ed); color: white; border-radius: 1rem 1rem 0 0;">
            <h5 class="card-title fs-5">Pravarti</h5>
            <p class="card-text">View Edit Or Add A New Pravarti.</p>
        </div>
        <div class="card-footer bg-white text-center rounded-bottom-4 py-3">
            <a href="{{ url('/pravarti') }}" class="btn btn-outline-info fw-semibold">📂 Go</a>
        </div>
    </div>
</div> -->

<div class="col">
    <div class="card h-100 border-0 shadow-lg rounded-4">
        <div class="card-body text-center" style="background: linear-gradient(to right, #56ccf2, #2f80ed); color: white; border-radius: 1rem 1rem 0 0;">
            <h5 class="card-title fs-5">NEWS</h5>
            <p class="card-text">View, Edit Or Add a New NEWS.</p>
        </div>
        <div class="card-footer bg-white text-center rounded-bottom-4 py-3">
            <a href="{{ url('/news') }}" class="btn btn-outline-primary fw-semibold">📂 Go</a>
        </div>
    </div>
</div>

  <div class="col">
            <div class="card h-100 border-0 shadow-lg rounded-4">
                <div class="card-body text-center" style="background: linear-gradient(to right, #ff9966, #ff5e62); color: white; border-radius: 1rem 1rem 0 0;">
            <h5 class="card-title fs-5">विचार</h5>
                        <p class="card-text">View, Edit Or Add a New Thoughts.</p>

                </div>
                <div class="card-footer bg-white text-center rounded-bottom-4 py-3">
                    <a href="{{ route('daily.thoughts') }}" class="btn btn-outline-danger fw-semibold">👁️ देखें</a>
                </div>
            </div>
        </div>

        <!-- Add more cards here with same structure -->

    </div>
</div>
@endsection
