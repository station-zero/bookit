function apiRequest(action,parameter)
{
    let data_obj = {};
    if( action == "login")
    {	
        data_obj = {
            'action':action,
            'email': $("#login_email").val(),
            'password': $("#login_password").val()
        }
    }

    if(action == "new_account")
    {
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
        data_obj = {
                'action': action,
                'id': parameter,
                'token': profile.getJWT()
                }
    }	
    
    if(action == "save_timeslots")
    {
        data_obj = {
            'action':action,
            'token': profile.getJWT(),
            'time_slots': parameter,
            'calendar_id':profile.getCalendarID()
            }

    }

    if( action == "add_user")
    {
    data_obj = {
            'action': action,
            'email': $("#add_user_email").val(),
            'token': profile.getJWT()
            }
    }

    if( action == "new_calendar")
    {
    data_obj = {
            'action': action,
            'title': $("#c_title").val(),
            'type': $("#c_type").val(),
            'token': profile.getJWT()
            }
    }	

    if( action == "get_calendars")
    {
        
        data_obj = {
            'action':action,
            'token':profile.getJWT()
            }
    }

    if( action == "calendar")
    {
        data_obj = {
            'action':action,
            'token':profile.getJWT(),
            'id':parameter
            }
    }

    if( action == "calendar_settings")
        {
            data_obj = {
                'action':action,
                'token':profile.getJWT(),
                'id':parameter
                }
        }
        
    
    callAPI(action, data_obj);
}