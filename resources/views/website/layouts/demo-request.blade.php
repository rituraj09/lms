<!DOCTYPE html>
<html>

<body style="font-family:Arial,sans-serif;background:#f4f6f8;padding:20px;">
    <div style="max-width:600px;margin:auto;background:#fff;border-radius:10px;overflow:hidden;">
        <div style="background:#2563EB;color:#fff;padding:20px;text-align:center;">
            <h2 style="margin:0;">MindShiksha EdTech</h2>
        </div>
        <div style="padding:24px;">
            @if ($isAdmin)
                <h3>New Demo Request</h3>
            @else
                <h3>Thank you, {{ $demoRequest->full_name }}!</h3>
                <p>We've received your demo request and our team will reach out within 1 business day.</p>
            @endif

            <table style="width:100%;border-collapse:collapse;margin-top:16px;">
                <tr>
                    <td style="padding:8px;font-weight:bold;">Name</td>
                    <td style="padding:8px;">{{ $demoRequest->full_name }}</td>
                </tr>
                <tr>
                    <td style="padding:8px;font-weight:bold;">Institution</td>
                    <td style="padding:8px;">{{ $demoRequest->institution }}</td>
                </tr>
                <tr>
                    <td style="padding:8px;font-weight:bold;">Role</td>
                    <td style="padding:8px;">{{ $demoRequest->role }}</td>
                </tr>
                <tr>
                    <td style="padding:8px;font-weight:bold;">Email</td>
                    <td style="padding:8px;">{{ $demoRequest->email }}</td>
                </tr>
                <tr>
                    <td style="padding:8px;font-weight:bold;">Phone</td>
                    <td style="padding:8px;">{{ $demoRequest->phone }}</td>
                </tr>
                @if ($demoRequest->preferred_slot)
                    <tr>
                        <td style="padding:8px;font-weight:bold;">Preferred Slot</td>
                        <td style="padding:8px;">{{ $demoRequest->preferred_slot }}</td>
                    </tr>
                @endif
                @if ($demoRequest->message)
                    <tr>
                        <td style="padding:8px;font-weight:bold;">Message</td>
                        <td style="padding:8px;">{{ $demoRequest->message }}</td>
                    </tr>
                @endif
            </table>
        </div>
    </div>
</body>

</html>
