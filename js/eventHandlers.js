$(function(){
    const check_gdpr = window.localStorage.getItem("gdpr");
    if(check_gdpr)
    {
        $("#gdpr_banner").hide();
    }

    $("#account_email").on("keyup", function(){
        const regex = /\S+@\S+\.\S+/;
        const email = $(this).val();
        const valid =  regex.test(email);
        
        if(valid)
        {
            apiRequest("check_email",email);
            $("#account_email").css({"color":"#000000"});
        }else{
            $("#account_email").css({"color":"#990000"});
        }
    });

    $(document).on('click', '.last_msg_box', function() {
        const id = $(this).data("receiver"); 
        profile.setReceiver(id);
        renderPage("goto","dm");
    });

    $(document).on('click', '#phone_menu_btn', function() {
        showMenu();
    });

    $(document).on('click', '#older_msg_btn', function() {
        viewOlderMessages();
    });


    $(document).on('click', '#new_btn', function() {
        renderPage("goto","#create_calendar");
        $("#c_type").val("day");
        $(".optional").hide();
    });

    $(document).on('click', '.send_msg_btn', function() {
        renderPage("goto","dm");
    });

    $(document).on('click', '.remove_user', function() {
        const id = $(this).data("id"); 
        apiRequest("remove_user",id);
    });

    $(document).on('click', '.remove_pending', function() {
        const id = $(this).data("id"); 
        apiRequest("remove_pending_user",id);
    });

    $(document).on('click', '#delete_calendar', function() {
        apiRequest("remove_calendar","");
    });

    $(window).on('popstate', function(event) {
        const hash = event.target.location.hash;
        urlPath(hash);
        error = 0;
    });

    $(window).on( "resize", function() {
        if(getScreen()=="phone")
            {
                $("#menu_bar").hide();
                $("#login_btn").hide();
                $("#phone_menu").show();
            }else{
                $("#menu_bar").show();
                $("#login_btn").show();
                $("#phone_menu").hide();
            }
    });

    $("#gdpr_ok").on("click", function() {
        $("#gdpr_banner").hide();
        window.localStorage.setItem("gdpr", 1);
    });

    $(".submit_btn").on("click", function() {
        const action = $(this).data("action");
        apiRequest(action,"");
    });

    $("#c_type").on("change", function(){
        if($(this).val() == "time")
        {
            $(".optional").show();
        }else{
            $(".optional").hide();
        }
    });

    const hash = window.location.hash;
    urlPath(hash);
});