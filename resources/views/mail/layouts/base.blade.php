@php
    // Fallbacks
    $brandName = $brandName ?? 'Wynfull Finance';
    $brandTag  = $brandTag  ?? 'Building Financial Warriors for Life';
    $preheader = $preheader ?? '';
@endphp
    <!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
    <meta charset="utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? $brandName }}</title>
    <style>

        body, table, td, a { font-family: Arial, Helvetica, sans-serif; }
        img { border:0; line-height:100%; outline:none; text-decoration:none; }
        table { border-collapse:collapse !important; }
        body { margin:0; padding:0; width:100% !important; height:100% !important; background-color:#F8FAFC; }

        @media screen and (max-width: 600px) {
            .container { width:100% !important; }
            .p-24 { padding:16px !important; }
            .h1 { font-size:22px !important; line-height:28px !important; }
            .h2 { font-size:18px !important; line-height:24px !important; }
            .text { font-size:14px !important; line-height:20px !important; }
            .logo-container { padding-right:8px !important; }
            .logo-box { width:40px !important; height:40px !important; }
            .brand-name { font-size:20px !important; }
            .brand-tagline { font-size:12px !important; }
        }
    </style>
</head>
<body style="background-color:#F8FAFC; margin:0; padding:0;">


<div style="display:none; max-height:0; overflow:hidden; font-size:1px; line-height:1px; color:#F8FAFC;">
    {{ $preheader }}
</div>

<!-- Wrapper -->
<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td align="center" style="padding:24px;">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" class="container" style="width:600px; max-width:600px; background-color:#FFFFFF; border:1px solid #E2E8F0; border-radius:12px; overflow:hidden;">
                <!-- Header -->
                <tr>
                    <td align="left" style="background-color:#0E4DA4; padding:20px 24px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="left" style="vertical-align:middle;">
                                    <table role="presentation" cellpadding="0" cellspacing="0" style="display:inline-table;">
                                        <tr>
                                            <td class="logo-container" style="vertical-align:middle; padding-right:12px;">
                                                <div class="logo-box" style="width:48px; height:48px; border-radius:12px; background:rgba(255,255,255,0.9); border:1px solid rgba(255,255,255,0.2); display:table-cell; vertical-align:middle; text-align:center; overflow:hidden;">
                                                    <img src="{{ asset('assets/img/wynfull-logo.png') }}" alt="Wynfull Finance Logo" style="width:100%; height:100%; object-fit:cover; display:block;">
                                                </div>
                                            </td>
                                            <td style="vertical-align:middle;">
                                                <div class="brand-name" style="color:#FFFFFF; font-weight:bold; font-size:24px; line-height:1.2; margin:0;">Wynfull</div>
                                                <div class="brand-tagline" style="color:#E8F2FF; font-size:14px; font-weight:500; margin:2px 0 0 0;">Finance</div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td class="p-24" style="padding:24px;">
                        @yield('content')
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center" style="padding:16px 24px; background-color:#F8FAFC;">
                        <p style="margin:0; color:#6B7280; font-size:12px; line-height:18px;">
                            © {{ date('Y') }} Wynfull Finance. All rights reserved.
                        </p>
                        <p style="margin:8px 0 0; color:#9CA3AF; font-size:12px; line-height:18px;">
                            You received this email because you have an account or requested access to Wynfull.
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
