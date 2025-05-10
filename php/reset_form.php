<?php
    $code = $_GET['i'];
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="da-dk" lang="da-dk" dir="ltr">
	<head>
		<title>Apoint - Nulstil password</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta content='yes' name='apple-mobile-web-app-capable'/>
		<meta content='yes' name='mobile-web-app-capable'/>
		<link rel="stylesheet" type="text/css" href="../css/main.css">
		<link rel="stylesheet" type="text/css" href="../css/formBox.css">
		<link rel="stylesheet" type="text/css" href="../css/phone.css">

    </head>
	<body>
	    <div id="main">
            <div id="menu_bar">
                <a class="menu_link" href="index.html">Tilbage</a>
            </div>
            <a href="https://apoint.dk#login" class="menu_link login_btn_a"><div id="login_btn">Login</div></a>
            <!-- front page -->
            <div id="content_body">
                <div  class="content_view" id="login">
                    <div class="form_box">
                        <h2>Nulstil password</h2>
                        <form action="reset.php" method="POST">
                            <input type="hidden" name="hash" value="<? echo $code ?>">
                            <table>
                                <tr><td>Nyt password: <br> <input type="password" name="new_pw"></tr>
                                <tr><td><input type="submit" value="Send"></td></tr>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>