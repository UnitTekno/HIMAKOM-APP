<div class="container">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-2">
        @for ($i = 1; $i <= $levels; $i++) <div class="col">
            <div class="btn level-cell d-flex align-items-center justify-content-center border p-2 {{ $i <= $unlocked ? 'bg-primary text-light' : 'bg-dark text-light' }} hover">
                @if ($i <= $unlocked) <h3 class="mb-0">{{ $i }}</h3>
                    @else
                    <h3 class="mb-0"><i class="ti ti-lock"></i></h3>
                    @endif
            </div>
    </div>
    @endfor
</div>