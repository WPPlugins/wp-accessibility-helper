jQuery(document).ready(function($) {

    jQuery("body").on("focusin",".switch-input", function(){
        jQuery(this).parents(".switch").addClass("focusin");
    });
    jQuery("body").on("focusout",".switch-input", function(){
        jQuery(this).parents(".switch").removeClass("focusin");
    });

    //Toggle admin section WAH Admin
    jQuery(".form_element_header").click(function(e){
        e.preventDefault();
        var this_el     = jQuery(this);
        var toggle_span = jQuery(this).find("span.toggle-wah-section span.dashicons");
        if(toggle_span.hasClass('dashicons-arrow-down-alt2')){
            toggle_span.removeClass("dashicons-arrow-down-alt2");
            toggle_span.addClass('dashicons-arrow-up-alt2');
            this_el.next(".wah_form_elements_wrapper").slideUp(200);
        } else {
            toggle_span.removeClass("dashicons-arrow-up-alt2");
            toggle_span.addClass('dashicons-arrow-down-alt2');
            this_el.next(".wah_form_elements_wrapper").slideDown(200);
        }
    });
    //Add new contrast item
    add_new_contrast_item();
    //Save contrast variations
    save_contrast_variations();
    //Validate on custom contrast mode
    jQuery("#wah_enable_custom_contrast").change(function(){
        if( !jQuery(this).is(":checked") ) {
            jQuery("#contrast_custom_dep").fadeOut();
        } else {
            jQuery("#contrast_custom_dep").fadeIn();
        }
    });
    //Update Attachments title
    jQuery(".attachment_post_title").change(function(){
        var pid    = jQuery(this).parents('tr').data('item');
        var ptitle = jQuery(this).val();
        var data = {
            action: 'update_attachment_title',
            pid:    pid,
            ptitle: ptitle,
        };
        jQuery.post(ajaxurl, data, function(response) {
            var results = jQuery.parseJSON(response);
            if(results){
              jQuery('tr[data-item='+pid+'] input.attachment_post_title').effect( "highlight", {color:"#06924B"}, 1000 );
            }
        });
    });
    //Update Attachments alt
    jQuery(".attachment_post_alt").change(function(){
       var pid    = jQuery(this).parents('tr').data('item');
       var palt   = jQuery(this).val();
       var data = {
           action: 'update_attachment_alt',
           pid:    pid,
           palt: palt,
       };
       jQuery.post(ajaxurl, data, function(response) {
           var results = jQuery.parseJSON(response);
           if(results){
             if(!results.palt) {
               jQuery('tr[data-item='+pid+'] input.attachment_post_alt').attr("placeholder", "no alt tag");
             }
             jQuery('tr[data-item='+pid+'] input.attachment_post_alt').effect( "highlight", {color:"#06924B"}, 1000 );
           }
       });
    });
    //WAH SCANNER
    jQuery("#wah_scanner").click(function(e){
        e.preventDefault();
        var postID = jQuery("#wah_scanner_selector").val();
        if(postID){
            jQuery("#fountainG").fadeIn(200);
            jQuery.ajax({
                type : "post",
                dataType : "json",
                url : ajaxurl,
                data : { action: "wah_scan_homepage", postID : postID },
                success: function(response) {
                    if(response.response_code == 200){
                        jQuery("#wah_scanner_results").html('');
                        if(response.images){
                           jQuery("#wah_scanner_results").append(response.images);
                        }
                        if(response.links){
                           jQuery("#wah_scanner_results").append(response.links);
                        }
                        jQuery("#fountainG").fadeOut(200);
                    } else {
                        alert("Error. Response code: "+response.response_code);
                    }
                },
                error: function(response){
                    jQuery("#fountainG").fadeOut(200);
                    jQuery("#wah_scanner_results").html('');
                    alert("Error. Please try again...");
                }
            });
        } else {
            alert("Please select page");
        }
    });
    jQuery("body").on("click",".wah_scanner_table_trigger",function(event){
        event.preventDefault();
        jQuery(this).toggleClass("active");
        jQuery(this).next(".wah_scanner_table").slideToggle(300);
    });
    //Save wah widgets order
    jQuery( "#sortable-wah-widget" ).sortable({
        placeholder: "ui-state-highlight",
        update: function( event, ui ) {
            jQuery("#fountainG").fadeIn(50);
            var neworder = [];
            jQuery('#sortable-wah-widget li').each(function() {
                var id  = jQuery(this).attr("id");
                var obj = {};
                obj.id  = id;
                neworder.push(obj.id);
            });
            if(neworder){
                jQuery.ajax({
                    type        : "post",
                    dataType    : "json",
                    url         : ajaxurl,
                    data        : {
                        action  : "wah_update_widgets_order",
                        alldata : neworder
                    },
                    success: function(response) {
                        if(response == 'ok'){
                            jQuery("#fountainG").fadeOut(350);
                        }
                    },
                    error: function(response){
                        jQuery("#fountainG").fadeOut(350);
                        alert("Error. Please try again...");
                    }
                });
            }
        }
    });
    jQuery( "#sortable" ).disableSelection();

    //remove custom contrast from repeater
    jQuery("body").on("click","button.wah-button.delete-contrast-params",function(e){
        e.preventDefault();

        jQuery(this).parents("li").addClass("toDelete");
        jQuery(this).parents("li").find(".action-loader").fadeIn(50);
        jQuery.ajax({
            type        : "post",
            dataType    : "json",
            url         : ajaxurl,
            data        : {
                action  : "remove_contrast_item"
            },
            success: function(response) {
                if(response.status == 'ok'){
                    jQuery(".contrast-params-list").find("li.toDelete").fadeOut(250, function(){
                        jQuery(this).remove();
                    });
                }
            }
        });
    });

    //Check title inputs dependencies
    jQuery('[data-depid]').each(function(){
        var depid = jQuery(this).data("depid");
        var depid_checkbox = jQuery("input#"+depid);
        if( !depid_checkbox.is(":checked") ) {
            jQuery(this).fadeOut();
        } else {
            jQuery(this).fadeIn();
        }
    });
    jQuery(".switch-input").change(function(){
        var depid = jQuery(this).attr("id");
        if( !jQuery(this).is(":checked") ) {
            jQuery('[data-depid='+depid+']').fadeOut();
        } else {
            jQuery('[data-depid='+depid+']').fadeIn();
        }
    });

});
//Add new contrast item
function add_new_contrast_item(){
    jQuery("button.wah-button.wah-add-item").click(function(e){
        e.preventDefault();
        var total_contrast_elements = jQuery('.contrast-params-list li').size() + 1;
        if(total_contrast_elements >= 5) {
            alert("Maximum 4 variations. Need more variations? Go PRO!");
        } else {
            jQuery(".wah-contrast-loader").fadeTo(100,1);
            jQuery.ajax({
                type        : "post",
                dataType    : "json",
                url         : ajaxurl,
                data        : {
                    action  : "add_new_contrast_item"
                },
                success: function(response) {
                    if(response.status == 'ok' && response.html){
                        jQuery("ul.contrast-params-list").append(response.html);
                        jscolor.installByClassName("jscolor");
                        jQuery(".wah-contrast-loader").fadeTo(100,0);
                    }
                },
                error: function(response){
                }
            });
        }

    });
}
//Save contrast variations
function save_contrast_variations(){

    jQuery("body").on("click","button.save-contrast-params",function(e){

        e.preventDefault();
        var contrast_variations = [];

        if( jQuery('ul.contrast-params-list li').length ){

            jQuery('ul.contrast-params-list li').each(function() {
                var target_element = jQuery(this).find("input");
                if(!target_element.val() || target_element.val() === ' ') {
                    alert("Fill all fields or delete unnecessary fields please.");
                } else {
                    jQuery(".wah-contrast-loader").fadeTo(100,1);
                    var bgcolor   = jQuery(this).find(".bg-color input").val();
                    var textcolor = jQuery(this).find(".text-color input").val();
                    var obj       = {};
                    obj.bgcolor   = {"bgcolor": bgcolor, "textcolor": textcolor};
                    contrast_variations.push(obj.bgcolor);
                }
            });

            if( contrast_variations ){
                // ajax save variations
                jQuery.ajax({
                    type        : "post",
                    dataType    : "json",
                    url         : ajaxurl,
                    data        : {
                        action  : "save_contrast_variations",
                        alldata : contrast_variations
                    },
                    success: function(response) {
                        if(response.status == 'ok'){
                            jQuery(".wah-contrast-loader").fadeTo(100,0);
                        }
                        if(response.status == 'error'){
                            alert(response.message);
                        }
                    }
                });
            }

        } else {
            jQuery(".action-message").fadeTo(250,1);
            jQuery.ajax({
                type        : "post",
                dataType    : "json",
                url         : ajaxurl,
                data        : {
                    action  : "save_empty_contrast_variations"
                },
                success: function(response) {
                    if(response.status == 'ok'){
                        jQuery(".action-message").fadeTo(250,0);
                    }
                },
                error: function(response){
                }
            });
        }
    });
}
