function callAPI(action, data_obj, type)
{	
	$.ajax({
		type: type,
		data: data_obj,
		url: "https://apoint.dk/php/api.php",
			success: function(data){
				const JsonData = jQuery.parseJSON(data);	
				if(type=="POST"){
					renderPage(JsonData.route,JsonData.val);
				}
				if(type=="GET"){
					inputValidationMsg(JsonData.validation);
				}
			},
		error: function(){
			renderPage("error", "something went wrong");
		}
	});
}