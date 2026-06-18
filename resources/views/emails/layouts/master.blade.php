<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no,address=no,email=no,date=no,url=no">
    <style>
        @media only screen and (max-width:600px) {

            .container {
                width: 100% !important;
            }

            .content-padding {
                padding: 20px !important;
            }

            .mobile-block,
            .mobile-block tbody,
            .mobile-block tr,
            .mobile-block td {
                display: block !important;
                width: 100% !important;
                box-sizing: border-box;
            }

            .mobile-label {
                padding-bottom: 5px !important;
                font-weight: bold !important;
            }

            .mobile-value {
                padding-top: 0 !important;
                padding-bottom: 15px !important;
            }

            .logo {
                width: 140px !important;
                height: auto !important;
            }

            h2 {
                font-size: 20px !important;
                line-height: 36px !important;
            }
        }
    </style>
</head>

<body style="
    margin:0;
    padding:0;
    background:#eef2f7;
    font-family:Arial,Helvetica,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#eef2f7">
        <tr>
            <td align="center" style="padding:20px 10px;">

                <table class="container"
                    width="600"
                    cellpadding="0"
                    cellspacing="0"
                    border="0"
                    style="
                    width:100%;
                    max-width:600px;
                    background:#ffffff;
                    border-radius:12px;">

                    @include('emails.components.header')

                    <tr>
                        <td class="content-padding" style="padding:35px;">
                            @yield('content')
                        </td>
                    </tr>

                    @include('emails.components.footer')

                </table>

            </td>
        </tr>
    </table>

</body>

</html>