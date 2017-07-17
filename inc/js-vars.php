<?php
    $role_links_setup = get_option('wah_role_links_setup');
    $remove_link_titles = get_option('wah_remove_link_titles');
    $header_element_selector = get_option('wah_header_element_selector');
    $sidebar_element_selector = get_option('wah_sidebar_element_selector');
    $footer_element_selector = get_option('wah_footer_element_selector');
    $main_element_selector = get_option('wah_main_element_selector');
    $nav_element_selector = get_option('wah_nav_element_selector');
    $lights_off_selector = get_option('wah_lights_selector');
    $wahi = isset($_GET['wahi']) ? base64_decode($_GET['wahi']) : '';
    $wahl = isset($_GET['wahl']) ? base64_decode($_GET['wahl']) : '';
?>

<script type="text/javascript">
    <?php if($role_links_setup): ?>var roleLink = 1;<?php endif; ?>
    <?php if($remove_link_titles): ?>var removeLinkTitles = 1;<?php endif; ?>
    <?php if($header_element_selector):?>var headerElementSelector = '<?php echo $header_element_selector; ?>';<?php endif; ?>
    <?php if($sidebar_element_selector):?>var sidebarElementSelector = '<?php echo $sidebar_element_selector; ?>';<?php endif; ?>
    <?php if($footer_element_selector):?>var footerElementSelector = '<?php echo $footer_element_selector; ?>';<?php endif; ?>
    <?php if($main_element_selector):?>var mainElementSelector = '<?php echo $main_element_selector; ?>';<?php endif; ?>
    <?php if($nav_element_selector):?>var navElementSelector = '<?php echo $nav_element_selector; ?>';<?php endif; ?>
    <?php if($wahi): ?>var wah_target_src = '<?php echo $wahi; ?>';<?php endif; ?>
    <?php if($wahl): ?>var wah_target_link = '<?php echo $wahl; ?>';<?php endif; ?>
    <?php if($lights_off_selector): ?>var wah_lights_off_selector = '<?php echo $lights_off_selector; ?>';<?php endif; ?>
</script>
