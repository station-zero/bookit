<?php
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
                echo json_encode(["route" => "goto", "val" => "#mail_send"]);
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
                $query = 'INSERT INTO calendar(owner, type, name, users) VALUES (:owner,:type,:name,:users)';
                $statement = $db->prepare($query);
                $statement->bindValue(':owner', $user['id']);
                $statement->bindValue(':type', $type);
                $statement->bindValue(':name', $title);
                $statement->bindValue(':users', $user['id']);
                
                $result = $statement->execute();

                if($db->changes() != 0)
                {
                    echo json_encode(["route" => "error", "val" => "Det virket!"]);
                }else{
                    echo json_encode(["route" => "error", "val" => "fejl i database"]);
                }
            }else{
                echo json_encode(["route" => "goto", "val" => "#login"]);
            }
        }

        if($input['action']=="save_dates")
        {
            $start = $input['start'];
            $end = $input['end'];
            $token = $input['token'];
            $calendar_id = $input['calendar_id'];
            
            $user = get_user($token); 
            if($user["valid"]==true)
            {
                $query = 'INSERT INTO calendar_blocks(start_time, end_time, user_id, calendar_id) VALUES (:start,:end,:user_id,:calendar_id)';
                $statement = $db->prepare($query);
                $statement->bindValue(':user_id', $user['id']);
                $statement->bindValue(':calendar_id', $calendar_id);
                $statement->bindValue(':start', $start);
                $statement->bindValue(':end', $end);
                $result = $statement->execute();

                if($db->changes() != 0)
                {
                    echo json_encode(["route" => "error", "val" => "Det virker!"]);
                }else{
                    echo json_encode(["route" => "error", "val" => "fejl i database"]);
                }
            }else{
                echo json_encode(["route" => "goto", "val" => "#login"]);
            }
        }
        
        if($input['action']=="save_timeslots")
        {
            
            $time_slots = $input['time_slots'];
            $token = $input['token'];
            $calendar_id = $input['calendar_id'];
            
            $user = get_user($token); 
            $str = "";
            if($user["valid"]==true)
            {
                foreach ($time_slots as $time_slot)
                {
                    $query = 'INSERT INTO calendar_blocks(start_time, end_time, user_id, calendar_id) VALUES (:start,:start,:user_id,:calendar_id)';
                    $statement = $db->prepare($query);
                    $statement->bindValue(':user_id', $user['id']);
                    $statement->bindValue(':calendar_id', $calendar_id);
                    $statement->bindValue(':start', $time_slot);
                    $result = $statement->execute();
                }
                
                if($db->changes() != 0)
                {
                    echo json_encode(["route" => "error", "val" => "Det virker!" . count($time_slots)]);
                }else{
                    echo json_encode(["route" => "error", "val" => "fejl i database"]);
                }
            }else{
                echo json_encode(["route" => "goto", "val" => "#login"]);
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
                echo json_encode(["route" => "goto", "val" => "#login"]);
            }
        }

        if($input['action']=="remove_booking")
        {
            $token = $input['token'];
            $id = $input['id'];
            
            $user = get_user($token); 
            if($user["valid"]==true)
            {
                $query = 'DELETE FROM calendar_blocks WHERE id=:id';
                $statement = $db->prepare($query);
                $statement->bindValue(':id', $id);
                $result = $statement->execute();

                echo json_encode(["route" => "error", "val" => "delete the booking: " . $id]);
                
            }else{
                echo json_encode(["route" => "goto", "val" => "#login"]);
            }
        }

        if($input['action']=="add_user")
        {
            $token = $input['token'];
            $email = $input['email'];
            $calendar_id = $input['calendar_id'];
            
            $user = get_user($token);
            $items = array();
                
            if($user["valid"]==true)
            {
                $query = 'INSERT INTO pending_users(email, calendar_id) VALUES (:email,:calendar_id)';
                $statement = $db->prepare($query);
                $statement->bindValue(':calendar_id', $calendar_id);
                $statement->bindValue(':email', $email);
                $result = $statement->execute();

                if($db->changes() != 0)
                {
                    echo json_encode(["route" => "#settings", "val" => $calendar_id]);
                }else{
                    echo json_encode(["route" => "error", "val" => "fejl i database"]);
                }
                
            }else{
            echo json_encode(["route" => "goto", "val" => "#login"]);
            }
        }

        if($input['action']=="calendar_settings")
        {
            $token = $input['token'];
            $id = $input['id'];
            $user = get_user($token);
            $items = array();
                
            if($user["valid"]==true)
            {
                $query = 'SELECT * FROM calendar WHERE id=:id';
                $statement = $db->prepare($query);
                $statement->bindValue(':id', $id);
                $result = $statement->execute();
                
                $type = "";
            
                while ($row = $result->fetchArray()) {
                    $user_list = array();
                    
                    $users = explode(",", $row['users']);
                    foreach($users as $user_id)
                    {
                        $user_list[] = array(
                            'id' => $user_id,
                            'name' => get_username($user_id),
                            'status' => "Active" 
                        );
                    }

                    
                    $items = array(
                        'id' => $row['id'], 
                        'type' => $row['type'], 
                        'users' => $user_list,
                        'pending' => get_pending_users($id)
                    );
                } 
                echo json_encode(["route" => "settings_view", "val" => $items]);
            }else{
            echo json_encode(["route" => "goto", "val" => "#login"]);
            }
        }

        if($input['action']=="calendar")
        {
            $token = $input['token'];
            $id = $input['id'];
            $user = get_user($token);

            if($user["valid"]==true)
            {
                $query = 'SELECT type, users FROM calendar WHERE id=:id';
                $statement = $db->prepare($query);
                $statement->bindValue(':id', $id);
                $result = $statement->execute();
                
                $type = "";
                $user_list = "";

                while ($row = $result->fetchArray()) {
                    $type = $row['type'];
                    $user_list = $row['users']; 
                }

                update_pending_users($id, $user_list);

                $items = array();
                
                $query = 'SELECT * FROM calendar_blocks WHERE calendar_id=:id';
                $statement = $db->prepare($query);
                $statement->bindValue(':id', $id);
                $result = $statement->execute();
                while ($row = $result->fetchArray()) {

                    $ownership = false;
                    
                    if($user["id"] == $row['user_id'])
                    {
                        $ownership = true;
                    }
                    $items[] = array(
                        'id' => $row['id'], 
                        'start' => $row['start_time'], 
                        'end' => $row['end_time'], 
                        'user_id' => $row['user_id'],
                        'user' => get_username($row['user_id']),
                        'ownership' => $ownership
                    );

                }
                if(allowed_in($id, $user["id"]))
                {
                    if($type=="day"){
                        echo json_encode(["route" => "calendar_view", "val" => $items]);
                    }else{
                        echo json_encode(["route" => "time_picker_view", "val" => $items]);
                    }
                }else{
                    echo json_encode(["route" => "error", "val" => "access denied"]);    
                }
            }else{
                echo json_encode(["route" => "goto", "val" => "#login"]);
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