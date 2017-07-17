/**************************
*** Define all elements ***
***************************/
function WahPopup(){
    this.template = '<div class="wah_dark_overlay"></div>'+
    '<div class="wah_popup_wrapper">'+
        '<div class="wah_popup_header"></div>'+
        '<div class="wah_popup_form">'+
            '<button class="wah_close_popup">[X]</button>'+
            '<form name="wah_update_image_alt">'+
                '<label for="wah_alt_input">Enter ALT:</label>'+
                '<input type="text" id="wah_alt_input">'+
                '<div class="wah_form_error">Please put some value. Minimum 3 characters.</div>'+
                '<button id="wah_alt_input_submit" data-source="">Save</button>'+
                '<div class="wah_ajax_loader"></div>'+
            '</form>'+
            '<div class="wah_popup_response_message"></div>'+
        '</div>'+
    '</div>';

    this.openPopup = function(){
        jQuery("body").append(this.template);
    },
    this.closePopup = function(){
        jQuery(".wah_dark_overlay, .wah_popup_wrapper").fadeOut().remove();
    },
    this.init = function(){
        console.log("popup loaded and ready to be called");
    };

    // this.prototype.sayHello = function() {
    //   console.log("Hello, I'm prototype function");
    // };

}
var wahPopup = new WahPopup();

var wah_udpate_image_button = '<button class="notifyer notifyer-right wah_image_update wah_button">Update ALT</button>';
var total_links = jQuery("a:not(.wahout)").length;
var total_links_buttons = '';
if(total_links && total_links > 0){
    total_links_buttons = '(<a href="#" class="wahout highlight_links">Highlight</a> | <a href="#" class="wahout check_links">Check</a>)';
}
var total_images = jQuery("img").length;
var total_images_buttons = '';
if(total_images && total_images > 0){
    total_images_buttons = '(<a href="#" class="wahout highlight_images">Highlight</a> | <a href="#" class="wahout check_images">Check</a>)';
}
var total_forms = jQuery("form").length;
var total_forms_buttons = '';
if(total_forms && total_forms > 0){
    total_forms_buttons = '(<a href="#" class="wahout highlight_forms">Highlight</a> | <a href="#" class="wahout check_forms">Check</a>)</li>';
}

var wah_analyzer_sidebar = '<div id="wah_analyzer_sidebar">'+
    '<div class="wah_analyzer_sidebar_toggle">[x]</div>'+
    '<div class="wah_analyzer_sidebar_title"><h4>WP Accessibility Helper</h4><h5>Accessibility made easy!</h5></div>'+
    '<div class="wah_analyzer_line"></div>'+
    '<ul>'+
        '<li>Links: '+total_links+' '+total_links_buttons+'</li>'+
        '<li>Images: '+total_images+' '+total_images_buttons+'</li>'+
        '<li>Forms: '+total_forms+' '+ total_forms_buttons+'</li>'+
    '</ul>'+
    '<div class="wah_analyzer_line"></div>'+
    '<div class="wah_errors_list">'+
        '<ul>'+
            '<li class="wah_color_type"><a href="#" data-errortype="good" class="wah_error_type wahout"><span class="wah-good"></span>Good (feature)</a></li>'+
            '<li class="wah_color_type"><a href="#" data-errortype="warning" class="wah_error_type wahout"><span class="wah-warning"></span>Warning (alert)</a></li>'+
            '<li class="wah_color_type"><a href="#" data-errortype="error" class="wah_error_type wahout"><span class="wah-error"></span>Error (fix it now)</a></li>'+
        '</ul>'+
    '</div>'+
    '<div class="wah_analyzer_sidebar_footer">Created by <a href="http://volkov.co.il" target="_blank">A.Volkov</a></div>'+
'<div>';

jQuery(document).ready(function(){

    //Wrap all images
    jQuery("img").each(function(){
        jQuery(this).wrap("<span class='wah_image_wrapper'></span>");
    });

    //Forms control
    jQuery("form").each(function(){
        jQuery(this).addClass("wah_analyzer_form");
    });

    jQuery("body").append(wah_analyzer_sidebar);

    /********* Links checker *********/
    jQuery(".highlight_links").click(function(e){
        e.preventDefault();
        jQuery("a").each(function(){
            wah_highlight_this_link(jQuery(this));
        });
    });
    jQuery(".check_links").click(function(e){
        e.preventDefault();
        var check_list_toggle = jQuery(this);

        if( !check_list_toggle.hasClass('active') ) {
            check_list_toggle.addClass('active');
            jQuery("a").each(function(){
                wah_check_this_link(jQuery(this));
            });
        } else {
            check_list_toggle.removeClass('active');
            wah_remove_notifyer();
        }
    });
    /********* Images checker *********/
    jQuery(".highlight_images").click(function(e){
        e.preventDefault();
        jQuery("img").each(function(){
            wah_highlight_this_image(jQuery(this));
        });
    });
    jQuery(".check_images").click(function(e){
        e.preventDefault();
        var check_images_toggle = jQuery(this);

        if( !check_images_toggle.hasClass('active') ) {
            check_images_toggle.addClass('active');
            jQuery("img").each(function(){
                wah_check_this_image( jQuery(this) );
            });
        } else {
            check_images_toggle.removeClass('active');
            jQuery("img").removeClass("highlighted_error");
            wah_remove_notifyer();
        }
    });
    /********* Forms checker *********/
    jQuery(".highlight_forms").click(function(e){
        e.preventDefault();
        jQuery("form").each(function(){
            wah_highlight_this_form(jQuery(this));
        });
    });
    jQuery(".check_forms").click(function(e){
        e.preventDefault();
        var check_forms_toggle = jQuery(this);
        if( !check_forms_toggle.hasClass('active') ) {
            check_forms_toggle.addClass('active');
            jQuery("form").each(function(){
                wah_check_this_form(jQuery(this));
            });
        } else {
            check_forms_toggle.removeClass('active');
            wah_remove_notifyer();
        }
    });


    jQuery(".wah_analyzer_sidebar_toggle").click(function(e){
        e.preventDefault();
        jQuery(this).toggleClass('closed');
        jQuery("#wah_analyzer_sidebar").toggleClass('closed');
    });

    jQuery("body").on("click",".wah_error_type", function(e){
        e.preventDefault();
        if(!jQuery(this).hasClass("active")){
            jQuery(".wah_error_type").removeClass("active");
            jQuery(this).addClass("active");
            wah_filter_results_by_type(jQuery(this).data("errortype"));
        } else {
            jQuery(this).removeClass("active");
            wah_unfilter_results_by_type(jQuery(this).data("errortype"));
        }
    });

    jQuery("body").on("click",".wah_image_update", function(e){
        e.preventDefault();
        var source = jQuery(this).parents(".wah_image_wrapper").find("img").attr("src");
        wah_generate_popup(source);
    });
    //Submit alt for image
    jQuery("body").on("click","#wah_alt_input_submit", function(e){
        e.preventDefault();
        var alt_input_value = jQuery("#wah_alt_input").val();
        if( alt_input_value && wah_alt_input !==' ' && alt_input_value.length > 2 ){
            jQuery(".wah_form_error").fadeOut();

            var target_src    = jQuery(this).data("source");
            var wah_alt_input = jQuery('#wah_alt_input').val();

            wah_show_ajax_loader();
            if(target_src){
                jQuery.ajax({
                    type        : "post",
                    dataType    : "json",
                    url         : ajax.ajaxurl,
                    data        : {
                        action          : "wah_update_image_alt",
                        target_src      : target_src,
                        wah_alt_input   : wah_alt_input
                    },
                    success: function(response) {
                        if(response.status == "ok") {
                            wah_hide_ajax_loader();
                            jQuery(".wah_popup_response_message").html('<span class="good">'+response.message+'</span>');
                        } else {
                            jQuery(".wah_popup_response_message").html(response.message);
                        }
                    }
                });
            }
        } else {
            jQuery(".wah_form_error").fadeIn();
        }
    });
    //Close popup
    jQuery("body").on("click",".wah_close_popup", function(e){
        e.preventDefault();
        wahPopup.closePopup();
    });
});

function wah_check_this_link(element) {
    if( !element.hasClass('wahout') ){
        var href_attr       = element.attr('href');
        var role_attr       = element.attr('role');
        var title_attr      = element.attr('title');
        var aria_label_attr = element.attr('aria-label');
        var link_text       = element.text();
        //check role
        if( typeof role_attr !== typeof undefined && role_attr !== false ){
            element.append('<div class="notifyer wah-good">role="'+role_attr+'"</div>');
        }
        //check aria-label
        if( typeof aria_label_attr !== typeof undefined && aria_label_attr !== false ){
            element.append('<div class="notifyer wah-good">aria-label</div>');
        }
        //check title
        if( typeof title_attr !== typeof undefined && title_attr !== false ){
            element.append('<div class="notifyer wah-good">title</div>');
        } else {
            element.append('<div class="notifyer wah-warning">no title</div>');
        }
        //check href
        if( typeof href_attr !== typeof undefined && href_attr == '#' ){
            element.append('<div class="notifyer wah-warning">url</div>');
        }
        //check text
        if( link_text === '' ){
            element.append('<div class="notifyer wah-error">no text</div>');
        }
    }
}
function wah_check_this_image(element){
    var image_alt = element.attr('alt');
    if( typeof image_alt == 'undefined' && image_alt == false || !image_alt ){
        element.parent().append('<div class="notifyer notifyer-left wah-error">no alt</div>');
        element.parent().append(wah_udpate_image_button);
        element.addClass('highlighted_error');
    }
}
function wah_check_this_form(element){
    var input_labels = element.find('label');
    if(input_labels && typeof input_labels !== 'undefined'){
        jQuery(input_labels).each(function(){
            jQuery(this).append('<div class="notifyer wah-good">label</div>');
        });
    }
}
/* Highlight */
function wah_highlight_this_link(element) {
    if( !element.hasClass('wahout') ){
        element.toggleClass("highlighted");
    }
}
function wah_highlight_this_form(element) {
    if( !element.hasClass('wahout') ){
        element.toggleClass("highlighted");
    }
}
function wah_highlight_this_image(element) {
    if( !element.hasClass('wahout') ){
        element.toggleClass("highlighted");
    }
}
/* Fitler */
function wah_filter_results_by_type(type){
    jQuery(".notifyer").hide();
    jQuery(".notifyer.wah-"+type).show();
}
function wah_unfilter_results_by_type(type){
    jQuery(".notifyer").show();
}

function wah_generate_popup(source) {
    wahPopup.openPopup();
    setTimeout(function(){
        jQuery("#wah_alt_input_submit").attr("data-source",source);
    }, 800);
}
function wah_show_ajax_loader(){
    jQuery(".wah_ajax_loader").fadeIn(250);
}
function wah_hide_ajax_loader(){
    jQuery(".wah_ajax_loader").fadeOut(250);
}
function wah_remove_notifyer(){
    jQuery(".notifyer").remove();
}
