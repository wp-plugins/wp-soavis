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
		settings_fields( $settings_group_id );
		do_settings_sections( $settings_group_id );
	?>
	
		<?php submit_button(); ?>
	</form>

	<br/>
	<br/>
	<p><?php echo sprintf(__('Download a set of example entities via <a href="%s">%s</a>.', $this->plugin_name), WP_SOAVIS_SAMPLE_LINK, WP_SOAVIS_SAMPLE_LINK); ?></p>
	<br/>
	<br/>
	<br/>
	<br/>
	<br/>
	<br/>
	<br/>
	<p><?php echo sprintf(__('wp-soavis version %s.', $this->plugin_name), wps_get_option(WP_SOAVIS_VERSION_NAME)); ?></p>

</div>
