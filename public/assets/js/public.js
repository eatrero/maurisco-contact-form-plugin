function maurisco_cf_validate(){
//	$('')

	return true;
}


(function ( $ ) {
	"use strict";

	$(function () {
		var userIp;
		$('#maurisco_cf_event_date').datepicker({ dateFormat: 'yy-mm-dd' });
		if(! $('#maurisco_id').val()) {
			$('#maurisco_cf').hide();
			$('#maurisco_cf_message').html('<div>Please login to WordPress and set your Maurisco API ID under Settings->Maurisco Contact Form Plugin before using the contact form on your site.</div>');
		}

		$.getJSON("http://jsonip.com?callback=?", function (data) {
			userIp = data.ip;
		});

		console.log('maurisco form initialized')

		// Place your public-facing JavaScript here
		$('#maurisco_cf_submit').click(function(event){
			event.preventDefault();
			console.log('maurisco form submit');

			if( maurisco_cf_validate() ) {
				// post cf
				console.log('got validation');
				$('#maurisco_cf').hide();
				$('#maurisco_cf_message').html('<div>thank you for your submission</div>');

				var apiId = $('#maurisco_id').val();
				if(!apiId) {
					$('#maurisco_cf').hide();
					$('#maurisco_cf_message').html('<div>Please login to WordPress and set your Maurisco API ID under settings before using the contact form on your site.</div>');
				}

				var name_0 = $('#maurisco_cf_name_0').val();
				var email_0 = $('#maurisco_cf_email_0').val();
				var phone = $('#maurisco_cf_phone').val();
				var name_1 = $('#maurisco_cf_name_1').val();
				var name_2 = $('#maurisco_cf_name_2').val();

				var event_date = $('#maurisco_cf_event_date').datepicker( "getDate" );
				var event_location_1 =  $('#maurisco_cf_event_location_1').val();
				var event_type =  $('#maurisco_cf_event_type').val();

				var question_1 = $('#maurisco_cf_question_1').val();
				var question_2 = $('#maurisco_cf_question_2').val();
				var comment_1 = $('#maurisco_cf_comment_1').val();

				var maurisco_cf_form_ajax_url = $('#maurisco_cf_url').val();
				var maurisco_cf_nonce = $('#maurisco_cf_nonce').val();

				var data = {
					 "action" : 'maurisco_cf_plugin',
					  "nonce" : maurisco_cf_nonce,
					 "name_0" : name_0,
 					 "name_1" : name_1,
					 "name_2" : name_2,
				    "email_0" : email_0,
					  "phone" : phone,
				 "event_date" : event_date,
		   "event_location_1" : event_location_1,
				 "event_type" : event_type,
				 "question_1" : question_1,
				 "question_2" : question_2,
				  "comment_1" : comment_1,
					"userIp"  : userIp
				   };

				console.log( data );

				$.ajax({
					type: "POST",
					url: maurisco_cf_form_ajax_url,
					data: data,
					dataType : 'json',
					error : function(data, status) {
						// error handler
						console.log(data);
					},
					success: function(data, status) {
						// success handler
						console.log(data);
					}
				});

			}
		});
	});

}(jQuery));