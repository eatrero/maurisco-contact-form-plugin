<?php

function maurisco_cf_get_leadtypes() {
//    error_log('get_leadtypes');
    $lead_types_t = 0; // get_transient( 'maurisco_lead_types_t' );
    $maurisco_api_id = get_option( 'maurisco_api_id' );
    $maurisco_api_key = get_option( 'maurisco_api_key' );

    /* If the transient does not exist do a lookup */
    if(!$lead_types_t){
//		echo 'type_arr ping server';

        $data = array(
            'apiId' => $maurisco_api_id,
            'apiKey' => $maurisco_api_key
            );
        if( defined(MARUISCO_CF_DEBUG) ){
//    		echo ' local ';
//            error_log('development debug lead_types');
            $url = 'https://192.168.1.157:8000/api/v1/lead_types';
        } else {
//    		echo ' production ';
            error_log('mauris.co lead_types');
            $url = 'https://mauris.co/api/v1/lead_types';
        }

        $response = wp_remote_get( $url, array( 'sslverify' => false, 'body' => $data ) );
//        error_log(serialize($response));

        if(is_wp_error($response)){
            return;
        }
        $response_code = $response['response']['code'];
        error_log($response_code);

        $body = $response['body'];
        $decode = json_decode($body);
        $type_arr = $decode->{'result'};

        set_transient( 'maurisco_lead_types_t', serialize( $type_arr ), 60 * MINUTE_IN_SECONDS );

        error_log('did api lookup of transient lead_type');
//        error_log(serialize($type_arr));

    } else {
        $type_arr = unserialize( $lead_types_t );

		echo 'got type_arr transient';

        error_log('used cached lead_type');
    }

    set_transient( 'maurisco_lead_types_t', serialize( $type_arr ), 5 );

    return $type_arr;
}