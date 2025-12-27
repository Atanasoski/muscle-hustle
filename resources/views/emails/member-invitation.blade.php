<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation to Join {{ $partner->name }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f3f4f6;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .header {
            background-color: {{ $partner->identity->primary_color ?? '#fa812d' }};
            padding: 40px 20px;
            text-align: center;
        }
        .logo {
            max-width: 120px;
            height: auto;
            margin-bottom: 20px;
        }
        .header-text {
            color: {{ $partner->identity->text_on_primary_color ?? '#ffffff' }};
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        .content {
            padding: 40px 20px;
            color: #374151;
            line-height: 1.6;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #111827;
        }
        .message {
            margin-bottom: 30px;
            font-size: 16px;
        }
        .cta-button {
            display: inline-block;
            background-color: {{ $partner->identity->primary_color ?? '#fa812d' }};
            color: {{ $partner->identity->text_on_primary_color ?? '#ffffff' }};
            padding: 16px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
        }
        .cta-button:hover {
            opacity: 0.9;
        }
        .secondary-info {
            background-color: #f9fafb;
            border-left: 4px solid {{ $partner->identity->primary_color ?? '#fa812d' }};
            padding: 16px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            background-color: #f9fafb;
            padding: 30px 20px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            border-top: 1px solid #e5e7eb;
        }
        .expiry-notice {
            color: #ef4444;
            font-weight: 600;
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header with Partner Branding -->
        <div class="header">
            @if($partner->identity && $partner->identity->logo_url)
                <img src="{{ $partner->identity->logo_url }}" alt="{{ $partner->name }}" class="logo">
            @endif
            <h1 class="header-text">Welcome to {{ $partner->name }}</h1>
        </div>

        <!-- Email Content -->
        <div class="content">
            <p class="greeting">Hello!</p>

            <div class="message">
                <p>You've been invited to join <strong>{{ $partner->name }}</strong> and start your fitness journey with us!</p>
                
                <p>As a member, you'll have access to:</p>
                <ul>
                    <li>Personalized workout tracking</li>
                    <li>Progress monitoring and analytics</li>
                    <li>Custom workout templates</li>
                    <li>Exercise library with detailed instructions</li>
                </ul>
            </div>

            <!-- Call to Action -->
            <div style="text-align: center;">
                <a href="{{ $signupUrl }}" class="cta-button">
                    Accept Invitation & Sign Up
                </a>
            </div>

            <!-- Alternative Link -->
            <div class="secondary-info">
                <p style="margin: 0; font-size: 14px;">
                    <strong>Can't click the button?</strong> Copy and paste this link into your browser:
                </p>
                <p style="margin: 8px 0 0 0; word-break: break-all; font-size: 13px;">
                    {{ $signupUrl }}
                </p>
            </div>

            <!-- Expiry Notice -->
            <p class="expiry-notice">
                ⏱️ This invitation expires on {{ $invitation->expires_at->format('F j, Y \a\t g:i A') }}
            </p>

            <p style="margin-top: 30px; color: #6b7280; font-size: 14px;">
                If you weren't expecting this invitation, you can safely ignore this email.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 10px 0;">
                <strong>{{ $partner->name }}</strong>
            </p>
            <p style="margin: 0;">
                Powered by Muscle Hustle
            </p>
        </div>
    </div>
</body>
</html>

