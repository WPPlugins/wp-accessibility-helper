<?php if( $wah_skiplinks_setup = get_option('wah_skiplinks_setup') ) : ?>
    <nav class="wah-skiplinks-menu">
    <!-- WP Accessibility Helper - Skiplinks Menu -->
    <?php wp_nav_menu( array( 'theme_location' => 'wah_skiplinks', 'container' => '', 'menu_class' => 'wah-skipper' ) ); ?>
    <!-- WP Accessibility Helper - Skiplinks Menu -->
</nav>
<?php endif; ?>
