    function timePicker(interval, bookings, screenType){

        const now = new Date();
                
        class  Date_obj {
            constructor() {
                this.DD = now.getDate();
                this.MM = now.getMonth();
                this.YY = now.getFullYear();
            }
        }
        let today = new Date_obj;
        let date = new Date_obj;
        let selectedDate = new Date_obj;

        let state = 0;

        const freeColor = "#3333ff";
        const selectedColor = "#33FF33";
        const takenColor = "#FF3333";
        
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        let time_slots = [];

        $("#pick_date_btn").html(date.DD + "/" + (date.MM + 1) + "/" + date.YY);
        
        $("#time_line").html("");
        $("#time_slots").html("");
            
        $("#small_calendar_wrapper").hide();
        $("#booking_list_view").show();
        $("#pick_date_btn").show();
        $("#view_time_list").show();
        $("#schedu_box").show();
            
        if(screenType=="phone")
        {
            $("#schedu_box").hide();
            $("#view_time_list").hide();
        }
        initCalendar();
        createBookingList();
        createTimeView();

        function createBookingList()
        {
            $("#booking_list_view").html("");
            let html = "";
            
            if(screenType=="phone"){
                $("#booking_list_view").append("<div id='back_time_picker'>Back</div>");
            }else{
                $("#booking_list_view").append("<div>Bookings</div>");
            }

            for(selected_date of time_slots)
            {
                $("#booking_list_view").append("<div class='booking_list_item'>" + prettyTimeFormat(selected_date) + "</div>"); 
            }
        }

        
        function createTimeView()
        {
            $("#time_line").html("");
            $("#time_slots").html("");
            $("#time_slots").hide();
            
            for(let i=0; i < 24; i++)
            {
                const hour = String(i).padStart(2, '0');
                $("#time_line").append("<div class='time_indicator'><div>" + hour + ":00" + "</div></div>");
                $("#time_line").append("<div class='time_indicator'><div>-</div></div>");                    
            }
            
            const max_slots = 1440/interval;
            for(let i=0; i < max_slots; i++)
            {
                let color = freeColor;
                let status = "free";
                let id = null;
                
                const startTime = timeConverter(i * interval);
                const endTime = timeConverter((i + 1) * interval);
                
                let HHMM = startTime.split(":");

                const booking = checkDate(date.YY,(date.MM + 1),date.DD,HHMM[0],HHMM[1]);
                
                const timeVal = date.YY + "-" + String(date.MM + 1).padStart(2,"0") + "-" + String(date.DD).padStart(2,"0") + " " + startTime;
                
                if(booking.length > 0)
                {
                    color = takenColor;
                    status = "taken";
                    id = booking_detail[1];
                    user = booking_detail[0];
                }

                for(selected_date of time_slots)
                {
                    if(selected_date==timeVal)
                    {
                        color = selectedColor;
                    }
                }

                const text = startTime + " - " + endTime;
                $("#time_slots").append("<div style='height:" + interval + "px; background:" + color + 
                "' class='time_slot' data-status='" + status + 
                "' data-selected='false' data-time='" + timeVal + "' data-id='" + id + "'>" + text + "</div>");
            }
            
            setTimeout(function(){
                $("#time_slots").show();
            },500); 
            
        }

        function checkDate(year,month,day,hour,min)
        {
            booking_detail = [];
            for(booking of bookings)
            {
                const bookingYY = booking.start.substring(0,4);
                const bookingMM = booking.start.substring(5,7);
                const bookingDD = booking.start.substring(8,10);
                const bookingHH = booking.start.substring(11,13); 
                const bookingMin = booking.start.substring(14,16); 
                console.log(checkSum(bookingYY,bookingMM,bookingDD), checkSum(year,(month),day));
               
                if(checkSum(bookingYY,bookingMM,bookingDD) == checkSum(year,(month),day) && hour==bookingHH && min==bookingMin)
                {
                    booking_detail.push(booking.user);
                    booking_detail.push(booking.id);
                }
            }
            return booking_detail;
        }
        
        function initCalendar()
        {
            $("#small_cal_month_viewer").text(monthNames[date.MM] + " - " + date.YY);
            $("#small_cal_month_picker").html(generateMonth(date.MM, date.YY));
        }

        function timeConverter(min)
        {
            const HH = parseInt(min / 60);
            const MM = min - (HH * 60);
            const result = String(HH).padStart(2, '0') + ":" + String(MM).padStart(2, '0');
            
            return result;
        }

        function checkSum(year,month,day)
        {
            return parseInt(String(year) + String(month).padStart(2, '0') + String(day).padStart(2, '0'));
        }

        function generateMonth(month, year)
        {
            const lastDateInMonth = new Date(year, month + 1, 0).getDate();
            const firstDayInMonth = new Date(year, month, 0).getDay();
            let dayNumber = 1;
            let divElement = "";
            let style = "";

            $(".smallDayPicker").css({"font-weight":"normal"});
            
            for(let i=1; i < 45; i++)
            {
                if(i > firstDayInMonth && dayNumber < lastDateInMonth + 1){

                    let className = "smallDayPicker";
                    
                    if(checkSum(year, month, dayNumber) < checkSum(today.YY,today.MM,today.DD))
                    {
                        className = "smallGreyedOut"
                    }else{
                        if(checkSum(year, month, dayNumber) == checkSum(selectedDate.YY,selectedDate.MM,selectedDate.DD))
                        {
                            style ='background:#99FF99';
                        }else{
                            style ="background:#FFFFFF";
                        }
                    }

                    divElement += "<div class='" + className + "' ";
                    divElement += "data-d='" +  dayNumber + "' ";
                    divElement += "data-m='" +  month + "' ";
                    divElement += "data-y='" +  year + "' ";
                    divElement += "style='" +  style + "' ";
                    divElement += ">" + dayNumber + "</div>";
                    dayNumber += 1;
                }else{
                    divElement += "<div class='smallDayPickerEmty'></div>";
                }
                if(i % 7 == 0){
                    divElement += "<div class='break'></div>";
                }
            }
            return divElement;
        }
        

        function prettyTimeFormat(dateTime){
            const split = dateTime.split(" ");
            const date = split[0].split("-");
            const time = split[1].split(":");
            
            const endtime = timeConverter((parseInt(time[0]) * 60) + interval);
            return date[2] + "/" + date[1] + "/" +  date[0] + " " + split[1] + " - " + endtime;  

        }

        function time_slot_info(id)
        {
            for(booking of bookings)
			{
			    if(id == booking.id)
				{
				    $("#time_slot_info_box_start").html(booking.start);
					$("#time_slot_info_box_end").html(booking.end);
					$("#time_slot_info_box_user").html(booking.user);
		            
                    if(booking.ownership==true)
					{
						$("#time_slot_info_btn").html("<div data-id='" + booking.id + "' id='time_slot_delete_cal_btn'>Remove booking</div>");
					}
					
                    $("#time_slot_info_box").show();
					$("#time_slot_wrapper").hide();
				}
			}
        }

        $(document).off("click", ".time_slot").on("click", ".time_slot", function(){
            const selected = $(this).data("selected");
            const time = $(this).data("time");
            const status = $(this).data("status");
            const id = $(this).data("id");
            if(selected==false)
            {
                if(status=="free"){
                    $(this).css({"background":selectedColor});
                    $(this).data("selected", true);
                    time_slots.push(time);
                }
            }else{          
                $(this).css({"background":freeColor});
                $(this).data("selected", false);
                time_slots = time_slots.filter(i => i !== time);
            }
            
            if(status=="taken")
            {
                time_slot_info(id);
            }
            createBookingList();
        });
        
        $("#small_cal_prev").on("click", function(){
            date.MM -= 1;
            if(date.MM < 0)
            {
                date.YY -= 1;
                date.MM = 11;
            }
            initCalendar();
        });
        
        $("#small_cal_next").on("click", function(){
            date.MM += 1;
            if(date.MM > 11)
            {
                date.YY += 1;
                date.MM = 0;
            }
            initCalendar();
        });

        $("#pick_date_btn").on("click", function(){
            $("#small_calendar_wrapper").show();
            $("#pick_date_btn").hide();
            $("#view_time_list").hide();
            
            $("#schedu_box").hide();            

            initCalendar();
        });

        $(document).on("click", ".smallDayPicker", function(){
            date.DD = $(this).data('d');
            date.MM = $(this).data('m');
            date.YY = $(this).data('y');

            selectedDate.DD = date.DD;
            selectedDate.MM = date.MM;
            selectedDate.YY = date.YY;
            

            $("#pick_date_btn").html(date.DD + "/" + (date.MM + 1) + "/" + date.YY);
           
            $("#small_calendar_wrapper").hide();
            $("#pick_date_btn").show();
            $("#schedu_box").show();
            $("#view_time_list").show();
            
            createTimeView();
        });

        $(document).on("click", "#back_time_picker", function(){
            $("#time_picker_cal").show();
            $("#schedu_box").show();
            $("#booking_list_view").hide();
        });

        $("#view_time_list").on("click", function(){

            if(state == 0 && screenType!="phone")
            {
                apiRequest("save_timeslots",time_slots);
            }

            if(screenType=="phone")
                {
                    $("#time_picker_cal").hide();
                    $("#schedu_box").hide();
                    $("#booking_list_view").show();
                    state = 1;
                }    
        });

        $(document).on("click", "#time_slot_delete_cal_btn", function(){
            const id = $(this).data("id");
            apiRequest("remove_booking",id);			
        });


        createTimeView();
    }