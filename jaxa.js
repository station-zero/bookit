function callAPI(action, data_obj)
{
	$.ajax({
		type: "POST",
		data: data_obj,
		url: "api.php",
			success: function(data){
				const JsonData = jQuery.parseJSON(data);
				
				if(action == "get_calendars")
				{
					renderCalendars(JsonData.calendars);						
				}

				renderPage(JsonData.route,JsonData.val);
			},
		error: function(){
			renderPage("error", "something went wrong");
		}
	});
}
