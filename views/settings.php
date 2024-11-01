<div class="wrap">
                    <h1>Voutstream Settings</h1>
                    <?php //settings_errors(); ?>
                    <h2 class="nav-tab-wrapper">
			            <a href="?page=voutstream-settings-admin&tab=licence" class="nav-tab <?php echo $active_tab == 'licence' ? 'nav-tab-active' : ''; ?>">Licence</a>
			            <a href="?page=voutstream-settings-admin&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>">Outstream Settings</a>
			        </h2>
                    <form method="post" action="options.php">
                    <?php
                    	if($active_tab=='licence'){
	                        // This prints out all hidden setting fields
	                        settings_fields( 'voutstream_licence_group' );
	                        do_settings_sections( 'voutstream-licence-admin' );
	                        ?>
	                        <a href="https://platform.voutstream.com/users/register" target="_blank" title="Register to grab a licence key">Don't have licence key?</a><br/>
	                        <a href="https://voutstream.wisecollab.com/hc/article/155-Wordpress-short-code-implementation" target="_blank" title="Wordpress short codes">How to implement?</a><br/>
	                        <a href="https://voutstream.wisecollab.com/hc/article/156-Wordpress-embed-code-examples" target="_blank" title="Shortcode examples">Shortcode examples</a>
	                        <?php 
                    	}else{
                    		
                    		// This prints out all hidden setting fields
                    		settings_fields( 'voutstream_settings_group' );
                    		do_settings_sections( 'voutstream-settings-admin' );
                    	}
                    		
                        submit_button();
                    ?>
                    
                    </form>
                </div>
<script>
function initRemoveTag(){
	jQuery('.remove-tag').off('click').on('click', function(e){
		e.preventDefault();
		jQuery(this).parent().remove();
		jQuery(this).remove();

	})
}
jQuery('#add-preroll').on('click', function(e){
	e.preventDefault();
	var index = parseInt(jQuery(this).attr('data-index'));

	jQuery('#prerolls').append('<div class="tag" style="position:relative">'+ index + '.)<br/> <textarea name="voutstream_settings[ads][]" class="regular-text code" placeholder="Ad Tag Url"></textarea><button class="remove-tag" style="position:absolute">Remove</button></div>')
	
	index++;

	jQuery(this).attr('data-index', index)
	initRemoveTag();
});
initRemoveTag();
</script>