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
                            <h4>ARCHIVE LOGBOOK</h4>
                        </div>

                        <div class="card-body">

                            <!-- Search Section -->
                            <form method="POST" action="{{ route('archiveLogbook.archive') }}">
                                @csrf

                                <div class="row align-items-center mb-3">

                                    <!-- Search (optional, does not affect archive) -->
                                    <div class="col-md-6 mb-2">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Search Logbook Validity Year" value="{{ request('search') }}">
                                    </div>

                                    <!-- Year Dropdown -->
                                    <div class="col-md-3 mb-2">
                                        @php
                                            $currentYear = now()->year;
                                            $startYear = $currentYear - 15;
                                            $endYear = $currentYear + 15;
                                        @endphp

                                        <select name="validity" class="form-control" required>
                                            <option value="">-- Select Year --</option>
                                            @for ($year = $endYear; $year >= $startYear; $year--)
                                                <option value="{{ $year }}">
                                                    {{ $year }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    <!-- Submit -->
                                    <div class="col-md-3 mb-2">
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-archive"></i> Archive Logbook
                                        </button>
                                    </div>

                                </div>
                            </form>

                            <hr class="my-2">
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-1">
                                    <thead>
                                        <tr>
                                            <th>CTRL #</th>
                                            <th>DATE RECEIVED</th>
                                            <th>SOURCE</th>
                                            <th>SUBJECT MATTER</th>
                                            <th>ACTION UNIT</th>
                                            <th>RECEIVED BY / DATE</th>
                                            <th>ACTION TAKEN</th>
                                            <th>DATE RELEASED</th>
                                            <th>REMARKS</th>
                                            <th>FILE NAME</th>
                                            <th>UPDATED DATE / BY</th>
                                            <th>VALIDITY</th>
                                            <th>LOGBOOK STATUS</th>
                                        </tr>
                                    </thead>

                                    <tbody id="logbookTable">
                                        @include('archived.partials.archive_logbook_rows')
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let search = this.value;

            fetch(`{{ route('archiveLogbook') }}?search=${search}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('logbookTable').innerHTML = data;
                });
        });
    </script>
@endsection
