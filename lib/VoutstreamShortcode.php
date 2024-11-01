<?php
/**
 * Voutstreamhortcode class will manage all shortcode manipulation
 */
class VoutstreamShortcode {
    
    public static function endsWith($haystack, $needle)
    {
    	$length = strlen($needle);
    	return ($length == 0) ? true : (substr($haystack, -$length) === $needle);
    }
	

    /**
     * output a script tag that won't be replaced by Rocketscript
     * @param string $handle
     */
    public static function no_rocketscript($handle) {
	     global $wp_scripts;
	      
	     $script = $wp_scripts->query($handle);
	     $src = $script->src;
	     if (!empty($script->ver)) {
	     $src = add_query_arg('ver', $script->ver, $src);
	     }
	     $src = esc_url(apply_filters('script_loader_src', $src, $handle));
	    
	     return  "<script data-cfasync='false' type='text/javascript' src='$src'></script>\n";
    }
    
    /**
     * Render short code
     */
    
    public static function voutstream_shortcode($attrs){
	
    	
    	$licence = get_option('voutstream_licence');
    	
    	
    	if(!empty($licence) && isset($licence['id']) && isset($licence['key']))
    	{
    		$settings = get_option('voutstream_settings');
    		
    		$randId =  self::randId();
    		
    		$options = shortcode_atts(
    				[
			    			'oid'=>'voutstream-'.$randId,
			    			'id'=>'voutstream-'.$randId.'-my-video',
			    			'adWillAutoPlay'=>true,
			    			'width'=>absint(isset($settings['width']) ? $settings['width'] : 640),
			    			'height'=>absint(isset($settings['height']) ? $settings['height'] : 360),
			    			'volume'=>absint(isset($settings['volume']) ? $settings['volume'] : 50),
			    			'sticky'=>absint(isset($settings['sticky']) ? $settings['sticky'] : 0),
			    			'src'=>isset($settings['src']) ? $settings['src'] : '//cdn.brid.tv/live/partners/3550/ld/102375.mp4',
			    			'type'=>isset($settings['type']) ? $settings['type'] : 'inread',
			    			'ads'=>isset($settings['ads']) ? $settings['ads'] : null,
			    	],
    				$attrs
    				);
    		
	    	if($options['ads']){
	    		
	    		if(!is_array($options['ads'])){
	    			
	    			$options['ads'] = explode(',', $options['ads']);
	    			foreach($options['ads'] as $k=>$v){
	    				$options['ads'][$k] = trim($v);
	    			}
	    		}
	    	
	    	$out = '';
	    	
	    	$out.= '<div id="'.$options['oid'].'" class="voutstream"></div>';
	    	
	    	wp_enqueue_script('voutstream-ads-manager.js', '//api.voutstream.com/sdks/js/'.$options['type'].'/'.$licence['id'].'/'.$licence['key'].'/.js', array('jquery'), null, true);

	    	$out.= self::no_rocketscript('voutstream-ads-manager.js');
	    	
	    	$out .= '<script>
	    	var player_'.$randId.' = $_voutstream('.json_encode($options).');
			</script>
	    			';
	    	
	    	return $out;
	    	
	    	}
    	}
    	
    }
   
	/*
	 * @return String random
	 */
    private static function randId($len = 8){

    	$tl = strlen(time());
    	return substr(time(),($tl-$len),$tl).rand();
    }
    

}

//Voutstream shortcode function
add_shortcode('voutstream', array('VoutstreamShortcode' ,'voutstream_shortcode'));
