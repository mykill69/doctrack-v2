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
                            <h4>DOCUMENT LOGBOOK</h4>
                        </div>

                        <div class="card-body">
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
                                            {{-- <th>TOTAL DURATION</th> --}}
                                            @if (in_array(auth()->user()->role, ['records_officer', 'administrator']))
                                                <th>ACTION</th>
                                            @endif
                                        </tr>
                                    </thead>


                                    <tbody>
                                        @foreach ($pendingDocs as $slipId => $logs)
                                            @php
                                                // take the first (or latest) log for this slip
                                                $log = $logs->first();

                                                // merged r_users names for this slip
                                                $rUsersNames = $logs
                                                    ->flatMap(fn($l) => explode(',', $l->routed_users))
                                                    ->unique()
                                                    ->map(function ($id) {
                                                        $user = \App\Models\User::find($id);
                                                        return $user ? $user->fname . ' ' . $user->lname : null;
                                                    })
                                                    ->filter()
                                                    ->implode(', ');
                                            @endphp

                                            <tr>
                                                {{-- CTRL # --}}
                                                <td>
                                                    <a href="{{ route('viewRouteSlip', $slipId) }}" target="_blank"
                                                        class="font-weight-bold;text-decoration:none;">
                                                        {{-- {{ $slipId }} --}}
                                                        {{ $log->rslip_id }}
                                                    </a>
                                                </td>

                                                {{-- DATE RECEIVED --}}
                                                <td>{{ \Carbon\Carbon::parse($log->date_received)->format('F j, Y') }}
                                                </td>

                                                {{-- SOURCE --}}
                                                <td>{{ $log->source }}</td>

                                                {{-- SUBJECT MATTER --}}
                                                <td>{{ $log->subject }}</td>

                                                {{-- ACTION UNIT --}}
                                                <td>Dr. Aladino C. Moraca</td>

                                                {{-- RECEIVED BY / DATE --}}
                                                <td>
                                                    <small>{{ $log->created_at->format('F j, Y') }}</small>
                                                </td>

                                                {{-- ACTION TAKEN --}}
                                                <td>{{ $rUsersNames ?: '—' }}</td>

                                                {{-- DATE RELEASED --}}
                                                <td>
                                                    {{ $log->created_at ? $log->created_at->format('m-d-Y h:i:s A') : '—' }}
                                                </td>

                                                {{-- REMARKS --}}
                                                <td>{{ $log->trans_remarks ?? 'N/A' }}</td>

                                                {{-- FILE NAME --}}
                                                <td>
                                                    @if ($log->file)
                                                        @php
                                                            $displayName = preg_replace(
                                                                '/^\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}_/',
                                                                '',
                                                                $log->file,
                                                            );
                                                        @endphp
                                                        <a href="{{ asset('storage/documents/' . $log->file) }}"
                                                            target="_blank">
                                                            {{ $displayName }}
                                                        </a>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>

                                                <td>
                                                    {{ $log->updated_at->format('m-d-Y h:i:s A') ?? 'N/A' }}
                                                </td>

                                                {{-- <td>
                                                    @if ($log->created_at && $log->updated_at)
                                                        {{ \Carbon\Carbon::parse($log->created_at)->diffForHumans($log->updated_at, true) }}
                                                    @else
                                                        —
                                                    @endif
                                                </td> --}}
                                                @if (in_array(auth()->user()->role, ['records_officer', 'administrator']))
                                                    <td>
                                                        <div class="buttons">
                                                            <a href="{{ route('editRecall', ['id' => $log->id]) }}"
                                                                class="btn btn-icon btn-info edit-slip-btn" target="_blank">
                                                                <span>Recall</span> <i class="fas fa-undo-alt"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @if (auth()->user()->dpa === null || auth()->user()->dpa == 0)
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                $('#dataPrivacy').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#dataPrivacy').modal('show');
            });
        </script>
    @endif

    <!-- Page Specific JS File -->
@endsection
