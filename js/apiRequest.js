function apiRequest(action,parameter)
{
    let data_obj = {};
    let type = "";

    switch(action)
    {
        case "login":
            type = "POST";
            data_obj = {
                'action':action,
                'email': $("#login_email").val(),
                'password': $("#login_password").val()
            }
        break;

        case "new_account":
            type = "POST";
            data_obj = {
                'action':action,
                'email': $("#account_email").val(),
                'password': $("#account_password").val(),
                'username': $("#account_username").val()
                }
        break;

        case "delete_userprofile":
            profile.setLoginStatus(false);
            type = "POST";
            data_obj = {
                'action':action,
                'token': profile.getJWT()
            }
        break;

        case "save_dates":
            let startInput = $("#startDate").val();
            let endInput = $("#endDate").val();
            let start = startInput.split("/");
            let end = endInput.split("/");
        
            type = "POST";        
            data_obj = {
                'action':action,
                'token': profile.getJWT(),
                'start': String(start[2]).padStart(2,"0") + "-" + String(start[1]).padStart(2,"0") + "-" + start[0] + " 00:00",
                'end': String(end[2]).padStart(2,"0") + "-" + String(end[1]).padStart(2,"0") + "-" + end[0] + " 00:00",
                'calendar_id':profile.getCalendarID()
            }
            break;

        case "remove_booking":
            type = "POST";
            data_obj = {
                'action': action,
                'id': parameter,
                'calendar_id': profile.getCalendarID,
                'token': profile.getJWT()
            }
            break;

        case "save_timeslots":
            type = "POST";
            data_obj = {
                'action':action,
                'token': profile.getJWT(),
                'time_slots': parameter,
                'calendar_id':profile.getCalendarID()
            }
            break;

        case "add_user":
            type = "POST";
            data_obj = {
                'action': action,
                'email': $("#add_user_email").val(),
                'calendar_id':profile.getCalendarID(),
                'token': profile.getJWT()
            }
            break;

        case "remove_user":
            type = "POST";
            data_obj = {
                'action': action,
                'id': parameter,
                'calendar_id':profile.getCalendarID(),
                'token': profile.getJWT()
            }
            break;

        case "remove_pending_user":
            type = "POST";
            data_obj = {
                'action': action,
                'id': parameter,
                'calendar_id':profile.getCalendarID(),
                'token': profile.getJWT()
            }
            break;
        
        case "remove_calendar":
            type = "POST";
            data_obj = {
                'action': action,
                'calendar_id':profile.getCalendarID(),
                'token': profile.getJWT()
            }
            break;

        case "new_calendar":
            type = "POST";
            data_obj = {
                'action': action,
                'title': $("#c_title").val(),
                'type': $("#c_type").val(),
                'interval': parseInt($("#c_interval").val()),
                'token': profile.getJWT()
            }
            break;
        
        case "view_calendars":
            type = "POST";
            data_obj = {
                'action':action,
                'token':profile.getJWT()
            }
            break;
            
        case "new_password":
            type = "POST";
            data_obj = {
                'action':action,
                'token':profile.getJWT(),
                'new_password':$("#new_password").val()
            }
            break;
            
        case "calendar":
            type = "POST";
            data_obj = {
                'action':action,
                'token':profile.getJWT(),
                'id':parameter
            }
            break;
            
        case "calendar_settings":
            type = "POST";
            data_obj = {
                'action':action,
                'token':profile.getJWT(),
                'id':parameter
            }
            break;
            
        case "reset":
            type = "POST";
            data_obj = {
                'action':action,
                'email':$("#reset_email").val()
            }
            break;
    
        case "check_email":
            type = "GET";
            data_obj = {
                'action':action,
                'email':parameter
            }
            break;
        
        case "get_messages":
            type = "POST";
            data_obj = {
                'action':action,
                'token':profile.getJWT()
            }
            break;
        
        case "send_message":
            const msg = $("#msg").val();
            type = "POST";
            data_obj = {
                'action':action,
                'token':profile.getJWT(),
                'message':msg,
                'receiver':profile.getReceiver()
            }
            break;
    
        case "logout":
            type = "POST";
            data_obj = {
                'action':action,
                'token':profile.getJWT()
            }
            break;
    }

    callAPI(data_obj, type);
}