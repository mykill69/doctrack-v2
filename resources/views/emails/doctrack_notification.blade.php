<!DOCTYPE html>
<html>

<head>
    <title>Document Receipt Notification</title>
</head>

<body>
    <p><strong>Subject:</strong> {{ $document->doc_title }}</p>

    <p>Dear <span style="font-weight: bold;">{{ $recipientName }}</span>,</p>

    <p>This is to notify you that a document has been recorded in the tracking system, and is now available for your
        reference.</p>


    <p>
        <a href="{{ route('viewInterOffice', ['id' => $document->track_slip]) }}"
            style="color:#1a73e8; text-decoration:none; font-weight:bold;">
            Click here to view the document
        </a>
    </p>
    <p style="margin-top: 25px;">
        Very truly yours,<br><br>
        {{ $document->user_name }}<br>
        Central Philippines State University<br>
        Kabankalan City, Negros Occidental
    </p>

    <hr style="margin-top: 40px;">
    <p style="font-size: 12px; color: #888;">
        This is an automated message. Please do not reply to this email.
    </p>
</body>

</html>
