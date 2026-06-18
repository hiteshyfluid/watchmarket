<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: sans-serif; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #111;">New Listing Report</h2>
    <p><strong>Issue Type:</strong> {{ $report->issueLabel() }}</p>
    @if($report->listing_url)
    <p><strong>Listing URL / ID:</strong> {{ $report->listing_url }}</p>
    @endif
    <p><strong>Description:</strong></p>
    <p style="background:#f7f7f7;padding:12px;border-left:3px solid #d4b160;">{{ $report->description }}</p>
    @if($report->serial_number)
    <p><strong>Serial Number:</strong> {{ $report->serial_number }}</p>
    @endif
    <hr style="border:none;border-top:1px solid #eee;margin:20px 0;">
    <p><strong>Reporter Name:</strong> {{ $report->reporter_name ?: '—' }}</p>
    <p><strong>Reporter Email:</strong> {{ $report->reporter_email }}</p>
    <p style="color:#888;font-size:12px;">Submitted: {{ $report->created_at->format('d M Y H:i') }}</p>
</body>
</html>
