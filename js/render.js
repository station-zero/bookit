function renderPage(route, val)
{
    let page = "";
    let menu = "";
    
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
    
    if(route=="goto")
    {
        if(val == ""){val = "#home";}

        if(val == "#calendars")
        {
            apiRequest("added_calendars","");
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
        calendar(val);
        page="#calendar";
    }

    if(route=="time_picker_view")
    {
        timePicker(val['interval'], val['bookings'], getScreen());
        page="#time_picker";

    }

    $("#login_btn").text("login");
    if(profile.getLoginStatus()==true)
    {
        menu = "<a href='#home' class='menu_link'>Home</a>";
        menu += "<a href='#calendars' class='menu_link'>Calendars</a>";
        menu += "<a href='#messages' class='menu_link'>Messages</a>";
        menu += "<a href='#profile' class='menu_link'>Profile</a>";
        
        $("#login_btn").text("Logout");
        $(".login_btn_a").attr("href", "#logout");
    }else{
        menu = "<a href='#home' class='menu_link'>Home</a>";
        $("#login_btn").text("Login");
        $(".login_btn_a").attr("href", "#login");
    }
    $("#menu_bar").html(menu);


    if(page!="")
    {
        if(page=="#calendar" || page=="#calendars" || page=="#messages" || page=="#time_picker" || page=="#profile" || page=="#create_calendar")
        {
            if(page=="#time_picker" || page=="#calendar")
            {
                if(profile.getCalendarID()==null)
                {
                    apiRequest("added_calendars","");
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
        history.pushState(null, null, page);
        
    }
}
