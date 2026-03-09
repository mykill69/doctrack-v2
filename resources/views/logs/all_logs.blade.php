@extends('home.main')
@section('body')
    <div class="main-content">
        <section class="section">
            <div class="row row-deck">
                <div class="col-12">
                    <div class="card position-relative text-center">
                        <img src="{{ asset('template/img/cpsu_logo.png') }}" alt="cpsu logo" width="110" height="110"
                            class="position-absolute" style="top: -50px; left: 50%; transform: translateX(-50%);">

                        <div class="card-header">
                            <h4>SYSTEM LOGS</h4>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped text-small" id="table-2">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Action</th>
                                            <th>IP Address</th>
                                            <th>User Agent</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($systemLogs as $log)
                                            <tr>
                                                <td>{{ $log->user ? $log->user->fname . ' ' . $log->user->lname : 'Guest' }}
                                                </td>
                                                <td>{{ $log->action }}</td>
                                                <td>
                                                    @php
                                                        $ipLength = strlen($log->ip_address);
                                                        $maskedIp = '';
                                                        for ($i = 0; $i < $ipLength; $i++) {
                                                            $maskedIp .= chr(rand(65, 90)); // random uppercase letter
                                                        }
                                                    @endphp

                                                    <span class="masked-ip">{{ $maskedIp }}</span>

                                                    @if (auth()->user()->role === 'Administrator')
                                                        <button class="btn btn-sm btn-info view-ip-btn"
                                                            data-ip="{{ $log->ip_address }}">View</button>
                                                    @endif
                                                </td>

                                                <td>{{ $log->user_agent }}</td>
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
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.view-ip-btn');

            buttons.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Reveal the actual IP
                    btn.previousElementSibling.textContent = btn.dataset.ip;
                    btn.remove(); // Remove button after revealing
                });
            });
        });
    </script>
@endsection
