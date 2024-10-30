<?php

if(!defined(ABSPATH)){
    $pagePath = explode('/wp-content/', dirname(__FILE__));
    include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
}

global $wpdb;

$totalImpressions = 0;
$totalClicks = 0;

$dtFrom = $_REQUEST['dtFrom'];
$dtTo = $_REQUEST['dtTo'];

$getFrom = strtotime($dtFrom);
$getTo = strtotime($dtTo);

$impressionData = $wpdb->get_results( 
	"
	SELECT time 
	FROM " . $wpdb->prefix . "bv_gr_clicks_impressions
	WHERE dttype = 'impression'
	"
);

foreach ( $impressionData as $impressionDataDetails ) 
{

	$getTm = $impressionDataDetails->time;
	$getThisDateSeconds = strtotime($getTm);

    if($getThisDateSeconds >= $getFrom && $getThisDateSeconds <= $getTo){
    $totalImpressions++;   
    }

}

$clicksData = $wpdb->get_results( 
	"
	SELECT time 
	FROM " . $wpdb->prefix . "bv_gr_clicks_impressions
	WHERE dttype = 'click'
	"
);

foreach ($clicksData as $clicksDataDetails) 
{

	$getTm = $clicksDataDetails->time;
	$getThisDateSeconds = strtotime($getTm);

    if($getThisDateSeconds >= $getFrom && $getThisDateSeconds <= $getTo){
    $totalClicks++;   
    }

}

echo $totalClicks."yyyyyyyyyyyyyyyy".$totalImpressions; 

?>