		
		function calendar(bookings){ 
			console.log("asdasd");
			const now = new Date();
			let DD = now.getDate();
			let YY = now.getFullYear();
			let MM = now.getMonth();

			let daysList = [];

			const todayDD = DD;
			const todayYY = YY;
			const todayMM = MM;
			const todayYYMMDD = checkSum(todayYY, todayMM, todayDD);

			const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

			let selectedColor = "#9988FF";

			class Selector {
				constructor() {
					this.DD = 0;
					this.MM = 0;
					this.YY = 0;
					this.checkSum = 0;
				}
			}
			
			let selectStartDate = new Selector;
			let selectEndDate = new Selector;
			let selected = false;
			
			$("#calendar_info_box").hide();

			function drawSelection()
			{
				let daysInMonths = [];
				let years = selectEndDate.YY - selectStartDate.YY;
				let totalMonths = selectEndDate.MM - selectStartDate.MM + (years * 12);
				let thisYear = selectStartDate.YY;
				let thisMonth = selectStartDate.MM - 1;
				let thisDay = selectStartDate.DD;
				let booking_arr = [];
				let prevYY = thisYear;
				let prevMM = thisMonth;
				let prevDD = 0;
				
				$(".dayPicker").each(function() {
					$(this).css({background:"#FFFFFF"});
				})

				for(var booking of bookings)
				{
					const bookingDateStart = correctDateArray(booking.start);
					booking_arr.push(checkSum(bookingDateStart[0], bookingDateStart[1], bookingDateStart[2]));
				}
				booking_arr.sort();
				
				for(let month=0; month <= totalMonths; month++)
				{
					thisMonth += 1; 
					
					if(thisMonth==12)
					{
						thisMonth = 0;
						thisYear += 1;
					}
					const test = thisMonth;
					
					const lastDateInMonth = new Date(thisYear, thisMonth + 1, 0).getDate();
					let startDay = 1;
					if(thisYear==selectStartDate.YY && thisMonth == selectStartDate.MM)
					{
						startDay = thisDay;
					}

					for (let day=startDay; day <= lastDateInMonth; day++)
					{
					
						const thisYYMMDD = checkSum(thisYear, thisMonth, day);
						
						if(thisMonth==selectStartDate.MM && day >= selectStartDate.DD && thisYear == selectStartDate.YY)
						{
							addDay = true;
						}

						if(thisMonth>selectStartDate.MM && thisYear >= selectStartDate.YY)
						{
							addDay = true;
						}

						if(thisMonth==selectEndDate.MM && day > selectEndDate.DD && thisYear == selectEndDate.YY)
						{
							addDay = false;
						}

						for(let booked of booking_arr)
						{
							if(thisYYMMDD == booked)
							{
								addDay = false;
								day = lastDateInMonth + 1;
								month = totalMonths + 1;
							}
						}

						if(addDay==true){
							
							prevDD = day;
							prevMM = thisMonth;
							prevYY = thisYear;
							daysInMonths.push(thisYYMMDD);
						}	
					}
				}
				selectEndDate.DD = prevDD;
				selectEndDate.MM = prevMM;
				selectEndDate.YY = prevYY;

				for(let date of daysInMonths)
				{
					let year = String(date).substring(0,4);
					let month = String(date).substring(4,6);
					let day = String(date).substring(6,8);
					
					$(".dayPicker").each(function() {	
						const divDD = $(this).data("d");
						const divMM = $(this).data("m");
						const divYY = $(this).data("y");
						if(divDD == day && divMM == month && divYY == year)
						{
							$(this).css({background:selectedColor});
						}
					});
				}

				$("#startDate").val(selectStartDate.YY + "/" + selectStartDate.MM + "/" + selectStartDate.DD);
				$("#endDate").val(selectEndDate.YY + "/" + selectEndDate.MM + "/" + selectEndDate.DD);

				}

			function checkSum(year,month,day)
			{
				return parseInt(String(year) + String(month).padStart(2, '0') + String(day).padStart(2, '0'));
			}

			function correctDateArray(date)
			{
				const timeDate = date.split(" ");
				const newDate = timeDate[0].split("-");
				
				let new_DD = newDate[2];
				let new_MM = parseInt(newDate[1]) - 1;
				let new_YY = parseInt(newDate[0]);
				
				if(new_MM == -1)
				{
					new_MM = 11;
					new_YY -= 1; 
				}
				return [String(new_YY), String(new_MM).padStart(2, '0'), String(new_DD).padStart(2, '0')];
			}

			function generateMonth(month, year)
			{
				const lastDateInMonth = new Date(year, month + 1, 0).getDate();
				const firstDayInMonth = new Date(year, month, 0).getDay();
				let dayNumber = 1;
				let divElement = "";

				for(let i=1; i < 45; i++)
				{
					if(i > firstDayInMonth && dayNumber < lastDateInMonth + 1){

						
						let todayClass = "";
						let className = "dayPicker";
						let elementId = "";
						let item_number = 0;
						
						const dayCheckSum = checkSum(year, month, dayNumber);

						for(var booking of bookings)
						{
							const bookingDateStart = correctDateArray(booking.start);
							const bookingStart = checkSum(bookingDateStart[0], bookingDateStart[1], bookingDateStart[2]);
							
							const bookingDateEnd = correctDateArray(booking.end);
							const bookingEnd = checkSum(bookingDateEnd[0], bookingDateEnd[1], bookingDateEnd[2]);
							
							if(dayCheckSum >= bookingStart && dayCheckSum <= bookingEnd)
							{
								className = "booked";
								elementId = item_number; 
							}
							item_number += 1;	
						}

						if(dayCheckSum < todayYYMMDD)
						{
							className = "greyedOut"
						}

						if(todayDD==dayNumber && todayMM==month && todayYY==year)
						{
							todayClass = "today";
						}

						divElement += "<div class='" + className + "' ";
						divElement += "data-d='" +  dayNumber + "' ";
						divElement += "data-m='" +  month + "' ";
						divElement += "data-y='" +  year + "' ";
						divElement += "data-id='" + elementId + "' ";
						divElement += ">" + dayNumber + "</div>";
						dayNumber += 1;
					}else{
						divElement += "<div class='dayPickerEmty'></div>";
					}
					if(i % 7 == 0){
						divElement += "<div class='.break'></div>";
					}
				}
				return divElement;
			}

			function drawCalendar(year,month,day)
			{
				let secondMonth = month + 1;
				let secondYear = year;
				
				if(secondMonth > 11)
				{
					secondMonth = 0;
					secondYear += 1; 
				}
				
				$("#first_date").html(monthNames[month] + "/" + year);
				$("#second_date").html(monthNames[secondMonth] + "/" + secondYear);	
				
				$("#first_month_picker").html(generateMonth(month, year));
				$("#second_month_picker").html(generateMonth(secondMonth, secondYear));	
				
				drawSelection();
				
			}

			function resetSelection()
			{
				$(".dayPicker").each(function() {
							$(this).css({"background":"#FFFFFF"})
							selectStartDate.DD = 0;
							selectStartDate.MM = 0;
							selectStartDate.YY = 0;
							selectEndDate.DD = 0;
							selectEndDate.MM = 0;
							selectEndDate.YY = 0;
							
				});
				selected=false;
			}

			$(document).on("click", ".greyedOut", function(){
				resetSelection();
			});

			$(document).on("click", ".dayPicker", function(){
				let day = $(this).data('d');
				let month = $(this).data('m');
				let year = $(this).data('y');
				
				
					if(selected==false)
					{
						resetSelection();
						selected=true;
						selectStartDate.DD = day;
						selectStartDate.MM = month;
						selectStartDate.YY = year;
						selectStartDate.checkSum = checkSum(selectStartDate.YY, selectStartDate.MM, selectStartDate.DD);
						
						$(this).css({background:selectedColor});
					}else{
						selectEndDate.DD = day;
						selectEndDate.MM = month;
						selectEndDate.YY = year;
						selectEndDate.checkSum = checkSum(selectEndDate.YY, selectEndDate.MM, selectEndDate.DD);
						selected=false;
						
						if(selectStartDate.checkSum > selectEndDate.checkSum)
						{
							resetSelection();
						}else{
							drawSelection();
						}
					}
			});

			$(document).on("mouseover", ".dayPicker", function(){	
				
				if(selected==true)
				{
					let day = $(this).data('d');
					let month = $(this).data('m');
					let year = $(this).data('y');

					selectEndDate.DD = day;
					selectEndDate.MM = month;
					selectEndDate.YY = year;

					selectEndDate.checkSum = checkSum(selectEndDate.YY, selectEndDate.MM, selectEndDate.DD);
					
					drawSelection();
				}
			});

			$(document).on("click", ".booked", function(){
				
				const id = $(this).data("id");
				$("#calendar_info_box_start").html(bookings[id].start);
				$("#calendar_info_box_end").html(bookings[id].end);
				$("#calendar_info_box_user").html(bookings[id].user);
				
				$("#calendar_info_box").show();
				$("#calendar_wrapper").hide();
			});

			$(document).on("click", "#calendar_info_close_btn", function(){
				$("#calendar_info_box").hide();
				$("#calendar_wrapper").show();			
			});
			
			$("#prev").on("click", function(){
				MM -= 1;
				if(MM < 0)
				{
					YY -= 1;
					MM = 11;
				}
				drawCalendar(YY,MM,DD);
			});
			
			$("#next").on("click", function(){
				MM += 1;
				if(MM > 11)
				{
					YY += 1;
					MM = 0;
				}
				drawCalendar(YY,MM,DD);
			});
			
			
			drawCalendar(YY,MM,DD);
			
		}
		
		
