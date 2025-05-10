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

function send_mail($email, $subject, $message)
{
    $subject_encoded = '=?UTF-8?B?'.base64_encode(utf8_encode($subject)).'?=';

    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: text/html; charset=UTF-8"';
    $headers[] = 'From: Apoint.dk <no-reply@apoint.dk>';
    $headers[] = 'X-Priority: 3';
    $headers[] = 'X-Mailer: PHP'. phpversion();
    
    mail($email, $subject_encoded, $message, implode("\r\n", $headers));
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

function email_aviable($email)
{
    $avilable = true;

    $query = 'SELECT * FROM users WHERE email=:email';
    $statement = $GLOBALS["db"]->prepare($query);
    $statement->bindValue(':email', $email);
    $result = $statement->execute();
    
    while ($row = $result->fetchArray()) {
        $avilable = false;
    }  
    return $avilable;
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


function update_pending_users($user_email)
{
    $pending_users = array();
    $user_id = null;

    $query = 'SELECT id FROM users WHERE email=:email';
    $statement = $GLOBALS["db"]->prepare($query);
    $statement->bindValue(':email', $user_email);
    $result = $statement->execute();
    while ($row = $result->fetchArray()) {
        $user_id = $row['id'];
    }    

    $query = 'SELECT * FROM pending_users WHERE email=:email';
    $statement = $GLOBALS["db"]->prepare($query);
    $statement->bindValue(':email', $user_email);
    $result = $statement->execute();
    
    while ($row = $result->fetchArray()) {
        $pending_users[] = array(
            'pending_id' => $row['id'],
            'calendar_id' => $row['calendar_id'],
            'user_id' => $user_id
        );
    }  
    
    foreach($pending_users as $user)
    {
        if($user['user_id'] != null)
        {
            $query = 'UPDATE calendar SET users=users||:user_list WHERE id=:calendar_id';
            $statement = $GLOBALS["db"]->prepare($query);
            $statement->bindValue(':calendar_id', $user['calendar_id']);
            $statement->bindValue(':user_list', "," . $user['user_id']);
            $result = $statement->execute();

            $query = 'DELETE FROM pending_users WHERE id=:id';
            $statement = $GLOBALS["db"]->prepare($query);
            $statement->bindValue(':id', $user['pending_id']);
            $result = $statement->execute();
                
        }
    
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
        if(round(microtime(true)) < $row['jwt_token_expire'])
        {
            $user_data['valid']=true;
            $user_data['id']=$row['id'];
            $user_data['email']=$row['email'];
        }
    }  

    return $user_data;
}

function email_validation($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
?>