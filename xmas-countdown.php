<?php
/*
Plugin Name: WordPress Christmas Countdown
Plugin URI: http://portalplanet.net/
Version: 1.0.1
Requires at least: 5.2
Tested up to: 5.8
Requires PHP: 7.2
Author: Justin Rains
Author URI: http://portalplanet.net/
Description: Allows a Christmas countdown widget to be displayed on your WordPress site.
*/
class wp_xmas_cd extends WP_Widget {
    // constructor
    public function __construct() {
//    function wp_xmas_cd() {
        /* Widget settings. */
        $widget_ops = array(
            'classname' => 'wp-xmas-cd', 
            'description' => 'A widget that shows the days until Christmas in your sidebar.' );

        /* Widget control settings. */
        $control_ops = array(
            'width' => 300,
            'height' => 40,
            'id_base' => 'wp-xmas-countdown-widget'
        );

        /* Create the widget. */
        parent::__construct(
            'wp-xmas-cd',
            'Christmas Countdown',
            $widget_ops//,
//            $control_ops
        );
//        $this->WP_Widget( 'wp-xmas-countdown-widget', 'Christmas Countdown', $widget_ops, $control_ops );
    }
    /**
     * Outputs the content of the widget.
     * @param $args
     * @param $instance
     */
    public function widget($args, $instance) {
        extract($args);
        extract($instance);
	$aftertext = get_option('aftertext');
	$beforetext = get_option('beforetext');
	$title = get_option('title');
	if ($title == '') {
		$title = "Christmas Countdown";
	}
	if ($beforetext == '') {
		$beforetext = "There are";
	}
	if ($aftertext == '') {
		$aftertext = "Days before Christmas";
	}
        // Show widget regardless if there are websites
        echo $before_widget;
        echo $before_title . $title . $after_title; 

        if ($beforetext) {
                printf('<div class="wp-xmas-countdown wp-xmas-countdown-widget-top">%s</div>', $beforetext);
        }
        $month = date('n');
        $day = date('j');
        $year = date('Y');
        if ($month == 12 && $day >= 26) {
            $year++;
        }
        $target = mktime(0, 0, 0, 12, 25, $year);
        $today = time ();
        $difference =($target-$today);
        $days =(int) ($difference/86400);
        if ($days < 0) { // is Christmas next year?
            $year++;
            $target = mktime(0, 0, 0, 12, 25, $year);
            $today = time ();
            $difference =($target-$today);
            $days =(int) ($difference/86400);
        }
        printf('<div class="wp-xmas-countdown wp-xmas-countdown-widget-middle">%s</div>', $days);
        if ($aftertext) {
                printf('<div class="wp-xmas-countdown wp-xmas-countdown-widget-bottom">%s</div>', $aftertext);
        }

        // Closing wrapper tag
        echo '</div>';

        // This always needs to go at the end.
        echo $after_widget; 
    }

function activate_xmas_cd() {
  add_option('xmas_cd_id', 'sprite-16');
}

function deactivate_xmas_cd() {
  delete_option('xmas_cd_id');
}

function admin_init_xmas_manager() {
  register_setting('xmas_cd_id', 'xmas_cd_id');
}

function admin_menu_xmas_manager() {
  add_options_page(
          'Christmas Countdown', 'Christmas Countdown', 
          'manage_options', 'wp_xmas', 'options_page_wp_xmas'
  );
}

function admin_register_countdown_styles() {
//wp_enqueue_script( 'digidigits-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), '20120206', true );
//    $my_js_ver  = date("ymd-Gis", filemtime( plugin_dir_path( __FILE__ ) . 'js/custom.js' ));
    $my_css_ver = date("ymd-Gis", filemtime( plugin_dir_path( __FILE__ ) . '/css/xmas-cd.css' ));
     
//    wp_enqueue_script( 'custom_js', plugins_url( 'js/custom.js', __FILE__ ), array(), $my_js_ver );
    wp_register_style( 'xmas_css',    plugins_url( '/css/xmas-cd.css',    __FILE__ ), false,   $my_css_ver );
    wp_enqueue_style ( 'xmas_css' );
}

function options_page_wp_xmas() {
  include(WP_PLUGIN_DIR.'/wp-xmas-countdown/options.php');  
}

// widget form creation
function form($instance) {
    // jQuery
    wp_enqueue_script('jquery');
    // This will enqueue the Media Uploader script
    wp_enqueue_media();

    // Check values
    if( $instance) {
        $title = esc_attr($instance['title']);
        $beforetext = esc_attr($instance['beforetext']);
        $aftertext = esc_attr($instance['aftertext']);
    } else {
        $title = 'Christmas Countdown';
        $beforetext = 'There Are Only';
        $month = date('n');
        $day = date('j');
        $year = date('Y');
        if ($month == 12 && $day >= 26) {
            $year++;
        }
        $aftertext = $year . 'Days Until Christmas!';
    }
?>
<p>
<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp_widget_plugin'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</p>
<p>
<label for="<?php echo $this->get_field_id('beforetext'); ?>"><?php _e('Before Text:', 'wp_widget_plugin'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('beforetext'); ?>" name="<?php echo $this->get_field_name('beforetext'); ?>" type="text" value="<?php echo $beforetext; ?>" />
</p>
<p>
<label for="<?php echo $this->get_field_id('aftertext'); ?>"><?php _e('After Text:', 'wp_widget_plugin'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('aftertext'); ?>" name="<?php echo $this->get_field_name('aftertext'); ?>" type="text" value="<?php echo $aftertext; ?>" />
</p>
<?php
    }
}
// UPLOAD ENGINE
function load_wp_media_files() {
    wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'load_wp_media_files' );
function register_countdown_widget() {
    register_widget( 'wp_xmas_cd' );
}

register_activation_hook(__FILE__, 'activate_xmas_cd');
register_deactivation_hook(__FILE__, 'deactivate_xmas_cd');

add_action( 'widgets_init', 'register_countdown_widget' );
?>
