<?php

function maurisco_cf_get_leadtypes() {
    $lead_types_t = 0; // get_transient( 'maurisco_lead_types_t' );
    $maurisco_api_id = get_option( 'maurisco_api_id' );
    $maurisco_api_key = get_option( 'maurisco_api_key' );

    /* If the transient does not exist do a lookup */
    if(!$lead_types_t){

        $data = array(
            'apiId' => $maurisco_api_id,
            'apiKey' => $maurisco_api_key
            );
        $url = 'https://192.168.1.157:8000/api/v1/lead_types';
        $response = wp_remote_get( $url, array( 'sslverify' => false, 'body' => $data ) );
        error_log(serialize($response));

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

    } else {
        $type_arr = unserialize( $lead_types_t );

        error_log('used cached lead_type');
    }

    set_transient( 'maurisco_lead_types_t', serialize( $type_arr ), 5 );

    return $type_arr;
}