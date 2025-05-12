function renderPage(route, val)
{
    let page = "";
    
    $("#add_user_email").val("");
    $("#c_title").val("");
    $("#account_email").val("");
    $("#account_password").val("");
    $("#account_username").val("");
    $("#new_password").val("");
    $(".toogle_tab").hide();

    if(route == "loggedin")
    {
        profile.setJWT(val['token']);
        profile.setID(val['id']);

        profile.setLoginStatus(true);
        
        if(profile.getRoute() == "#login")
        {
            route = "goto";
            val = "#calendars";
        }else{
            urlPath(profile.getRoute())
        }
    }

    switch (route) {
        case "error":
            page="#error";
            $("#error_msg").html(val);
            break;
    
        case "new_message":
            profile.setReceiver(val);
            page = "#messages";
            break;
    
        case "message_page":
            renderMessages(val);
            page = "#messages";
            break;
    
        case "#calendar":  
            apiRequest("calendar",val);
            profile.setCalendarID(val);
            page = "#load";
            break;

        case "calendars_list":
            calendarList(val);
            page = "#calendars";
            break;

        case "#settings":
            apiRequest("calendar_settings",val);
            profile.setCalendarID(val);
            page = "#load";
            break;

        case "settings_view":
            settings(val);
            page = "#settings_view";
            break;
        
        case "#success":
            $("#success_msg").html(val);
            page = "#success";
            break;

        case "calendar_view":
            calendar(val['bookings'],val['title']);
            page="#calendar";
            break;

        case "time_picker_view":
            timePicker(val['interval'], val['bookings'], getScreen());
            page="#time_picker";
            break;
            
        case "goto":
            switch (val) {
            case "":
                if(app==false){
                    val = "#home";
                }else{
                    val = "#calendars";
                }
                break;

            case "#calendars":
                apiRequest("view_calendars","");
                val="#load";
                break;

            case "dm":
                apiRequest("get_messages","");
                val = "#load";
                break;

            case "#messages":
                profile.setReceiver(null);
                apiRequest("get_messages","");
                val = "#load";
                break;

            case "#logout":
                profile.setLoginStatus(false);
                apiRequest("logout","");
                val = "#load";
                break;
            
           
        }
        page = val;
    }
    
    if(page!="")
    {
        if(page=="#calendar" || page=="#calendars" || page=="#messages" || page=="#time_picker" || page=="#profile" || page=="#create_calendar" || page=="#settings_view")
        {
            if(page=="#time_picker" || page=="#calendar" || page=="#settings_view")
            {
                if(profile.getCalendarID()==null)
                {
                    apiRequest("view_calendars","");
                }
            }

            if(profile.getLoginStatus()!=true)
            {
                page="#login";
            }
        }

        $(".content_div").hide();
        $(page).show();
        if(page=="#messages" && profile.getReceiver()!=null)
        {
            $('html,body').animate({scrollTop:10000});
        }else{
            $('html,body').animate({scrollTop:0});
        }
    }

    generateMenu();
}
