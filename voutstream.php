<?php
/**
 * Plugin Name: Voutstream.com - Outstream ads
 * Plugin URI: https://wordpress.org/plugins/voutstream/
 * Description: Embed outstream ads (outstream, in-read, in-post)
 * Version: 1.0.2
 * Author: voutstream.com
 * Author URI: https://voutstream.com
 * Settings: Voutstream
 * License: GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 */

define('PLUGIN_DIR', dirname( __FILE__ ).'/');
define('PLUGIN_BASE_FILE', plugin_basename(__FILE__));
define('VOUTSTREAM_PLUGIN_VERSION', '1.0.2');

if(!class_exists('Voutstream')){

	class Voutstream {
	    
	    public static $instance = null;
	    
	    static $voutstream_options  = array('voutstream_options');

		public static function activate(){
			if(get_option('voutstream_licence')==''){
				update_option('voutstream_licence','');
				update_option('voutstream_settings','');
				self::alert('activated');
			}else{
				self::alert('reactivated');
			}
        }
        public static function alert($action, $email='contact@voutstream.com'){
        	$blog = get_site_url();
        	@wp_mail($email, 'Voutstream plugin '.$action, 'Blog ('.$blog.') has '.$action.' Voutstream plugin at:'.date('Y-m-d H:i:s').' Plugin Ver:'.VOUTSTREAM_PLUGIN_VERSION.' Php Ver.:'.phpversion());
        }
        public static function getConst($const){
        	return defined($const) ? constant($const) : '';
        }
       
        public static function deactivate(){
           	self::alert('deactivated');
        }
        public static function uninstall(){
        	//Delete ssw options from options table
            delete_option('voutstream_licence');
            delete_option('voutstream_settings');
            self::alert('uninstalled');
        }
		public function __construct() {
		    register_activation_hook( __FILE__, array( 'Voutstream', 'activate' ) );
		    register_deactivation_hook( __FILE__, array( 'Voutstream', 'deactivate' ) );
		    register_uninstall_hook( __FILE__, array( 'Voutstream', 'uninstall' ) );
		   
		    self::$instance = $this;
		}

		private static function instance() {
		    if ( self::$instance ) return self::$instance;
		    else return new Voutstream();
		}

	}
	
	require_once PLUGIN_DIR. 'lib/VoutstreamShortcode.php';
}


if(class_exists('Voutstream'))
{
	$voutstream = new Voutstream();
	//Most of the Html, templates, pages
	require_once PLUGIN_DIR. 'VoutstreamSettings.php';
	
}