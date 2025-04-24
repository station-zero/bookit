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

$token  = base64_encode(random_bytes(12));
$secret = base64_encode(random_bytes(24));

$jwt_token = jwt($token, $secret, date("c"));   

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
        <a href="https://www.apoint.dk/verify.php?i=' . $code . '">Click HERE </a>
    </body>
    </html>
    ';

    mail($email, $subject, $message, $headers);
}

switch ($method) {
    case 'POST':
        if($input['action']=="login")
        {
            $username = $input['username'];
            $password = $input['password'];
           
            
            
            echo json_encode(["message" => "successfully1 ", "JWT" => $jwt_token]); 
        }
        
        if($input['action']=="new_account")
        {
            //create new account
            $username = $input['username'];
            $password = $input['password'];
            $email = $input['email'];
            $hashed_pw = hash("sha256", $password);
            $verified_code = base64_encode(random_bytes(12));

            $results = $db->prepare('INSERT INTO users(username, email, hashed_password, verified) VALUES (?,?,?,?)');
            $results->bindValue(1, $username);
            $results->bindValue(2, $email);
            $results->bindValue(3, $hashed_pw);
            $results->bindValue(4, $verified_code);
            $results->execute();
             
            if(!$results){
                echo json_encode(["message" => $db->lastErrorMsg()]);
            }else{
                send_mail($email,$verified_code);
                echo json_encode(["message" => "OKAY:" . $verified_code]);
            }
        }

        if($input['action']=="get_boards")
        {
            $boards = array();
            $boards[] = array('title' => 'John Doe1', 'id' => 30);
            $boards[] = array('title' => 'John Doe2', 'id' => 32);
            
            $dataArray = array('boards' => $boards, 'msg' => 10); 
             
            //create new account
            echo json_encode($dataArray); 
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