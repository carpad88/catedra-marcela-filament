<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
    <meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="format-detection" content="date=no"/>
    <meta name="format-detection" content="address=no"/>
    <meta name="format-detection" content="telephone=no"/>
    <link href="https://fonts.googleapis.com/css?family=Merriweather:400,400i,700,700i|Literata:400,400i,700,700i"
          rel="stylesheet"/>

    <title>Cátedra Marcela :: {{ $title ?? '' }}</title>

    <style>
        [style*="Literata"] {
            font-family: 'Literata', Arial, sans-serif !important;
        }

        [style*="Merriweather"] {
            font-family: 'Merriweather', Georgia, serif !important;
        }

        /* Linked Styles */
        body {
            padding: 0 !important;
            margin: 0 !important;
            display: block !important;
            min-width: 100% !important;
            width: 100% !important;
            background: #f3f3f3;
            -webkit-text-size-adjust: none
        }

        a {
            color: #acacac;
            text-decoration: none
        }

        p {
            padding: 6px 0 !important;
            margin: 0 !important
        }

        img {
            -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */
        }

        .text-top a {
            color: #000000;
        }

        /* Mobile styles */
        @media only screen and (max-device-width: 480px), only screen and (max-width: 480px) {
            .mobile-shell {
                width: 100% !important;
                min-width: 100% !important;
            }

            .center {
                margin: 0 auto !important;
            }

            .separator1 {
                padding: 0px 0px 0px 0px !important;
            }


            .section {
                padding: 15px 15px 15px 15px !important;
            }


            .item-img img {
                width: 90px !important;
                height: auto !important;
            }

            .row30 {
                padding: 20px 0px 20px 0px !important;
            }


            .section30 {
                padding: 30px 15px 30px 15px !important;
            }


            .section3 {
                padding: 0px 15px 30px 15px !important;
            }


            .td {
                width: 100% !important;
                min-width: 100% !important;
            }


            .text-top,
            .h2-header,
            .text-top2,
            .img-m-center {
                text-align: center !important;
            }

            .m-auto {
                height: auto !important;
            }

            .mobile-br-15 {
                height: 15px !important;
            }

            .mobile-br-25 {
                height: 25px !important;
            }

            .mobile-br-35 {
                height: 35px !important;
            }

            .m-td,
            .m-td-top,
            .hide-for-mobile {
                display: none !important;
                width: 0 !important;
                height: 0 !important;
                font-size: 0 !important;
                line-height: 0 !important;
                min-height: 0 !important;
            }

            .fluid-img img {
                width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
            }

            .column,
            .column-top {
                float: left !important;
                width: 100% !important;
                display: block !important;
            }

            .m-td {
                width: 15px !important;
            }

            .hide {
                display: none;
            }
        }

    </style>

    {{ $head ?? '' }}
</head>

<body class="body"
      style="padding:0 !important; margin:0 !important; display:block !important; min-width:100% !important; width:100% !important; background:#f3f3f3; -webkit-text-size-adjust:none">

<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f3f3f3">
    <tr>
        <td align="center" valign="top">
            <table width="650" border="0" cellspacing="0" cellpadding="0" class="mobile-shell">
                <tr>
                    <td class="td"
                        style="width:650px; min-width:650px; font-size:0pt; line-height:0pt; padding:30px 0px 30px 0px; margin:0; font-weight:normal; Margin:0">

                        {{ $header ?? '' }}


                        <!-- Content -->
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#fff">
                            <tr>
                                <td class="section30" style="padding:30px 30px 0px 30px">
                                    <table width="100%" border="0" cellspacing="0"
                                           cellpadding="0">
                                        <tr>
                                            <td style="padding-bottom: 30px;">
                                                <table width="100%" border="0" cellspacing="0"
                                                       cellpadding="0">
                                                    <tr>
                                                        <th class="column-top"
                                                            style="font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; vertical-align:top; Margin:0">
                                                            <table width="100%" border="0"
                                                                   cellspacing="0"
                                                                   cellpadding="0">
                                                                <tr>
                                                                    <td style="color:#000000; font-family:Arial,sans-serif, 'Literata'; font-size:16px; line-height:24px; text-align:left">

                                                                        {{ Illuminate\Mail\Markdown::parse($slot) }}


                                                                        <div
                                                                            style="font-size:0pt; line-height:0pt;"
                                                                            class="mobile-br-15"></div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </th>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <table width="100%" border="0" cellspacing="0"
                                                       cellpadding="0" bgcolor="#e3e3e3"
                                                       class="border"
                                                       style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                    <tr>
                                                        <td bgcolor="#e3e3e3" height="1"
                                                            class="border"
                                                            style="font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%">
                                                            &nbsp;
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <!-- END content -->

                        {{ $footer ?? '' }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
