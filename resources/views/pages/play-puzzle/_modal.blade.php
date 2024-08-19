@foreach ($puzzles as $puzzle)
<div class="modal fade" id="puzzle-password-modal-{{ $puzzle->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enter Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="puzzle-password-form-{{ $puzzle->id }}" method="POST" action="">
                    @csrf
                    <div class="mb-3">
                        <label for="password-{{ $puzzle->id }}" class="form-label">Password</label>
                        <div class="input-group input-group-flat">
                            <input type="password" class="form-control" id="password-{{ $puzzle->id }}" name="password" required>
                            <span class="input-group-text">
                                <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip" onclick="togglePasswordVisibility(event, 'password-{{ $puzzle->id }}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <circle cx="12" cy="12" r="2" />
                                        <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" />
                                    </svg>
                                </a>
                            </span>
                        </div>
                        <div id="password-error-{{ $puzzle->id }}" class="invalid-feedback"></div>
                    </div>
                </form>                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary ms-auto btn-submit" data-puzzle-id="{{ $puzzle->id }}">Submit</button>
            </div>
        </div>
    </div>
</div>
<script>
    function togglePasswordVisibility(event, inputId) {
        event.preventDefault(); // Prevent the default anchor click behavior

        const passwordInput = document.getElementById(inputId);
        const currentType = passwordInput.getAttribute('type');

        // Toggle the input type between 'password' and 'text'
        if (currentType === 'password') {
            passwordInput.setAttribute('type', 'text');
            event.target.setAttribute('title', 'Hide password');
            event.target.querySelector('svg').classList.add('show');
        } else {
            passwordInput.setAttribute('type', 'password');
            event.target.setAttribute('title', 'Show password');
            event.target.querySelector('svg').classList.remove('show');
        }
    }
</script>
@endforeach
