@extends('tablar::page')

@section('css')
@endsection

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
                <h1 class="header-title">Puzzle of Our Regeneration</h1>
            </div>
            @can('read-puzzle')
            <div class="card-body d-flex flex-column justify-content-center p-3">
                <h2>Levels</h2>
                <x-levels-grid :levels="$levels" :user-level="$userLevel" :puzzles="$puzzles"/>
            </div>
            @endcan
        </div>
    </div>
</div>

@include('pages.play-puzzle._modal')
{{-- <script>
    document.querySelectorAll('.level-cell').forEach(cell => {
        cell.addEventListener('click', () => {
            event.preventDefault();

            var myModal = new bootstrap.Modal(document.getElementById('passwordModal'));
            myModal.show();
        });
    });
</script> --}}
@endsection

@section('js')
    @include('pages.play-puzzle._scripts')
@endsection