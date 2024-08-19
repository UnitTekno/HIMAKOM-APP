@extends('tablar::page')

@section('content')
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ ucwords($activeMenu) ?? '' }}
                </div>
                <h2 class="page-title">
                    {{ ucwords($activeSubMenu) ?? '' }}
                </h2>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container">
        <div class="card" id="card-play-puzzle">
            <div class="card-header">
                <div class="card-header-puzzle">
                    <h1 class="header-title">Puzzle of Our Regeneration</h1>
                    <p class="header-description">"{{ $puzzle->title }}"</p>
                </div>
            </div>
            @can('read-puzzle')
            <div class="card-body d-flex flex-column justify-content-center p-3">

                <div id="carouselExample" class="carousel slide">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="{{ $puzzle->picture }}" class="d-block w-100" alt="{{ $puzzle->title }}">
                        </div>
                    </div>
                    {{-- <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button> --}}
                </div>
            </div>
            @endcan
            <div class="card-footer">
                <div class="d-flex justify-content-start">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection