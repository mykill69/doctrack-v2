@extends('home.main')
@section('body')
    <div class="main-content">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <section class="section">
            <div class="row row-deck">
                <div class="col-12">
                    <div class="card position-relative text-center">
                        <!-- Logo centered on top of the card header -->
                        <img src="{{ asset('template/img/cpsu_logo.png') }}" alt="cpsu logo" width="110" height="110"
                            class="position-absolute" style="top: -50px; left: 50%; transform: translateX(-50%);">

                        <div class="card-header">
                            <h4>MANAGEMENT LOGS</h4>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped text-tiny" id="table-2">
                                    <thead>
                                        <tr>
                                            <th>Logs</th>
                                            <th>Changes</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($systemLogs as $log)
                                            <tr>
                                                <td>
                                                    {{ $log->user->fname ?? 'N/A' }} {{ $log->user->lname ?? '' }}
                                                    <span class="badge badge-info"> {{ $log->action }}</span>

                                                    {{-- Display model name dynamically --}}
                                                    @if ($log->model_type === 'Office')
                                                        {{ \App\Models\Office::find($log->model_id)->office_name ?? 'N/A' }}
                                                    @elseif($log->model_type === 'Group')
                                                        {{ \App\Models\Group::find($log->model_id)->group_name ?? 'N/A' }}
                                                    @else
                                                        ID: {{ $log->model_id }}
                                                    @endif
                                                    {{ $log->model_type }}
                                                </td>
                                                <td>
                                                    {{-- Pretty print JSON changes --}}
                                                    <pre>{{ json_encode($log->changes, JSON_PRETTY_PRINT) }}</pre>
                                                </td>
                                                <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
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

    <script>
        $(document).ready(function() {
            $('#table-2').DataTable({
                "order": [
                    [4, "desc"]
                ], // Sort by date
                "pageLength": 25
            });
        });
    </script>
@endsection
