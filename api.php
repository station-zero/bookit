<?php
header('Access-Control-Allow-Origin: *');
date_default_timezone_set("Europe/Copenhagen");
            
$db = new SQLite3('database.db');

$jwt_token = "";

include("controllers.php");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $input = $_POST;

        if($input['action']=="login")
        {
            $email = $input['email'];
            $password = $input['password'];
            $hashed_password = hash("sha256", $password);
            
            $token  = base64_encode(random_bytes(12));
            $secret = base64_encode(random_bytes(24));            
            $jwt_token = jwt($token, $secret, date("c")); 
            $expire = round(microtime(true)) + (60 * 120);  

            
                $query = 'UPDATE users SET jwt_token=:token, jwt_token_expire=:expire WHERE email=:email AND hashed_password=:hashed_password';
                $statement = $db->prepare($query);
                $statement->bindValue(':token', $jwt_token);
                $statement->bindValue(':expire', $expire);
                $statement->bindValue(':email', $email);
                $statement->bindValue(':hashed_password', $hashed_password);
                $result = $statement->execute();
                
                if($db->changes() != 0)
                {
                    $user = get_user($jwt_token);
                    update_pending_users($user['email']);
                    
                    $user_data = array('id' => $user['id'], 'token' => $jwt_token); 

                    echo json_encode(["route" => "loggedin", "val" => $user_data]);
                    
                }else{
                    echo json_encode(["route" => "error", "val" => "Forkret password eller e-mail"]);
                }
        }

        if($input['action']=="logout")
        {
            $token = $input['token'];
         
            $user = get_user($token); 
            if($user["valid"]==true)
            {
                $query = 'UPDATE users SET jwt_token_expire=:expire WHERE jwt_token=:jwt_token';
                $statement = $db->prepare($query);
                $statement->bindValue(':expire', 0);
                $statement->bindValue(':jwt_token', $token);
                $result = $statement->execute();
                
                if($db->changes() != 0)
                {
                    echo json_encode(["route" => "error", "val" => "logget ud"]);
                }else{
                    echo json_encode(["route" => "error", "val" => "Fejl i Database"]);
                } 
            }
        }

        if($input['action']=="reset")
        {
            $email = $input['email'];
            $reset_hash = base64_encode(random_bytes(12));

            $query = 'UPDATE users SET reset=:reset WHERE email=:email';
            $statement = $db->prepare($query);
            $statement->bindValue(':reset', $reset_hash);
            $statement->bindValue(':email', $email);
            $result = $statement->execute();
            if($db->changes() != 0)
            {
                send_reset_mail($email, $reset_hash);
                echo json_encode(["route" => "error", "val" => "Mail sendt"]);
            }else{
                echo json_encode(["route" => "error", "val" => "Fejl i Database"]); 
            }
        }

        if($input['action']=="new_account")
        {
            $username = $input['username'];
            $password = $input['password'];
            $email = $input['email'];
            $hashed_pw = hash("sha256", $password);
            
            $verified_code = base64_encode(random_bytes(12));
            $verified_code = preg_replace('/[^a-zA-Z0-9_ -]/s','-',$verified_code);
            if(email_validation($email)==false)
            {
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
                    echo json_encode(["route" => "error", "val" => "Fejl i database"]);
                }
            }else{
            echo json_encode(["route" => "error", "val" => "E-mail eksistere allerede i systemet"]);
            }    
        }

        if($input['action']=="new_calendar")
        {
            $title = $input['title'];
            $type = $input['type'];
            $token = $input['token'];
            $interval = $input['interval'];

            $user = get_user($token); 
            if($user["valid"]==true)
            {
                $query = 'INSERT INTO calendar(owner, type, name, users, interval) VALUES (:owner,:type,:name,:users,:interval)';
                $statement = $db->prepare($query);
                $statement->bindValue(':owner', $user['id']);
                $statement->bindValue(':type', $type);
                $statement->bindValue(':name', $title);
                $statement->bindValue(':users', $user['id']);
                $statement->bindValue(':interval', $interval);
                $result = $statement->execute();

                if($db->changes() != 0)
                {
                    echo json_encode(["route" => "goto", "val" => "#calendars"]);
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
                    echo json_encode(["route" => "goto", "val" => "#calendar/" + $calendar_id]);
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
            $str = "";
            
            $user = get_user($token); 
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

        if($input['action']=="added_calendars")
        {
            $token = $input['token'];
            
            $user = get_user($token); 
            if($user["valid"]==true)
            {
                $query = 'SELECT id, users, name FROM calendar';
                $statement = $db->prepare($query);
                $statement->bindValue(':owner', $user['id']);
                $result = $statement->execute();
                
                $items = array();
            
                while ($row = $result->fetchArray()) {
                    $user_list = explode(",",$row['users']);
                    foreach($user_list as $user_obj)
                    {   
                        if($user_obj == $user['id'])
                        {
                            $items[] = array(
                                'id' => $row['id'],
                                'title' => $row['name']
                            );
                        }
                    }
                }
                echo json_encode(["route" => "calendars_list", "val" => $items]);
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
                $query = 'DELETE FROM calendar_blocks WHERE id=:id AND owner=:user_id';
                $statement = $db->prepare($query);
                $statement->bindValue(':id', $id);
                $statement->bindValue(':user_id', $user['id']);
                
                $result = $statement->execute();

                echo json_encode(["route" => "error", "val" => "delete the booking: " . $id]);
                
            }else{
                echo json_encode(["route" => "goto", "val" => "#login"]);
            }
        }

        if($input['action']=="remove_pending_user")
        {
            $token = $input['token'];
            $id = $input['id'];
            $calendar_id = $input['calendar_id'];
            
            $user = get_user($token); 
            if($user["valid"]==true)
            {
                $query = 'DELETE FROM pending_users WHERE id=:id';
                $statement = $db->prepare($query);
                $statement->bindValue(':id', $id);
                $result = $statement->execute();

                echo json_encode(["route" => "#settings", "val" => $calendar_id]);
                
            }else{
                echo json_encode(["route" => "goto", "val" => "#login"]);
            }
        }

        if($input['action']=="remove_user")
        {
            $token = $input['token'];
            $id = $input['id'];
            $calendar_id = $input['calendar_id'];
            $user_list = "";
            $owner = null;

            $user = get_user($token); 
            if($user["valid"]==true)
            {
                $query = 'SELECT users, owner FROM calendar WHERE id=:calendar_id';
                $statement = $db->prepare($query);
                $statement->bindValue(':calendar_id', $calendar_id);
                $result = $statement->execute();
                
                while ($row = $result->fetchArray()) {
                    $user_list = $row['users'];
                    $owner = $row['owner'];
                }

                $user_list = str_replace($id,"",$user_list);
                $user_list = str_replace(",,",",",$user_list);
                
                $query = 'UPDATE calendar SET users=:users WHERE id=:calendar_id';
                $statement = $db->prepare($query);
                $statement->bindValue(':users', $user_list);
                $statement->bindValue(':calendar_id', $calendar_id);
                $result = $statement->execute();
                
                if($db->changes() != 0)
                {
                    if($id!=$user['id'])
                    {
                        echo json_encode(["route" => "#settings", "val" => $calendar_id]);
                    }else{
                        echo json_encode(["route" => "goto", "val" => "#calendars"]);
                    }
                }else{
                    echo json_encode(["route" => "error", "val" => "222"]);
                }   
               
            }else{
                echo json_encode(["route" => "goto", "val" => "#login"]);
            }
        }

        if($input['action']=="add_user")
        {
            $token = $input['token'];
            $email = $input['email'];
            $calendar_id = $input['calendar_id'];
            $items = array();
            
            $user = get_user($token);    
            if($user["valid"]==true)
            {
                $query = 'INSERT INTO pending_users(email, calendar_id) VALUES (:email,:calendar_id)';
                $statement = $db->prepare($query);
                $statement->bindValue(':calendar_id', $calendar_id);
                $statement->bindValue(':email', $email);
                $result = $statement->execute();

                if($db->changes() != 0)
                {
                    update_pending_users($email);
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
            $items = array();

            $user = get_user($token);    
            if($user["valid"]==true)
            {
                $query = 'SELECT * FROM calendar WHERE id=:id';
                $statement = $db->prepare($query);
                $statement->bindValue(':id', $id);
                $result = $statement->execute();
                
                while ($row = $result->fetchArray()) {
                    $user_list = array();
                    
                    if($user["id"] == $row['owner'])
                    {
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
                            'valid' => true,
                            'id' => $row['id'], 
                            'type' => $row['type'], 
                            'users' => $user_list,
                            'pending' => get_pending_users($id)
                        );
                    }else{
                        $items = array(
                            'valid' => false
                        );
                    }
                } 
                echo json_encode(["route" => "settings_view", "val" => $items]);
            }else{
            echo json_encode(["route" => "goto", "val" => "#login"]);
            }
        }

        if($input['action']=="remove_calendar")
        {
            $token = $input['token'];
            $calendar_id = $input['calendar_id'];
            
            $user = get_user($token); 
            if($user["valid"]==true)
            {
                $query = 'DELETE FROM calendar WHERE id=:calendar_id AND owner=:user_id';
                $statement = $db->prepare($query);
                $statement->bindValue(':calendar_id', $calendar_id);
                $statement->bindValue(':user_id', $user['id']);
                $result = $statement->execute();

                echo json_encode(["route" => "error", "val" => "deleted"]);
                
            }else{
                echo json_encode(["route" => "goto", "val" => "#login"]);
            }
        }

        if($input['action']=="send_message")
        {
            
            $token = $input['token'];
            $receiver = $input['receiver'];
            $message = $input['message'];
            $date = date("H:i d/m/Y");
            
            $user = get_user($token); 
            if($user["valid"]==true)
            {
                $query = 'INSERT INTO msg(sender, receiver, message, timestamp) VALUES (:sender,:receiver,:message,:timestamp)';
                $statement = $db->prepare($query);
                $statement->bindValue(':sender', $user['id']);
                $statement->bindValue(':receiver', $receiver);
                $statement->bindValue(':message', $message);
                $statement->bindValue(':timestamp',$date);
                $result = $statement->execute();
                if($db->changes() != 0)
                {
                    echo json_encode(["route" => "goto", "val" => "#dm"]);
                }else{
                    echo json_encode(["route" => "error", "val" => "fejl i database"]);
                }
            }else{
                echo json_encode(["route" => "goto", "val" => "#login"]);
            }
        }

        if($input['action']=="get_messages")
        {
            $token = $input['token'];
            $message_list = array();
           
            $user = get_user($token); 
            if($user["valid"]==true)
            {
                $query = 'SELECT * FROM msg WHERE receiver=:id OR sender=:id';
                $statement = $db->prepare($query);
                $statement->bindValue(':id', $user['id']);
                $result = $statement->execute();
                
                while ($row = $result->fetchArray()) {            
                    $message_list[] = array(
                        'id' => $row['id'],
                        'sender_id' => $row['sender'],
                        'receiver_id' => $row['receiver'],
                        'sender_name' => get_username($row['sender']),
                        'receiver_name' => get_username($row['receiver']),
                        'timestamp' => $row['timestamp'],
                        'msg' => $row['message']
                    );
                }     
                echo json_encode(["route" => "message_page", "val" => $message_list]);
            }else{
                echo json_encode(["route" => "goto", "val" => "#login"]);                
            }
        }

        if($input['action']=="calendar")
        {
            $token = $input['token'];
            $id = $input['id'];
            $interval = 0;

            $user = get_user($token);
            if($user["valid"]==true)
            {
                $query = 'SELECT type, users, interval FROM calendar WHERE id=:id';
                $statement = $db->prepare($query);
                $statement->bindValue(':id', $id);
                $result = $statement->execute();
                
                $type = "";
                $user_list = "";

                while ($row = $result->fetchArray()) {
                    $type = $row['type'];
                    $user_list = $row['users'];
                    $interval = $row['interval'];
                }

                $data = array();
                $bookings = array();
                
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

                    $bookings[] = array(
                        'id' => $row['id'], 
                        'start' => $row['start_time'], 
                        'end' => $row['end_time'], 
                        'user_id' => $row['user_id'],
                        'user' => get_username($row['user_id']),
                        'ownership' => $ownership
                    );  
                }

                $data = array(
                    'interval' => $interval,
                    'bookings' => $bookings
                );

                if(allowed_in($id, $user["id"]))
                {
                    if($type=="day"){
                        echo json_encode(["route" => "calendar_view", "val" => $bookings]);
                    }else{
                        echo json_encode(["route" => "time_picker_view", "val" => $data]);
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
    
    case 'GET':
        $input = $_GET;

        if($input['action']=="check_email")
        {
            $email = $input['email'];
            $valid = true;
            
            if(email_validation($email)==true)
            {
                $query = 'SELECT email FROM users WHERE email=:email';
                $statement = $db->prepare($query);
                $statement->bindValue(':email', $email);
                $result = $statement->execute();
               
                while ($row = $result->fetchArray()) {
                    $valid = false;
                }

                if($valid == false){
                    echo json_encode(["message" => "taken"]);
                }else{
                    echo json_encode(["message" => "ok"]);
                }
            }else{
                echo json_encode(["message" => "invalid"]);
            }
        }
        break;

    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;        
}

?>