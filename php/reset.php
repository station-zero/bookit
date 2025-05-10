<?php
    $db = new SQLite3("database.db");

    $new_pw = $_POST["new_pw"];
    $reset_hash = $_POST["hash"];
    $hashed_pw = hash("sha256", $new_pw);
          
        $query = "UPDATE users SET hashed_password=:hashed_password, reset='' WHERE reset=:reset";
        $statement = $db->prepare($query);
        $statement->bindValue(":hashed_password", $hashed_pw);
        $statement->bindValue(":reset", $reset_hash);
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
                        <h2>Password ændret</h2>
                        <?
                            if($db->changes() != 0)
                            {
                                echo "Dit password er blevet ændret";
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
