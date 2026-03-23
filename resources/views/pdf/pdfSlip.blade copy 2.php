<style>
    /* HEADER */
    .slip-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .header-center {
        text-align: center;
        flex: 1;
    }

    .header-center img {
        display: block;
        margin: 0 auto;
        height: 70px;
    }

    .header-center h1 {
        margin-top: 6px;
        font-size: 22px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .route-number {
        font-weight: bold;
        font-size: 14px;
    }

    /* BODY */
    .slip-body {
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-size: 14px;
    }

    .slip-row {
        display: flex;
        margin-bottom: 8px;
    }

    .slip-row strong {
        width: 180px;
        flex-shrink: 0;
    }

    .slip-row span {
        flex: 1;
    }

    .slip-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 8px;
    }

    .slip-col {
        font-size: 14px;
    }

    .slip-col strong {
        margin-right: 6px;
    }

    .text-right {
        text-align: right;
    }

    .slip-row {
        font-size: 14px;
        margin-top: 6px;
    }

    /* FOOTER */
    .slip-footer {
        display: flex;
        justify-content: space-between;

        margin-top: 30px;
        padding-top: 8px;
        font-size: 9px;
        font-family: Verdana, sans-serif;
    }

    .footer-col {
        width: 33.33%;
    }

    .footer-col.center {
        text-align: center;
    }

    .footer-col.left {
        text-align: left;
    }

    .footer-col.right {
        text-align: right;
    }

    /* SLIP GRID FOR TO + DATE */
    .to-date-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        /* vertically center both columns */
        margin-bottom: 10px;
    }

    /* LEFT COLUMN (TO) */
    .to-left {
        flex: 0 0 50%;
        display: flex;
        align-items: center;
        /* vertically center image + text */
        position: relative;
    }

    /* TO IMAGE OVERLAY */
    .to-overlay {
        position: relative;
        width: 100%;
        height: 50px;
        /* adjust to fit text */
    }

    /* TO IMAGE */
    .to-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 80%;
        /* fills container width */
        height: 70px;
        /* fixed height */
        object-fit: contain;
        /* preserves image aspect ratio */
        display: block;
        object-fit: contain;
        z-index: 1;
    }

    /* TEXT ON TOP OF IMAGE */
    .to-text {
        position: relative;
        z-index: 2;
        font-size: 13px;
        font-weight: bold;
        color: #000;
        margin-left: 50px;
        /* space after image */
        line-height: 1.2;
        display: flex;
        align-items: center;
        height: 100%;
    }

    /* RIGHT COLUMN (DATE) */
    .to-right {
        flex: 0 0 auto;
        /* width depends on content */
        display: flex;
        align-items: center;
        /* vertical center with TO */
        justify-content: flex-end;
        font-size: 14px;
    }

    .remark-item {
        display: flex;
        align-items: center;
        padding: 2px 0;
        font-size: 12px;
        margin-top: 1%;
    }

    .remark-img {
        width: 30px;
        height: 30px;
        object-fit: contain;
        margin-right: 6px;
    }

    .header-left {
        margin-left: 55%;
        margin-top: -5%;
        text-align: right;
    }

    table {
        font-family: Arial, Helvetica, sans-serif;
    }
</style>

<div class="slip-wrapper">
    <div class="content-wrapper">
        @foreach ($pdfData as $data)
            <div class="card-body">
                <div class="table-container">
                    <div class="slip-header">
                        <div class="header-left"> <span class="route-number">{{ $slip->op_ctrl }}</span></div>
                        <div class="header-center">
                            <img src="{{ public_path('template/img/header_new.png') }}" class="header-img"
                                alt="Header Image">
                            <h1 style="font-family: Arial, Helvetica, sans-serif;">Routing Slip</h1>
                            <br>
                        </div>
                    </div>

                    <div class="slip-body">
                        {{-- FROM --}}
                        <p class="slip-row" style="border-bottom: 1px solid black;padding-bottom:12px;">
                            <span style="font-family: Arial, Helvetica, sans-serif;font-weight:bold;font-style:italic;">
                                @foreach ($data['departments'] as $dept)
                                    @if ($dept['type'] === 'reassigned')
                                        From the Office of {{ $dept['value'] }}
                                    @else
                                        {{ $dept['value'] }}
                                    @endif
                                @endforeach
                            </span>
                        </p>
                    </div>

                    <div class="slip-grid to-date-row">
                        {{-- TO COLUMN --}}
                        <div style="width: 60%;text-align:left;">
                            <div class="to-overlay">
                                <img src="{{ asset('template/img/to.png') }}" class="to-img">
                                <span class="to-text" style="font-family: Arial, Helvetica, sans-serif;">
                                    {{ $data['toText'] }}
                                </span>
                            </div>
                        </div>

                        {{-- DATE COLUMN --}}
                        <div style="width: 100%;text-align:right;margin-top:-50%;">
                            <strong>Date:</strong>
                            <span style="font-family: Arial, Helvetica, sans-serif;">
                                {{ \Carbon\Carbon::parse($data['dateText'])->format('F d, Y') }}
                            </span>
                        </div>
                    </div>

                    <div class="remarks-table" style="width:100%; margin-top:10%; border-collapse: collapse;">
                        <table style="width:100%;">
                            <tr>
                                {{-- LEFT COLUMN --}}
                                <td style="width:50%; vertical-align: top; text-align:left;">
                                    @foreach (['Appropriate Action', 'Calendar', 'Comment & Recommendation', 'Draft Reply', 'Endorsement'] as $leftItem)
                                        <div class="remark-item">
                                            <img src="{{ optional($data['routingPdf'])->trans_remarks === $leftItem
                                                ? public_path('template/img/square_check.png')
                                                : public_path('template/img/square.png') }}"
                                                alt="Checkmark" class="remark-img">
                                            <span>{{ $leftItem }}</span>
                                        </div>
                                    @endforeach
                                </td>

                                {{-- RIGHT COLUMN --}}
                                <td style="width:50%; vertical-align: top; text-align:left;">
                                    @foreach (['File', 'Information', 'Review/Study', 'See the Office', 'Others'] as $rightItem)
                                        <div class="remark-item">
                                            <img src="{{ optional($data['routingPdf'])->trans_remarks === $rightItem
                                                ? public_path('template/img/square_check.png')
                                                : public_path('template/img/square.png') }}"
                                                alt="Checkmark" class="remark-img">
                                            <span>{{ $rightItem }}</span>
                                        </div>
                                    @endforeach
                                </td>
                            </tr>
                        </table>
                    </div>

                    <hr>
                    <div><span>Remarks:</span><br><br><br><br><br></div>

                    <div class="row" style="width: 100%;">
                        <table style="text-align:center;width: 100%;">
                            <tr>
                                {{-- E-SIGNATURE --}}
                                @if ($data['signatoryEsig'] && isset($data['signatoryEsig']->esig_path))
                                    <div style="margin-bottom: -6%;">
                                        <img src="{{ $data['signatoryEsig']->esig_path }}" alt="Electronic Signature"
                                            style="height:70px;">
                                    </div>
                                @endif

                                {{-- NAME --}}
                                <p style="font-weight:bold; font-size: 18px; font-family: Verdana, sans-serif;">
                                    <u>{{ $data['signatoryName'] }}</u>
                                </p>

                                {{-- TITLE --}}
                                <p style="margin-top: -20px; font-style: italic; font-family: Verdana, sans-serif;">
                                    {{ $data['signatoryTitle'] }}
                                </p>
                            </tr>
                        </table>
                    </div>

                    <br><br>
                    <div class="row">
                        <table style="font-size: 12px;width:100%;">
                            <tr>
                                <th style="width: 40%;text-align:left;"> <span> Doc Control Code: CPSU-F-QA-23</span>
                                </th>
                                <th style="width: 40%;text-align:center;"> <span> Effective Date: 09/12/2018</span>
                                </th>
                                <th style="width: 20%;text-align:right;"> <span> Page No.: 1 of 1</span></th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            @if (!$loop->last)
                <div style="page-break-after: always;"></div>
            @endif
        @endforeach
    </div>
</div>
