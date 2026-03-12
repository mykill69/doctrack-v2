<!DOCTYPE html>
<html lang="en">

<!--   Tue, 07 Jan 2020 03:33:27 GMT -->

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>CPSU Doctrack &mdash; v2</title>


    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">





    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('template/assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/modules/fontawesome/css/all.min.css') }}">




    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('template/assets/modules/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/modules/summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/modules/owlcarousel2/dist/assets/owl.carousel.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('template/assets/modules/owlcarousel2/dist/assets/owl.theme.default.min.css') }}">

    <link rel="stylesheet" href="{{ asset('template/assets/modules/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/modules/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/modules/jquery-selectric/selectric.css') }}">
    <link rel="stylesheet"
        href="{{ asset('template/assets/modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('template/assets/modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">

    <link rel="stylesheet" href="{{ asset('template/assets/modules/datatables/datatables.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('template/assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('template/assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css') }}">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('template/assets/css/style.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/components.min.css') }}">


    <link rel="shortcut icon" type="" href="{{ asset('template/img/cpsu_logo.png') }}">



    <style>
        table,
        table th,
        table td {
            font-family: "Source Sans Pro", -apple-system, BlinkMacSystemFont, " Segoe UI", Roboto, " Helvetica Neue", Arial, sans-serif, " Apple Color Emoji", " Segoe UI Emoji", " Segoe UI Symbol";
            font-size: 12px;
            color: #000000;
        }
    </style>


</head>

<body class="layout-4">


    <!-- Page Loader -->
    {{-- <div class="page-loader-wrapper">
        <span class="loader"><span class="loader-inner"></span></span>
    </div> --}}

    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>

            <!-- Start app top navbar -->
            <nav class="navbar navbar-expand-lg main-navbar">
                <form class="form-inline mr-auto" method="GET" action="{{ route('routingTimelineSearch') }}">
                    <ul class="navbar-nav mr-3">
                        <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i
                                    class="fas fa-bars"></i></a></li>
                        <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i
                                    class="fas fa-search"></i></a></li>
                    </ul>
                    <div class="search-element">
                        <input class="form-control" type="search" name="query"
                            placeholder="Search Control Number here" aria-label="Search" data-width="250">
                        <button class="btn" type="submit"><i class="fas fa-search"></i></button>
                        <div class="search-backdrop"></div>
                        <div class="search-result">
                            <!-- results will be rendered here dynamically -->
                        </div>
                    </div>
                </form>

                <ul class="navbar-nav navbar-right">

                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown"
                            class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                            <img alt="image" src="template/assets/img/avatar/avatar-1.png"
                                class="rounded-circle mr-1">
                            <div class="d-sm-none d-lg-inline-block">
                                @if (auth()->user()->role === 'records_officer')
                                    {{ auth()->user()->fname }} {{ auth()->user()->lname }} -
                                    {{ auth()->user()->mname }}
                                @else
                                    {{ auth()->user()->fname }} {{ auth()->user()->lname }}
                                @endif
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            {{-- <div class="dropdown-title">Logged in 5 min ago</div> --}}
                            <a href="features-profile.html" class="dropdown-item has-icon"><i class="far fa-user"></i>
                                Profile</a>

                            <a href="features-settings.html" class="dropdown-item has-icon" data-toggle="modal"
                                data-target="#aboutDts"><i class="fas fa-info-circle"></i>
                                About DTS</a>
                            <a href="features-activities.html" class="dropdown-item has-icon" data-toggle="modal"
                                data-target="#dataPrivacy"><i
                                    class="fas fa-scroll"></i> Terms & Conditions</a>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('logout') }}" class="dropdown-item has-icon text-danger"><i
                                    class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </li>
                </ul>
            </nav>

            <!-- Start main left sidebar menu -->
            @include('home.sidebar')

            @yield('body')
            <!-- Start app main Content -->


            <!-- Start app Footer part -->
            <footer class="main-footer">
                <div class="footer-left" style="font-style: italic;">
                    Maintained and Managed by Management Information System Office. All rights reserved.
                </div>
                <div class="footer-right">

                </div>
            </footer>
        </div>
    </div>


    @include('modal.aboutDts')
    @include('modal.dataPrivacy')

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- JS Libraies -->
    <!-- Core JS -->
    <script src="{{ asset('template/assets/bundles/lib.vendor.bundle.js') }}"></script>
    <script src="{{ asset('template/js/CodiePie.js') }}"></script>

    <!-- jQuery UI -->
    <script src="{{ asset('template/assets/modules/jquery-ui/jquery-ui.min.js') }}"></script>
    <!-- DataTables -->
    <script src="{{ asset('template/assets/modules/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('template/assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}">
    </script>
    <script src="{{ asset('template/assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js') }}"></script>
    <!-- Page Specific -->
    <script src="{{ asset('template/js/page/modules-datatables.js') }}"></script>

    <!-- JS Libraries -->
    <script src="{{ asset('template/assets/modules/jquery.sparkline.min.js') }}"></script>
    <script src="{{ asset('template/assets/modules/chart.min.js') }}"></script>
    <script src="{{ asset('template/assets/modules/owlcarousel2/dist/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('template/assets/modules/summernote/summernote-bs4.js') }}"></script>
    <script src="{{ asset('template/assets/modules/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>

    <script src="{{ asset('template/assets/modules/cleave-js/dist/cleave.min.js') }}"></script>
    <script src="{{ asset('template/assets/modules/cleave-js/dist/addons/cleave-phone.us.js') }}"></script>
    <script src="{{ asset('template/assets/modules/jquery-pwstrength/jquery.pwstrength.min.js') }}"></script>
    <script src="{{ asset('template/assets/modules/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('template/assets/modules/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}">
    </script>
    <script src="{{ asset('template/assets/modules/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ asset('template/assets/modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('template/assets/modules/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('template/assets/modules/jquery-selectric/jquery.selectric.min.js') }}"></script>

    <!-- Template -->
    <script src="{{ asset('template/js/scripts.js') }}"></script>
    <script src="{{ asset('template/js/custom.js') }}"></script>
    <script src="{{ asset('template/js/page/index.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.datatable').each(function() {
                if ($.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable().destroy();
                }
                $(this).DataTable({
                    order: [
                        [1, 'desc']
                    ],
                    pageLength: 10,
                    responsive: false
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.search-element input[name="query"]').on('keyup', function() {
                let query = $(this).val();
                if (query.length < 2) return; // optional: wait for at least 2 chars

                $.ajax({
                    url: "{{ route('routingTimelineSearch') }}",
                    method: "GET",
                    data: {
                        query: query
                    },
                    success: function(data) {
                        let resultsContainer = $('.search-result');
                        resultsContainer.html(''); // clear old results

                        if (data.logs.length === 0) {
                            resultsContainer.append(
                                '<div class="search-item">No results found</div>');
                        } else {
                            data.logs.forEach(log => {
                                resultsContainer.append(`
        <div class="search-item">
            <a href="/routing-timeline/${log.id}?slip_id=${log.slip_id}">
                ${log.creator ? log.creator.fname + ' ' + log.creator.lname : 'Guest'} — ${log.trans_remarks}
            </a>
        </div>
    `);
                            });
                        }
                    }
                });
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('success'))
                Swal.fire({
                    position: 'center', // center of the screen
                    icon: 'success', // success icon
                    title: "{{ session('success') }}",
                    showConfirmButton: false, // no OK button
                    timer: 3000, // auto-dismiss after 3 seconds
                    timerProgressBar: true, // show progress bar
                    background: '#28a745', // Bootstrap success green
                    color: '#ffffff', // text color
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
            @endif
        });
    </script>

    <script>
        $(document).on('click', '.edit-user-btn', function() {
            let button = $(this);
            let id = button.data('id');

            // Dynamically set form action using named route
            $('#editUserForm').attr('action', "{{ route('userEdit', ['id' => ':id']) }}".replace(':id', id));

            // Populate fields
            $('#edit_fname').val(button.data('fname'));
            $('#edit_mname').val(button.data('mname'));
            $('#edit_lname').val(button.data('lname'));
            $('#edit_email').val(button.data('email'));
            $('#edit_department').val(button.data('department')).trigger('change');
            $('#edit_role').val(button.data('role')).trigger('change');

            // Show modal
            $('#editUserModal').modal('show');
        });
    </script>



    <script>
        function acknowledgeLog(logId) {
            Swal.fire({
                title: 'Acknowledge this document?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, acknowledge',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/inter-office/log/${logId}/acknowledge`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(() => location.reload());
                }
            });
        }

        function returnLog(logId) {
            Swal.fire({
                title: 'Return with comments',
                input: 'textarea',
                inputPlaceholder: 'Enter remarks...',
                inputAttributes: {
                    'aria-label': 'Return remarks'
                },
                showCancelButton: true,
                confirmButtonText: 'Submit',
                preConfirm: (remarks) => {
                    if (!remarks) {
                        Swal.showValidationMessage('Remarks are required');
                    }
                    return remarks;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/inter-office/log/${logId}/return`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            remarks: result.value
                        })
                    }).then(() => location.reload());
                }
            });
        }
    </script>

</body>



<!--   Tue, 07 Jan 2020 03:35:12 GMT -->

</html>
