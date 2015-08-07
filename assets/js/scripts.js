function maurisco_cf_validate(){
	return true;
}


(function ( $ ) {
	"use strict";

	$(function () {
		$('#maurisco_cf_date').datepicker();

		// Place your public-facing JavaScript here
		$('#maurisco_cf_submit').submit('click',function(event){
			event.preventDefault();

			if( maurisco_cf_validate() ) {
				// post cf
				console.log('got validation');
				$('#maurisco_cf').hide();
				$('#maurisco_cf_message').html('<div>thank you for your submission</div>');

				var first_name = $('#maurisco_cf_first_name').val();
				var last_name = $('#maurisco_cf_last_name').val();
				var email = $('#maurisco_cf_email').val();
				var date =  $('#maurisco_cf_date').val().split('/');
				date = new Date(date[2],date[0]-1,date[1]);
				var datePicked = $('#maurisco_cf_date').datepicker( "getDate" );
				var location =  $('#maurisco_cf_location').val();
				var type =  $('#maurisco_cf_wedding').val();

				console.log(date === datePicked);
				console.log(date);
				console.log(datePicked);


				var data = { "client" : [{ "first_name" : first_name,
										    "last_name" : last_name}],
							  "email" : email,
						  "event_date": datePicked,
						   "location1": location,
						       "type" : type
						};

				console.log( data );
			}
		});
	});

}(jQuery));;(function ( $ ) {
	"use strict";

	$(function () {

		// Place your administration-specific JavaScript here

	});

}(jQuery));