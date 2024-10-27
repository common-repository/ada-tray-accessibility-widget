<?php
/*
Plugin Name: Ada Tray Accessibility Widget
Description: ADA TRAY Plugin helps increase compliance with Website Content Accessibility Guidelines (WCAG) 2.1, Americans with Disability Act (ADA), and Section 508 web accessibility requirements without requiring re-coding of your website.
Version: 2.4
Author: adatray.com
Author URI: https://www.adatray.com
*/

define('ADATRAY_DIR', plugin_dir_path(__FILE__));

function ADATRAY_load(){
    if(is_admin()) require_once(ADATRAY_DIR.'includes/adashield.php');
}
ADATRAY_load();

function ada_tray_code(){
    wp_register_style( 'akismet.css', plugin_dir_url( __FILE__ ) . 'assets/style.css', array());
    wp_enqueue_style( 'akismet.css');
    $all_options = get_option('ADATRAY_settings');
    if(!empty($all_options['code'])) {
    $code = $all_options['code'];
        echo "<script type='text/javascript'>
 (function () {   
   var params = {'propertyId':$code};
   var paramsArr = [];
   var pl2 = document.createElement('script');
   for(key in params) { paramsArr.push(key + '=' + encodeURIComponent(params[key])) };
   pl2.type = 'text/javascript';
   pl2.async = true;
   pl2.src = 'https://www.adatray.com/adawidget/?' + btoa(paramsArr.join('&'));
   (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(pl2);
 })();
</script>";
    }
}
add_action('wp_footer', 'ada_tray_code');
