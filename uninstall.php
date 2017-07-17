<?php
	// If uninstall is not called from WordPress, exit
	if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	    exit();
	}
	delete_option( 'wah_contrast_setup' );
	delete_option( 'wah_font_setup' );
	delete_option( 'wah_font_setup_type' );
	delete_option( 'wah_font_setup_title' );
	delete_option( 'wah_reset_font_size' );
	delete_option( 'wah_close_button_title' );
	delete_option( 'wah_role_links_setup');
	delete_option( 'wah_remove_link_titles' );
	delete_option( 'wah_underline_links_setup' );
	delete_option( 'wah_underline_links_setup_title' );
	delete_option( 'wah_remove_styles_setup' );
	delete_option( 'wah_remove_styles_setup_title' );
	delete_option( 'wah_contrast_setup_title' );
	delete_option( 'wah_image_url' );
	delete_option( 'wah_header_element_selector' );
	delete_option( 'wah_sidebar_element_selector' );
	delete_option( 'wah_footer_element_selector' );
	delete_option( 'wah_main_element_selector' );
	delete_option( 'wah_nav_element_selector' );
	delete_option( 'wah_custom_css' );
	delete_option( 'wah_remove_styles_title' );
	delete_option( 'wah_clear_cookies_title');
	delete_option( 'wah_hidden_stats');
	delete_option( 'wah_choose_color_title');
	delete_option( 'wah_on_off_title');
	delete_option( 'wah_custom_font');
	delete_option( 'wah_keyboard_navigation_setup');
	delete_option( 'wah_keyboard_navigation_title');
	delete_option( 'wah_readable_fonts_setup');
	delete_option( 'wah_readable_fonts_title');
	delete_option( 'wah_left_side');
	delete_option( 'wah_hide_on_mobile');
	delete_option( 'wah_greyscale_title');
	delete_option( 'wah_greyscale_enable');
	delete_option( 'wah_darktheme_enable');
	delete_option( 'wah_highlight_links_enable');
	delete_option( 'wah_highlight_links_title');
	delete_option( 'wah_invert_enable');
	delete_option( 'wah_invert_title');
	delete_option( 'wah_remove_animations_setup');
	delete_option( 'wah_remove_animations_title');
	delete_option( 'wah_stats');
	delete_option( 'wah_sidebar_widgets_order');
	delete_option( 'wah_author_credits');
	delete_option( 'wah_contrast_variations');
	delete_option( 'wah_lights_off_setup');
	delete_option( 'wah_lights_off_title');
	delete_option( 'wah_lights_selector');
?>
