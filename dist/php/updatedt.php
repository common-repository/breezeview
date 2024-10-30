<?php

if(!defined(ABSPATH)){
    $pagePath = explode('/wp-content/', dirname(__FILE__));
    include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
}

global $wpdb;

$dtType = $_REQUEST['dtType'];

$t=time();

$wpdb->insert( 
	$wpdb->prefix . "bv_gr_clicks_impressions",
	array(
	    'time' => date("Y-m-d",$t),
		'dttype' => $dtType
	), 
	array( 
		'%s'
	) 
);


?>