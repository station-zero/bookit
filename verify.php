<?
    $db = new SQLite3('database.db');
    $code = $_GET['i'];
    
    $statement = $db->prepare('UPDATE users SET verified="" WHERE verified=:code');
    $statement->bindValue(':code', $code);
    
    $result = $statement->execute();
    
    if($db->changes() != 0)
    {
        echo "changed";
        
    }else{
        echo "error 111";
    }
?>

