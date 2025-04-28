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
        page="#loggedin";
        profile.setJWT(val);
        profile.setLoginStatus(true);
    }

    if(route=="#calendar")
    {
        apiRequest("calendar",val);
        page = route;
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

    if(route=="api")
    {
        apiRequest(val,"");
    }

    menu += "<a href='#home' class='menu_link'>Home</a>";
    if(profile.getLoginStatus()==true)
    {
        menu = "<a href='#dashboard' class='menu_link'>Home</a>";
    }
    $("#menu_bar").html(menu);


    if(page!="")
    {
        $(".content_div").hide();
        $(page).show();
    }
}
