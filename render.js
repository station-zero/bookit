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
        profile.setJWT(val);
        profile.setLoginStatus(true);
        
        if(profile.getRoute() == "#login")
        {
            route = "goto";
            val = "#dashboard";
        }else{
            urlPath(profile.getRoute())
        }
    }

    if(route=="#calendar")
    {
        apiRequest("calendar",val);
        profile.setCalendarID(val);
        page = "#load";
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
        page = val;
    }

    if(route=="calendar_view")
    {
        calendar(val);
        page="#calendar";
    }

    if(route=="time_picker_view")
    {
        timePicker(60, val, getScreen());
        page="#time_picker";

    }

    if(route=="api")
    {
        apiRequest(val,"");
        page = "#load";
    }

    menu += "<a href='#home' class='menu_link'>Home</a>";
    $("#login_btn").text("login");
    if(profile.getLoginStatus()==true)
    {
        menu = "<a href='#dashboard' class='menu_link'>Dashboard</a>";
        $("#login_btn").text("Profile");
    }
    $("#menu_bar").html(menu);


    if(page!="")
    {
        $(".content_div").hide();
        $(page).show();
        
    }
}
