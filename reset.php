<?php
    $db = new SQLite3('database.db');

    $new_pw = $_POST['new_pw'];
    $reset_hash = $_POST['hash'];
    $hashed_pw = hash("sha256", $new_pw);
          
        $query = 'UPDATE users SET hashed_password=:hashed_password, reset="" WHERE reset=:reset';
        $statement = $db->prepare($query);
        $statement->bindValue(':hashed_password', $hashed_pw);
        $statement->bindValue(':reset', $reset_hash);
        $result = $statement->execute();
        if($db->changes() != 0)
        {
            echo "success";
        }else{
            echo "error"; 
        }
?>