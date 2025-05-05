function callAPI(action, data_obj, type)
{
	if(type=="POST")
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
					console.log(data);
				},
			error: function(){
				renderPage("error", "something went wrong");
			}
		});
	}

	if(type=="GET")
		{
			$.ajax({
				type: "GET",
				data: data_obj,
				url: "api.php",
					success: function(data){
						const JsonData = jQuery.parseJSON(data);
						message(JsonData.message);
					},
				error: function(){
					renderPage("error", "something went wrong");
				}
			});
		}
}
