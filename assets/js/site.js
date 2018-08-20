$(document).ready(function() {
	
	//Field jQuery mask
	$('.date-departure').mask('00/00/0000');	

	//Filling dateArrival field
	$('input[name="dateDeparture"]').change(function() {		
		valDateDeparture = $(this).val();
		valRegion = $('input[name="region"]').val();
		getArrivalDate(valDateDeparture, valRegion);
	});
	$('input[name="region"]').change(function() {		
		valDateDeparture = $('input[name="dateDeparture"]').val();
		valRegion = $(this).val();
		getArrivalDate(valDateDeparture, valRegion);
	});

	//Opening modal window (calendar)
	$(".span_day").click(function() {
		openModal($(this));	
	});

	//Submitting form with ajax
	$('form.js-form').on('submit', function() {		
		var rideForm = $(this),
			url = rideForm.attr('action'),
			type = rideForm.attr('method'),
			data = {};

		rideForm.find('[name]').each(function(index,value) {
			var formItem = $(this),
				name = formItem.attr('name'),
				value = formItem.val();

			data[name] = value;
		});

		$.ajax({
			url: url,
			type: type,
			data: data,
			success: function (response) {				
				getResponse(response);	
			}			
		});		

		return false;	
	});

});

//Function for form. Validating and saving
function getResponse(response) {
	response = JSON.parse(response);
	if (response.success == false) {
		//Filling message-box
		$('#js_message_box').removeClass('alert-success');
		$('#js_message_box').addClass('alert-danger');
		$('#js_message_box').find('span').html(response.message);		
		$('#js_message_box').slideDown();
		setTimeout(function(){ $('#js_message_box').slideUp(); }, 2600);
	} else if (response.success == true) {
		//Clear form after success submit
		$('.js-form')[0].reset();
		//Filling message-box
		$('#js_message_box').removeClass('alert-danger');
		$('#js_message_box').addClass('alert-success');
		$('#js_message_box').find('span').html(response.message);
		$('#js_message_box').slideDown();
		setTimeout(function(){ $('#js_message_box').slideUp(); }, 2600);	
	}	
}

//Function for closing message-box
function closeBox() {	
	$('#js_message_box').slideUp();
}

//Function for getting arrivalDate in form
function getArrivalDate(valDateDeparture, valRegion) {
	var rideForm = $('.js-form'),		
		url = rideForm.attr('action'),
		type = rideForm.attr('method'),		
		data = {actionFunc: 'actionGetArrivalTime', dateDeparture: valDateDeparture, region: valRegion}

	$.ajax({
		url: url,
		type: type,
		data: data,
		success: function (response) {	
			response = JSON.parse(response);
			if (response.success == true) {			
				$('input[name="dateArrival"]').val(response.message);
			} else {
				$('input[name="dateArrival"]').val("");
			}
		}			
	});	
}

//Function for filling modal window (calendar)
function openModal(element) {	
	var classList = element.attr('class').split(/\s+/);
	
	//Deleting unnecessary classes
	var index = classList.indexOf('blue');
	var index1 = classList.indexOf('span_day');
	if (index > -1) {
		classList.splice(index, 1);
	}
	if (index1 > -1) {
		classList.splice(index1, 1);
	}

	//Cleaning array
	classListClean = cleanArray(classList);

	//Checking emptiness
	if(classListClean[0] != null) { 
		var data = {actionFunc: 'actionGetRidesModal', classList: classListClean}

		$.ajax({
			url: "controllers/RideController.php",
			type: "post",
			data: data,
			success: function (response) {	
				response = JSON.parse(response);
				
				if (response.success == true) {
					var massiv = response.message;
					var text = "";	

					massiv.forEach(function(item, i, arr) {
						var text1 = "<div class='ride-container'>Курьер: " + item.courier + "<br />";
						var text2 = "Регион: " + item.region + "<br />";
						var text3 = "Дата отъезда: " + item.dateDeparture + "<br />";
						var text4 = "Дата прибытия в регион: " + item.dateArrival + "<br />";
						var text5 = "Время в пути, суток: " + item.time + "</div>";

						text += text1 + text2 + text3 + text4 + text5;
					});
				
				} else {
					var text = "Поездки не найдены";
				}
				
				$('.js-modal-p').html(text);
				$('#myModal').modal('show');
			}			
		});

	} else {
	   $('.js-modal-p').html("Поездки не найдены");
	   $('#myModal').modal('show');
	}	
}

//Function for cleaning arrays
function cleanArray(actual) {
	var newArray = new Array();
		for (var i = 0; i < actual.length; i++) {
			if (actual[i]) {
				newArray.push(actual[i]);
			}
		}
	return newArray;
}