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
    
    callAPI(action, data_obj);
}