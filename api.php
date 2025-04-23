<?php
//include 'db.php';
header('Access-Control-Allow-Origin: *');

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

switch ($method) {
    case 'POST':
        if($input['action']=="login")
        {
            echo json_encode(["message" => "successfully1 ", "JWT" => $jwt_token]); 
        }
        
        if($input['action']=="new_account")
        {
            //create new account
            echo json_encode(["message" => "successfully1 ", "JWT" => $jwt_token]); 
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

$conn->close();
?>