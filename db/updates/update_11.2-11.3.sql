INSERT INTO `templates` (`id`, `name`, `content`, `created`, `updated`, `description`) VALUES (NULL, 'IFRA Document Template', '<!doctype html>
<html lang="en">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
 <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
 <link href="/css/ifraCert.css" rel="stylesheet">
</head>

<body>
 <div>
 <p style="margin-bottom: 0.63in"><img src="%LOGO%" width="200px" /></p>
 </div>
 <h1 class="western"><font face="Arial, sans-serif"><span style="font-style: normal">CERTIFICATE OF CONFORMITY OF FRAGRANCE MIXTURES WITH IFRA STANDARDS</span></font><br>
 </h1>
 <p align=center style="widows: 0; orphans: 0"><font face="Helvetica 65 Medium, Arial Narrow, sans-serif"><font size=4><b><font face="Arial, sans-serif"><font size=2 style="font-size: 11pt"><u>This Certificate assesses the conformity of a fragrance mixture with IFRA Standards and provides restrictions for use as necessary. It is based only on those materials subject to IFRA Standards for the toxicity endpoint(s) described in each Standard. </u></font></font></b></font></font>
 </p>
 <p align=center style="widows: 0; orphans: 0"><br>
 </p>
 <hr size="1">
 </p>
 <p class="western"><font face="Arial, sans-serif"><u><b>CERTIFYING PARTY:</b></u></font></p>
 <p class="western"><font face="Arial, sans-serif">%BRAND_NAME%</font></p>
 <p class="western"><font face="Arial, sans-serif">%BRAND_ADDRESS%</font></p>
 <p class="western"><font face="Arial, sans-serif">%BRAND_EMAIL%</font></p>
 <p class="western"><font face="Arial, sans-serif">%BRAND_PHONE%</font></p>


 </p>
 <p class="western"><font face="Arial, sans-serif"><u><b>CERTIFICATE DELIVERED TO: </b></u></font>
 </p>
 <p class="western"><font face="Arial, sans-serif"><span ><b>Customer: </b></span></font></p>
 <p class="western"><font face="Arial, sans-serif">%CUSTOMER_NAME%</font></p>
 <p class="western"><font face="Arial, sans-serif">%CUSTOMER_ADDRESS%</font></p>
 <p class="western"><font face="Arial, sans-serif">%CUSTOMER_EMAIL%</font></p>
 <p class="western"><font face="Arial, sans-serif">%CUSTOMER_WEB%</font></p>

 <p class="western"><br>
 </p>
 <p class="western"><font face="Arial, sans-serif"><u><b>SCOPE OF THE CERTIFICATE:</b></u></font></p>
 <p class="western"><font face="Arial, sans-serif"><span >Product: <B>%PRODUCT_NAME%</b></span></font></p>
 <p class="western">Size:<strong> %PRODUCT_SIZE%ml</strong></p>
 <p class="western">Concentration: <strong>%PRODUCT_CONCENTRATION%%</strong></p>
 <hr size="1"><br>
 <font face="Arial, sans-serif"><span ><U><B>COMPULSORY INFORMATION:</b></u></span></font>
 <p class="western" style="margin-right: -0.12in">
 <font face="Arial, sans-serif"><span >We certify that the above mixture is in compliance with the Standards of the INTERNATIONAL FRAGRANCE ASSOCIATION (IFRA), up to and including the <strong>%IFRA_AMENDMENT%</strong> Amendment to the IFRA Standards (published </span><b>%IFRA_AMENDMENT_DATE%</span></b>),
 provided it is used in the following</span></font> <font face="Arial, sans-serif"><span >category(ies)
 at a maximum concentration level of:</span></font></p>
 <p class="western" style="margin-right: -0.12in"> </p>
 <table class="table table-stripped">
 <tr>
 <th bgcolor="#d9d9d9"><strong>IFRA Category</strong></th>
 <th bgcolor="#d9d9d9"><strong>Description</strong></th>
 <th bgcolor="#d9d9d9"><strong>Level of use (%)*</strong></th>
 </tr>
 <tr>
 <td align="center">%IFRA_CAT_LIST%</td>
 </tr>
 </table>
 <p class="western" style="margin-right: -0.12in"><font face="Arial, sans-serif"><I>*Actual use level or maximum use level at 100% concentration</I></font> </p>
 <p class="western" style="margin-right: -0.12in">
 <font face="Arial, sans-serif"><span >For other kinds of, application or use at higher concentration levels, a new evaluation may be needed; please contact </span></font><font face="Arial, sans-serif"><b>%BRAND_NAME%</b></font><font face="Arial, sans-serif"><span >.
 </span></font></p>
 <p class="western" style="margin-right: -0.12in"><font face="Arial, sans-serif"><span >Information about presence and concentration of fragrance ingredients subject to IFRA Standards in the fragrance mixture </span></font><font face="Arial, sans-serif"><B>%PRODUCT_NAME%</b></font><font face="Arial, sans-serif"><span> is as follows:</span></font></p>
 <p class="western" style="margin-right: -0.12in"> </p>
 <table class="table table-stripped">
 <tr>
 <th width="22%" bgcolor="#d9d9d9"><strong>Material(s) under the scope of IFRA Standards:</strong></th>
 <th width="12%" bgcolor="#d9d9d9"><strong>CAS number(s):</strong></th>
 <th width="28%" bgcolor="#d9d9d9"><strong>Recommendation (%) from IFRA Standard:</strong></th>
 <th width="19%" bgcolor="#d9d9d9"><strong>Concentration (%) in finished product:</strong></th>
 <th width="19%" bgcolor="#d9d9d9">Risk</th>
 </tr>
 %IFRA_MATERIALS_LIST%
 </table>
 <p> </p>
 <p><font face="Arial, sans-serif"><span >Signature </span></font><font face="Arial, sans-serif"><span><i>(If generated electronically, no signature)</i></span></font></p>
 <p><font face="Arial, sans-serif"><span >Date: </span></font><strong>%CURRENT_DATE%</strong></p>
 </p>
 <div>
 <p style="margin-right: 0in; margin-top: 0.08in">
 <font face="Segoe UI, sans-serif"><font size=1 style="font-size: 8pt"><span><u>Disclaimer</u>:
 </span></font></font></p>
 <p style="margin-right: 0in; margin-top: 0.08in"><font face="Segoe UI, sans-serif"><font size=1 style="font-size: 8pt"><span>This Certificate provides restrictions for use of the specified product based only on those materials restricted by IFRA Standards for the toxicity endpoint(s) described in each Standard.</span></font></font></p>
 <p style="margin-right: 0in; margin-top: 0.08in"><font face="Segoe UI, sans-serif"><font size=1 style="font-size: 8pt"><span>This Certificate does not provide certification of a comprehensive safety assessment of all product constituents.</span></font></font></p>
 <p style="margin-right: 0in; margin-top: 0.08in"><font face="Segoe UI, sans-serif"><font size=1 style="font-size: 8pt"><span> This certificate is the responsibility of the fragrance supplier issuing it. It has not been prepared or endorsed by IFRA in anyway. </span></font></font>
 </p>
 </div>
</body>
</html>', current_timestamp(), current_timestamp(), 'The default IFRA document template');