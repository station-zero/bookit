<?php
//include 'db.php';
header('Access-Control-Allow-Origin: *');

$db = new SQLite3('database.db');

$jwt_token = "";

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

    $headers['From'] = 'noreply@apoint.dk';
    $headers['MIME-Version'] = 'MIME-Version: 1.0';
    $headers['Content-type'] = 'text/html; charset=iso-8859-1';

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

switch ($method) {
    case 'POST':
        if($input['action']=="login")
        {
            $email = $input['email'];
            $password = $input['password'];
            $hashed_password = hash("sha256", $password);
            
            $token  = base64_encode(random_bytes(12));
            $secret = base64_encode(random_bytes(24));            
            $jwt_token = jwt($token, $secret, date("c"));   

            $query = 'UPDATE users SET jwt_token=:token WHERE email=:email AND hashed_password=:hashed_password';
            $statement = $db->prepare($query);
            $statement->bindValue(':token', $jwt_token);
            $statement->bindValue(':email', $email);
            $statement->bindValue(':hashed_password', $hashed_password);
            $result = $statement->execute();
            
            if($db->changes() != 0)
            {
                echo json_encode(["route" => "loggedin", "val" => $jwt_token]);
            }else{
                echo json_encode(["route" => "error", "val" => "2"]);
            } 
        }
        
        if($input['action']=="new_account")
        {
            //create new account
            $username = $input['username'];
            $password = $input['password'];
            $email = $input['email'];
            $hashed_pw = hash("sha256", $password);
            
            $verified_code = base64_encode(random_bytes(12));
            $verified_code = preg_replace('/[^a-zA-Z0-9_ -]/s','-',$verified_code);

            $query = 'INSERT INTO users(username, email, hashed_password, verified) VALUES (:username,:email,:hashed_pw,:verified)';
            $statement = $db->prepare($query);
            $statement->bindValue(':username', $username);
            $statement->bindValue(':email', $email);
            $statement->bindValue(':hashed_pw', $hashed_pw);
            $statement->bindValue(':verified', $verified_code);
            $result = $statement->execute();
            
            if($db->changes() != 0)
            {
                send_mail($email,$verified_code);
                echo json_encode(["route" => "mail_send", "val" => ""]);
            }else{
                echo json_encode(["route" => "error", "val" => "1"]);
            }
        }

        if($input['action']=="new_calendar")
        {
            $title = $input['title'];
            $type = $input['type'];
            $token = $input['token'];
            
            $user = get_user($token); 
            if($user["valid"]==true)
            {
                $query = 'INSERT INTO calendar(owner, type, name) VALUES (:owner,:type,:name)';
                $statement = $db->prepare($query);
                $statement->bindValue(':owner', $user['id']);
                $statement->bindValue(':type', $type);
                $statement->bindValue(':name', $title);
                $result = $statement->execute();

                if($db->changes() != 0)
                {
                    echo json_encode(["route" => "error", "val" => "Det virket!"]);
                }else{
                    echo json_encode(["route" => "error", "val" => "4"]);
                }
            }else{
                echo json_encode(["route" => "error", "val" => "5"]);
            }
        }            

        if($input['action']=="get_calendars")
        {
            $token = $input['token'];
            $user = get_user($token); 
            if($user["valid"]==true)
            {
                $query = 'SELECT * FROM calendar WHERE owner=:owner';
                $statement = $db->prepare($query);
                $statement->bindValue(':owner', $user['id']);
                $result = $statement->execute();
                
                $items = array();
            
                while ($row = $result->fetchArray()) {
                    $items[] = array('id' => $row['id'], 'title' => $row['name']); 
                }    
                echo json_encode(["route" => "dashboard", "val" => "dashboard", "calendars" => $items]);
            }else{
                echo json_encode(["route" => "error", "val" => "6"]);
            }
        }
        break;
    
    case 'PUT':
        echo json_encode(["message" => "successfully3"]);
        break;

    case 'DELETE':
        echo json_encode(["message" => "successfully4"]);
        break;

    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}

?>