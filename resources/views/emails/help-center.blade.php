<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f4f7; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 2rem auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: #0f172a; padding: 2rem; text-align: center; }
        .header h1 { color: #D2B48C; margin: 0; font-size: 1.5rem; }
        .body { padding: 2rem; }
        .field { margin-bottom: 1.25rem; }
        .field-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; margin-bottom: 0.25rem; }
        .field-value { font-size: 1rem; color: #1e293b; line-height: 1.6; }
        .details-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem; white-space: pre-wrap; word-wrap: break-word; }
        .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 1.25rem 2rem; text-align: center; font-size: 0.8rem; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎫 New Support Request</h1>
        </div>
        <div class="body">
            <div class="field">
                <div class="field-label">Name</div>
                <div class="field-value">{{ $firstName }}</div>
            </div>
            <div class="field">
                <div class="field-label">Email</div>
                <div class="field-value"><a href="mailto:{{ $userEmail }}">{{ $userEmail }}</a></div>
            </div>
            <div class="field">
                <div class="field-label">Issue Type</div>
                <div class="field-value">{{ $issueType }}</div>
            </div>
            <div class="field">
                <div class="field-label">Details</div>
                <div class="field-value details-box">{{ $details }}</div>
            </div>
        </div>
        <div class="footer">
            This message was sent from the GovKloud Help Center.
        </div>
    </div>
</body>
</html>
