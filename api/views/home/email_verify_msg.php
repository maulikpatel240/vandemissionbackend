
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>FRDP</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<style type="text/css">
/* Client-specific Styles */
#outlook a {
	padding: 0;
} /* Force Outlook to provide a "view in browser" menu link. */
a:hover {
	opacity: 0.8;
}
body {
	width: 100% !important;
	-webkit-text-size-adjust: 100%;
	-ms-text-size-adjust: 100%;
	margin: 0;
	padding: 0;
}
/* Prevent Webkit and Windows Mobile platforms from changing default font sizes, while not breaking desktop design. */
.ExternalClass {
	width: 100%;
} /* Force Hotmail to display emails at full width */
.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {
	line-height: 100%;
} /* Force Hotmail to display normal line spacing.  More on that: http://www.emailonacid.com/forum/viewthread/43/ */
#backgroundTable {
	margin: 0;
	padding: 0;
	width: 100% !important;
	line-height: 100% !important;
}
img {
	outline: none;
	text-decoration: none;
	border: none;
	-ms-interpolation-mode: bicubic;
}
a img {
	border: none;
}
.image_fix {
	display: block;
}
p {
	margin: 0px 0px !important;
}
table td {
	border-collapse: collapse;
}
table {
	border-collapse: collapse;
	mso-table-lspace: 0pt;
	mso-table-rspace: 0pt;
}
a {
	color: #33b9ff;
	text-decoration: none;
	text-decoration: none!important;
}
/*STYLES*/
table[class=full] {
	width: 100%;
	clear: both;
}

/*IPAD STYLES*/
@media only screen and (max-width: 640px) {
a[href^="tel"], a[href^="sms"] {
	text-decoration: none;
	color: #33b9ff; /* or whatever your want */
	pointer-events: none;
	cursor: default;
}
.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
	text-decoration: default;
	color: #33b9ff !important;
	pointer-events: auto;
	cursor: default;
}
table[class=devicewidth] {
	width: 440px!important;
	text-align: center!important;
}
table[class=devicewidthinner] {
	width: 420px!important;
	text-align: center!important;
}
table[class=mainsmall1] {
	float: left!important;
}
table[class=mainsmall2] {
	float: right!important;
}
table[class=banner-gap] {
	display: none!important;
}
img[class="bannerbig"] {
	width: 440px!important;
	height: 241px!important;
}
img[class="spacinglines"] {
	width: 420px!important;
}
}

/*IPHONE STYLES*/
@media only screen and (max-width: 480px) {
a[href^="tel"], a[href^="sms"] {
	text-decoration: none;
	color: #33b9ff; /* or whatever your want */
	pointer-events: none;
	cursor: default;
}
.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
	text-decoration: default;
	color: #33b9ff !important;
	pointer-events: auto;
	cursor: default;
}
table[class=devicewidth] {
	width: 280px!important;
	text-align: center!important;
}
table[class=devicewidthinner] {
	width: 260px!important;
	text-align: center!important;
}
table[class=mainsmall1] {
	float: left!important;
	width: 120px!important;
	height: 90px!important;
}
table[class=mainsmall2] {
	float: right!important;
	width: 120px!important;
	height: 90px!important;
}
img[class=mainsmall1] {
	width: 120px!important;
	height: 90px!important;
}
img[class=mainsmall2] {
	width: 120px!important;
	height: 90px!important;
}
table[class=banner-gap] {
	display: none!important;
}
img[class="bannerbig"] {
	width: 93px!important;
	height: 65px!important;
}
img[class="redisign"] {
	width: 280px!important;
	height: 220px!important;
}
img[class="spacinglines"] {
	width: 260px!important;
}
}
</style>
</head>

<body style="margin:0; padding:0">
<center>
  <table width="650" border="0" cellspacing="0" cellpadding="0" bgcolor="#f4f1f1" class="devicewidth" style="direction:rtl !important" >
    <tbody align="center">
      <tr>
        <td style="height:1px;">&nbsp;</td>
      </tr>
      <tr>
          <td align="center"><a href="javascript:void(0)" title="Get in touch"><img src="<?php echo Yii::$app->params['back'] ?>img/logo.png" width="50" class="bannerbig"  alt="Letsnurture"/></a></td>
      </tr>
      <tr>
        <td style="height:1px;">&nbsp;</td>
      </tr>
    </tbody>
  </table>
  <table width="650" border="0" cellspacing="0" cellpadding="0" bgcolor="#faf8f8" class="devicewidth" style="direction:rtl !important" >
    <tr>
      <td align="center"><table width="576" border="0" cellspacing="0" cellpadding="15" class="devicewidth" >
          <tbody>
            <tr>
              <td  align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="devicewidth">
                  <tbody>
                    <tr>
                      <td>
                          <h3 style="font-family:arial; text-align:center; font-size:27px ; font-weight:normal; margin-bottom:0">Email Verify</h3>
                          </br></br>
                          <p style="font-family:arial; font-size:16px; text-align:center; padding:5px 0; line-height:24px;">
                              <?php echo $data['message']; ?>
                          </p>
                            </br>
                          <p style="font-family:arial; font-size:13px; color:#616c76; text-align:center; padding-bottom:12px;">Copyright © 2016 FRDP, All rights reserverd</p>
                          
                     </td> 
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          </tbody>
        </table>
        <table width="576" border="0" cellspacing="0" cellpadding="30" class="devicewidth" >
          <tbody>
            <tr>
              <td style="height:25px;">&nbsp;</td>
            </tr>
            <tr>
              <td>
                <p> Thank you<br />
                  <i>FRDP Team</i>
                
                <p> </td>
            </tr>
          </tbody>
        </table></td>
    </tr>
  </table>
</center>
</body>
</html>
<?php exit; ?>
