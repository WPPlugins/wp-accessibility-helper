<?php
add_action( 'wp_ajax_update_attachment_title', 'update_attachment_title' );
function update_attachment_title() {

	$result = array();
	$pid    = isset($_POST['pid']) ? sanitize_text_field($_POST['pid']):       '';
	$ptitle = isset($_POST['ptitle']) ? sanitize_text_field($_POST['ptitle']): '';

	if($pid){
		$result['plink'] = get_permalink($pid);
		$result['pid']   = $pid;
		if($ptitle){
			$attachment_post = array(
				'ID'           => $pid,
				'post_title'   => $ptitle,
			);
			wp_update_post( $attachment_post );
			$result['ptitle']   = $ptitle;
		}
		echo json_encode( $result );
	}
	die();
}

add_action( 'wp_ajax_update_attachment_alt', 'update_attachment_alt' );
function update_attachment_alt() {

	$result = array();
	$pid    = isset($_POST['pid']) ? sanitize_text_field($_POST['pid']):       '';
	$palt   = isset($_POST['palt']) ? sanitize_text_field($_POST['palt']): '';

	if($pid){

		$result['plink'] = get_permalink($pid);
		$result['pid']   = $pid;

		if($palt){
			update_post_meta($pid, '_wp_attachment_image_alt', $palt);
			$alt            = get_post_meta($post->ID, '_wp_attachment_image_alt', true);
			$result['palt'] = $alt;
		}

		echo json_encode( $result );

	}

	die();
}


//WAH SCANNER
add_action('wp_ajax_wah_scan_homepage','wah_scan_homepage');
function wah_scan_homepage(){

	$result   = array();
	$postID   = isset($_POST['postID']) ? sanitize_text_field($_POST['postID']) : '';
	$url      = get_permalink($postID);

	$response      = wp_remote_get( $url, array('timeout' => 20) );
	$response_code = wp_remote_retrieve_response_code( $response );
	$body          = isset($response) ? $response['body']:   '';

	if( $body && $postID && $response_code == 200 ){

		$scanner_array = array();

		//get all images
		preg_match_all('/<img[^>]+>/i',$body, $images);
		$scanner_array['images'] = $images[0];

		//get all links
		$regexp_links = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
		if(preg_match_all("/$regexp_links/siU", $body, $links)) {
			$scanner_array['links'] = $links[0];
		}

		if($scanner_array['images']){
			ob_start();
			?>

			<div class="form_row">
				<h3 class="wah_scanner_table_trigger">
					<span></span><?php _e("Images Report Table","wp-accessibility-helper"); ?>
				</h3>
				<table class="widefat fixed wah_scanner_table" cellspacing="5">
					<thead>
						<tr>
							<th class="manage-column column-cb check-column wah_th" scope="col" style="width:130px !important;">
								<?php _e("Thumbnail","wp-accessibility-helper"); ?>
							</th>
							<th class="manage-column image_src">
								<?php _e("Image Source","wp-accessibility-helper"); ?>
							</th>
							<th class="manage-column column-cb check-column wah_th" scope="col">
								<?php _e("Alt","wp-accessibility-helper"); ?>
							</th>
							<th class="manage-column column-cb check-column wah_th" scope="col">
								<?php _e("Width","wp-accessibility-helper"); ?>
							</th>
							<th class="manage-column column-cb check-column wah_th" scope="col">
								<?php _e("Height","wp-accessibility-helper"); ?>
							</th>
							<th class="manage-column column-cb check-column wah_th" scope="col">
								<?php _e("Action","wp-accessibility-helper"); ?>
							</th>
							<th class="manage-column column-cb check-column wah_th" scope="col">
								<?php _e("Preview","wp-accessibility-helper"); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($scanner_array['images'] as $key=>$image_html):

							preg_match( '@src="([^"]+)"@' , $image_html, $match_src );
							$src = array_pop($match_src);

							preg_match( '@alt="([^"]+)"@' , $image_html, $match_alt );
							$alt = array_pop($match_alt);

							preg_match( '@width="([^"]+)"@' , $image_html, $match_width );
							$width = array_pop($match_width);

							preg_match( '@width="([^"]+)"@' , $image_html, $match_height );
							$height = array_pop($match_height);

							$attachment_id = wah_get_attachment_id_by_src($src);

						?>
						<tr>
						<td class="wah_scanner_thumbnail"><?php echo $image_html; ?></td>
						<td>
							<?php echo $src ? '<xmp>'.$src.'</xmp>' : '<span class="not-valid">'.__("not valid","wp-accessibility-helper").'</span>'; ?>
						</td>
						<td>
							<?php echo $alt ? '<span class="valid">'.$alt.'</span>' : '<span class="not-valid">not valid</span>'; ?>
						</td>
						<td>
							<?php echo $width ? '<span class="valid">'.$width.'</span>' : '<span class="warning">X</span>'; ?>
						</td>
						<td>
							<?php echo $height ? '<span class="valid">'.$height.'</span>' : '<span class="warning">X</span>'; ?>
						</td>
						<td>
							<?php if($attachment_id){ ?>
								<a href="<?php echo get_edit_post_link( $attachment_id); ?>" target="_blank">
									<?php _e("Edit image","wp-accessibility-helper"); ?>
								</a>
							<?php } ?>
						</td>
						<td>
							<a target="_blank" href="<?php echo add_query_arg('wahi',base64_encode($src), get_permalink($postID));?>">
								<?php _e("Preview","wp-accessibility-helper"); ?>
							</a>
						</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<?php
			$result['images'] = ob_get_clean();
		}

		if( $scanner_array['links'] ) {
			ob_start();
		?>

		<div class="form_row">
			<h3 class="wah_scanner_table_trigger">
				<span></span><?php _e("Links Report Table","wp-accessibility-helper"); ?>
			</h3>
			<table class="widefat fixed wah_scanner_table" cellspacing="5">
				<thead>
					<tr>
						<th class="manage-column column-cb check-column wah_th" scope="col" style="width:26px;">
							<?php _e("ID","wp-accessibility-helper"); ?>
						</th>
						<th class="manage-column column-cb check-column wah_th" scope="col">
							<?php _e("Source","wp-accessibility-helper"); ?>
						</th>
						<th class="manage-column column-cb check-column wah_th" scope="col">
							<?php _e("URL","wp-accessibility-helper"); ?>
						</th>
						<th class="manage-column column-cb check-column wah_th" scope="col">
							<?php _e("Title","wp-accessibility-helper"); ?>
						</th>
						<th class="manage-column column-cb check-column wah_th" scope="col">
							<?php _e("Aria-label","wp-accessibility-helper"); ?>
						</th>
						<th class="manage-column column-cb check-column wah_th" scope="col">
							<?php _e("Preview","wp-accessibility-helper"); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$link_html_counter = 0;
					foreach($scanner_array['links'] as $key=>$link_html):
						$link_html_counter++;
						preg_match( '@href="([^"]+)"@' , $link_html, $match_href );
						$href = array_pop($match_href);

						preg_match( '@title="([^"]+)"@' , $link_html, $match_title );
						$title = array_pop($match_title);

						preg_match( '@aria-label="([^"]+)"@' , $link_html, $match_aria_label );
						$aria_label = array_pop($match_aria_label);
					?>
						<tr>
							<td><?php echo $link_html_counter; ?></td>
							<td class="wah_scanner_thumbnail">
								<xmp><?php echo $link_html; ?></xmp>
							</td>
							<td>
								<?php
								if($href && $href !="#") {
									echo $href;
								} elseif($href && $href =="#") {
									echo '<span class="warning">'.__("empty href","wp-accessibility-helper").'</span>';
								} else {
									echo '<span class="warning">X</span>';
								}
								?>
							</td>
							<td><?php echo $title ? $title : '<span class="warning">X</span>'; ?></td>
							<td><?php echo $aria_label ? $aria_label : '<span class="warning">X</span>'; ?></td>
							<td>
								<a href="<?php echo add_query_arg('wahl',base64_encode($href), get_permalink($postID));?>" target="_blank">
									<?php _e("Preview link","wp-accessibility-helper"); ?>
								</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<?php
			$result['links'] = ob_get_clean();
		}

		$result['response_code'] = $response_code;

		echo json_encode( $result );

	} else {

		$result['response_code'] = $response_code;

		echo json_encode( $result );

	}

	die();

}

function wah_get_attachment_id_by_src($image_url){
	global $wpdb;
	$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ));
	if($attachment){
		return $attachment[0];
	}
}

/**************************************************
*   Update image alt - front scanner
**************************************************/
add_action('wp_ajax_wah_update_image_alt','wah_update_image_alt');
add_action('wp_ajax_nopriv_wah_update_image_alt','wah_update_image_alt');
function wah_update_image_alt(){

    $response = array();
    $attachment_source = isset($_POST['target_src']) ? sanitize_text_field($_POST['target_src']):       '';
	$wah_alt_input     = isset($_POST['wah_alt_input']) ? sanitize_text_field($_POST['wah_alt_input']): '';

    if( $attachment_source ) {

        $attachment_id = get_attachment_id( $attachment_source );

        if($attachment_id && $wah_alt_input){
			update_post_meta($attachment_id, '_wp_attachment_image_alt', $wah_alt_input);
			$response['atid']    = $attachment_id;
            $response['status']  = 'ok';
            $response['message'] = __('Image has been updated.','wp-accessibility-helper');
        } else {
			$response['atid']    = -1;
            $response['status']  = 'error';
            $response['message'] = __('It look likes, this image is not on your server...','wp-accessibility-helper');
        }

        echo json_encode($response);
    }


    die();
}

/*****************************************************
*	Save wah widgets order
*****************************************************/
add_action( 'wp_ajax_wah_update_widgets_order', 'wah_update_widgets_order' );
function wah_update_widgets_order(){

	$response       = '';
	$data           = isset($_POST['alldata']) ? $_POST['alldata']: '';
	$widgets_status = wah_get_widgets_status();

    $widgetsObject = array();
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

	$s_data = array();
	foreach( $data as $id ) {
		$s_data[$id] = $widgetsObject[$id];
	}
	$s_data = serialize($s_data);
	update_option('wah_sidebar_widgets_order', $s_data);
	$response = 'ok';

	echo json_encode($response);
	die();
}

/*****************************************************
*	Add new contrast item from repeater
*****************************************************/
add_action( 'wp_ajax_add_new_contrast_item', 'add_new_contrast_item' );
function add_new_contrast_item(){
	$response = array();
	ob_start();
?>
	<li>
		<div class="contrast-mode-item bg-color">
			<label><?php _e('Background color','wp-accessibility-helper'); ?></label>
			<input type="text" class="jscolor" placeholder="<?php _e('Background color','wp-accessibility-helper'); ?>" />
		</div>
		<div class="contrast-mode-item text-color">
			<label><?php _e('Text color','wp-accessibility-helper'); ?></label>
			<input type="text" class="jscolor" placeholder="<?php _e('Text color','wp-accessibility-helper'); ?>" />
		</div>
		<div class="contrast-mode-item action">
			<button class="wah-button delete-contrast-params">
				<?php _e("Delete","wp-accessibility-helper"); ?>
			</button>
			<span class="action-loader"></span>
		</div>
	</li>
<?php
	$response['status'] = 'ok';
	$response['html'] = ob_get_clean();
	echo json_encode($response);
	die();
}

/*****************************************************
***	Remove contrast item from repeater				***
*****************************************************/
add_action( 'wp_ajax_remove_contrast_item', 'remove_contrast_item' );
function remove_contrast_item(){
	$response = array();
	$response['status'] = 'ok';
	echo json_encode($response);
	die();
}
/***************************************************
**		Save contrast variations				****
****************************************************/
add_action( 'wp_ajax_save_contrast_variations', 'save_contrast_variations' );
function save_contrast_variations(){
	$response = array();
	$alldata  = isset($_POST['alldata']) ? $_POST['alldata']: '';
	if( count($alldata) >= 5 ){
		$response['status'] = 'error';
		$response['message'] = __("Maximum 4 variations. Need more variations? Go PRO!");
		echo json_encode($response);
	} else {
		if($alldata){
			$data = serialize($alldata);
			update_option('wah_contrast_variations',$data);
			$response['status'] = 'ok';
			echo json_encode($response);
		}
	}
	die();
}
/***************************************************
**		Save EMPTY contrast variations			****
****************************************************/
add_action( 'wp_ajax_save_empty_contrast_variations', 'save_empty_contrast_variations' );
function save_empty_contrast_variations(){
	$response = array();
	$alldata  = '';
	update_option('wah_contrast_variations',$alldata);
	$response['status'] = 'ok';
	$response['message'] = __("Removed!","wp-accessibility-helper");
	echo json_encode($response);
	die();
}

/****************************************************
*** 	Get all contrast variations				*****
****************************************************/
function wah_get_contrast_variations(){
	$contrast_variations = get_option('wah_contrast_variations');
	$contrast_variations = unserialize($contrast_variations);
	if($contrast_variations){
		return $contrast_variations;
	}
}
