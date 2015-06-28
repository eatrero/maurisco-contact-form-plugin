function maurisco_cf_validate(){
//	$('')

	return true;
}


(function ( $ ) {
	"use strict";

	function BASE64() {
		var keyStr = 'ABCDEFGHIJKLMNOP' +
			'QRSTUVWXYZabcdef' +
			'ghijklmnopqrstuv' +
			'wxyz0123456789+/' +
			'=';
		return {
			encode: function (input) {
				var output = "";
				var chr1, chr2, chr3 = "";
				var enc1, enc2, enc3, enc4 = "";
				var i = 0;

				do {
					chr1 = input.charCodeAt(i++);
					chr2 = input.charCodeAt(i++);
					chr3 = input.charCodeAt(i++);

					enc1 = chr1 >> 2;
					enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
					enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
					enc4 = chr3 & 63;

					if (isNaN(chr2)) {
						enc3 = enc4 = 64;
					} else if (isNaN(chr3)) {
						enc4 = 64;
					}

					output = output +
					keyStr.charAt(enc1) +
					keyStr.charAt(enc2) +
					keyStr.charAt(enc3) +
					keyStr.charAt(enc4);
					chr1 = chr2 = chr3 = "";
					enc1 = enc2 = enc3 = enc4 = "";
				} while (i < input.length);

				return output;
			},

			decode: function (input) {
				var output = "";
				var chr1, chr2, chr3 = "";
				var enc1, enc2, enc3, enc4 = "";
				var i = 0;

				// remove all characters that are not A-Z, a-z, 0-9, +, /, or =
				var base64test = /[^A-Za-z0-9\+\/\=]/g;
				if (base64test.exec(input)) {
					alert("There were invalid base64 characters in the input text.\n" +
					"Valid base64 characters are A-Z, a-z, 0-9, '+', '/',and '='\n" +
					"Expect errors in decoding.");
				}
				input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

				do {
					enc1 = keyStr.indexOf(input.charAt(i++));
					enc2 = keyStr.indexOf(input.charAt(i++));
					enc3 = keyStr.indexOf(input.charAt(i++));
					enc4 = keyStr.indexOf(input.charAt(i++));

					chr1 = (enc1 << 2) | (enc2 >> 4);
					chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
					chr3 = ((enc3 & 3) << 6) | enc4;

					output = output + String.fromCharCode(chr1);

					if (enc3 != 64) {
						output = output + String.fromCharCode(chr2);
					}
					if (enc4 != 64) {
						output = output + String.fromCharCode(chr3);
					}

					chr1 = chr2 = chr3 = "";
					enc1 = enc2 = enc3 = enc4 = "";

				} while (i < input.length);

				return output;
			}
		};
	}



	$(function () {
		$('#maurisco_cf_date').datepicker();

		// Place your public-facing JavaScript here
		$('#maurisco_cf').submit('click',function(event){
			event.preventDefault();

			if( maurisco_cf_validate() ) {
				// post cf
				console.log('got validation');
				$('#maurisco_cf').hide();
				$('#maurisco_cf_message').html('<div>thank you for your submission</div>');

				// post to
				var apiUrl = 'https://mauris.co/api/v1/lead';
				var base64 = BASE64();

				var apiID = 'L13K0IGVvxX9Ot3Q';
				var apiKey = 'ioEUC7iggCJ6mxBt1vKzdaTebcmI44Bi';
				var encoded = base64.encode( apiID + ':' + apiKey );

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


				/*				$.ajax({
                                    type: "POST",
                                    url: apiUrl,
                                    data: { "client" : [{ "first_name" : "Ed",
                                                         "last_name" : "Atrero"}],
                                            "email" : "eatrero@gmail.com",
                                            "event_date": {"year":2015, "month":9, "date":20},
                                            "location1": "La Jolla",
                                            "type" : "Wedding"
                                    },
                                    dataType : 'json',
                                    beforeSend : function(xhr) {
                                        // set header
                                        xhr.setRequestHeader("Authorization", "Basic " + encoded);
                                    },
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
                                });*/

			}
		});
	});

}(jQuery));;(function ( $ ) {
	"use strict";

	$(function () {

		// Place your administration-specific JavaScript here

	});

}(jQuery));