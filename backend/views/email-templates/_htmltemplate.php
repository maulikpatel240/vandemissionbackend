<style type="text/css" rel="stylesheet" media="all">

    .htmltemplate a {
        color: #414EF9;
    }

    /* Layout ------------------------------ */
    .htmltemplate .email-wrapper {
        width: 100%;
        margin: 0;
        padding: 0;
        background-color: #F5F7F9;
    }
    .htmltemplate .email-content {
        width: 100%;
        margin: 0;
        padding: 0;
    }

    /* Masthead ----------------------- */
    .htmltemplate .email-masthead {
        padding: 25px 0;
        text-align: center;
    }
    .htmltemplate .email-masthead_logo {
        max-width: 400px;
        border: 0;
    }
    .htmltemplate .email-masthead_name {
        font-size: 16px;
        font-weight: bold;
        color: #839197;
        text-decoration: none;
        text-shadow: 0 1px 0 white;
    }

    /* Body ------------------------------ */
    .htmltemplate .email-body {
        width: 100%;
        margin: 0;
        padding: 0;
        border-top: 1px solid #E7EAEC;
        border-bottom: 1px solid #E7EAEC;
        background-color: #FFFFFF;
    }
    .htmltemplate .email-body_inner {
        width: 570px;
        margin: 0 auto;
        padding: 0;
    }
    .htmltemplate .email-footer {
        width: 570px;
        margin: 0 auto;
        padding: 0;
        text-align: center;
    }
    .htmltemplate .email-footer p {
        color: #839197;
    }
    .htmltemplate .body-action {
        width: 100%;
        margin: 30px auto;
        padding: 0;
        text-align: center;
    }
    .htmltemplate .body-sub {
        margin-top: 25px;
        padding-top: 25px;
        border-top: 1px solid #E7EAEC;
    }
    .htmltemplate .content-cell {
        padding: 35px;
    }
    .htmltemplate .align-right {
        text-align: right;
    }

    /* Type ------------------------------ */
    .htmltemplate h1 {
        margin-top: 0;
        color: #292E31;
        font-size: 19px;
        font-weight: bold;
        text-align: left;
    }
    .htmltemplate h2 {
        margin-top: 0;
        color: #292E31;
        font-size: 16px;
        font-weight: bold;
        text-align: left;
    }
    .htmltemplate h3 {
        margin-top: 0;
        color: #292E31;
        font-size: 14px;
        font-weight: bold;
        text-align: left;
    }
    .htmltemplate p {
        margin-top: 0;
        color: #839197;
        font-size: 16px;
        line-height: 1.5em;
        text-align: left;
    }
    .htmltemplate p.sub {
        font-size: 12px;
    }
    .htmltemplate p.center {
        text-align: center;
    }

    /* Buttons ------------------------------ */
    .htmltemplate .button {
        display: inline-block;
        width: 200px;
        background-color: #414EF9;
        border-radius: 3px;
        color: #ffffff;
        font-size: 15px;
        line-height: 45px;
        text-align: center;
        text-decoration: none;
        -webkit-text-size-adjust: none;
        mso-hide: all;
    }
    .htmltemplate .button--green {
        background-color: #28DB67;
    }
    .htmltemplate .button--red {
        background-color: #FF3665;
    }
    .htmltemplate .button--blue {
        background-color: #414EF9;
    }
    .htmltemplate .button::hover {
        color: #f9f9f9;
        text-decoration: none;
    }
    .htmltemplate .cursor-pointer{
        cursor: pointer;
    }
    /*Media Queries ------------------------------ */
    @media only screen and (max-width: 600px) {
        .htmltemplate .email-body_inner,
        .htmltemplate .email-footer {
            width: 100% !important;
        }
    }
    @media only screen and (max-width: 500px) {
        .htmltemplate .button {
            width: 100% !important;
        }
    }
</style>
<div class="htmltemplate">
    <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table class="email-content" width="100%" cellpadding="0" cellspacing="0">
                    <!-- Logo -->
                    <tr>
                        <td class="email-masthead">
                            <a href="{{site_url}}" target="_blank" class="email-masthead_name">{{site_name}}</a>
                        </td>
                    </tr>
                    <!-- Email Body -->
                    <tr>
                        <td class="email-body" width="100%">
                            <table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0">
                                <!-- Body content -->
                                <tr>
                                    <td class="content-cell">
                                        <h1>Hi {{name}},</h1>
                                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>

                                        <p>Thanks,<br>{{site_name}} Team</p>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table class="email-footer" align="center" width="570" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="content-cell">
                                        <p class="sub center">{{copyright_text}}</p>
                                        <p class="sub center">
                                            {{site_name}}
                                            <br>{{site_address}}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
