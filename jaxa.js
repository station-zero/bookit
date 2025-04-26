function callAPI(action, data_obj)
{
	$.ajax({
		type: "POST",
		data: data_obj,
		url: "api.php",
			success: function(data){
				const JsonData = jQuery.parseJSON(data);
				console.log(JsonData);
				if(action == "get_calendars")
				{
					$.each(JsonData.calendars, function(i, item) {
						addBoard(item.id,item.title);
					});						
				}
				renderPage(JsonData.route,JsonData.val);
			},
		error: function(){
			renderPage("error", "something went wrong");
		}
	});
}
