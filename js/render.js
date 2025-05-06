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
        console.log(val['token']);
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
		
        if(val == "#dashboard")
        {
            apiRequest("get_calendars","");
        }

        if(val == "#calendars")
        {
            apiRequest("added_calendars","");
            val="#load";
        }

        if(val=="dm")
        {
            apiRequest("get_messages","");
            page = "#load";
        }

        if(val=="#messages")
        {
            profile.setReceiver(null);
            apiRequest("get_messages","");
            page = "#load";
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

    menu += "<a href='#home' class='menu_link'>Home</a>";
    $("#login_btn").text("login");
    if(profile.getLoginStatus()==true)
    {
        menu = "<a href='#home' class='menu_link'>Home</a>";
        menu += "<a href='#calendars' class='menu_link'>Calendars</a>";
        menu += "<a href='#dashboard' class='menu_link'>Control Panel</a>";
        menu += "<a href='#messages' class='menu_link'>Messages(0)</a>";
        
        $("#login_btn").text("Profile");
    }
    $("#menu_bar").html(menu);


    if(page!="")
    {
        $(".content_div").hide();
        $(page).show();
        
    }
}
