<?php

class VoutstreamSettings{
	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;
	
	private $numberFields = ['id', 'width', 'height'];
	private $arrayFields = ['ads'];
	
		public function __construct()
		{
			
			add_action( 'admin_menu', array( $this, 'menu' ) );
			add_action( 'admin_init', array( $this, 'page_init' ) );
			
		}
		/**
		 * Add settings link on plugin page
		 *
		 */
		public static function voutstream_settings_link($links) {
			$settings_link = '<a href="options-general.php?page=voutstream-settings-admin">Settings</a>';
			array_unshift($links, $settings_link);
			return $links;
		}
		/**
         * Admin init
         *
         */
        public function page_init(){

        	if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
        		$this->initLicence();
        		$this->initSettings();
        	}
         
        }
        private function initSettings(){
        	
        	$this->registerGroup('settings', 'Default outstream configuration');
        	 
        	$fields = [	
        				['name'=>'width', 'label'=>'Width'],
        				['name'=>'height', 'label'=>'Height'],
        				['name'=>'sticky', 'label'=>'Sticky', 'type'=>'checkbox'],
        				['name'=>'volume', 'label'=>'Volume'],
        				['name'=>'ads', 'label'=>'Preroll', 'callback'=>'input_preroll_callback'],
        			
        	];
        	$this->printFields($fields);
        	
        }
        
        private function printFields($fields=[], $key='settings'){
        	foreach($fields as $k=>$v){
        	
        		$type = isset($v['type']) ? $v['type'] : 'text';
        		$field_callback = isset($v['callback']) ? $v['callback'] : 'input_callback';
        		add_settings_field(
        				$v['name'], // ID
        				$v['label'], // width
        				array( $this, $field_callback ), // Callback
        				'voutstream-'.$key.'-admin', // Page
        				'voutstream_'.$key.'_section', // Section
        				['name'=>$v['name'], 'group'=>'voutstream_'.$key, 'type'=>$type]
        				);
        	}
        }
        
        private function initLicence(){
        	$this->registerGroup('licence');
        	$fields = [
        			['name'=>'id', 'label'=>'ID'],
        			['name'=>'key', 'label'=>'Licence key'],
        			];
        	
        	$this->printFields($fields, 'licence');
        	
        }
        private function registerGroup($key='settings', $title='Licence'){
        	
        	register_setting(
        			'voutstream_'.$key.'_group', // Option group
        			'voutstream_'.$key, // Option name
        			array( $this, 'sanitize' ) // Sanitize
        			);
        	
        	add_settings_section(
        			'voutstream_'.$key.'_section', // ID
        			$title, // Title
        			null, //array( $this, 'print_section_info' ), // Callback
        			'voutstream-'.$key.'-admin' // Page
        			);
        }
        
        /**
         * Options page callback
         */
        public function create_admin_page()
        {
        	// Set class property
        	$this->options = array_merge((array)get_option( 'voutstream_licence' ) ,  (array)get_option( 'voutstream_settings' ));
        	
        	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'licence';
        	
        	require_once(PLUGIN_DIR.'/views/settings.php');
        	die();
        }
      
        public function menu() {
          
            //Add Video settings screen
            //add_options_page('Voutstream', 'Voutstream', 'administrator', 'voutstream-video-ad-manage', array('VoutstreamHtml', 'manage_ads'));
            
        	if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
	        	// This page will be under "Settings"
        		add_options_page(
        			'Voutstream settings',
        			'Voutstream Settings',
        			'manage_options',
        			'voutstream-settings-admin',
        			array( $this, 'create_admin_page' )
        			);
        		
        		add_filter('plugin_action_links_'.PLUGIN_BASE_FILE , array($this, 'voutstream_settings_link'));
        	}		
        }
        /**
         * Sanitize each setting field as needed
         *
         * @param array $input Contains all settings fields as array keys
         */
        public function sanitize( $input )
        {
        	$new_input = array();
        	
        	foreach($input as $field=>$value){
        		
        		if(in_array($field, $this->numberFields)){ //numbers
        			
        			$new_input[$field] = absint( $value );
        			
        		}else if(in_array($field, $this->arrayFields)){ //hnadle arrays in options
        		
        			$new_arr = [];
        			foreach($value as $k=>$v){
        				if(!empty($v)){
        					$new_arr[$k] = sanitize_text_field($v);
        				}
        			}
        			$new_input[$field] = $new_arr;
        			
        		}else{	//text
        			$new_input[$field] = sanitize_text_field( $value );
        		}
        	}
        	//print_r($new_input);
        	
			return $new_input;
        }
        
        /**
         * Print the Section text
         */
        public function print_section_info()
        {
        	print 'Enter your licence below:';
        }
        /**
         * Pre-roll input
         */
        public function input_preroll_callback(){
        	
        	$args = func_get_args();
        	
        	if(!isset($args[0]['name'])){
        		echo 'name argument is missing';
        		return;
        	}
        	if(!isset($args[0]['group'])){
        		echo 'group (option name) argument is missing';
        		return;
        	}
        	 
        	$name = $args[0]['name'];
        	$group = $args[0]['group']; //option name
        	$type = isset($args[0]['type']) ? $args[0]['type'] : 'text';
        	 
        	$prerolls = $this->options[$name];
        	?>
        	<p>
	     	<button id="add-preroll" data-index="<?php echo count($prerolls)+1; ?>">Add more Pre-rolls</button>
	 		</p>
	 		<div id="prerolls">
	 	
	 		<?php 
        	//var_dump($prerolls);
        	
        	if(!empty($prerolls)){
	        	foreach($prerolls as $k=>$value){
	        		?><div class="tag" style="position:relative"> <?php echo ($k+1); ?>.)<br/><?php 
	        		printf(
	        				'<textarea id="'.$name.'_'.$k.'" name="'.$group.'['.$name.']['.$k.']" placeholder="#'.($k+1).' Pre-roll ad tag Url" class="regular-text code" />%s</textarea>',
	        				esc_attr($value)
	        				);
	        		 if($k>0) { ?>
	        		    <button class="remove-tag" data-key="<?php echo $k; ?>" style="position:absolute">Remove</button>
					<?php }
	        		?></div><?php 
	        	}
        	}
        	?>
        	</div>
        	<?php 
        }
        /**
         * Regular input fileds
         */
        public function input_callback(){
        	
        	$args = func_get_args();
      
        	if(!isset($args[0]['name'])){
        		echo 'name argument is missing';
        		return;
        	}
        	if(!isset($args[0]['group'])){
        		echo 'group (option name) argument is missing';
        		return;
        	}
        	
        	$name = $args[0]['name'];
        	$group = $args[0]['group']; //option name
        	$type = isset($args[0]['type']) ? $args[0]['type'] : 'text';
        	
        	printf(
        			'<input type="'.$type.'" id="'.$name.'" name="'.$group.'['.$name.']" value="%s" class="regular-text code"/>',
        			isset( $this->options[$name] ) ? esc_attr( $this->options[$name]) : ''
        			);
        }
        
}
if( is_admin() )
	$my_settings_page = new VoutstreamSettings();
?>
