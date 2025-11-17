<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2563eb;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            background-color: #f9fafb;
            padding: 20px;
            margin-top: 20px;
        }
        .field {
            margin-bottom: 15px;
        }
        .label {
            font-weight: bold;
            color: #1f2937;
        }
        .value {
            margin-top: 5px;
            padding: 10px;
            background-color: white;
            border-left: 3px solid #2563eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Contact Form Submission</h2>
        </div>
        
        <div class="content">
            <div class="field">
                <div class="label">From:</div>
                <div class="value">{{ $name }}</div>
            </div>
            
            <div class="field">
                <div class="label">Email:</div>
                <div class="value">{{ $email }}</div>
            </div>
            
            <div class="field">
                <div class="label">Subject:</div>
                <div class="value">{{ $subject }}</div>
            </div>
            
            <div class="field">
                <div class="label">Message:</div>
                <div class="value">{{ $messageContent }}</div>
            </div>
        </div>
        
        <p style="margin-top: 20px; color: #6b7280; font-size: 12px;">
            This email was sent from the TheTradeVisor contact form at {{ now()->format('Y-m-d H:i:s') }} UTC
        </p>
    </div>
</body>
</html>
