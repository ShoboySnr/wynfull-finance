<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
    <!--[if mso]>
    <xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml>
    <style>
        td,th,div,p,a,h1,h2,h3,h4,h5,h6 {font-family: "Segoe UI", sans-serif; mso-line-height-rule: exactly;}
    </style>
    <![endif]-->
    <title>A New Resource is Available</title>
    <style>
        /* This is a simple reset for email clients */
        body, table, td, div, p, a {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        a {
            color: #0A52A1; /* --brand-blue */
        }
        .body-bg {
            background-color: #F8F9FA; /* --background-body */
        }
        .card-bg {
            background-color: #FFFFFF; /* --background-card */
        }
        .text-primary {
            color: #1D2939; /* --text-primary */
        }
        .text-secondary {
            color: #475467; /* --text-secondary */
        }
        .text-muted {
            color: #98A2B3; /* --text-muted */
        }
        .border-color {
            border-color: #EAECF0; /* --border-color */
        }
        @media (max-width: 600px) {
            .container {
                width: 100% !important;
                padding-left: 16px !important;
                padding-right: 16px !important;
            }
            .content {
                padding: 24px !important;
            }
        }
    </style>
</head>
<body class="body-bg" style="margin: 0; padding: 0; width: 100%; word-break: break-word;">
<!--[if (gte mso 9)|(IE)]>
<table width="600" align="center" border="0" cellpadding="0" cellspacing="0" style="background-color: #FFFFFF; border-radius: 16px; border: 1px solid #EAECF0;">
    <tr>
        <td>
<![endif]-->

<table class="container" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td align="center" style="padding: 40px 0;">
            <!-- Main Content Card -->
            <table class="container" width="600" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width: 600px; max-width: 600px; background-color: #FFFFFF; border-radius: 16px; overflow: hidden; border: 1px solid #EAECF0;">
                <!-- 1. Header with Logo -->
                <tr>
                    <td align="center" style="padding: 32px 40px 24px 40px; border-bottom: 1px solid #EAECF0;">
                        <a href="{{ url('/') }}" style="text-decoration: none;">
                            <img src="{{ url(asset('assets/img/wynfull-logo.png')) }}" alt="Wynfull Finance Logo" style="width: 56px; height: 56px;">
                        </a>
                    </td>
                </tr>

                <!-- 2. Main Content Body -->
                <tr>
                    <td class="content" style="padding: 32px 40px; font-family: Arial, sans-serif; font-size: 16px; color: #475467; line-height: 1.6;">

                        <h2 style="font-family: Arial, sans-serif; color: #1D2939; margin-top: 0; margin-bottom: 24px; font-size: 24px; font-weight: 600;">
                            A New Resource is Available
                        </h2>

                        <p style="margin-bottom: 24px;">Hi {{ $user->name }},</p>

                        <p style="margin-bottom: 24px;">
                            A new resource collection, <strong>"{{ $collection->title }}"</strong>, has been published by an admin and is now available for you.
                        </p>

                        {{-- Description Panel --}}
                        @if($collection->description)
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F8F9FA; border-radius: 8px; border: 1px solid #EAECF0; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px; font-style: italic; color: #475467;">
                                        {{ $collection->description }}
                                    </td>
                                </tr>
                            </table>
                        @endif

                        {{-- Button --}}
                        <table border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation">
                            <tr>
                                <td align="center" style="padding: 16px 0 16px 0;">
                                    <a href="{{ route('resources.library') }}" target="_blank" style="background-color: #0A52A1; color: #FFFFFF; padding: 14px 28px; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 16px; display: inline-block;">
                                        View Resource Library
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <p style="margin-top: 24px; margin-bottom: 0;">Thanks,</p>
                        <p style="margin-top: 4px; margin-bottom: 0;">The Wynfull Finance Team</p>

                    </td>
                </tr>

                <!-- 3. Footer -->
                <tr>
                    <td align="center" style="padding: 24px 40px; border-top: 1px solid #EAECF0; background-color: #F8F9FA;">
                        <p style="color: #98A2B3; font-size: 12px; margin: 0;">
                            &copy; {{ date('Y') }} Wynfull Finance. All rights reserved.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!--[if (gte mso 9)|(IE)]>
</td>
</tr>
</table>
<![endif]-->
</body>
</html>
```
