<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset Link</title>
</head>
<body>
    
    <p>Hello <?php echo $name ?>,</p>

    <p>You are receiving this email because we received a request to reset your password. Please click the button below to reset your password:</p>

    <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0" style="margin: auto;">
        <tr>
            <td align="center">
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <a href="<?php echo $resetLink ?>" class="button" target="_blank">Reset Password</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p>If you did not request a password reset, no further action is required.</p>

    <p>Best Regards,<br>Your patrabibaha Team</p>

</body>
</html>
