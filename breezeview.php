<?php
/*
* Plugin Name: BreezeView
* Description: Helps you display your business Google Reviews
* Version: 3.0.3
* Author: BreezeMaxWeb
*/

$thisPage = 'Yes';

add_action('wp_head','bv_gr_button');
add_action('wp_enqueue_scripts', 'bv_gr_add_frontend_resources');
add_action('admin_enqueue_scripts', 'bv_gr_add_backend_resources');

add_action( 'admin_init', 'bv_gr_settings_init' );

add_filter('admin_footer_text', 'bv_gr_remove_footer_admin');

add_action('admin_print_scripts', 'bv_gr_inline_scripts_admin');

add_action('admin_footer', 'bv_gr_footer_scripts_admin');

/* Set up DB Table */
global $jal_db_version;
$jal_db_version = '1.0';

function bv_gr_db_install() {
	global $wpdb;
	global $jal_db_version;

	$table_name = $wpdb->prefix . 'bv_gr_clicks_impressions';
	
	$charset_collate = $wpdb->get_charset_collate();

    $a1 = 'CREATE';
    $a2 = 'TABLE';
    $a3 = 'IF NOT EXISTS'; 

	$sql = "$a1 $a2 $a3 $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time date DEFAULT '0000-00-00' NOT NULL,
		dttype varchar(255) NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'jal_db_version', $jal_db_version );
}

register_activation_hook( __FILE__, 'bv_gr_db_install' );
/* Set up DB Table */

function bv_gr_inline_scripts_admin(){

if($thisPage == "No"){
return;
}

$inlineAdminScript = '<script>

    var clicksG = 0;
    var impressionsG = 0;

function showClicksImpressionsDt(y){

var from = jQuery("#bv_gr-datepicker1").val();
var to = jQuery("#bv_gr-datepicker2").val();

if(from != "" && to != ""){

      jQuery.ajax({
            type: "POST",
            url: "'.plugins_url('dist/php/getdt.php', __FILE__ ).'",
            data: "dtFrom=" + from + "&dtTo=" + to,
            success: function(data)
            {
            
            var allD = data.split("yyyyyyyyyyyyyyyy");
            
            document.getElementById("total-clicks-section").innerHTML = allD[0];
            document.getElementById("total-impressions-section").innerHTML = allD[1];
            
            var CRO = 0;
            
            if(parseInt(allD[1]) != 0){
            CRO = ((parseInt(allD[0])/parseInt(allD[1]))*100).toFixed(2)
            }
            
            document.getElementById("CRO").innerHTML = CRO.toString();
            
            clicksG = parseInt(allD[0]);
            impressionsG = parseInt(allD[1]);
            
            google.charts.load(\'current\', {\'packages\':[\'corechart\']});
            google.charts.setOnLoadCallback(drawChart2);

            }
        });
        
}

}

function drawChart2(){

        var data = google.visualization.arrayToDataTable([
          [\'Data Type\', \'Click/Impression\'],
          [\'Clicks\',     clicksG],
          [\'Impressions\', impressionsG]
        ]);

        var options = {
          title: \'Clicks vs Impressions\',
          \'is3D\':true,
              titleTextStyle: {
        fontName: \'Arial\', 
        fontSize: 18
    }
        };

        var chart = new google.visualization.PieChart(document.getElementById(\'piechart\'));

        chart.draw(data, options);
}

</script>';

echo $inlineAdminScript;

}

function bv_gr_footer_scripts_admin(){

global $wpdb;

$clicksTotalGraph = 0;
$impressionsTotalGraph = 0;

$impressionDataGraph = $wpdb->get_results( 
	"
	SELECT time 
	FROM " . $wpdb->prefix . "bv_gr_clicks_impressions
	WHERE dttype = 'impression'
	"
);

foreach ( $impressionDataGraph as $impressionDataDetailsGraph ) 
{
    $impressionsTotalGraph++;
}

$clicksDataGraph = $wpdb->get_results( 
	"
	SELECT time 
	FROM " . $wpdb->prefix . "bv_gr_clicks_impressions
	WHERE dttype = 'click'
	"
);

foreach ($clicksDataGraph as $clicksDataDetailsGraph) 
{
    $clicksTotalGraph++;   
}

$footerAdminScript = '<script>

google.charts.load(\'current\', {\'packages\':[\'corechart\']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {

        var data = google.visualization.arrayToDataTable([
          [\'Data Type\', \'Click/Impression\'],
          [\'Clicks\',     '.$clicksTotalGraph.'],
          [\'Impressions\', '.$impressionsTotalGraph.']
        ]);

        var options = {
          title: \'Clicks vs Impressions\',
          \'is3D\':true,
              titleTextStyle: {
        fontName: \'Arial\', 
        fontSize: 18
        }
        
        };
        
        var chart = new google.visualization.PieChart(document.getElementById(\'piechart\'));

        chart.draw(data, options);
}</script>';

echo $footerAdminScript;

}

function bv_gr_add_backend_resources($hook){

if($hook != 'toplevel_page_google_reviews_display') {
$thisPage = 'No';
return;
}

wp_register_style('date-range-style', '//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css');
wp_register_style('admin-css', plugins_url('dist/css/admin-style.css', __FILE__ ));
wp_register_script('date-range-picker', '//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js','','',true);
wp_register_script('momentjs-jquery-min', '//cdn.jsdelivr.net/momentjs/latest/moment.min.js','','',true);
wp_register_script('charts-loader', '//www.gstatic.com/charts/loader.js');

wp_enqueue_style('date-range-style');
wp_enqueue_style('admin-css');
wp_enqueue_script('date-range-picker');
wp_enqueue_script('momentjs-jquery-min');
wp_enqueue_script('charts-loader');

}

function bv_gr_add_frontend_resources(){

// get API Key
$options_ak = get_option('bv_gr_api_key');
$apiK = esc_html($options_ak['bv_gr_field_api_key']);

// Place ID
$options_pi = get_option('bv_gr_place_id');
$placeid = esc_html($options_pi['bv_gr_field_place_id']);

/******* Register Resources ********/
wp_register_style('gr-style', plugins_url('dist/css/style.css', __FILE__ ),'','','screen');
wp_register_style('gr-style2', plugins_url('dist/css/font-awesome.min.css', __FILE__ ),'','','screen');
wp_register_style('gr-style3', plugins_url('dist/css/AdminLTE.min.css', __FILE__ ),'','','screen');

wp_register_script('gr-script', plugins_url('dist/js/demo.js', __FILE__ ),'','',true);
wp_register_script('gr-script2', plugins_url('dist/js/adminlte.min.js', __FILE__ ),'','',true);
wp_register_script('gr-script3', plugins_url('dist/js/script.js', __FILE__ ),'','',true);
wp_register_script('gr-script4', '//maps.googleapis.com/maps/api/js?libraries=places&key='.$apiK,'','',false);
wp_register_script('gr-script5', plugins_url('dist/js/bootstrap.js', __FILE__ ),'','',true);
/******* Register Resources ********/


/******* Enqueue Resources ********/

wp_enqueue_style('gr-style');
wp_enqueue_style('gr-style2');
wp_enqueue_style('gr-style3');

wp_enqueue_script('gr-script');
wp_enqueue_script('gr-script2');
wp_enqueue_script('gr-script3');
wp_enqueue_script('gr-script4');
wp_enqueue_script('gr-script5');

/******* Enqueue Resources ********/

/******* Custom Resources ********/

$customJS = 'jQuery(document).ready(function() {
   jQuery("#google-reviews").googlePlaces({
        placeId: \''.$placeid.'\'
      , render: [\'reviews\']
      , min_rating: 4
      , max_rows:9
   });

   jQuery(".close-icon-img").attr("src","'.plugins_url('dist/images/close-icon.png', __FILE__ ).'");

});

function updateClicksImpressions(xy){

      jQuery.ajax({
            type: "POST",
            url: "'.plugins_url('dist/php/updatedt.php', __FILE__ ).'",
            data: "dtType=" + xy,
            success: function(data)
            {

            }
        });

}

      jQuery.ajax({
            type: "POST",
            url: "'.plugins_url('dist/php/updatedt.php', __FILE__ ).'",
            data: "dtType=" + "impression",
            success: function(data)
            {
            

            }
        });
        
';

wp_add_inline_script('gr-script', $customJS);

/******* Custom Resources ********/

}

function bv_gr_button(){

echo '<a href="#" data-toggle="control-sidebar" class="cd-btn js-cd-panel-trigger" id="bv_gr_google_button"><img src="'.plugins_url('dist/images/Google-Button-Powered-by-Google.png', __FILE__ ).'" width="100" onclick=\'updateClicksImpressions("click")\' /></a>';

echo '<aside class="control-sidebar control-sidebar-light" id="bv_gr_sidebar">
    <div class="tab-content">
      <!-- Home tab content -->
      <div class="tab-pane" id="control-sidebar-home-tab">
       
      </div>
      </div>
  </aside>';

}

function bv_gr_remove_footer_admin(){

if($thisPage == "No"){
return;
}

    echo '';
}

function bv_gr_settings_init() {

// register a new section in the "google_reviews_display" page
 add_settings_section(
 'bv_gr_section_settings',
 __( 'Google Reviews:', 'google_reviews_display' ),
 'bv_gr_section_settings_greviews',
 'google_reviews_display'
 );
 
/***************    Register Fields    *******************/

// register Place ID
register_setting('google_reviews_display', 'bv_gr_place_id');


 // register API Key
register_setting('google_reviews_display', 'bv_gr_api_key');
 // register API Key
 
/***************    Register Fields    *******************/

/***************    Set Fields    *******************/

 // Place ID - Set Field
 add_settings_field(
 'bv_gr_place_id',
 __( 'Place ID', 'google_reviews_display' ),
 'bv_gr_place_id_greviews',
 'google_reviews_display',
 'bv_gr_section_settings',
 array('label_for' => 'bv_gr_field_place_id', 'class' => 'bv_gr_row', 'bv_gr_custom_data' => 'custom')
 );
 // Place ID - Set Field

 // API Key - Set Field
 add_settings_field(
 'bv_gr_api_key',
 __( 'API Key', 'google_reviews_display' ),
 'bv_gr_api_key_greviews',
 'google_reviews_display',
 'bv_gr_section_settings',
 array('label_for' => 'bv_gr_field_api_key', 'class' => 'bv_gr_row', 'bv_gr_custom_data' => 'custom')
 );
 // API Key - Set Field
 
/***************    Set Fields    *******************/ 
 
}

/***************    Admin Fields    *******************/
// Place ID - Admin Field
function bv_gr_place_id_greviews( $args ) {
?>
 <input id="<?php echo esc_attr( $args['label_for'] ); ?>"
 data-custom="<?php echo esc_attr( $args['bv_gr_custom_data'] ); ?>"
 name="bv_gr_place_id[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php $options_r = get_option('bv_gr_place_id'); $placeid = esc_html($options_r['bv_gr_field_place_id']); if($placeid==""){ $placeid = ""; } echo $placeid; ?>" />

<?php
}
// Place ID - Admin Field

// API Key - Admin Field
function bv_gr_api_key_greviews( $args ) {
?>
 <input id="<?php echo esc_attr( $args['label_for'] ); ?>"
 data-custom="<?php echo esc_attr( $args['bv_gr_custom_data'] ); ?>"
 name="bv_gr_api_key[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php $options_r = get_option('bv_gr_api_key'); $apikey = esc_html($options_r['bv_gr_field_api_key']); if($apikey==""){ $apikey = ""; } echo $apikey; ?>" />

<?php
}
// API Key - Admin Field

/***************    Admin Fields    *******************/

function bv_gr_section_settings_greviews($args) {
?>

 <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Set Fields:', 'google_reviews_display' ); ?></p>

<?
}

/***************    Admin Menu    *******************/
function bv_gr_options_page() {
 // add top level menu page
 add_menu_page(
 'BreezeView Interface',
 'BreezeView Interface',
 'manage_options',
 'google_reviews_display',
 'bv_gr_options_page_html'
 );
}

add_action('admin_menu', 'bv_gr_options_page');
/***************    Admin Menu    *******************/

/**
 * top level menu:
 * callback functions
 */
function bv_gr_options_page_html() {
 // check user capabilities
 if ( ! current_user_can( 'manage_options' ) ) {
 return;
 }
 
 // add error/update messages
 
 // check if the user have submitted the settings
 // wordpress will add the "settings-updated" $_GET parameter to the url
 if ( isset( $_GET['settings-updated'] ) ) {
 // add settings saved message with the class of "updated"
 add_settings_error( 'bv_gr_messages', 'bv_gr_message', __( 'Settings Saved', 'google_reviews_display' ), 'updated' );
 }
 
 // show error/update messages
 settings_errors( 'bv_gr_messages' );
 ?>
 <div class="wrap">
 <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
 <form action="options.php" method="post">
 <?php
 // output security fields for the registered setting "conv"
 settings_fields( 'google_reviews_display' );
 // output setting sections and their fields
 // (sections are registered for "conv", each field is registered to a specific section)
 do_settings_sections('google_reviews_display');
 // output save settings button
 submit_button( 'Save Settings' );
 ?>
 </form>

<?php

global $wpdb;

$totalCRO = 0;
$clicksTotal = 0;
$impressionsTotal = 0;

$todaysDate = date('Y-m-d');

$impressionData = $wpdb->get_results( 
	"
	SELECT time 
	FROM " . $wpdb->prefix . "bv_gr_clicks_impressions
	WHERE dttype = 'impression'
	"
);

foreach ( $impressionData as $impressionDataDetails ) 
{
    $impressionsTotal++;
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
    $clicksTotal++;   
}

if($impressionsTotal != 0){
$totalCRO = round((($clicksTotal/$impressionsTotal)*100),2);   
}

?>

<p></p>

<h3>CRO Analysis:</h3>

<div>
From: <input type="date" id="bv_gr-datepicker1" name="bt-dt-range-from" onchange='showClicksImpressionsDt(this);'> To: <input type="date" id="bv_gr-datepicker2" name="bt-dt-range-to" onchange='showClicksImpressionsDt(this);'>
</div>

<table class="form-table bv_gr-form-table-st" style="border: 1px solid #000;"><tbody>

<tr>
<th>Clicks</td>
<th>Impressions</td>
<th>CRO (%)</td>
</tr>

<tr>
<td><p id="total-clicks-section"><?php echo $clicksTotal; ?></p></td>
<td><p id="total-impressions-section"><?php echo $impressionsTotal; ?></p></td>
<td><p id="CRO"><?php echo $totalCRO; ?></p></td>
</tr>

</tbody></table>

<div id="piechart" style="width: 400px; height: 300px;"></div>

<?php

}
?>