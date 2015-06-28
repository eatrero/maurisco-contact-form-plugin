function maurisco_cf_validate(){
//	$('')

	return true;
}


(function ( $ ) {
	"use strict";

	$(function () {
		var userIp;
		$('#maurisco_cf_date').datepicker({ dateFormat: 'yy-mm-dd' });
		if(! $('#maurisco_id').val()) {
			$('#maurisco_cf').hide();
			$('#maurisco_cf_message').html('<div>Please login to WordPress and set your Maurisco API ID under Settings->Maurisco Contact Form Plugin before using the contact form on your site.</div>');
		}

		$.getJSON("http://jsonip.com?callback=?", function (data) {
			userIp = data.ip;
		});



		// Place your public-facing JavaScript here
		$('#maurisco_cf').submit('click',function(event){
			event.preventDefault();

			if( maurisco_cf_validate() ) {
				// post cf
				console.log('got validation');
				$('#maurisco_cf').hide();
				$('#maurisco_cf_message').html('<div>thank you for your submission</div>');

				// post to
//				var apiUrl = 'https://mauris.co/api/v1/lead';
				var apiUrl = 'https://localhost:8000/api/v1/lead';

				var apiId = $('#maurisco_id').val();
				if(!apiId) {
					$('#maurisco_cf').hide();
					$('#maurisco_cf_message').html('<div>Please login to WordPress and set your Maurisco API ID under settings before using the contact form on your site.</div>');
				}

				var first_name = $('#maurisco_cf_first_name').val();
				var last_name = $('#maurisco_cf_last_name').val();
				var email = $('#maurisco_cf_email').val();
				var datePicked = $('#maurisco_cf_date').datepicker( "getDate" );
				var location =  $('#maurisco_cf_location').val();
				var type =  $('#maurisco_cf_event_type').val();
				var maurisco_cf_comments1 = $('textarea#maurisco_cf_comments1').val();

				console.log(datePicked);
				console.log(type);


				var data = {
					action:  'maurisco_cf_plugin',
					nonce: maurisco_cf_form.nonce,
					client : [{ "first_name" : first_name,
						 	    "last_name" : last_name}],
					  "email" : email,
				  "event_date": datePicked,
				   "location1": location,
					   "type" : type,
				   "comment1" : maurisco_cf_comments1,
					"userIp"  : userIp
				   };

				console.log( data );
				console.log( maurisco_cf_form );

				$.ajax({
					type: "POST",
					url: maurisco_cf_form.ajax_url,
					data: data,
					dataType : 'json',
					error : function(data, status) {
						// error handler
						console.log('error');
						console.log(data);
						console.log(status);
					},
					success: function(data, status) {
						// success handler
						console.log('success');
						console.log(data);
						console.log(status);
					}
				});

			}
		});
	});

}(jQuery));