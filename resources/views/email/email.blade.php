
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<style>
    @media only screen and (max-width: 600px) {
        .inner-body {
            width: 100% !important;
        }

        .footer {
            width: 100% !important;
        }
    }

    @media only screen and (max-width: 500px) {
        .button {
            width: 100% !important;
        }
    }
    /* Base */

    body, body *:not(html):not(style):not(br):not(tr):not(code) {
        font-family: Avenir, Helvetica, sans-serif;
        box-sizing: border-box;
    }

    body {
        background-color: #f5f8fa;
        color: #74787E;
        height: 100%;
        hyphens: auto;
        line-height: 1.4;
        margin: 0;
        -moz-hyphens: auto;
        -ms-word-break: break-all;
        width: 100% !important;
        -webkit-hyphens: auto;
        -webkit-text-size-adjust: none;
        word-break: break-all;
        word-break: break-word;
    }

    p,
    ul,
    ol,
    blockquote {
        line-height: 1.4;
        text-align: left;
    }

    a {
        color: #3869D4;
    }

    a img {
        border: none;
    }

    /* Typography */

    h1 {
        color: #2F3133;
        font-size: 19px;
        font-weight: bold;
        margin-top: 0;
        text-align: left;
    }

    h2 {
        color: #2F3133;
        font-size: 16px;
        font-weight: bold;
        margin-top: 0;
        text-align: left;
    }

    h3 {
        color: #2F3133;
        font-size: 14px;
        font-weight: bold;
        margin-top: 0;
        text-align: left;
    }

    p {
        color: #74787E;
        font-size: 16px;
        line-height: 1.5em;
        margin-top: 0;
        text-align: left;
    }

    p.sub {
        font-size: 12px;
    }

    img {
        max-width: 100%;
    }

    /* Layout */

    .wrapper {
        background-color: #f5f8fa;
        margin: 0;
        padding: 0;
        width: 100%;
        -premailer-cellpadding: 0;
        -premailer-cellspacing: 0;
        -premailer-width: 100%;
    }

    .content {
        margin: 0;
        padding: 0;
        width: 100%;
        -premailer-cellpadding: 0;
        -premailer-cellspacing: 0;
        -premailer-width: 100%;
    }

    /* Header */

    .header {
        box-sizing: border-box;
        text-align: center;
        background-color: #f5f8fa;
        border-color: #74787e;
        padding: 25px 0;
    }

    .header a {
        color: #bbbfc3;
        font-size: 19px;
        font-weight: bold;
        text-decoration: none;
        text-shadow: 0 1px 0 white;
    }

    /* Body */

    .body {
        background-color: #FFFFFF;
        border-bottom: 1px solid #EDEFF2;
        border-top: 1px solid #EDEFF2;
        margin: 0;
        padding: 0;
        width: 100%;
        -premailer-cellpadding: 0;
        -premailer-cellspacing: 0;
        -premailer-width: 100%;
    }

    .inner-body {
        background-color: #FFFFFF;
        margin: 0 auto;
        padding: 0;
        width: 570px;
        -premailer-cellpadding: 0;
        -premailer-cellspacing: 0;
        -premailer-width: 570px;
    }

    /* Subcopy */

    .subcopy {
        border-top: 1px solid #EDEFF2;
        margin-top: 25px;
        padding-top: 25px;
    }

    .subcopy p {
        font-size: 12px;
    }

    /* Footer */

    .footer {

        box-sizing: border-box;
        text-align: center;
        background-color: #f5f8fa;
        border-color: #74787e;
        padding: 25px 0;
        width: 100%;
    }

    .footer p {
        color: #AEAEAE;
        font-size: 12px;
        text-align: center;
    }

    /* Tables */

    .table table {
        margin: 30px auto;
        width: 100%;
        -premailer-cellpadding: 0;
        -premailer-cellspacing: 0;
        -premailer-width: 100%;
    }

    .table th {
        border-bottom: 1px solid #EDEFF2;
        padding-bottom: 8px;
        margin: 0;
    }

    .table td {
        color: #74787E;
        font-size: 15px;
        line-height: 18px;
        padding: 10px 0;
        margin: 0;
    }

    .content-cell {
        padding: 35px;
    }

</style>

<table class="wrapper" width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center">
            <table class="content" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="header" style="box-sizing: border-box; text-align: center; background-color: #f5f8fa; border-color: #74787e; padding: 25px 0;">
                        <a href="https://listing2text.com/">
                            <img src="api.listing2text.com/upload/logoColor.png" />
                        </a>
                    </td>
                </tr>

            <!-- Email Body -->
                <tr>
                    <td class="body" width="100%" cellpadding="0" cellspacing="0">
                        <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0">
                            <!-- Body content -->
                            <tr>
                                <td class="content-cell">
                                    <h3>{!!  $register->text !!}</h3>

                                    <p>{!! $register->token !!}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td>
                        <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" style="box-sizing: border-box; text-align: center; background-color: #f5f8fa; border-color: #74787e; padding: 25px 0; width: 100%;">
                            <tr>
                                <td class="content-cell" align="center">
                                    © 2018 Listing2Text. All rights reserved.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
