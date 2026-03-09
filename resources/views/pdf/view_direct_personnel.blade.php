<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <title>{{ $slip->source ?? 'Direct To Personnel Distribution List' }}</title>

    <style>
        @page {
            margin: 130px 40px 100px 40px;
        }

        body {
            font-family: Arial, sans-serif;
        }

        header {
            position: fixed;
            top: -120px;
            left: 0;
            right: 0;
            height: 100px;
        }

        footer {
            position: fixed;
            bottom: -80px;
            left: 0;
            right: 0;
            height: 60px;
        }

        header img,
        footer img {
            width: 100%;
        }

        h1 {
            text-align: center;
            text-transform: uppercase;
            font-size: 16px;
            margin-top: -3%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            height: 32px;
        }

        th {
            background-color: #f8f8f8;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>

    <header>
        <img src="{{ public_path('template/img/header_new.png') }}">
    </header>

    <footer>
        <img src="{{ public_path('template/img/footer_new.png') }}">
    </footer>

    <main>
        <h1>Distribution List</h1>

        @php
            use App\Models\User;

            $rowsPerPage = 15;

            // Get routed user IDs
            $userIds = $slip->routed_users ? array_values(array_filter(explode(',', $slip->routed_users))) : [];

            // Fetch users in correct order
            $users = User::whereIn('id', $userIds)->get()->keyBy('id');

            // Convert IDs to full names
            $names = collect($userIds)->map(function ($id) use ($users) {
                return $users[$id]->fname . ' ' . $users[$id]->lname ?? '';
            });

            // Chunk into pages of 15
            $chunks = $names->chunk($rowsPerPage);

            // If no data, still render 1 page
            if ($chunks->isEmpty()) {
                $chunks = collect([collect()]);
            }

            $rowCount = 1;
        @endphp

        @foreach ($chunks as $pageIndex => $chunk)
            <table>
                <thead>
                    <tr>
                        <th colspan="6" height="70">{{ $slip->subject }}</th>
                    </tr>
                    <tr>
                        <th>NO.</th>
                        <th>OFFICE OF CUSTODIAN</th>
                        <th>DATE ISSUED</th>
                        <th>DATE RETRIEVED</th>
                        <th>RECEIVED BY</th>
                        <th>SIGNATURE</th>
                    </tr>
                </thead>
                <tbody>

                    {{-- ACTUAL DATA --}}
                    @foreach ($chunk as $name)
                        <tr>
                            <td>{{ $rowCount }}</td>
                            <td>{{ $name }}</td>
                            <td>{{ $slip->date_received ? \Carbon\Carbon::parse($slip->date_received)->format('M d, Y') : '' }}
                            </td>
                            <td>{{ $slip->date_received ? \Carbon\Carbon::parse($slip->date_received)->format('M d, Y') : '' }}
                            </td>
                            <td>{{ $name }}</td>
                            <td>&nbsp;</td>
                        </tr>
                        @php $rowCount++; @endphp
                    @endforeach

                    {{-- FILL BLANK ROWS TO 15 --}}
                    @for ($i = $chunk->count(); $i < $rowsPerPage; $i++)
                        <tr>
                            <td>{{ $rowCount }}</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td> {{-- No date for blank rows --}}
                            <td>&nbsp;</td> {{-- No date for blank rows --}}
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        @php $rowCount++; @endphp
                    @endfor

                </tbody>

            </table>

            {{-- PAGE BREAK EXCEPT LAST PAGE --}}
            @if (!$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach

    </main>

</body>

</html>
