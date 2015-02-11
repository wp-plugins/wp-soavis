<?php

/**
 * Provide a dashboard view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://www.soavis.eu
 * @package    WP_SoaVis
 * @subpackage WP_SoaVis/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">

	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<form method="post" action="options.php">
	
	<?php
		settings_fields( 'wp_soavis_option_group' );
		do_settings_sections( 'wp-soavis-setting-admin' );
	?>
	
		<?php submit_button(); ?>
	</form>

	<!-- TODO: Provide markup for your options page here. -->
	<h2>Test output of the WP SoaVis admin page!</h2>
	<p>Value of option wp_soavis_id = <?php echo wps_get_option( 'wp_soavis_id' ); ?></p>
	<p>Value of option wp_soavis_title = <?php echo wps_get_option( 'wp_soavis_title' ); ?></p>
	<p>Value of option wp_soavis_debug_out = <?php 
		echo wps_get_option( 'wp_soavis_debug_out' );
		echo '<br/>DEBUG: wp_soavis_title wp_settings_fields:<br/><br/>';

		global $wp_settings_fields;
		global $WP_SoaVis_Object;
		$wp_soavis_options = get_option('wp_soavis_options');

		$WP_SoaVis_Object->debugMP('pr','WP SoaVis Admin page wp_soavis_options',$wp_soavis_options,__FILE__,__LINE__);
		$WP_SoaVis_Object->debugMP('pr','WP SoaVis Admin page wp_settings_fields',$wp_settings_fields['wp-soavis-setting-admin'],__FILE__,__LINE__);

	?></p>
	
</div>
