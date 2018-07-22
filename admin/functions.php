<?php
  if (!function_exists('get_pageNumber')) {
    function get_pageNumber(){
        if( get_query_var('page') )
            $paged = get_query_var('page');
        elseif( !empty( $_GET['paged'] ) && is_numeric( $_GET['paged'] ) )
            $paged = (int)$_GET['paged'];
        else
            $paged = 1;
        return $paged;
    }
  }

  if (!function_exists('get_allowedTypes')) {
    function get_allowedTypes(){
        $allowTypes = array( 'attachment' );
        return $allowTypes;
    }
  }

  if (!function_exists('get_postsCounter')) {
    function get_postsCounter( $type , $status = NULL ){
        if( in_array( $type , get_allowedTypes() ) ){
            if( empty( $status ) || !is_array( $status ) )
                $status = array( 'publish' , 'inherit' );
            $postCounter = 0;
            $count_posts = wp_count_posts( $type );
            if( isset( $count_posts ) ){
                foreach( $status as $st )
                    $postCounter = $postCounter + $count_posts->{ sanitize_text_field( $st ) };
            }
            return $postCounter;
        }
    }
  }

  if (!function_exists('get_pagination')) {
    function get_pagination( $type , $posts_per_page = 10 ){
        if( in_array( $type , get_allowedTypes() ) ){
            $postCounter   = get_postsCounter( 'attachment' , array( 'inherit' ) );
            $currentPage   = get_pageNumber();
            $numberOfPages = $postCounter / $posts_per_page;
            $thisUrl       = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $output[] = '<ul class="admin_page_pagination">';
            if( $currentPage != 1 && ceil( $numberOfPages ) > 1){
                $prevPage = add_query_arg( 'paged' , ( $currentPage - 1 ) , sanitize_text_field( $thisUrl ) );
                $output[] = "<li class='item prev_page'><a href='$prevPage'><i>&#10094;&#10094;</i></a></li>";
            }
            for($i=1 ; $i <= ceil( $numberOfPages ) ; $i++ ){
                $newUrl       = add_query_arg( 'paged' , $i , sanitize_text_field( $thisUrl ) );
                $currentClass = '';
                if( $currentPage == $i )
                    $currentClass = 'current';
                $output[] = "<li class='item sanitize_url( $currentClass )'><a href='$newUrl'>$i</a></li>";
            }
            if( $currentPage != ceil( $numberOfPages ) ){
                $nextPage = $prevPage = add_query_arg( 'paged' , ( $currentPage + 1 ) , sanitize_text_field( $thisUrl ) );
                $output[] = "<li class='item next_page'><a href='$nextPage'><i>&#10095;&#10095;</i></a></li>";
            }
            $output[] = '</ul>';
            echo implode( '' , $output );
        }
    }
  }

  if (!function_exists('get_client_ip')) {
    //Get client IP
    function get_client_ip() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
  }

  if (!function_exists('stats_collector')) {

    //Collect clients data
    function stats_collector(){
        $params["add_activation"]   = true;
        $params["site_name"]        = urlencode(get_bloginfo('name'));
        $params["site_url"]         = urlencode(get_bloginfo('url'));
        $params["site_admin_email"] = urlencode(get_bloginfo('admin_email'));
        $params["site_wp_version"]  = urlencode(get_bloginfo('version'));
        $params["site_language"]    = urlencode(get_bloginfo('language'));
        $params["site_theme_name"]  = urlencode(wp_get_theme());
        $params["ip"]               = urlencode(get_client_ip());
        send_data_to_server($params);
    }
  }

  if (!function_exists('myfunction')) {

    //Send data to server
    function send_data_to_server($params){
        $api_url = "http://volkov.co.il";
        $api_url = add_query_arg($params,$api_url);
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $api_url);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        echo "<h3>". __("Thanks!","wp-accessibility-helper") . "</h3>";
        die();
    }
  }

  if (!function_exists('wah_get_admin_widgets_list')) {
    //Get admin widgets list
    function wah_get_admin_widgets_list(){
        $wah_keyboard_navigation_setup = get_option('wah_keyboard_navigation_setup');
        $wah_readable_fonts_setup = get_option('wah_readable_fonts_setup');
        $contrast_setup = get_option('wah_contrast_setup');
        $underline_links_setup = get_option('wah_underline_links_setup');
        $wah_highlight_links_enable = get_option('wah_highlight_links_enable');
        $wah_greyscale_enable = get_option('wah_greyscale_enable');
        $wah_invert_enable = get_option('wah_invert_enable');
        $wah_remove_animations_setup = get_option('wah_remove_animations_setup');
        $remove_styles_setup = get_option('wah_remove_styles_setup');
        $wah_lights_off_setup = get_option('wah_lights_off_setup');
        $widgetsObject = array();
        $widgetsObject["widget-1"] = array(
            "active" => 1,
            "html"   => 'Font resize',
            "class"  => "active"
        );
        $widgetsObject["widget-2"] = array(
            "active" => $wah_keyboard_navigation_setup,
            "html"   => 'Keyboard navigation',
            "class"  => $wah_keyboard_navigation_setup ? "active" : "notactive"
        );
        $widgetsObject["widget-3"] = array(
            "active" => $wah_readable_fonts_setup,
            "html"   => 'Readable Font',
            "class"  => $wah_readable_fonts_setup ? "active" : "notactive"
        );
        $widgetsObject["widget-4"] = array(
            "active" => $contrast_setup,
            "html"   => 'Contrast',
            "class"  => $contrast_setup ? "active" : "notactive"
        );
        $widgetsObject["widget-5"] = array(
            "active" => $underline_links_setup,
            "html"   => 'Underline links',
            "class"  => $underline_links_setup ? "active" : "notactive"
        );
        $widgetsObject["widget-6"] = array(
            "active" => $wah_highlight_links_enable,
            "html"   => 'Highlight links',
            "class"  => $wah_highlight_links_enable ? "active" : "notactive"
        );
        $widgetsObject["widget-7"] = array(
            "active" => 1,
            "html"   => 'Clear cookies',
            "class"  => "active"
        );
        $widgetsObject["widget-8"] = array(
            "active" => $wah_greyscale_enable,
            "html"   => 'Image Greyscale',
            "class"  => $wah_greyscale_enable ? "active" : "notactive"
        );
        $widgetsObject["widget-9"] = array(
            "active" => $wah_invert_enable,
            "html"   => 'Invert colors',
            "class"  => $wah_invert_enable ? "active" : "notactive"
        );
        $widgetsObject["widget-10"] = array(
            "active" => $wah_remove_animations_setup,
            "html"   => 'Remove Animations',
            "class"  => $wah_remove_animations_setup ? "active" : "notactive"
        );
        $widgetsObject["widget-11"] = array(
            "active" => $remove_styles_setup,
            "html"   => 'Remove styles',
            "class"  => $remove_styles_setup ? "active" : "notactive"
        );
        $widgetsObject["widget-12"] = array(
            "active" => $wah_lights_off_setup,
            "html"   => 'Lights Off',
            "class"  => $wah_lights_off_setup ? "active" : "notactive"
        );
        $wah_widgets_order = get_option('wah_sidebar_widgets_order');
        if(!$wah_widgets_order){
            return $widgetsObject;
        } else {
            $wah_serialize_widgets  = unserialize($wah_widgets_order);
            $sortedWidgetsObject    = array();
            foreach ($wah_serialize_widgets as $id=>$array) {
                $sortedWidgetsObject[$id] = array(
                    "active" => $array["active"],
                    "html"   => $array["html"],
                    "class"  => $array["class"]
                );
            }
            return $sortedWidgetsObject;
        }
    }
  }

  if (!function_exists('wah_get_widgets_status')) {
    //Get widgets status
    function wah_get_widgets_status(){
        $widgets_status = array();
        $widgets_status['wah_keyboard_navigation_setup'] = get_option('wah_keyboard_navigation_setup');
        $widgets_status['wah_readable_fonts_setup']      = get_option('wah_readable_fonts_setup');
        $widgets_status['contrast_setup']                = get_option('wah_contrast_setup');
        $widgets_status['underline_links_setup']         = get_option('wah_underline_links_setup');
        $widgets_status['wah_highlight_links_enable']    = get_option('wah_highlight_links_enable');
        $widgets_status['wah_greyscale_enable']          = get_option('wah_greyscale_enable');
        $widgets_status['wah_invert_enable']             = get_option('wah_invert_enable');
        $widgets_status['wah_remove_animations_setup']   = get_option('wah_remove_animations_setup');
        $widgets_status['remove_styles_setup']           = get_option('wah_remove_styles_setup');
        $widgets_status['wah_lights_off_setup']          = get_option('wah_lights_off_setup');
        return $widgets_status;
    }
  }

  if (!function_exists('update_serialize_order_array')) {
    //Update serialize array of ordered widgets
    function update_serialize_order_array(){
        $widgetsObject          = array();
        $widgets_status         = wah_get_widgets_status();
        $wah_serialize_widgets  = get_option('wah_sidebar_widgets_order');
        if(!$wah_serialize_widgets){
            $widgetsObject["widget-1"] = array(
                "active" => 1,
                "html"   => 'Font resize',
                "class"  => "active"
            );
            $widgetsObject["widget-2"] = array(
                "active" => $widgets_status['wah_keyboard_navigation_setup'],
                "html"   => 'Keyboard navigation',
                "class"  => $widgets_status['wah_keyboard_navigation_setup'] ? "active" : "notactive"
            );
            $widgetsObject["widget-3"] = array(
                "active" => $widgets_status['wah_readable_fonts_setup'],
                "html"   => 'Readable Font',
                "class"  => $widgets_status['wah_readable_fonts_setup'] ? "active" : "notactive"
            );
            $widgetsObject["widget-4"] = array(
                "active" => $widgets_status['contrast_setup'],
                "html"   => 'Contrast',
                "class"  => $widgets_status['contrast_setup'] ? "active" : "notactive"
            );
            $widgetsObject["widget-5"] = array(
                "active" => $widgets_status['underline_links_setup'],
                "html"   => 'Underline links',
                "class"  => $widgets_status['underline_links_setup'] ? "active" : "notactive"
            );
            $widgetsObject["widget-6"] = array(
                "active" => $widgets_status['wah_highlight_links_enable'],
                "html"   => 'Highlight links',
                "class"  => $widgets_status['wah_highlight_links_enable'] ? "active" : "notactive"
            );
            $widgetsObject["widget-7"] = array(
                "active" => 1,
                "html"   => 'Clear cookies',
                "class"  => "active"
            );
            $widgetsObject["widget-8"] = array(
                "active" => $widgets_status['wah_greyscale_enable'],
                "html"   => 'Image Greyscale',
                "class"  => $widgets_status['wah_greyscale_enable'] ? "active" : "notactive"
            );
            $widgetsObject["widget-9"] = array(
                "active" => $widgets_status['wah_invert_enable'],
                "html"   => 'Invert colors',
                "class"  => $widgets_status['wah_invert_enable'] ? "active" : "notactive"
            );
            $widgetsObject["widget-10"] = array(
                "active" => $widgets_status['wah_remove_animations_setup'],
                "html"   => 'Remove Animations',
                "class"  => $widgets_status['wah_remove_animations_setup'] ? "active" : "notactive"
            );
            $widgetsObject["widget-11"] = array(
                "active" => $widgets_status['remove_styles_setup'],
                "html"   => 'Remove styles',
                "class"  => $widgets_status['remove_styles_setup'] ? "active" : "notactive"
            );
            $widgetsObject["widget-12"] = array(
                "active" => $widgets_status['wah_lights_off_setup'],
                "html"   => 'Lights Off',
                "class"  => $widgets_status['wah_lights_off_setup'] ? "active" : "notactive"
            );
        } else {
            $wah_serialize_widgets = unserialize($wah_serialize_widgets);
            foreach( $wah_serialize_widgets as $serialize_id=>$wah_serialize_data ) {
                if( $serialize_id == "widget-1" ){
                    $active_status = 1;
                    $html = 'Font resize';
                } elseif($serialize_id == "widget-2"){
                    $active_status = $widgets_status['wah_keyboard_navigation_setup'];
                    $html = 'Keyboard navigation';
                } elseif($serialize_id == "widget-3"){
                    $active_status = $widgets_status['wah_readable_fonts_setup'];
                    $html = 'Readable Font';
                } elseif($serialize_id == "widget-4"){
                    $active_status = $widgets_status['contrast_setup'];
                    $html = 'Contrast';
                } elseif($serialize_id == "widget-5"){
                    $active_status = $widgets_status['underline_links_setup'];
                    $html = 'Underline links';
                } elseif($serialize_id == "widget-6"){
                    $active_status = $widgets_status['wah_highlight_links_enable'];
                    $html = 'Highlight links';
                } elseif($serialize_id == "widget-7"){
                    $active_status = 1;
                    $html = 'Clear cookies';
                } elseif($serialize_id == "widget-8"){
                    $active_status = $widgets_status['wah_greyscale_enable'];
                    $html = 'Image Greyscale';
                } elseif($serialize_id == "widget-9"){
                    $active_status = $widgets_status['wah_invert_enable'];
                    $html = 'Invert colors';
                } elseif($serialize_id == "widget-10"){
                    $active_status = $widgets_status['wah_remove_animations_setup'];
                    $html = 'Remove Animations';
                } elseif($serialize_id == "widget-11"){
                    $active_status = $widgets_status['remove_styles_setup'];
                    $html = 'Remove styles';
                } elseif($serialize_id == "widget-12"){
                    $active_status = $widgets_status['wah_lights_off_setup'];
                    $html = 'Lights off';
                }
                $widgetsObject[$serialize_id] = array(
                    "active" => $active_status,
                    "html"   => $html,
                    "class"  => $active_status ? "active" : "notactive"
                );
            }
        }
        $serialize_data = serialize($widgetsObject);
        update_option('wah_sidebar_widgets_order', $serialize_data);
    }
    
  }

  if (!function_exists('render_select_element')) {
    // Select element
    function render_select_element($label, $option, $id){
        $font_resize_options = array(
            "rem"       => __("REM units resize","wp-accessibility-helper"),
            "zoom"      => __("Zoom in/out page","wp-accessibility-helper"),
            "script"    => __("Script base resize","wp-accessibility-helper")
        );
    ?>
        <div class="form_row">
            <div class="form30">
                <label for="<?php echo $id; ?>" class="text_label"><?php echo $label; ?></label>
            </div>
            <div class="form70">
                <select name="<?php echo $id; ?>" id="<?php echo $id; ?>">
                    <?php foreach( $font_resize_options as $key=>$value ): ?>
                        <option value="<?php echo $key; ?>" <?php if( $option == $key ) : ?>selected="selected"<?php endif; ?>>
                            <?php echo $value; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    <?php }
    
  }

  if (!function_exists('render_switch_element')) {
    //Switch element
    function render_switch_element($label, $option, $id, $on = 'On', $off = 'Off'){ ?>
        <div class="form_row">
            <div class="form30">
                <label for="<?php echo $id; ?>" class="text_label"><?php echo $label; ?></label>
            </div>
            <div class="form70">
                <label class="switch">
                    <input class="switch-input"  name="<?php echo $id; ?>" id="<?php echo $id; ?>"  type="checkbox" value="<?php echo $option; ?>" <?php if($option == 1): ?>checked<?php endif; ?> />
                    <span class="switch-label" data-on="<?php echo $on; ?>" data-off="<?php echo $off; ?>"></span>
                    <span class="switch-handle"></span>
                </label>
            </div>
        </div>
    <?php }
  }

  if (!function_exists('render_title_element')) {
    //Form title element
    function render_title_element($label, $option, $id, $placeholder = '', $depid = ''){ ?>
        <div class="form_row" <?php if($depid) : ?>data-depid="<?php echo $depid; ?>"<?php endif; ?>>
            <div class="form30">
                <label for="<?php echo $id; ?>" class="text_label"><?php echo $label; ?></label>
            </div>
            <div class="form70">
                <input type="text" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo $option; ?>" placeholder="<?php echo $placeholder; ?>" />
            </div>
        </div>
    <?php } 
  }

  if (!function_exists('render_form_section_title')) {
    //Form section title
    function render_form_section_title($label){ ?>
        <h3 class="form_element_header">
            <button type="button" title="<?php echo $label; ?>"><?php echo $label; ?></button>
            <span aria-hidden="true" class="toggle-wah-section">
                <span class="dashicons dashicons-arrow-down-alt2"></span>
            </span>
        </h3>
    <?php }
  }

  if (!function_exists('render_logo_position')) {
    //Logo position
    function render_logo_position($label,$wah_logo_top, $wah_logo_right, $wah_logo_bottom, $wah_logo_left){ ?>
        <div class="form_row" data-depid="wah_custom_logo_position">
            <div class="form30">
                  <label for="upload_icon" class="text_label"><?php echo $label; ?></label>
            </div>
            <div class="form70">
                <div class="wah-logo-controller">
                    <div class="wah-logo-controller-inner">
                    <div class="row top_row">
                        <div class="col-full-width">
                            <div class="logo-input-label">Top</div>
                            <div class="logo-input logo-input-top">
                                <input type="number" name="wah_logo_top" min="-2000" max="2000" value="<?php echo $wah_logo_top; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row middle_row">
                        <div class="col-half">
                            <div class="logo-input-label">Left</div>
                            <div class="logo-input logo-input-left">
                                <input type="number" name="wah_logo_left" min="-2000" max="2000" value="<?php echo $wah_logo_left; ?>">
                            </div>
                        </div>
                        <div class="col-half">
                            <div class="logo-input-label">Right</div>
                            <div class="logo-input logo-input-right">
                                <input type="number" name="wah_logo_right" min="-2000" max="2000" value="<?php echo $wah_logo_right; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row bottom_row">
                        <div class="col-full-width">
                            <div class="logo-input-label">Bottom</div>
                            <div class="logo-input logo-input-bottom">
                                <input type="number" name="wah_logo_bottom" min="-2000" max="2000" value="<?php echo $wah_logo_bottom; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    <?php }
    
  }

  if (!function_exists('render_wah_header_notice')) {
    //WAH Header notice
    function render_wah_header_notice() { ?>
        <div class="wah_admin_header">
            <div class="wah_admin_header_inner">
                <div class="wah_admin_header_overlay"></div>
                <div class="wah_admin_header_content">
                    <h2>WP Accessibility Helper <span>by Alex Volkov</span></h2>
                    <hr />
                    <p>Author: <a href="http://volkov.co.il/" target="_blank">Alexander Volkov</a></p>
                    <p>Official website: <a href="http://accessibility-helper.co.il" target="_blank">http://accessibility-helper.co.il</a></p>
                    <p>Email: accessibility.helper@gmail.com</p>                
                    <p><strong style="color:#31f95f;">Get PRO version?</strong> <a href="http://accessibility-helper.co.il/pro/" target="_blank">GO PRO!</a></p>
                    <p>Support forum: <a href="https://wordpress.org/support/plugin/wp-accessibility-helper" target="_blank">Forum</a></p>
                    <p>Rate us here: <a href="https://wordpress.org/support/plugin/wp-accessibility-helper/reviews/?rate=5#new-post" style="text-decoration:none;" target="_blank">
                    <?php for($i=1;$i<=5;$i++): ?>
                        <span class="dashicons dashicons-star-filled"></span>
                    <?php endfor; ?>
                    </a></p>
                    <p>License: <a href="https://www.gnu.org/licenses/gpl-2.0.html">GPL-2.0</a></p>
                    <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BLMSYWA9YW8C2"
                        target="_blank" class="donate-button" title="Donate here!">Donate here!</a>
                </div>
                <div class="wah_admin_header_share">
                    <ul>
                        <li>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=https%3A//wordpress.org/plugins/wp-accessibility-helper/" title="Share on Facebook" class="wah-facebook-share" target="_blank"></a>
                        </li>
                        <li>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=https%3A//wordpress.org/plugins/wp-accessibility-helper/&title=WP%20Accessibility%20Helper&summary=&source=" title="Share on LinkedIn" class="wah-linkedin-share" target="_blank"></a>
                        </li>
                        <li>
                            <a href="https://twitter.com/home?status=WP%20Accessibility%20Helper%20-%20https%3A//wordpress.org/plugins/wp-accessibility-helper/" title="Share on Twitter" class="wah-twitter-share" target="_blank"></a>
                        </li>
                        <li>
                            <a href="https://plus.google.com/share?url=https%3A//wordpress.org/plugins/wp-accessibility-helper/" title="Share on Google plus" class="wah-gplus-share" target="_blank"></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    <?php }
  }
