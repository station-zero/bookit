<?php
function b64Encoding($data) {
    $base64 = base64_encode($data);
    if ($base64 === false) {
      return false;
    }
    $url = strtr($base64, '+/', '-_');
    return rtrim($url, '=');
}
  
function jwt($token, $secret, $time)
{
// RFC-defined structure
    $header = ["alg" => "HS256", "typ" => "JWT"];
  
    // payload data
    $payload = ["token" => $token, "stamp" => $time];
  
    //signing the Json Web Token
    $jwt = b64Encoding(json_encode($header)) . "." . b64Encoding(json_encode($payload));
    $jwt = $jwt . "." . b64Encoding(hash_hmac('SHA256', $jwt, base64_decode($secret), true));
    return $jwt;
}

$method = $_SERVER['REQUEST_METHOD'];
$input = $_POST;

function send_mail($email, $code)
{
    $subject = 'Hello!';

    $headers = "From: noreply@apoint.dk\n";
    $headers .= "MIME-Version: 1.0\n";
    $headers .= "Content-type: text/html; charset=iso 8859-1";

    $message = '
    <html>
    <head>
        <title>Verify my ass...</title>
    </head>
    <body>
        <p>CLick on that F***ing link!:</p>
        <a href="https://www.apoint.dk/verify.php?i=' . $code . '">KLIK OG VIND EN MIO.</a>
    </body>
    </html>
    ';

    mail($email, $subject, $message, $headers);
}

function get_username($id)
{
    $name = "";

    $query = 'SELECT * FROM users WHERE id=:id';
    $statement = $GLOBALS["db"]->prepare($query);
    $statement->bindValue(':id', $id);
    $result = $statement->execute();
    
    while ($row = $result->fetchArray()) {
        $name = $row['username'];
    }  
    return $name;
}

function allowed_in($calendar_id, $user_id)
{
    $status = false;
    $query = 'SELECT users FROM calendar WHERE id=:calendar_id';
    $statement = $GLOBALS["db"]->prepare($query);
    $statement->bindValue(':calendar_id', $calendar_id);
    
    $result = $statement->execute();
    while ($row = $result->fetchArray()) {
        $user_list = explode(",",$row['users']);
        
        foreach($user_list as $id)
        {
            if($id == $user_id)
            {
                $status = true;     
            }
        }
    }

    return $status;
}

function get_pending_users($calendar_id)
{
    $pending_users = array();
    
    $query = 'SELECT * FROM pending_users WHERE calendar_id=:calendar_id';
    $statement = $GLOBALS["db"]->prepare($query);
    $statement->bindValue(':calendar_id', $calendar_id);
    $result = $statement->execute();
    
    while ($row = $result->fetchArray()) {
        $pending_users[] = array(
            'id' => $row['id'], 
            'name' => $row['email'],
            'status' => "pending" 
        );
    }
    return $pending_users;
}

function update_pending_users($calendar_id, $user_list)
{
    $pending_users = array();
    $remove_list = array();
    
    $query = 'SELECT * FROM pending_users WHERE calendar_id=:calendar_id';
    $statement = $GLOBALS["db"]->prepare($query);
    $statement->bindValue(':calendar_id', $calendar_id);
    $result = $statement->execute();
    
    while ($row = $result->fetchArray()) {
        $pending_users[] = array(
            'id' => $row['id'], 
            'email' => $row['email'] 
        );
    }  
    
    foreach($pending_users as $user)
    {
        $query = 'SELECT id FROM users WHERE email=:email';
        $statement = $GLOBALS["db"]->prepare($query);
        $statement->bindValue(':email', $user['email']);
        $result = $statement->execute();
        while ($row = $result->fetchArray()) {
            $user_list .= "," . $row['id'];
            $remove_list[] = $user['id'];
        }
    }

    $query = 'UPDATE calendar SET users=:user_list WHERE id=:calendar_id';
    $statement = $GLOBALS["db"]->prepare($query);
    $statement->bindValue(':calendar_id', $calendar_id);
    $statement->bindValue(':user_list', $user_list);
    
    $result = $statement->execute();

    foreach($remove_list as $item)
    {
        $query = 'DELETE FROM pending_users WHERE id=:id';
        $statement = $GLOBALS["db"]->prepare($query);
        $statement->bindValue(':id', $item);
        $result = $statement->execute();
    }
}

function get_user($token)
{
    $user_data = array( 
        'valid' => false 
    );
    
    $query = 'SELECT * FROM users WHERE jwt_token=:token AND verified=""';
    $statement = $GLOBALS["db"]->prepare($query);
    $statement->bindValue(':token', $token);
    $result = $statement->execute();
    
    while ($row = $result->fetchArray()) {
        $user_data['valid']=true;
        $user_data['id']=$row['id'];
        $user_data['email']=$row['email'];
    }  

    return $user_data;
}

function email_validation($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
?>