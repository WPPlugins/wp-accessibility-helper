<?php
/**********************************************
***   Add front body classes                ***
**********************************************/
if ( ! function_exists( 'wp_access_helper_body_class' ) ) {

    function wp_access_helper_body_class($classes) {
        global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;
        if( $is_lynx ) $classes[] = 'lynx';
        elseif( $is_gecko ) $classes[] = 'gecko';
        elseif( $is_opera ) $classes[] = 'opera';
        elseif( $is_NS4 ) $classes[] = 'ns4';
        elseif( $is_safari ) $classes[] = 'safari';
        elseif( $is_chrome ) $classes[] = 'chrome';
        elseif( $is_IE ) {
            $classes[] = 'ie';
            if( preg_match( '/MSIE ( [0-11]+ )( [a-zA-Z0-9.]+ )/', $_SERVER['HTTP_USER_AGENT'], $browser_version ) )
            $classes[] = 'ie' . $browser_version[1];
        } else $classes[] = 'unknown';
        if( $is_iphone ) $classes[] = 'iphone';

        if ( stristr( $_SERVER['HTTP_USER_AGENT'],"mac") ) {
            $classes[] = 'osx';
        } elseif ( stristr( $_SERVER['HTTP_USER_AGENT'],"linux") ) {
            $classes[] = 'linux';
        } elseif ( stristr( $_SERVER['HTTP_USER_AGENT'],"windows") ) {
            $classes[] = 'windows';
        }

        $classes[]             = 'wp-accessibility-helper';
        $contrast_setup        = get_option('wah_contrast_setup') ? get_option('wah_contrast_setup') : 0;
        $font_setup_type       = get_option('wah_font_setup_type') ? get_option('wah_font_setup_type') : 'zoom';
        $remove_styles_setup   = get_option('wah_remove_styles_setup') ? get_option('wah_remove_styles_setup') : 0;
        $location_setup        = get_option('wah_left_side') ? 'left' : 'right';
        $underline_links_setup = get_option('wah_underline_links_setup') ? get_option('wah_underline_links_setup') : 0;
        $wah_left_side         = get_option('wah_left_side');

        if( $contrast_setup ) { $classes[]        = 'accessibility-contrast_mode_on'; }
        if( $font_setup_type ) { $classes[]       = 'wah_fstype_'.$font_setup_type; }
        if( $remove_styles_setup ) { $classes[]   = 'accessibility-remove-styles-setup'; }
        if( $underline_links_setup ) { $classes[] = 'accessibility-underline-setup'; }
        if( $location_setup == 'left' ) {
            $classes[] = 'accessibility-location-left';
        } else {
            $classes[] = 'accessibility-location-right';
        }
    	return $classes;
    }
    add_filter('body_class','wp_access_helper_body_class');
}
/****************************************************
****   WAH Analyzer                              ***
****************************************************/
add_action('wp','wah_analyzer');
function wah_analyzer(){
    if( wah_analyzer_isset() && wah_admin_only() ) {
        run_front_dom_scanner();
    } elseif( wah_analyzer_isset() && !wah_admin_only() ) {
        echo "<h1 style='text-align:center;'>".__("You do NOT have permissions to access this page","wp-accessibility-helper")."</h1>";
        echo "<h3 style='text-align:center;'>".__("Please contact site administrator.","wp-accessibility-helper")."</h3>";
        die();
    }
}
function run_front_dom_scanner() {
    wp_register_style( 'wah_analyzer-styles',  plugins_url() . '/wp-accessibility-helper/admin/wah-analyzer/style.css' );
    wp_enqueue_style( 'wah_analyzer-styles' );

    wp_localize_script( 'wah_analyzer-js', 'ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ));
    wp_register_script( 'wah_analyzer-js', plugins_url().'/wp-accessibility-helper/admin/wah-analyzer/wah_analyzer.js' , array('jquery'), '', true );
    wp_enqueue_script( 'wah_analyzer-js' );
}
/****************************************************
****   Get attachment id by image source         ***
****************************************************/
function get_attachment_id( $url ) {

	$attachment_id = 0;
	$dir           = wp_upload_dir();

	if ( false !== strpos( $url, $dir['baseurl'] . '/' ) ) { // Is URL in uploads directory?

		$file = basename( $url );
		$query_args = array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'fields'      => 'ids',
			'meta_query'  => array(
				array(
					'value'   => $file,
					'compare' => 'LIKE',
					'key'     => '_wp_attachment_metadata',
				),
			)
		);

		$query = new WP_Query( $query_args );
		if ( $query->have_posts() ) {
			foreach ( $query->posts as $post_id ) {

				$meta = wp_get_attachment_metadata( $post_id );
				$original_file       = basename( $meta['file'] );
				$cropped_image_files = wp_list_pluck( $meta['sizes'], 'file' );

				if ( $original_file === $file || in_array( $file, $cropped_image_files ) ) {
					$attachment_id = $post_id;
					break;
				}
			}
		}

	}
	return $attachment_id;
}
/***********************************************
****   Analyzer Access                      ***
***********************************************/
function wah_analyzer_isset(){
    if( isset($_GET['wah_analyzer']) && $_GET['wah_analyzer'] == 'wah' ) {
        return true;
    }
    return false;
}
function wah_admin_only(){
    if( current_user_can('administrator') ){
        return true;
    }
    return false;
}
/**********************************************
***     Widgets                             ***
**********************************************/
function wah_get_front_widgets_list(){

    //Get all vars
    $font_setup_title = get_option('wah_font_setup_title') ? get_option('wah_font_setup_title'): __("Font Resize","wp-accessibility-helper");
    $reset_font_size_title = get_option('wah_reset_font_size') ? get_option('wah_reset_font_size') : __("Reset font size","wp-accessibility-helper");
    $font_setup_type = get_option('wah_font_setup_type') ? get_option('wah_font_setup_type') : 'zoom';
    $reset_button = '';
    if( $font_setup_type == 'script' ) {
        $reset_button = '<button tabindex="-1" type="button" class="wah-action-button wah-font-reset wahout" title="'.__("Reset font size","wp-accessibility-helper").'"
            aria-label="'.__("Reset font size","wp-accessibility-helper").'">'. $reset_font_size_title .'</button>';
    }

    $contrast_setup = get_option('wah_contrast_setup');
    $contrast_setup_title = get_option('wah_contrast_setup_title') ? get_option('wah_contrast_setup_title'):               __("Contrast","wp-accessibility-helper");
    $choose_color_title = get_option('wah_choose_color_title') ? get_option('wah_choose_color_title') : __("Choose color","wp-accessibility-helper");
    $custom_contrast_variations = get_option('wah_enable_custom_contrast');

    $underline_links_setup = get_option('wah_underline_links_setup');
    $underline_links_setup_title = get_option('wah_underline_links_setup_title') ? get_option('wah_underline_links_setup_title'): __("Underline links","wp-accessibility-helper");
    $role_links_setup = get_option('wah_role_links_setup');
    $remove_link_titles = get_option('wah_remove_link_titles');
    $remove_styles_setup = get_option('wah_remove_styles_setup');
    $remove_styles_setup_title = get_option('wah_remove_styles_setup_title') ? get_option('wah_remove_styles_setup_title'):     __("Remove styles","wp-accessibility-helper");
    $close_button_title = get_option('wah_close_button_title') ? get_option('wah_close_button_title') : __("Close","wp-accessibility-helper");

    $wah_clear_cookies_title = get_option('wah_clear_cookies_title') ? get_option('wah_clear_cookies_title') : __("Clear cookies","wp-accessibility-helper");

    $wah_greyscale_enable = get_option('wah_greyscale_enable');
    $wah_greyscale_title = get_option('wah_greyscale_title') ? get_option('wah_greyscale_title') : __("Images Greyscale","wp-accessibility-helper");

    $wah_highlight_links_enable = get_option('wah_highlight_links_enable');
    $wah_highlight_title = get_option('wah_highlight_links_title') ? get_option('wah_highlight_links_title'): __("Highlight Links","wp-accessibility-helper");

    $wah_invert_enable = get_option('wah_invert_enable');
    $wah_invert_title = get_option('wah_invert_title') ? get_option('wah_invert_title'): __("Invert Colors","wp-accessibility-helper");

    $wah_remove_animations_setup = get_option('wah_remove_animations_setup');
    $wah_remove_animations_title = get_option('wah_remove_animations_title') ? get_option('wah_remove_animations_title'): __("Remove Animations","wp-accessibility-helper");

    $wah_readable_fonts_setup = get_option('wah_readable_fonts_setup');
    $wah_readable_fonts_title = get_option('wah_readable_fonts_title') ? get_option('wah_readable_fonts_title'): __("Readable Font","wp-accessibility-helper");

    $wah_keyboard_navigation_setup = get_option('wah_keyboard_navigation_setup');
    $wah_keyboard_navigation_title = get_option('wah_keyboard_navigation_title') ? get_option('wah_keyboard_navigation_title'): __("Keyboard navigation","wp-accessibility-helper");

    $wah_lights_off_setup = get_option('wah_lights_off_setup');
    $wah_lights_off_title = get_option('wah_lights_off_title') ? get_option('wah_lights_off_title') : __("Lights Off","wp-accessibility-helper");

    //Build widgets array
    $wah_default_front_widget["widget-1"] = array(
        "active" => 1,
        "html"   => '<div class="a_module wah_font_resize">
            <div class="a_module_title">'.$font_setup_title.'</div>
            <div class="a_module_exe font_resizer">
                <button tabindex="-1" type="button" class="wah-action-button smaller wahout" title="'.__("smaller font size","wp-accessibility-helper").'"
                    aria-label="'.__("smaller font size","wp-accessibility-helper").'">A-</button>
                <button tabindex="-1" type="button" class="wah-action-button larger wahout" title="'.__("larger font size","wp-accessibility-helper").'"
                    aria-label="'.__("larger font size","wp-accessibility-helper").'">A+</button>'. $reset_button . '
            </div>
        </div>'
    );
    $wah_default_front_widget["widget-2"] = array(
        "active"    => $wah_keyboard_navigation_setup,
        "html"      => '<div class="a_module wah_keyboard_navigation">
            <div class="a_module_exe">
                <button tabindex="-1" type="button" class="wah-action-button wahout wah-call-keyboard-navigation"
                aria-label="'.$wah_keyboard_navigation_title.'" title="'.$wah_keyboard_navigation_title.'">'.$wah_keyboard_navigation_title.'</button>
            </div>
        </div>'
    );
    $wah_default_front_widget["widget-3"] = array(
        "active"    => $wah_readable_fonts_setup,
        "html"      => '<div class="a_module wah_readable_fonts">
            <div class="a_module_exe readable_fonts">
                <button tabindex="-1" type="button" class="wah-action-button wahout wah-call-readable-fonts" aria-label="'.$wah_readable_fonts_title.'" title="'.$wah_readable_fonts_title.'">'.$wah_readable_fonts_title.'</button>
            </div>
        </div>'
    );
    if($custom_contrast_variations){
        $wah_default_front_widget["widget-4"] = array(
            "active"    => $contrast_setup
        );
        $wah_default_front_widget["widget-4"]["html"] = get_custom_contrast_variations($contrast_setup_title,$choose_color_title);
    } else {
        $wah_default_front_widget["widget-4"] = array(
            "active"    => $contrast_setup,
            "html"      => '<div class="a_module wah_contrast_trigger">
                <div class="a_module_title">'.$contrast_setup_title.'</div>
                <div class="a_module_exe">
                    <button tabindex="-1" type="button" id="contrast_trigger" class="contrast_trigger wah-action-button wahout wah-call-contrast-trigger" title="Contrast">'.$choose_color_title.'</button>
                    <div class="color_selector" aria-hidden="true">
                        <button type="button" class="convar black wahout" data-bgcolor="#000" data-color="#FFF"
                        title="'.__("black","wp-accessibility-helper").'">'.__("black","wp-accessibility-helper").'</button>
                        <button type="button" class="convar white wahout" data-bgcolor="#FFF" data-color="#000"
                        title="'.__("white","wp-accessibility-helper").'">'.__("white","wp-accessibility-helper").'</button>
                        <button type="button" class="convar green wahout" data-bgcolor="#00FF21" data-color="#000"
                        title="'.__("green","wp-accessibility-helper").'">'.__("green","wp-accessibility-helper").'</button>
                        <button type="button" class="convar blue wahout" data-bgcolor="#0FF" data-color="#000"
                        title="'.__("blue","wp-accessibility-helper").'">'.__("blue","wp-accessibility-helper").'</button>
                        <button type="button" class="convar red wahout" data-bgcolor="#F00" data-color="#000"
                        title="'.__("red","wp-accessibility-helper").'">'.__("red","wp-accessibility-helper").'</button>
                        <button type="button" class="convar orange wahout" data-bgcolor="#FF6A00" data-color="#000" title="'.__("orange","wp-accessibility-helper").'">'.__("orange","wp-accessibility-helper").'</button>
                        <button type="button" class="convar yellow wahout" data-bgcolor="#FFD800" data-color="#000"
                        title="'.__("yellow","wp-accessibility-helper").'">'.__("yellow","wp-accessibility-helper").'</button>
                        <button type="button" class="convar navi wahout" data-bgcolor="#B200FF" data-color="#000"
                        title="'.__("navi","wp-accessibility-helper").'">'.__("navi","wp-accessibility-helper").'</button>
                    </div>
                </div>
            </div>'
        );
    }

    $wah_default_front_widget["widget-5"] = array(
        "active"    => $underline_links_setup,
        "html"      => '<div class="a_module wah_underline_links">
            <div class="a_module_exe">
                <button tabindex="-1" type="button" class="wah-action-button wahout wah-call-underline-links" aria-label="'.$underline_links_setup_title.'" title="'.$underline_links_setup_title.'">'.$underline_links_setup_title.'</button>
            </div>
        </div>'
    );
    $wah_default_front_widget["widget-6"] = array(
        "active"    => $wah_highlight_links_enable,
        "html"      => '<div class="a_module wah_highlight_links">
            <div class="a_module_exe">
                <button tabindex="-1" type="button" class="wah-action-button wahout wah-call-highlight-links" aria-label="'.$wah_highlight_title.'" title="'.$wah_highlight_title.'">'.$wah_highlight_title.'</button>
            </div>
        </div>'
    );
    $wah_default_front_widget["widget-7"] = array(
        "active"    => 1,
        "html"      => '<div class="a_module wah_clear_cookies">
            <div class="a_module_exe">
                <button tabindex="-1" type="button" class="wah-action-button wahout wah-call-clear-cookies"
                aria-label="'.$wah_clear_cookies_title.'" title="'.$wah_clear_cookies_title.'">'.$wah_clear_cookies_title.'</button>
            </div>
        </div>'
    );
    $wah_default_front_widget["widget-8"] = array(
        "active"    => $wah_greyscale_enable,
        "html"      => '<div class="a_module wah_greyscale">
            <div class="a_module_exe">
                <button tabindex="-1" type="button" id="greyscale" class="greyscale wah-action-button wahout wah-call-greyscale"
                aria-label="'.$wah_greyscale_title.'" title="'.$wah_greyscale_title.'">'.$wah_greyscale_title.'</button>
            </div>
        </div>'
    );
    $wah_default_front_widget["widget-9"] = array(
        "active"    => $wah_invert_enable,
        "html"      => '<div class="a_module wah_invert">
            <div class="a_module_exe">
                <button tabindex="-1" type="button" class="wah-action-button wahout wah-call-invert"
                aria-label="'.$wah_invert_title.'" title="'.$wah_invert_title.'">'.$wah_invert_title.'</button>
            </div>
        </div>'
    );
    $wah_default_front_widget["widget-10"] = array(
        "active"    => $wah_remove_animations_setup,
        "html"      => '<div class="a_module wah_remove_animations">
            <div class="a_module_exe">
                <button tabindex="-1" type="button" accesskey="'.apply_filters( 'wah_remove_animations_accesskey', 'a' ).'" class="wah-action-button wahout wah-call-remove-animations"
                aria-label="'.$wah_remove_animations_title.'" title="'.$wah_remove_animations_title.'">'.$wah_remove_animations_title.'</button>
            </div>
        </div>'
    );
    $wah_default_front_widget["widget-11"] = array(
        "active"    => $remove_styles_setup,
        "html"      => '<div class="a_module wah_remove_styles">
            <div class="a_module_exe">
                <button tabindex="-1" type="button" class="wah-action-button wahout wah-call-remove-styles"
                aria-label="'.$remove_styles_setup_title.'" title="'.$remove_styles_setup_title.'">'.$remove_styles_setup_title.'</button>
            </div>
        </div>'
    );
    $wah_default_front_widget["widget-12"] = array(
        "active"    => $wah_lights_off_setup,
        "html"      => '<div class="a_module wah_lights_off">
            <div class="a_module_exe">
                <button tabindex="-1" type="button" id="wah_lights_off" class="wah-action-button wahout wah-lights-off wah-call-lights-off"
                aria-label="'.$wah_lights_off_title.'">'.$wah_lights_off_title.'</button>
            </div>
        </div>'
    );
    return $wah_default_front_widget;
}

function wah_calculate_enabled_widgets(){
    $front_widgets     = wah_get_front_widgets_list();
    $enabled_widgets   = array();
    $wah_widgets_order = get_option('wah_sidebar_widgets_order');
    if($wah_widgets_order){
        $wah_widgets       = unserialize($wah_widgets_order);
        foreach ($wah_widgets as $id=>$value) {
            if($value["active"] && $value["active"] == 1){
                $enabled_widgets[$id] = $front_widgets[$id];
            }
        }
    } else {
        foreach ($front_widgets as $id=>$value) {
            if($value["active"] && $value["active"] == 1){
                $enabled_widgets[$id] = $front_widgets[$id];
            }
        }
    }

	return apply_filters('wah_enabled_widgets',$enabled_widgets);
}

function wah_render_enabled_widgets_list(){
    $enabled_widgets = wah_calculate_enabled_widgets();
    foreach($enabled_widgets as $wah_widget){
        echo $wah_widget["html"];
    }
}

function wah_default_contrast_options(){
    $contrast_array = array();
    $contrast_array["contrast-1"] = array(
        "label"   => "black",
        "bgcolor" => "#000",
        "color"   => "#FFF"
    );
    $contrast_array["contrast-2"] = array(
        "label"   => "white",
        "bgcolor" => "#FFF",
        "color"   => "#000"
    );
    $contrast_array["contrast-3"] = array(
        "label"   => "green",
        "bgcolor" => "#00FF21",
        "color"   => "#000"
    );
    $contrast_array["contrast-4"] = array(
        "label"   => "blue",
        "bgcolor" => "#0FF",
        "color"   => "#000"
    );
    $contrast_array["contrast-5"] = array(
        "label"   => "red",
        "bgcolor" => "#F00",
        "color"   => "#000"
    );
    $contrast_array["contrast-6"] = array(
        "label"   => "orange",
        "bgcolor" => "#FF6A00",
        "color"   => "#000"
    );
    $contrast_array["contrast-7"] = array(
        "label"   => "yellow",
        "bgcolor" => "#FFD800",
        "color"   => "#000"
    );
    $contrast_array["contrast-8"] = array(
        "label"   => "navi",
        "bgcolor" => "#B200FF",
        "color"   => "#000"
    );

    return $contrast_array;
}

function get_custom_contrast_variations($contrast_setup_title,$choose_color_title){
    $contrast_variations = get_option('wah_contrast_variations');
    $contrast_variations = unserialize($contrast_variations);
    $custom_contrast_html = '';
    ob_start();
    if($contrast_variations){  ?>
            <div class="a_module">
                <div class="a_module_title"><?php echo $contrast_setup_title; ?></div>
                <div class="a_module_exe">
                    <button type="button" id="contrast_trigger" class="contrast_trigger wah-action-button wahout wah-call-contrast-trigger">
                        <?php echo $choose_color_title; ?>
                    </button>
                    <div class="color_selector" aria-hidden="true">
                        <?php if( count($contrast_variations) >= wah_get_limit_contrast_variations() ) : ?>
                            <?php _e("Maximum 4 contrast variations","wp-accessibility-helper"); ?>
                        <?php else: ?>
                            <?php foreach($contrast_variations as $contrast) : ?>
                                <button type="button" class="convar wahout wahcolor" style="background:#<?php echo $contrast['bgcolor']; ?> !important;" data-bgcolor="#<?php echo $contrast['bgcolor']; ?>" data-color="#<?php echo $contrast['textcolor']; ?>"></button>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
    <?php }
    $custom_contrast_html = ob_get_clean();
    return $custom_contrast_html;

}

function wah_get_limit_contrast_variations(){
    return 5;
}

function wah_render_last_skiplink(){
    $close_button_title = get_option('wah_close_button_title') ? get_option('wah_close_button_title') : __("Close","wp-accessibility-helper");
?>
    <button type="button" title="<?php _e("Close sidebar","wp-accessibility-helper"); ?>" class="wah-skip close-wah-sidebar">
        <?php echo $close_button_title; ?>
    </button>
<?php }

