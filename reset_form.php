<?php
    $code = $_GET['i'];
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="da-dk" lang="da-dk" dir="ltr">
	<head>
		<title>Apoint</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
	<body>
        <form action="reset.php" method="POST">
            <input name="hash" value="<? echo $code ?>">
            <table>
                <tr><td>Nyt password: </td><td><input name="new_pw"></tr>
                <tr><td colspan="2"><input type="submit" value="Send"></td></tr>
            </table>
            
        </form>
	</body>
</html>


