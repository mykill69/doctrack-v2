@extends('home.main')

@section('body')
    <div class="main-content">
        <section class="section">
            <div class="row row-deck">
                <div class="col-12">
                    <div class="card position-relative">
                        <img src="{{ asset('template/img/cpsu_logo.png') }}" alt="cpsu logo" width="110" height="110"
                            class="position-absolute" style="top: -50px; left: 50%; transform: translateX(-50%);">

                        <div class="card-header">
                            <h4>TRANSACTION LOGS</h4>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped datatable" id="table-logs">
                                    <thead>
                                        <tr>
                                            <th>System Logs</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse ($logs as $log)
                                            @php
                                                // Default badge
                                                $badgeClass = 'badge-secondary';

                                                // Determine badge class based on log type
                                                if ($log['text'] === 'Acknowledged the document') {
                                                    $badgeClass = 'badge-success';
                                                } elseif ($log['text'] === 'Routed to users') {
                                                    $badgeClass = 'badge-info';
                                                } elseif (str_starts_with($log['text'], 'Re-routed document to')) {
                                                    $badgeClass = 'badge-warning';
                                                } elseif ($log['text'] === 'Archived') {
                                                    $badgeClass = 'badge-warning'; // ✅ Badge for Archived
                                                }
                                            @endphp

                                            <tr>
                                                <td>
                                                    @if ($log['text'] === 'Archived' && $log['file'])
                                                        
                                                        <strong>RECORDS OFFICE</strong> <span class="badge {{ $badgeClass }} me-1">Archived</span>
                                                        <em>{{ $log['file'] }}</em> of year
                                                        <strong class="badge badge-warning">{{ optional($log['rslipObj'])->validity ?? '—' }}</strong>
                                                    @else
                                                        <strong>{{ $log['user'] ? $log['user']->fname . ' ' . $log['user']->lname : 'Unknown User' }}</strong>

                                                        <span class="badge {{ $badgeClass }} ms-1">
                                                            {{ $log['text'] }}
                                                        </span>

                                                        @if (!empty($log['routed_users']) && $log['text'] !== 'Acknowledged the document')
                                                            to <strong>{{ $log['routed_users'] }}</strong>
                                                        @endif

                                                        @if (!empty($log['rslip']))
                                                            with Control #: <strong>{{ $log['rslip'] }}</strong>
                                                        @endif

                                                        @if (!empty($log['file']))
                                                            — File: <em>{{ $log['file'] }}</em>
                                                        @endif
                                                    @endif
                                                </td>

                                                <td>
                                                    {{ \Carbon\Carbon::parse($log['date'])->format('Y-m-d H:i:s') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center text-muted">
                                                    No transaction logs found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
