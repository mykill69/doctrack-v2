@extends('home.main')
@section('body')
    <style>
        .timeline {
            list-style: none;
            padding: 0;
            position: relative;
        }

        .timeline:before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 7px;
            width: 7px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
            padding-left: 60px;
        }

        .timeline-badge {
            position: absolute;
            left: 4px;
            top: 0;
            width: 20px;
            height: 20px;
            text-align: center;
            line-height: 20px;
        }

        .timeline-badge i {
            font-size: 38px;
            /* make icon bigger */
            color: #563D7C;
            background-color: #fff;
            border-radius: 50%;
            width: 38px;
            height: 38px;
        }

        .timeline-panel {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            border: 1px solid #c7d2db;
        }
    </style>

    <div class="main-content">
        <section class="section">
            <div class="row row-deck">
                <div class="col-12">
                    <div class="card position-relative">
                        <!-- Logo centered on top of the card header -->
                        <img src="{{ asset('template/img/cpsu_logo.png') }}" alt="cpsu logo" width="110" height="110"
                            class="position-absolute" style="top: -50px; left: 50%; transform: translateX(-50%);">

                        <div class="card-header text-center">
                            <h4>ROUTING DOCUMENT TIMELINE</h4>
                        </div>

                        <div class="card-body">
                            <div class="row">

                                {{-- Timeline Events --}}
                                <div class="col-md-8">
                                    @if ($logs->isNotEmpty())
                                        @php $firstLog = $logs->first(); @endphp
                                        @if ($firstLog)
                                            <div class="card shadow mb-3">
                                                <div class="card-header text-light" style="background-color: #1F5036;">
                                                    @php
                                                        $fileName = $firstLog->file ?? 'No File';
                                                        $trimmedFileName = preg_replace(
                                                            '/^\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}_/',
                                                            '',
                                                            $fileName,
                                                        );
                                                    @endphp

                                                    <h5 class="mb-0 text-light">
                                                        <a href="{{ asset('storage/documents/' . $fileName) }}"
                                                            target="_blank" style="text-decoration: none;color:#fff">
                                                            {{ $trimmedFileName }} <small>[click to view file]</small>
                                                        </a>
                                                    </h5>
                                                </div>

                                                <div class="card-body">
                                                    <ul class="timeline">
                                                        @php $previousLogTime = null; @endphp
                                                        @foreach ($logs as $log)
                                                            @php
                                                                $routedUsers = explode(',', $log->r_users ?? '');
                                                                $currentUserId = auth()->id();
                                                                $isDisabled = !in_array($currentUserId, $routedUsers);
                                                            @endphp

                                                            {{-- Only show timeline if user has access or is admin --}}
                                                            @if (auth()->user()->role === 'Administrator' ||
                                                                    in_array($currentUserId, $routedUsers) ||
                                                                    $log->creator_id == $currentUserId)
                                                                <li class="timeline-item">
                                                                    <div class="timeline-badge text-center">
                                                                        <i class="fas fa-user-circle"></i>
                                                                    </div>

                                                                    <div class="timeline-panel">
                                                                        <div class="row mb-2 align-items-center">
                                                                            {{-- Routed Users --}}
                                                                            <div class="col-md-5">
                                                                                @php
                                                                                    $routedUsersList = App\Models\User::whereIn(
                                                                                        'id',
                                                                                        $routedUsers,
                                                                                    )->get();
                                                                                    $routedNames = $routedUsersList
                                                                                        ->map(
                                                                                            fn($u) => $u->fname .
                                                                                                ' ' .
                                                                                                $u->lname,
                                                                                        )
                                                                                        ->implode(', ');
                                                                                @endphp
                                                                                <strong>
                                                                                    <h2
                                                                                        class="{{ $isDisabled ? 'text-danger' : '' }}">
                                                                                        {{ $routedNames }}</h2>
                                                                                </strong>
                                                                            </div>

                                                                            {{-- Reassigned To --}}
                                                                            <div class="col-md-2">
                                                                                <strong>Re-route To:</strong><br>
                                                                                @php
                                                                                    $reassignedIds = $log->reassigned_to
                                                                                        ? explode(
                                                                                            ',',
                                                                                            $log->reassigned_to,
                                                                                        )
                                                                                        : [];
                                                                                    $reassignedUsers = $users->whereIn(
                                                                                        'id',
                                                                                        $reassignedIds,
                                                                                    );
                                                                                @endphp
                                                                                @if ($reassignedUsers->count())
                                                                                    @foreach ($reassignedUsers as $user)
                                                                                        <small
                                                                                            class="badge badge-success">{{ $user->fname }}
                                                                                            {{ $user->lname }}</small>
                                                                                    @endforeach
                                                                                @else
                                                                                    <small
                                                                                        class="badge badge-secondary">—</small>
                                                                                @endif
                                                                            </div>

                                                                            {{-- Remarks --}}
                                                                            <div class="col-md-3">
                                                                                <strong>Remarks:</strong><br>
                                                                                <span><small
                                                                                        class="badge badge-success">{{ $log->ass_comment ?? '—' }}</small></span>
                                                                            </div>

                                                                            {{-- Updated Date --}}
                                                                            <div class="col-md-2 text-right text-muted"
                                                                                style="font-size:11px;">
                                                                                @if ($previousLogTime)
                                                                                    <span><i class="far fa-clock"></i>
                                                                                        {{ $log->updated_at->diffForHumans($previousLogTime) }}
                                                                                        previous</span>
                                                                                @else
                                                                                    <span><i class="far fa-clock"></i>
                                                                                        {{ $log->created_at->diffForHumans() }}</span>
                                                                                @endif
                                                                                <br>
                                                                                <small>{{ $log->updated_at->format('F j, Y h:i A') }}</small>
                                                                            </div>
                                                                        </div>

                                                                        <hr>
                                                                        {{-- Action Buttons --}}
                                                                        @if ($log->trans_status == 3)
                                                                            <button class="btn btn-success btn-md"
                                                                                disabled>This Document Has Been
                                                                                Acknowledged</button>
                                                                        @elseif($log->trans_status == 1 && $log->transaction_type == 2)
                                                                            <button
                                                                                class="btn btn-primary btn-md swal-acknowledge"
                                                                                data-log-id="{{ $log->id }}"
                                                                                @if ($isDisabled) disabled @endif>Acknowledge</button>
                                                                        @elseif($log->trans_status == 2)
                                                                            <button class="btn btn-warning btn-md"
                                                                                disabled>This Document Has Been
                                                                                Re-routed</button>
                                                                        @else
                                                                            <div class="dropdown mt-1">
                                                                                <button
                                                                                    class="btn btn-info btn-md dropdown-toggle"
                                                                                    type="button"
                                                                                    id="actionsDropdown{{ $log->id }}"
                                                                                    data-toggle="dropdown"
                                                                                    aria-haspopup="true"
                                                                                    aria-expanded="false"
                                                                                    @if ($isDisabled) disabled @endif>Select
                                                                                    Option</button>
                                                                                <div class="dropdown-menu"
                                                                                    aria-labelledby="actionsDropdown{{ $log->id }}">
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item swal-acknowledge"
                                                                                        data-log-id="{{ $log->id }}">Acknowledge</a>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item swal-reroute"
                                                                                        data-log-id="{{ $log->id }}"
                                                                                        data-slip-id="{{ $log->slip_id }}">Re-route</a>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </div>

                                                                    @php $previousLogTime = $log->updated_at; @endphp
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                        <li class="timeline-item">
                                                            <div class="timeline-badge text-center"><i
                                                                    class="fas fa-clock"></i></div>
                                                            <div class="timeline-panel text-center"><small
                                                                    class="text-muted">End of Timeline</small></div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        @else
                                            <h4 class="text-muted mt-5">This document was not routed to you or
                                                no documents found.</h4>
                                        @endif
                                    @else
                                        <h4 class="text-muted mt-5">This document was not routed to you or no
                                            documents found.</h4>
                                    @endif
                                </div>

                                {{-- Document Info --}}
                                <div class="col-md-4">
                                    @php $firstLog = $logs->first(); @endphp

                                    @if (
                                        $firstLog &&
                                            (auth()->user()->role === 'Administrator' ||
                                                in_array(auth()->id(), explode(',', $firstLog->r_users ?? '')) ||
                                                $firstLog->creator_id == auth()->id()))
                                        <div class="card shadow mb-3">
                                            <div class="card-header text-light" style="background-color: #1F5036;">
                                                <h5>Document Information</h5>
                                            </div>
                                            <div class="card-body p-0">
                                                <table class="table table-bordered mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <th scope="row">Control #</th>
                                                            <td>{{ optional($firstLog)->rslip_id ?? '—' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Subject</th>
                                                            <td>{{ optional($firstLog)->subject ?? '—' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Source</th>
                                                            <td>{{ optional($firstLog)->source ?? '—' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Remarks</th>
                                                            <td>{{ optional($firstLog)->trans_remarks ?? '—' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Creator</th>
                                                            <td>{{ optional($firstLog->creator)->fname ?? '—' }}
                                                                {{ optional($firstLog->creator)->lname ?? '' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Created At</th>
                                                            <td>{{ optional($firstLog)->created_at ? \Carbon\Carbon::parse($firstLog->created_at)->format('F j, Y h:i A') : '—' }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                  
                                    @endif
                                </div>

                            </div> {{-- row --}}
                        </div>

                        {{-- card-body --}}
                    </div>
                </div>
            </div>
        </section>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ackButtons = document.querySelectorAll('.swal-acknowledge');

            ackButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const logId = this.dataset.logId;

                    Swal.fire({
                        title: 'Do you confirm to acknowledge this routing?',
                        icon: 'info',
                        html: `
                    <button id="ackBtn${logId}" class="btn btn-primary">I acknowledge this document</button>
                `,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        position: 'center',
                    });

                    // Wait for SweetAlert to render, then attach click event
                    document.getElementById(`ackBtn${logId}`).addEventListener('click', function() {

                        // Axios POST request
                        axios.post('{{ route('acknowledgeLog') }}', {
                                log_id: logId,
                                slip_id: slipId, // 👈 PASS IT
                                routed_to: routedTo,
                                comments: comments
                            }, {
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(res => {
                                if (res.data.success) {
                                    Swal.fire({
                                        title: 'Acknowledged!',
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location
                                            .reload(); // reload to update timeline
                                    });
                                }
                            })
                            .catch(err => {
                                Swal.fire('Error!', 'Something went wrong.', 'error');
                                console.error(err);
                            });

                    });

                });
            });
        });
    </script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.swal-acknowledge').forEach(button => {

                button.addEventListener('click', function() {

                    const logId = this.dataset.logId;

                    Swal.fire({
                        title: 'Confirm Acknowledgement',
                        icon: 'info',
                        html: `
                    <button id="ackBtn${logId}" class="btn btn-primary">
                        I acknowledge this document
                    </button>
                `,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    });

                    document
                        .getElementById(`ackBtn${logId}`)
                        .addEventListener('click', function() {

                            axios.post('{{ route('acknowledgeLog') }}', {
                                    log_id: logId
                                }, {
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                })
                                .then(res => {
                                    if (res.data.success) {
                                        Swal.fire({
                                            title: 'Acknowledged!',
                                            icon: 'success',
                                            timer: 1500,
                                            showConfirmButton: false
                                        }).then(() => location.reload());
                                    }
                                })
                                .catch(err => {
                                    Swal.fire('Error!', 'Something went wrong.', 'error');
                                    console.error(err);
                                });

                        });
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.swal-reroute').forEach(button => {

                button.addEventListener('click', function() {

                    const logId = this.dataset.logId;
                    const slipId = this.dataset.slipId;

                    Swal.fire({
                        title: 'Re-route Slip Form',
                        icon: 'info',
                        html: `
                    <form id="rerouteForm${logId}">
                        <div class="form-group mb-2">
                            <label>Select personnel to re-route:</label>
                            <select id="routedTo${logId}" class="form-control select2" multiple required>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->fname }} {{ $user->lname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-2">
                            <label>Comments:</label>
                            <textarea id="comments${logId}" class="form-control" rows="2"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary mt-2">
                            Submit
                        </button>
                    </form>
                `,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,

                        // ✅ INIT SELECT2 HERE
                        didOpen: () => {
                            $('#routedTo' + logId).select2({
                                dropdownParent: $('.swal2-popup'),
                                width: '100%',
                                placeholder: 'Select personnel'
                            });
                        }
                    });

                    document
                        .getElementById(`rerouteForm${logId}`)
                        .addEventListener('submit', function(e) {

                            e.preventDefault();

                            const routedTo = $('#routedTo' + logId).val(); // ✅ select2-safe
                            const comments = document.getElementById(`comments${logId}`).value;

                            axios.post('{{ route('rerouteLog') }}', {
                                    log_id: logId,
                                    slip_id: slipId,
                                    routed_to: routedTo, // ARRAY
                                    comments: comments
                                }, {
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                })
                                .then(res => {
                                    if (res.data.success) {
                                        Swal.fire({
                                            title: 'Re-routed Successfully!',
                                            icon: 'success',
                                            timer: 1500,
                                            showConfirmButton: false
                                        }).then(() => location.reload());
                                    }
                                })
                                .catch(err => {
                                    Swal.fire('Error!', 'Something went wrong.', 'error');
                                    console.error(err);
                                });
                        });
                });
            });
        });
    </script>
@endsection
