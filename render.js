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
        page = "#load";
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
        //const bookings = [{'start': "2025-05-12 00:00",'end': "2025-05-15 00:00",'id':12,'user':'james'},{'start': "2025-05-25 00:00",'end': "2025-06-06 00:00",'id':16,'user':'Cowboy Joe'}];
        console.log(val);
        calendar(val);
        page="#calendar";
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
