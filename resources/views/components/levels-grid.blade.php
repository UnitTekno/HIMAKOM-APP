<div class="container">
    @if($levels > 0)
        <div class="row row-cols-5 g-2">
            @foreach ($puzzles as $puzzle) 
                <div class="col">
                    <div class="btn level-cell d-flex align-items-center justify-content-center border p-2 
                        {{ $puzzle->index <= $userLevel->puzzle_level 
                            ? 'bg-primary text-light' 
                            : 'bg-dark text-light' 
                        }} hover" 
                        data-puzzle-id="{{ $puzzle->id }}"
                        data-action-url="{{ $puzzle->index < $userLevel->puzzle_level 
                            ? route('puzzle.play-puzzle.detail', ['id' => $puzzle->id]) 
                            : '' }}"
                        data-toggle="{{ $puzzle->index == $userLevel->puzzle_level ? 'modal' : '' }}"
                        data-target="{{ $puzzle->index == $userLevel->puzzle_level ? '#puzzle-password-modal-' . $puzzle->id : '' }}">
                        @if ($puzzle->index <= $userLevel->puzzle_level) 
                            <h3 class="mb-0">{{ $puzzle->index }}</h3>
                        @else
                            <h3 class="mb-0"><i class="ti ti-lock"></i></h3>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-warning text-center" role="alert">
            Puzzle of Our Regeneration belum tersedia.
        </div>
    @endif
</div>
