<?
    $db = new SQLite3('database.db');
    $code = $_GET['i'];
    
    $statement = $db->prepare('UPDATE users SET verified="" WHERE verified=:code');
    $statement->bindValue(':code', $code);
    
    $result = $statement->execute();
    
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
                        <h2>Velkommen til Apoint.dk</h2>
                        <?
                            if($db->changes() != 0)
                            {
                                echo "Din konto er nu aktiveret";
                                echo "<a href='https://www.apoint.dk'>tilbage til Apoint.dk</a>";
                            }else{
                                echo "Ups, der skete en fejl";
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>