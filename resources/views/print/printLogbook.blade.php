@extends('home.main')
@section('body')
    <div class="main-content">
        <section class="section">
            <div class="row row-deck">
                <div class="col-12">
                    <div class="card position-relative text-center">
                        <!-- Logo centered on top of the card header -->
                        <img src="{{ asset('template/img/cpsu_logo.png') }}" alt="cpsu logo" width="110" height="110"
                            class="position-absolute" style="top: -50px; left: 50%; transform: translateX(-50%);">

                        <div class="card-header">
                            <!-- Add padding-top to account for the logo -->
                            <h4>PRINT LOGBOOK</h4>
                        </div>

                        <div class="card-body">

                            <!-- Search Section -->
                            <form id="printForm">
                                <div class="row align-items-center mb-3">

                                    <!-- Control # FROM -->
                                    <div class="col-md-3 mb-2">
                                        <input name="ctrl_from" class="form-control" placeholder="Control # From" required>
                                    </div>

                                    <!-- Control # TO -->
                                    <div class="col-md-3 mb-2">
                                        <input name="ctrl_to" class="form-control" placeholder="Control # To" required>
                                    </div>

                                    <!-- Routing Status -->
                                    <div class="col-md-3 mb-2">
                                        <select name="routing_status" class="form-control">
                                            <option value="">-- Select Routing Status --</option>
                                            <option value="1">Pending</option>
                                            <option value="2">In Progress</option>
                                            <option value="3">Completed</option>
                                        </select>
                                    </div>

                                    <!-- Print -->
                                    <div class="col-md-3 mb-2">
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-print"></i> Print
                                        </button>
                                    </div>

                                </div>
                            </form>

                            <hr class="my-2">
                            <div class="table-responsive">

                                <iframe id="pdfFrame" src="" width="100%" height="700"
                                    style="border:1px solid #ccc;"></iframe>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


    <script>
        document.getElementById('printForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const params = new URLSearchParams(new FormData(this)).toString();

            document.getElementById('pdfFrame').src =
                "{{ route('logbookPdf') }}?" + params;
        });
    </script>
@endsection
