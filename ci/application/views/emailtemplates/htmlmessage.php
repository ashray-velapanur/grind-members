<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title></title>
  <style>
     .content a { color: #ef4925; }
	.body {font-size: 12px;font-family:arial,sans-serif;line-height:1.231;color:#444;text-align: left;}
	.headline {font-size:18px;}
	.unsubscribe {padding-top:15px;font-size:10px;font-weight:bold;font-family:arial,sans-serif;line-height:1.231;color:#999999;text-decoration: none}
  </style>
</head>
<body style="margin:0;background:#babcbe;">
  <table width="100%" height="100%" cellpadding="0" cellspacing="37" border="0" bgcolor="#5f6260"><tr><td align="center">
    <table width="538" border="0" cellspacing="0" cellpadding="0" style="background:#fff;font-size: 12px;font-family:arial,sans-serif;line-height:1.231;color:#444;">
      <tr>
        <td><img src="http://grindspaces.com/email/generic_head.png" width="538" height="123" alt="grind" style="display:block;border:0;"></td>
      </tr>
      <tr>
        <td>
          <table border="0" cellspacing="0" cellpadding="20">
            <tr>
              <td colspan="2" style="vertical-align:top" class="content">
                <b><span style="color:#ef4925;"><?= $subject; ?></span></b><br><br>
                <?= $message; ?>
              </td>
            </tr>
            <tr>
              <td width="200">
                <div style="font-size:16px;font-weight:bold;border-bottom:1px solid #ccc;margin-bottom:7px;padding-bottom:5px;">Contact Us</div>
                Grind<br>
                419 Park Ave South<br>
                Second floor<br>
                New York, NY 10016<br><br>
                T: +1 646 558 3250<br>
                <a href="mailto:<?= $from; ?>" style="color:#ef4925"><?= $from; ?></a>
              </td>
              <td>&nbsp;</td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td><img src="http://grindspaces.com/email/white-border-bottom_dark.png" width="538" height="6" alt="" style="display:block;border:0;"></td>
      </tr>
    </table>
  </td></tr></table>
</body>
</html>