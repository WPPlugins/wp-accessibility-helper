<style>
xmp {
    word-break: break-all;
    white-space: normal;
    margin: 0;
    background: #191818;
    color: #FFF;
    font-size: 11px;
    padding: 2px;
    line-height: 1.2;
}
#wah_scanner_results {
    width: 100%;
    display: inline-block;
    float: left;
    margin-top: 1rem;
}
.widefat .check-column {
    font-weight: bold !important;
    padding: 5px !important;
    width: auto;
}
.form_row:after,
.form_row:before {
    content: ' ';
    display: block;
    clear: both;
}
.wah_scanner_thumbnail img {
    width: 50px !important;
    max-width: 50px !important;
    height: 50px !important;
}
.wah_scanner_table {
    display: none;
    margin-top: 10px;
}
.wah_scanner_table_trigger {
    cursor:pointer;
    margin: 0;
}
.wah_scanner_table_trigger span {
    width: 0;
    height: 0;
    display: inline-block;
    margin-right: 8px;
    border-style: solid;
    border-width: 5px 0 6px 9px;
    border-color: transparent transparent transparent #236478;
}
.wah_scanner_table_trigger.active span {
    border-width: 11px 7.5px 0 7.5px;
    border-color: #236478 transparent transparent transparent;
}
.not-valid {
    color: red;
    font-weight: bold;
}
.warning {
    color: #ff4b00;
    font-weight: bold;
}
.valid {
    color: green;
    font-weight: bold;
}
.beta {
    font-weight: bold;
    font-size: 11px;
    color: #e6ff6d;
    position: relative;
    left: 10px;
    -moz-transform: scale(1) rotate(-45deg);
    -webkit-transform: scale(1) rotate(-45deg);
    -o-transform: scale(1) rotate(-45deg);
    -ms-transform: scale(1) rotate(-45deg);
    transform: scale(1) rotate(-45deg);
    display: inline-block;
    top: -5px;
}
.rtl .beta {
    left:-5px;
}
.server_information {
    background: #ccc;
    padding: 10px;
    border: 1px solid black;
}
</style>

<div class="wrap">

    <h1 style="background: #236478;color: #FFF;padding: 10px;line-height: 1;">
        <?php _e("WAH - DOM Scanner","wp-accessibility-helper"); ?><span class="beta">beta</span>
    </h1>
    
    <?php render_wah_header_notice(); ?>

    <div class="form_row">
        <h3><?php _e("WAH DOM Scanner","wp-accessibility-helper"); ?></h3>
        <p><?php _e("<p>DOM Scanner allows you to scan (analyze) your web pages for WCAG errors. For example, use ALT tags in the pictures, using the 'title' and 'aria-label' in the link.</p><p>This plugin feature is in beta stage of development, so we are very grateful to you if you will inform our team about all the bugs and 'strangeness'.</p>","wp-accessibility-helper"); ?></p>
        <p><?php _e("'allow_url_fopen' on your server must to be enabled.","wp-accessibility-helper"); ?></p>
    </div>

    <?php if( ini_get('allow_url_fopen') ) : ?>

    <div class="form_row">
        <div class="server_information">
            <p><strong><?php _e("Server information:","wp-accessibility-helper"); ?></strong></p>
            <ol>
                <li>'allow_url_fopen' is <strong>enabled</strong> on your server.</li>
                <li>Current PHP version: <strong><?php echo phpversion(); ?></strong></li>
            </ol>
        </div>
    </div>

    <div class="form_row">

        <div id="fountainG">
            <?php for($i=1;$i<=8;$i++): ?>
                <div id="fountainG_<?php echo $i; ?>" class="fountainG"></div>
            <?php endfor; ?>
        </div>

        <?php
            $all_pages = get_pages();
            $all_posts = get_posts(array('posts_per_page'=>-1));
        ?>
        <select name="wah_scanner_selector" id="wah_scanner_selector">
            <option value=""><?php _e("Select Page or Post","wp-accessibility-helper"); ?></option>
            <?php if($all_pages): ?>
                <optgroup label="<?php _e("Page:","wp-accessibility-helper"); ?>">
                    <?php foreach($all_pages as $option_page): ?>
                        <option value="<?php echo $option_page->ID; ?>"><?php echo $option_page->post_title; ?></option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endif; ?>
            <?php if($all_posts): ?>
                <optgroup label="<?php _e("Post:","wp-accessibility-helper"); ?>">
                    <?php foreach($all_posts as $option_post): ?>
                        <option value="<?php echo $option_post->ID; ?>"><?php echo $option_post->post_title; ?></option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endif; ?>
        </select>


        <button class="button button-default" id="wah_scanner">
            <?php _e("Start WAH Scanner","wp-accessibility-helper"); ?>
        </button>

        <div id="wah_scanner_results"></div>

    </div>

    <?php else: ?>
        <div class="form_row">
            <div class="server_information">
                <h3>'allow_url_fopen' is disabled. Please, contact your hosting provider.</h3>
            </div>
        </div>
    <?php endif; ?>

</div>
