<!DOCTYPE html>
<html>

<head>
    <title>Document Receipt Notification</title>
</head>

<body>

<p><strong>Subject:</strong> {{ $document->subject }}</p>

<p>Dear {{ $recipientName }},</p>

<p>
This is to inform you that a document has been forwarded for your
<strong>{{ $transRemarks ?? 'action' }}</strong>.
</p>

<p>
{{-- <a href="{{ url('tracking?route_id=' . $document->rslip_id) }}"
style="color:#1a73e8;text-decoration:none;font-weight:bold;"> --}}
<a href="{{ route('routingTimeline', $document->rslip_id) }}?slip_id={{ $document->rslip_id }}"
   style="color:#1a73e8;text-decoration:none;font-weight:bold;">
Click here to view the document
</a>
</p>

<p style="margin-top:25px;">
Very truly yours,<br><br>

<strong>Records Office</strong><br>
Central Philippines State University<br>
Kabankalan City, Negros Occidental
</p>

<hr style="margin-top:40px;">

<p style="font-size:12px;color:#888;">
This is an automated email. Please do not reply directly to this message.
</p>

</body>
</html>