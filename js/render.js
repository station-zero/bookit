function renderPage(route, val)
{
    let page = "";
    
    $("#add_user_email").val("");
    $("#c_title").val("");
    $("#account_email").val("");
    $("#account_password").val("");
    $("#account_username").val("");

    if(route=="error")
    {
        page="#error";
        $("#error_msg").html(val);
    }
        
    if(route=="loggedin")
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

    if(route=="new_message")
    {
        setReceiver(val);
        //TODO hide userlist
        page = "#messages";
    }

    if(route=="message_page")
    {
        renderMessages(val);
        page = "#messages";        
    }
    
    if(route=="#calendar")
    {
        apiRequest("calendar",val);
        profile.setCalendarID(val);
        page = "#load";
    }

    if(route=="calendars_list")
    {
        calendarList(val);
        page = "#calendars";
    }

    if(route=="#settings")
    {
        apiRequest("calendar_settings",val);
        profile.setCalendarID(val);
        page = "#load";
    }

    if(route=="settings_view")
    {
        settings(val);
        page = "#settings_view";
    }

    if(route=="#success")
    {
        $("#success_msg").html(val);
        page = "#success";
    }
    
    if(route=="goto")
    {
        if(val == ""){
            if(app==false){
                val = "#home";
            }else{
                val = "#calendars";
            }
        }

        if(val == "#calendars")
        {
            apiRequest("view_calendars","");
            val="#load";
        }

        if(val=="dm")
        {
            apiRequest("get_messages","");
            val = "#load";
        }

        if(val=="#messages")
        {
            profile.setReceiver(null);
            apiRequest("get_messages","");
            val = "#load";
        }

        if(val=="#logout")
        {
            profile.setLoginStatus(false);
            apiRequest("logout","");
            val = "#load";
        }

        page = val;
    }

    if(route=="calendar_view")
    {
        calendar(val['bookings'],val['title']);
        page="#calendar";
    }

    if(route=="time_picker_view")
    {
        timePicker(val['interval'], val['bookings'], getScreen());
        page="#time_picker";

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
