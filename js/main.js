function getScreen()
{
    let viewType = "fullscreen";
    const screenWidth = $(window).width();

    if(screenWidth<800)
    {
        viewType = "phone";
    }
    return viewType;
}

function inputValidationMsg(message)
{
    $(".msg_box").text(message);
    if(message=="taken")
    {
        $("#account_email").css({"color":"#990000"});
        error = 1;
    }else{
        error = 0;
    }
}

function showMenu()
{
    const display = $("#menu_bar").css("display");
    if(display == "block")
    {
        $("#menu_bar").hide();
        $("#login_btn").hide();
    }else{
        $("#menu_bar").show();
        $("#login_btn").show();
    }
}

function calendarList(calendars)
{
    $("#calendar_list_view").html("");
    $.each(calendars, function(i, item) {
        let html = "<div class='calendar_box'>";
        html += "<a href='#calendar/" + item.id + "''>";
        html += "<div class='boxing'>" + item.title  + "</div></a>";
        html += "<div class='tab'><a href='#settings/" + item.id + "'><div class='settings_btn'></div></a></div>";
        $("#calendar_list_view").append(html);
    });

    let new_btn = "<div class='calendar_box'>";
    new_btn += "<div class='boxing' id='new_btn'>Opret kalendar</div>";
    new_btn += "<div class='tab'></div></div>";

    $("#calendar_list_view").append(new_btn);
}

function settings(calendar)
{
    $("#settings_c_url").html("<a href='#calendar/" + calendar['id'] + "'>apoint.dk#calendar/" + calendar['id'] + "</a>");
    $("#settings_c_type").html(calendar['type']);

    if(calendar['valid'] == true)
    {        
        profile.setCalendarUsers(calendar['users']);

        let html = "";

        $("#settings_c_users").html("");
        html += "<table><tr><td>Brugere</td><td>Status</td><td>Fjern adgang</td></tr>";

        for(user of calendar['users'])
        {	
            if(user['id']!="")
            {
                html += "<tr><td>" + user['name'] + "</td><td>" + user['status'] + "</td>";
                html += "<td><div class='remove_user' data-id='" + user['id'] + "'><img src='images/delete.png'></div></td></tr>";
            }
        }
        for(pending_user of calendar['pending'])
        {	
            if(pending_user['id']!="")
            {
                html += "<tr><td>" + pending_user['name'] + "</td><td>" + pending_user['status'] + "</td>";
                html += "<td><div class='remove_pending' data-id='" + pending_user['id'] + "'><img src='images/delete.png'></div></td></tr>";
            }
        }
        html += "</table>";
        $("#settings_c_users").append(html);
        $("#settings_option").html("<div id='delete_calendar'>Slet kalendar</div>");
        $(".owner_details").show();
    }else{
        $(".owner_details").hide();
        $("#settings_option").html("<div class='remove_user' data-id='" + profile.getID() + "'>Forlad kalendar</a>");
    }
}

function urlPath(hash)
{
    const path = hash.split("/");
    if(path.length == 2){
        renderPage(path[0],path[1]);
    }else{
        renderPage("goto",path[0]);
    }
    $(".msg_box").text("");
    if($("#phone_menu").css("display")=="block")
    {
        $("#menu_bar").hide();
        $("#login_btn").hide();
    }
    
    profile.setRoute(hash);
}
