function apiRequest(action,parameter)
{
    let data_obj = {};
    let type = "";

    if( action == "login")
    {	
        type = "POST";
        data_obj = {
            'action':action,
            'email': $("#login_email").val(),
            'password': $("#login_password").val()
        }
    }

    if(action == "new_account")
    {
        type = "POST";
        data_obj = {
            'action':action,
            'email': $("#account_email").val(),
            'password': $("#account_password").val(),
            'username': $("#account_username").val()
            }
    }

    if(action == "save_dates")
    {
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
    }

    if( action == "remove_booking")
    {
        type = "POST";
        data_obj = {
                'action': action,
                'id': parameter,
                'token': profile.getJWT()
        }
    }	
    
    if(action == "save_timeslots")
    {
        type = "POST";
        data_obj = {
            'action':action,
            'token': profile.getJWT(),
            'time_slots': parameter,
            'calendar_id':profile.getCalendarID()
        }
    }

    if(action == "add_user")
    {
        type = "POST";
        data_obj = {
            'action': action,
            'email': $("#add_user_email").val(),
            'calendar_id':profile.getCalendarID(),
            'token': profile.getJWT()
        }
    }

    if(action == "remove_user")
    {
        type = "POST";
        data_obj = {
            'action': action,
            'id': parameter,
            'calendar_id':profile.getCalendarID(),
            'token': profile.getJWT()
        }
    }

    if(action == "remove_pending_user")
    {
        type = "POST";
        data_obj = {
            'action': action,
            'id': parameter,
            'calendar_id':profile.getCalendarID(),
            'token': profile.getJWT()
        }
    }    
    
    if(action == "remove_calendar")
    {
        type = "POST";
        data_obj = {
            'action': action,
            'calendar_id':profile.getCalendarID(),
            'token': profile.getJWT()
        }
    }

    if( action == "new_calendar")
    {
        type = "POST";
        data_obj = {
            'action': action,
            'title': $("#c_title").val(),
            'type': $("#c_type").val(),
            'interval': parseInt($("#c_interval").val()),
            'token': profile.getJWT()
        }
    }	

    if( action == "get_calendars")
    {
        type = "POST";
        data_obj = {
            'action':action,
            'token':profile.getJWT()
        }
    }

    if( action == "added_calendars")
    {
        type = "POST";
        data_obj = {
            'action':action,
            'token':profile.getJWT()
        }
    }

    if( action == "calendar")
    {
        type = "POST";
        data_obj = {
            'action':action,
            'token':profile.getJWT(),
            'id':parameter
        }
    }

    if( action == "calendar_settings")
    {
        type = "POST";
        data_obj = {
            'action':action,
            'token':profile.getJWT(),
            'id':parameter
            }
    }

    if( action == "check_email")
    {
        type = "GET";
        data_obj = {
            'action':action,
            'email':parameter
            }
    }
    
    if( action == "get_messages")
    {
        type = "POST";
        data_obj = {
            'action':action,
            'token':profile.getJWT()
        }
    }

    if( action == "send_message")
    {
        const msg = $("#msg").val();
        type = "POST";
        data_obj = {
            'action':action,
            'token':profile.getJWT(),
            'message':msg,
            'receiver':profile.getReceiver()
        }
    }    
    
    callAPI(action, data_obj, type);
}