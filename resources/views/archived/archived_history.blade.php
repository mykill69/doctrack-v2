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
                            <h4>ARCHIVED HISTORY LOGBOOK</h4>
                        </div>

                        <div class="card-body">

                            <!-- Search Section -->

                            <div class="row align-items-center mb-3">

                                <!-- Search (optional, does not affect archive) -->
                                <div class="col-md-12 mb-2">
                                    <input type="text" id="searchInput" name="search" class="form-control"
                                        placeholder="Search Logbook Validity Year" value="{{ request('search') }}">
                                </div>

                            </div>


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
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');

            searchInput.addEventListener('keyup', function() {
                const query = this.value;

                fetch(`{{ route('archivedHistory') }}?search=${query}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('logbookTable').innerHTML = html;
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    </script>
@endsection
