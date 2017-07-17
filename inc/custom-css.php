<?php
$wah_hide_on_mobile = get_option('wah_hide_on_mobile');
$wah_custom_css     = get_option('wah_custom_css');
if( $wah_hide_on_mobile || $wah_custom_css ): ?><style><?php endif; ?>
    <?php if( $wah_hide_on_mobile ) : ?>
        @media only screen and (max-width: 480px) {div#wp_access_helper_container {display: none;}}
    <?php endif;
    if( $wah_custom_css ) {
        echo $wah_custom_css;
    } ?>
<?php if($wah_hide_on_mobile || $wah_custom_css): ?></style><?php endif; ?>
