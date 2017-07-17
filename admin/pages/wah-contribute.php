<?php
  $wah_hidden_stats = isset($_POST['wah_hidden_stats']) ? sanitize_text_field($_POST['wah_hidden_stats']) : '';
  if( $wah_hidden_stats == 'Y' && !empty($wah_hidden_stats) ) {
    $wah_stats = isset($_POST['wah_stats']) ? 1 : 0;
        update_option('wah_stats', $wah_stats);
    if($wah_stats == 1){
        stats_collector();
    }

    $wah_author_credits = isset($_POST['wah_author_credits']) ? 1 : 0;
        update_option('wah_author_credits', $wah_author_credits);
?>
    <div class="updated"><p><strong><?php _e('Options saved.','wp-accessibility-helper'); ?></strong></p></div>
<?php
  } else {
    $wah_stats = get_option('wah_stats');
    $wah_author_credits = get_option('wah_author_credits');
  }
?>

<div class="wrap">

    <?php echo "<h1>" . __( 'WAH - Contribute', 'wp-accessibility-helper' ) . "</h1>"; ?>
    <?php render_wah_header_notice(); ?>
    
    <form name="oscimp_form" class="clearfix" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
        <input type="hidden" name="wah_hidden_stats" value="Y">

            <br />
            <?php render_switch_element(__("Show author credits?","wp-accessibility-helper"),$wah_author_credits,"wah_author_credits"); ?>
            <hr />

            <div class="form_row">
                <p><?php _e("Hello dear friend!","wp-accessibility-helper"); ?></p>
                <p><?php _e("It is not a secret that creating a new template for Wordpress demands significant effort, time and sometimes even money (imagine that!). Please help us make an even more friendly and secure product for you, which you and other users will be able to enjoy absolutely for free.","wp-accessibility-helper"); ?></p>
                <p><?php _e("Would you agree to share with us several details about your site listed below?","wp-accessibility-helper"); ?></p>
                <ol>
                    <li><?php _e("Wordpress version + theme name","wp-accessibility-helper"); ?></li>
                    <li><?php _e("PHP version - for better debugging","wp-accessibility-helper"); ?></li>
                    <li><?php _e("Your IP address - for better translations","wp-accessibility-helper"); ?></li>
                    <li><?php _e("Website admin email","wp-accessibility-helper"); ?></li>
                    <li><?php _e("Website language & url address","wp-accessibility-helper"); ?></li>
                </ol>

                <p><?php _e("If your answer is yes, please tick the checkbox below, this would really help us in further development of the plugin.
                We guarantee that your information will only be used for this internal purpose and will never be given to any third parties.","wp-accessibility-helper"); ?></p>
                <p><?php _e("Thank you in advance for your understanding and contribution!","wp-accessibility-helper"); ?></p>
                <p><?php _e("Cinserely,","wp-accessibility-helper"); ?></p>
                <p><?php _e("- WP Accessibility Helper Team","wp-accessibility-helper"); ?></p>
            </div>

            <?php render_switch_element(__("Agree","wp-accessibility-helper"),$wah_stats,"wah_stats"); ?>

            <p class="submit">
              <input type="submit" name="Submit" class="button button-primary button-large" value="<?php _e('Update Options', 'wp-accessibility-helper' ) ?>" />
            </p>

    </form>

</div>
