<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            margin: 120px 40px 80px 40px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 0;
        }

        /* ===== HEADER ===== */
        header {
            position: fixed;
            top: -100px;
            left: 0;
            right: 0;
            height: 100px;
            text-align: center;
        }

        header img {
            width: 55%;
        }

        /* ===== FOOTER ===== */
        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
        }

        footer img {
            width: 45%;
            height: 30px;
        }

        /* ===== TABLE ===== */
        table.dashboardTable {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid black;
            page-break-inside: auto;
        }

        table.dashboardTable th,
        table.dashboardTable td {
            border: 1px solid black;
            padding: 3px;
            height: 27px;
            vertical-align: middle;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        th {
            text-align: center;
            font-weight: bold;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <header>
        <img src="{{ public_path('template/img/header_new.png') }}">
        <h3 style="margin-top:-5px;">DOCUMENT LOGBOOK</h3>
    </header>

    {{-- FOOTER --}}
    <footer>
        <img src="{{ public_path('template/img/footer-logbook.png') }}">
    </footer>

    @php
        $rowsPerPage = 15;
        $chunks = $routingSlips->values()->chunk($rowsPerPage);
        $totalPages = $chunks->count();
    @endphp

    @foreach ($chunks as $pageIndex => $chunk)
        <table class="dashboardTable">
            <thead>
                <tr>
                    <th style="width: 5%;">CTRL #</th>
                    <th style="width: 8%;">DATE RECEIVED</th>
                    <th style="width: 14%;">SOURCE</th>
                    <th style="width: 25%;">SUBJECT MATTER</th>
                    <th style="width: 10%;">ACTION UNIT</th>
                    <th style="width: 8%;">RECEIVED BY / DATE</th>
                    <th style="width: 12%;">ACTION TAKEN</th>
                    <th style="width: 8%;">DATE RELEASED</th>
                    <th style="width: 7%;">REMARKS</th>
                    <th style="width: 5%;">STORAGE</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($chunk as $row)
                    <tr>
                        <td>{{ $row->rslip_id }}</td>

                        <td>
                            {{ $row->date_received ? \Carbon\Carbon::parse($row->date_received)->format('M d, Y') : 'N/A' }}
                        </td>

                        <td>{{ $row->source ?? 'N/A' }}</td>

                        {{-- SUBJECT (truncate to avoid overflow) --}}
                        @php
                            $subject = $row->subject ?? 'N/A';
                            $words = explode(' ', $subject);
                            if (count($words) > 80) {
                                $subject = implode(' ', array_slice($words, 0, 80)) . '...';
                            }
                        @endphp
                        <td>{{ $subject }}</td>


                        <td>
                            @if ($row->transaction_type == 1)
                                Dr. Aladino C. Moraca
                            @elseif ($row->transaction_type == 2)
                                &nbsp;
                            @endif
                        </td>


                        <td>
                            {{ $row->updated_at ? $row->updated_at->format('M d, Y') : 'N/A' }}
                        </td>



                        <td>
                            @php
                                $routedUser = $users[$row->routed_users] ?? null;
                                $reassignedUser = $users[$row->reassigned_to] ?? null;
                            @endphp

                            {{-- Original routed user --}}
                            @if ($routedUser)
                               <strong> {{ $routedUser->fname . ' ' . $routedUser->lname }} </strong>
                            @endif

                            {{-- Re-assigned user --}}
                            @if ($reassignedUser)
                                , re-assigned to
                                <strong>
                                    {{ $reassignedUser->fname . ' ' . $reassignedUser->lname }}
                                </strong>
                            @endif
                        </td>



                        <td>{{ $row->created_at->format('m-d-Y h:i:s A') }}</td>
                        <td>{{ $row->trans_remarks ?? ' ' }}</td>
                        <td>&nbsp;</td>
                    </tr>
                @endforeach

                {{-- FILL EMPTY ROWS TO KEEP 15 PER PAGE --}}
                @for ($i = count($chunk); $i < $rowsPerPage; $i++)
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                @endfor
            </tbody>
        </table>

        {{-- PAGE BREAK --}}
        @if ($pageIndex + 1 < $totalPages)
            <div class="page-break"></div>
        @endif
    @endforeach

</body>

</html>
``
