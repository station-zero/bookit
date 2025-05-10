function generateMenu()
{
    let menu = "";
    let phone_menu = "";
    
    menu = "<a href='#home' class='menu_link'>Forside</a>";
        
    $("#login_btn").text("login");
    if(profile.getLoginStatus()==true)
    {
        menu += "<a href='#calendars' class='menu_link'>Dine kalendare</a>";
        menu += "<a href='#messages' class='menu_link'>Beskeder</a>";
        menu += "<a href='#profile' class='menu_link'>Profil</a>";
        
        phone_menu = "<a href='#calendars' class='icon_btn'><div id='calendar_icon'></div></a>";
        phone_menu += "<a href='#messages' class='icon_btn'><div id='message_icon'></div></a>";
		phone_menu += "<a href='#profile'class='icon_btn'><div id='profile_icon'></div></a>";
		phone_menu += "<a href='#logout' class='icon_btn'><div id='login_icon'></div></a>";
			
        $("#login_btn").text("Logout");
        $(".login_btn_a").attr("href", "#logout");
    }else{
        menu += "<a href='#new_account' class='menu_link'>Opret bruger</a><a href='#about' class='menu_link'>Om Apoint</a>";
        if(app==true)
        {
            $("#phone_menu_btn").hide();
            phone_menu = "<a href='#login' class='icon_btn'><div id='login_icon' style='float:right;'></div></a>";
        }else{
            phone_menu = "<div id='phone_menu_btn'></div>";
        }

        $("#login_btn").text("Login");
        $(".login_btn_a").attr("href", "#login");
    }

    $("#menu_bar").html(menu);
    $("#phone_menu").html(phone_menu);
}