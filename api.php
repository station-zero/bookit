<?php
//include 'db.php';
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
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
    return $jwt 
}

$token  = "Bmn0c8rQDJoGTibk" // base64_encode(random_bytes(12));
$secret "yXWczx0LwgKInpMFfgh0gCYCA8EKbOnw" = // base64_encode(random_bytes(24));

$jwt_token = jwt($token, $secret, date("c"));   

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'POST':
        if($input['token']==$token)
        {
            if($input['action']=="login")
            {
                echo json_encode(["message" => "successfully", "JWT" => $jwt_token]); 
            }

            if($input['action']=="new_account")
            {
                echo json_encode(["message" => "successfully"]); 
            }
        }

    case 'PUT':
        echo json_encode(["message" => "successfully"]);
        break;

    case 'DELETE':
        echo json_encode(["message" => "successfully"]);
        break;

    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}

$conn->close();
?>