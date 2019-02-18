<?php
/*
*
Plugin Name: weather plugin
Plugin URI: https://github.com/nijhumjawad2000
Description: This is a new weather plugin
Author: nijhum jawad
Version: 1.0.0
Author URI: http://njawad.techlaunch.io
*
*/

$plugin_url = WP_PLUGIN_URL . '/weather';

function plugin_install() {

    global $wpdb;
    return true;

}

function option_menu() {

    add_options_page(
        'Weather Plugin',
        'Weather Plugin',
        'manage_options',
        'weather-plugin',
        'option_page'
    );

}

add_action('admin_menu', 'option_menu');

function option_page() {   
    
    if( !current_user_can( 'manage_options' ) ) { 

        wp_die( 'You don\'t have sufficient permissions to access this page.' );    

    }

    global $plugin_url;
    global $city;
    
    if( isset( $_POST['form_submitted'] ) ) {

        $hidden_field = esc_html( $_POST['form_submitted'] );
        
        if( $hidden_field == 'Y' ) {
            
            $city = esc_html( $_POST['city'] );
            
            $weather =  getWeather($city );
            
        }
        
    }
    
    require('weather.php');
}

function getWeather($city){   
    
    $json_feed_url = 'https://api.openweathermap.org/data/2.5/weather?q=' . $city . '&appid=b97bb751313b5a72afe4f1be164184c5';
    
    $args = array('timeoute' => 120);
    
    $json_feed = wp_remote_get( $json_feed_url, $args );
    
    $weather_updates = json_decode( $json_feed['body'] );

?>
<div class="main">
    <div class="inside">
        <article class="weather-plugin">
            <div class="weatherIcon">
                <h1 class="head">Local Weather Details : </h1>
                <div id="icon"><img src="http://openweathermap.org/img/w/<?= $weather_updates->weather[0]->icon?>.png"
                        alt="Weather icon" /></div>
                <div class="info">City :
                    <?= $weather_updates->name . ', ' . $weather_updates->sys->country ?>
                </div>
                <div class="info">Temperature :
                    <?= floor(($weather_updates->main->temp) - 273.15) . '&#8451';?>
                </div>
                <div class="info">Min. Temperature :
                    <?= floor(($weather_updates->main->temp_min) - 273.15) . '&#8451;' ; ?>
                </div>
                <div class="info">Max. Temperature :
                    <?= floor(($weather_updates->main->temp_max) - 273.15) . '&#8451;' ; ?>
                </div>
                <div class="info">Description :
                    <?= $weather_updates->weather[0]->description;?>
                </div>
                <?= $weather_updates->main->humidity ; ?> 
                 <?= $weather_updates->main->pressure ; ?>  
                 <?= $weather_updates->wind->speed ; ?> 
            </div>
        </article>
    </div>
</div>
<?php  
}
function plugin_deactivate()
{
    global $wpdb;
    echo "deactivate";
}
function weather_styles() {
	wp_enqueue_style( 'weather_styles', plugins_url( 'weather/style.css' ) );
}
add_action( 'admin_head', 'weather_styles' );
add_action('wp_enqueue_scripts', 'getWeather');
register_activation_hook(__FILE__, 'plugin_install');
register_deactivation_hook(__FILE__, 'plugin_deactivate');
?>