<script type="module">
    class Play {

        initDtEvents() {
            // Handle clicks on level-cell
            $('.level-cell').on('click', (e) => {
                const puzzleId = $(e.currentTarget).data('puzzle-id');
                const actionUrl = $(e.currentTarget).data('action-url');
                const toggle = $(e.currentTarget).data('toggle');

                if (toggle === 'modal') {
                    const modal = new bootstrap.Modal($(`#puzzle-password-modal-${puzzleId}`));
                    modal.show();

                    const storeUrl = `/puzzle/play-puzzle/store/${puzzleId}`;
                    $(`#puzzle-password-form-${puzzleId}`).attr('action', storeUrl);

                    $(`#puzzle-password-modal-${puzzleId}`).data('bs.modal', modal);
                } else if (actionUrl) {
                    window.location.href = actionUrl;
                }
            });
        }

        initDtSubmit() {
            $('.btn-submit').on('click', function(e) {
                e.preventDefault();

                const puzzleId = $(this).data('puzzle-id');
                const form = $(`#puzzle-password-form-${puzzleId}`);

                form.find(".is-invalid").removeClass("is-invalid");
                form.find(".invalid-feedback").remove();
                $(this).attr('disabled', true).addClass('btn-loading');

                const formData = new FormData(form[0]);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    complete: () => {
                        $(this).attr('disabled', false).removeClass('btn-loading');
                    },
                    success: (response) => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500,
                        }).then(() => {
                            const modal = $(`#puzzle-password-modal-${puzzleId}`).data('bs.modal'); 
                            modal.hide();
                            
                            const detailUrl = `/puzzle/play-puzzle/detail?id=${response.puzzle_id}`;
                            window.location.href = detailUrl;

                            $('#card-play-puzzle').before(`
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success!</strong> ${response.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            `);

                            $('.alert').delay(3000).slideUp(300);
                        });
                    },
                    error: (response) => {
                        if (response.status === 422) {
                            const errors = response.responseJSON.errors;
                            for (const key in errors) {
                                if (Object.hasOwnProperty.call(errors, key)) {
                                    const element = errors[key];
                                    $(`#password-${puzzleId}`).addClass("is-invalid");
                                    $(`#password-${puzzleId}`).after(`<div class="invalid-feedback">${element[0]}</div>`);
                                }
                            }
                        } else if (response.status === 400) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Jawaban Salah!',
                                text: response.responseJSON.message,
                            });
                        } else {
                            Swal.fire('Error!', 'Something went wrong!', 'error');
                        }
                    },
                });
            });
        }
    }

    $(document).ready(function() {
        const play = new Play();
        play.initDtEvents();
        play.initDtSubmit();
    });
</script>
