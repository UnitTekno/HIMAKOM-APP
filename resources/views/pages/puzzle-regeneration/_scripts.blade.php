<script type="module">
    class Puzzle {
        constructor() {
            // Empty & Subject
            this.emptyImage = "{{ asset(config('tablar.default.preview.path')) }}"
            this.subject = 'puzzle';

            // Modal
            this.modalAdd = new bootstrap.Modal($(`#modal-add-puzzle`));
            this.modalEdit = new bootstrap.Modal($(`#modal-edit-puzzle`));

            // Form
            this.formAdd = $(`#form-add-puzzle`);
            this.formEdit = $(`#form-edit-puzzle`);


            // URL
            this.storeUrl = "{{ route('puzzle.puzzle-regeneration.store') }}";
            this.editUrl = "{{ route('puzzle.puzzle-regeneration.edit', ':id') }}";
            this.deleteUrl = "{{ route('puzzle.puzzle-regeneration.destroy', ':id') }}";
            this.updateUrl = "{{ route('puzzle.puzzle-regeneration.update', ':id') }}";

            // DataTable
            this.table = $('#table-puzzle');
            this.tableDataUrl = "{{ route('puzzle.puzzle-regeneration.index') }}";
            this.tableColumns = [{
                    title: 'No',
                    data: null,
                    orderable: false,
                    searchable: false,
                    responsivePriority: 1,
                    width: '1%',
                    className: 'dt-center',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'picture',
                    name: 'picture',
                    title: 'Picture',
                    orderable: false,
                    searchable: false,
                    className: 'dt-center',
                    responsivePriority: 1,
                    width: '10%',
                    render: function(data, type, row) {
                        return `<img src="${data}" alt="picture" class="img-fluid" width="100">`;
                    }
                },
                {
                    data: 'title',
                    name: 'title',
                    title: 'Title',
                    responsivePriority: 2,
                    width: '10%'
                },
                {
                    data: 'expected_answer',
                    name: 'expected_answer',
                    title: 'Expected Answer',
                    responsivePriority: 3,
                    width: '10%'
                },
                {
                    data: null,
                    title: 'Action',
                    orderable: false,
                    searchable: false,
                    responsivePriority: 1,
                    width: '1%',
                    render: function(data, type, row) {
                        let html = '';
                        let btn = '';

                        @can('update-puzzle')
                            btn += `
                                <li><a class="dropdown-item btn-edit" href="" data-id="${data.id}"><i class="ti ti-pencil"></i>&nbsp; Edit</a></li>
                            `;
                        @endcan

                        @can('delete-puzzle')
                            btn += `
                                <li><a class="dropdown-item btn-delete" href="" data-id="${data.id}"><i class="ti ti-trash"></i>&nbsp; Delete</a></li>
                            `;
                        @endcan

                        @if (auth()->user()->hasAnyPermission(['update-puzzle', 'delete-puzzle']))
                            html = `
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        Action
                                    </button>
                                    <ul class="dropdown-menu">                                    
                                        ${btn}
                                    </ul>
                                </div>
                            `;
                        @endif

                        return html;
                    }
                },
            ];
        }

        initDtEvents() {
            $(`#modal-add-${this.subject}`).on("hidden.bs.modal", (e) => {
                $(".is-invalid").removeClass("is-invalid");
                $(".invalid-feedback").remove();
                $(`#preview-add-picture`).attr("src", this.emptyImage);

                this.formAdd[0].reset();
            });

            $(`#modal-edit-${this.subject}`).on("hidden.bs.modal", (e) => {
                $(".is-invalid").removeClass("is-invalid");
                $(".invalid-feedback").remove();
                $(`#preview-edit-picture`).attr("src", this.emptyImage);

                this.formEdit[0].reset();
            });

            $(`#add-picture`).on("change", () => {
                const file = $(`#add-picture`)[0].files[0];
                const name = $(`#add-picture`)[0].name;
                const reader = new FileReader();

                reader.onload = function(e) {

                    $(`#preview-add-${name}`).attr("src", e.target.result);
                };
                reader.readAsDataURL(file);
            });

            $(`#edit-picture`).on("change", () => {
                const file = $(`#edit-picture`)[0].files[0];
                const name = $(`#edit-picture`)[0].name;
                const reader = new FileReader();

                reader.onload = function(e) {

                    $(`#preview-edit-${name}`).attr("src", e.target.result);
                };
                reader.readAsDataURL(file);
            });
        }

        initDtTable() {
            this.table.DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: this.tableDataUrl,
                columns: this.tableColumns,
                drawCallback: () => {
                    $('.btn-edit').on('click', (e) => {
                        e.preventDefault();

                        // Set ID, We'll use it later
                        const id = $(e.currentTarget).data("id");
                        $(`#edit-id-${this.subject}`).val(id);

                        $.ajax({
                            url: this.editUrl.replace(":id", id),
                            method: "GET",
                            success: (response) => {
                                $('#edit-title').val(response.data?.title);
                                $('#edit-expected_answer').val(response.data?.expected_answer);
                                $(`#preview-edit-picture`).attr("src", response.data?.picture);
                                this.modalEdit.show();
                            },
                            error: function(xhr, ajaxOptions, thrownError) {
                                Swal.fire("Error!", thrownError, "error");
                            },
                        });
                    });

                    $('.btn-delete').on('click', (e) => {
                        e.preventDefault();

                        const id = $(e.currentTarget).data("id");

                        Swal.fire({
                            title: "Are you sure?",
                            text: "You won't be able to revert this!",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Yes, delete it!",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: this.deleteUrl.replace(":id", id),
                                    method: "DELETE",
                                    success: (response) => {
                                        if (response.status === "success") {
                                            Swal.fire({
                                                icon: "success",
                                                title: "Berhasil",
                                                text: response.message,
                                                showConfirmButton: false,
                                                timer: 1500,
                                            }).then(() => {
                                                this.table.DataTable().ajax.reload();

                                                $(`#card-${this.subject}`).before(`
                                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                        <strong>Success!</strong> ${response.message}
                                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                    </div>
                                                `);

                                                $(".alert").delay(3000).slideUp(300);
                                            });
                                        }
                                    },
                                    error: (xhr, ajaxOptions, thrownError) => {
                                        Swal.fire("Error!", thrownError, "error");

                                        $(`#card-${this.subject}`).before(`
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <strong>Error!</strong> ${thrownError}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                        `);

                                        $(".alert").delay(3000).slideUp(300);
                                    },
                                });
                            }
                        });
                    });
                }
            });
        }

        initDtSubmit() {
            $(`#submit-add-${this.subject}`).on("click", (e, item) => {
                e.preventDefault();

                $(".is-invalid").removeClass("is-invalid");
                $(".invalid-feedback").remove();
                $(`#submit-add-${this.subject}`).attr("disabled", true);
                $(`#submit-add-${this.subject}`).addClass("btn-loading");

                const formData = new FormData(this.formAdd[0]);

                $.ajax({
                    url: this.storeUrl,
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    complete: () => {
                        $(`#submit-add-${this.subject}`).attr("disabled", false);
                        $(`#submit-add-${this.subject}`).removeClass("btn-loading");
                    },
                    success: (response) => {
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil",
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500,
                        }).then(() => {
                            this.modalAdd.hide();

                            this.table.DataTable().ajax.reload();

                            $(`#card-${this.subject}`).before(`
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success!</strong> ${response.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            `);

                            $(".alert").delay(3000).slideUp(300);
                        });
                    },
                    error: (response) => {
                        if (response.status === 422) {
                            const errors = response.responseJSON.errors;
                            for (const key in errors) {
                                if (Object.hasOwnProperty.call(errors, key)) {
                                    const element = errors[key];
                                    $(`#add-${key}`).addClass("is-invalid");
                                    $(`#add-${key}`).after(`<div class="invalid-feedback">${element[0]}</div>`);
                                }
                            }
                        } else {
                            Swal.fire("Error!", "Something went wrong!", "error");
                        }
                    },
                });
            });

            $(`#submit-edit-${this.subject}`).on("click", (e, item) => {
                e.preventDefault();

                $(".is-invalid").removeClass("is-invalid");
                $(".invalid-feedback").remove();
                $(`#submit-edit-${this.subject}`).attr("disabled", true);
                $(`#submit-edit-${this.subject}`).addClass("btn-loading");

                const formData = new FormData(this.formEdit[0]);
                formData.append("_method", "PUT");

                $.ajax({
                    url: this.updateUrl.replace(":id", formData.get("id")),
                    method: "POST",
                    processData: false,
                    contentType: false,
                    data: formData,
                    complete: () => {
                        $(`#submit-edit-${this.subject}`).attr("disabled", false);
                        $(`#submit-edit-${this.subject}`).removeClass("btn-loading");
                    },
                    success: (response) => {
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil",
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500,
                        }).then(() => {
                            this.modalEdit.hide();

                            this.table.DataTable().ajax.reload();


                            $(`#card-${this.subject}`).before(`
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <strong>Success!</strong> ${response.message}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                `);

                            $(".alert").delay(3000).slideUp(300);
                        });
                    },
                    error: (response) => {
                        if (response.status === 422) {
                            const errors = response.responseJSON.errors;
                            for (const key in errors) {
                                if (Object.hasOwnProperty.call(errors, key)) {
                                    const element = errors[key];
                                    $(`#edit-${key}`).addClass("is-invalid");
                                    $(`#edit-${key}`).after(`<div class="invalid-feedback">${element[0]}</div>`);
                                }
                            }
                        } else {
                            Swal.fire("Error!", "Something went wrong!", "error");
                        }
                    },
                });
            });

        }
    }

    $(document).ready(function() {
        const puzzle = new Puzzle();
        puzzle.initDtEvents();
        puzzle.initDtTable();
        puzzle.initDtSubmit();
    });
</script>
