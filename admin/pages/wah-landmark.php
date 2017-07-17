<?php
  $wah_hidden_landmark = isset($_POST['wah_hidden_landmark']) ? sanitize_text_field($_POST['wah_hidden_landmark']) : '';

  if( $wah_hidden_landmark == 'Y' && !empty($wah_hidden_landmark) ) {

    $header_element_selector = isset($_POST['wah_header_element_selector']) ? $_POST['wah_header_element_selector'] : '';
        update_option('wah_header_element_selector', $header_element_selector);

    $sidebar_element_selector = isset($_POST['wah_sidebar_element_selector']) ? $_POST['wah_sidebar_element_selector'] : '';
        update_option('wah_sidebar_element_selector', $sidebar_element_selector);

    $footer_element_selector = isset($_POST['wah_footer_element_selector']) ? $_POST['wah_footer_element_selector'] : '';
        update_option('wah_footer_element_selector', $footer_element_selector);

    $main_element_selector = isset($_POST['wah_main_element_selector']) ? $_POST['wah_main_element_selector'] : '';
        update_option('wah_main_element_selector', $main_element_selector);

    $nav_element_selector = isset($_POST['wah_nav_element_selector']) ? $_POST['wah_nav_element_selector'] : '';
        update_option('wah_nav_element_selector', $nav_element_selector);

    $wah_custom_css = isset($_POST['wah_custom_css']) ? $_POST['wah_custom_css'] : '';
        update_option('wah_custom_css', $wah_custom_css);

?>
      <div class="updated"><p><strong><?php _e('Options saved.','wp-accessibility-helper'); ?></strong></p></div>
<?php
  } else {

    $header_element_selector  = get_option('wah_header_element_selector');
    $sidebar_element_selector = get_option('wah_sidebar_element_selector');
    $footer_element_selector  = get_option('wah_footer_element_selector');
    $main_element_selector    = get_option('wah_main_element_selector');
    $nav_element_selector     = get_option('wah_nav_element_selector');
    $wah_custom_css           = get_option('wah_custom_css');
  }
?>

<div class="wrap">
  <div class="element_row">
    <h1><?php _e("Landmark Control","wp-accessibility-helper"); ?></h1>
    <?php render_wah_header_notice(); ?>

    <h2><?php _e("ARIA and HTML5","wp-accessibility-helper"); ?></h2>
    <p>
        <?php
            $url = 'https://accessibility.oit.ncsu.edu/training/accessibility-handbook/aria-landmarks.html';
            $link = sprintf( wp_kses( __( 'More information about HTML5 landmarks you can find <a href="%s" target="_blank">HERE</a>.', 'my-text-domain' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( $url ) );
            echo $link;
        ?>
    </p>

    <div class="element_container">
      <table class="accessibility_table">
          <thead>
              <tr>
                  <th scope="col">ARIA Landmark</th>
                  <th scope="col">HTML5 Element</th>
              </tr>
          </thead>
          <tbody>
              <tr>
                  <td>banner</td>
                  <td>&lt;header&gt;</td>
              </tr>
              <tr>
                  <td>complementary</td>
                  <td>&lt;aside&gt;</td>
              </tr>
              <tr>
                  <td>contentinfo</td>
                  <td>generic &lt;div&gt; acting as the footer</td>
              </tr>
              <tr>
                  <td>form</td>
                  <td>&lt;form&gt; or generic &lt;div&gt;</td>
              </tr>
              <tr>
                  <td>main</td>
                  <td>&lt;main&gt;</td>
              </tr>
              <tr>
                  <td>navigation</td>
                  <td>&lt;nav&gt;</td>
              </tr>
              <tr>
                  <td>search</td>
                  <td>&lt;form&gt; or generic &lt;div&gt;</td>
              </tr>
              <tr>
                  <td>application</td>
                  <td>generic &lt;div&gt;</td>
              </tr>
          </tbody>
      </table>
    </div>
  </div>
    <div class="element_row">
        <h2><?php _e("Control HTML5 elements selectors","wp-accessibility-helper"); ?></h2>
        <div class="element_container">
            <form name="oscimp_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                <input type="hidden" name="wah_hidden_landmark" value="Y">

                <p>
                  <label for="header_element_selector" class="text_label">
                      <?php _e("Header element selector","wp-accessibility-helper"); ?>
                  </label>
                  <input type="text" name="wah_header_element_selector"
                      id="header_element_selector"
                      value="<?php echo $header_element_selector; ?>" />
                </p>

                <p>
                  <label for="sidebar_element_selector" class="text_label">
                      <?php _e("Sidebar element selector","wp-accessibility-helper"); ?>
                  </label>
                  <input type="text" name="wah_sidebar_element_selector"
                      id="sidebar_element_selector"
                      value="<?php echo $sidebar_element_selector; ?>" />
                </p>

                <p>
                  <label for="footer_element_selector" class="text_label">
                      <?php _e("Footer element selector","wp-accessibility-helper"); ?>
                  </label>
                  <input type="text" name="wah_footer_element_selector"
                      id="footer_element_selector"
                      value="<?php echo $footer_element_selector; ?>" />
                </p>

                <p>
                  <label for="main_element_selector" class="text_label">
                      <?php _e("Main element selector","wp-accessibility-helper"); ?>
                  </label>
                  <input type="text" name="wah_main_element_selector"
                      id="main_element_selector"
                      value="<?php echo $main_element_selector; ?>" />
                </p>

                <p>
                  <label for="nav_element_selector" class="text_label">
                      <?php _e("Navigation element selector","wp-accessibility-helper"); ?>
                  </label>
                  <input type="text" name="wah_nav_element_selector"
                      id="nav_element_selector"
                      value="<?php echo $nav_element_selector; ?>" />
                </p>
                <h3><?php _e("Custom CSS","wp-accessibility-helper"); ?></h3>
                <p>
                    <textarea name="wah_custom_css" style="width:60%; height:200px;"
                        id="wah_custom_css"><?php echo $wah_custom_css; ?></textarea>
                </p>
                <p class="submit">
                  <input type="submit" name="Submit"
                    class="button button-primary button-large"
                    value="<?php _e('Update Options', 'wp-accessibility-helper' ) ?>" />
                </p>
            </form>
        </div>
    </div>
</div>
