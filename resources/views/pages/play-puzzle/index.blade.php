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
                <h1>Puzzle Regeneration</h1>
            </div>
            @can('read-puzzle')
            <div class="card-body d-flex flex-column justify-content-center p-3">
                <h2>Levels</h2>
                <x-levels-grid :levels="$levels" :unlocked="$unlocked" />
            </div>
            @endcan
        </div>
    </div>
</div>

<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordModalLabel">Enter Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- <form id="passwordForm" method="POST" action=""> -->
                <!-- test dummy -->
                <form id="passwordForm" method="" action="">
                    @csrf
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
                </form>
                <!-- test dummy -->
                <a href="play-puzzle/detail">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
@include('pages.puzzle-regeneration._scripts')
<script>
    document.querySelectorAll('.level-cell').forEach(cell => {
        cell.addEventListener('click', () => {
            event.preventDefault();

            var myModal = new bootstrap.Modal(document.getElementById('passwordModal'));
            myModal.show();
        });
    });
</script>
@endsection