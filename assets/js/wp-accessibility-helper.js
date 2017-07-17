/************************
    JS Cookies
*************************/
;(function(factory){if(typeof define==='function'&&define.amd){define(factory);}else if(typeof exports==='object'){module.exports=factory();}else{var OldCookies=window.Cookies;var api=window.Cookies=factory();api.noConflict=function(){window.Cookies=OldCookies;return api;};}}(function(){function extend(){var i=0;var result={};for(;i<arguments.length;i++){var attributes=arguments[i];for(var key in attributes){result[key]=attributes[key];}}return result;}function init(converter){function api(key,value,attributes){var result;if(typeof document==='undefined'){return;}if(arguments.length>1){attributes=extend({path:'/'},api.defaults,attributes);if(typeof attributes.expires==='number'){var expires=new Date();expires.setMilliseconds(expires.getMilliseconds()+attributes.expires*864e+5);attributes.expires=expires;}try{result=JSON.stringify(value);if(/^[\{\[]/.test(result)){value=result;}}catch(e){}if(!converter.write){value=encodeURIComponent(String(value)).replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g,decodeURIComponent);}else{value=converter.write(value,key);}key=encodeURIComponent(String(key));key=key.replace(/%(23|24|26|2B|5E|60|7C)/g,decodeURIComponent);key=key.replace(/[\(\)]/g,escape);return(document.cookie=[key,'=',value,attributes.expires&&'; expires='+attributes.expires.toUTCString(),attributes.path&&'; path='+attributes.path,attributes.domain&&'; domain='+attributes.domain,attributes.secure?'; secure':''].join(''));}if(!key){result={};}var cookies=document.cookie?document.cookie.split('; '):[];var rdecode=/(%[0-9A-Z]{2})+/g;var i=0;for(;i<cookies.length;i++){var parts=cookies[i].split('=');var name=parts[0].replace(rdecode,decodeURIComponent);var cookie=parts.slice(1).join('=');if(cookie.charAt(0)==='"'){cookie=cookie.slice(1,-1);}try{cookie=converter.read?converter.read(cookie,name):converter(cookie,name)||cookie.replace(rdecode,decodeURIComponent);if(this.json){try{cookie=JSON.parse(cookie);}catch(e){}}if(key===name){result=cookie;break;}if(!key){result[name]=cookie;}}catch(e){}}return result;}api.set=api;api.get=function(key){return api(key);};api.getJSON=function(){return api.apply({json:true},[].slice.call(arguments));};api.defaults={};api.remove=function(key,attributes){api(key,'',extend(attributes,{expires:-1}));};api.withConverter=init;return api;}return init(function(){});}));
/************************
    JS Cookies END
*************************/
jQuery(window).load(function(){
    jQuery("a.aicon_link").fadeIn(350);
});

jQuery(document).ready(function(){

    var $body = jQuery("body"),
    $body_link = jQuery("body a");

    var currFFZoom = 1;
    var currIEZoom = 100;

    //Accessibility
    jQuery("#wp_access_helper_container").prependTo('body');

    // Add Skiplinks to DOM
    jQuery(".wah-skiplinks-menu").prependTo('body');

    //Greyscale images
    jQuery(".greyscale").click(function(){
        jQuery("img").each(function(){
            jQuery(this).toggleClass("active_greyscale");
        });
    });
    // Open sidebar
    jQuery(".aicon_link").click(function(event){
        event.preventDefault();
        jQuery(".accessability_container").addClass("active");
        jQuery("#access_container button").removeAttr("tabindex");
        jQuery("#access_container").attr("aria-hidden","false");
    });
    // Close sidebar
    jQuery(".close_container, .close-wah-sidebar").click(function(event){
        event.preventDefault();
        jQuery(".accessability_container").removeClass("active");
        jQuery("#access_container button").attr("tabindex","-1");
        jQuery("#access_container").attr("aria-hidden","true");
    });
    //FONT SIZE
    if( jQuery("body").hasClass('wah_fstype_rem') ) {
        jQuery(".smaller").click(function(event){
            event.preventDefault();
            var fontSize = parseInt(jQuery("html").css("font-size"));
            if( fontSize > 12 ){
              fontSize     = fontSize - 1 + "px";
              jQuery("html").css({'font-size':fontSize});
            }
        });
        jQuery(".larger").click(function(event){
            event.preventDefault();
            var fontSize = parseInt(jQuery("html").css("font-size"));
            if( fontSize < 24 ){
              fontSize     = fontSize + 1 + "px";
              jQuery("html").css({'font-size':fontSize});
            }
        });
    } else if ( jQuery("body").hasClass("wah_fstype_zoom") ){
        jQuery(".larger").click(function(){
            var step;
            if ( $body.hasClass('gecko') ){
                step = 0.05;
                currFFZoom += step;
                $body.css('MozTransform','scale(' + currFFZoom + ','+ currFFZoom + ')');
                $body.css('transform-origin','50% 50%');
            } else {
                step = 5;
                currIEZoom += step;
                $body.css('zoom', ' ' + currIEZoom + '%');
            }
        });
        jQuery(".smaller").click(function(){
            var step;
            if ( $body.hasClass('gecko') ){
                step = 0.05;
                currFFZoom -= step;
                $body.css('MozTransform','scale(' + currFFZoom + ','+ currFFZoom +')');
                $body.css('transform-origin','50% 50%');
            } else {
                step = 5;
                currIEZoom -= step;
                $body.css('zoom', ' ' + currIEZoom + '%');
            }
        });
    } else {
        var resizable_elements = jQuery("a,p,span,h1,h2,h3,h4,h5,h6");
        resizable_elements.each(function(){
            jQuery(this).attr('data-wahfont',parseInt(jQuery(this).css('font-size')));
        });
        wah_font_resizer();
    }

    //Remove styles
    jQuery(".wah-call-remove-styles").click(function(event){
        event.preventDefault();
        jQuery('link:not(#wpah-front-styles-css)').each(function(index,value){
            if(jQuery(this).attr('disabled')){
                jQuery(this).removeAttr('disabled');
            } else {
                jQuery(this).attr('disabled','disabled');
            }
        });
    });

    //Underline links
    jQuery(".wah-call-underline-links").click(function(event){
        event.preventDefault();
        $body.toggleClass('is_underline');
    });

    //wp-accessibility-helper #contrast
    jQuery("#contrast_trigger").click(function(event){
        event.preventDefault();
        jQuery(".color_selector").toggleClass('is_visible');
        jQuery(".color_selector").attr('aria-hidden','false');
    });

    // Color variable selector
    jQuery(".convar").click(function(event){
        event.preventDefault();
        var bg_color   = jQuery(this).data("bgcolor");
        var text_color = jQuery(this).data("color");
        jQuery('body :not(.wahcolor), body').css({
            'background-color': bg_color,
            'color':text_color
        });
        setContrastCookie(bg_color,text_color);
        jQuery(".color_selector").removeClass('is_visible');
        jQuery(".color_selector").attr('aria-hidden','true');
    });

    //enable rel="link"
    if( typeof roleLink != 'undefined' && roleLink == 1 ) {
      $body_link.each(function(){
        jQuery(this).attr("role","link");
      });
    }

    //remove link title attribute
    if( typeof removeLinkTitles != 'undefined' && removeLinkTitles == 1 ) {
      $body_link.each(function(){
        if(jQuery(this).attr("title")){
          jQuery(this).attr("title","");
          jQuery(this).removeAttr("title");
        }
      });
    }

    //add header landmark
    if( typeof headerElementSelector != 'undefined' && headerElementSelector ) {
        $body.find(headerElementSelector).attr("role","banner");
    }

    //add sidebar landmark
    if( typeof sidebarElementSelector != 'undefined' && sidebarElementSelector ) {
        $body.find(sidebarElementSelector).attr("role","complementary");
    }

    //add footer landmark
    if( typeof footerElementSelector != 'undefined' && footerElementSelector ) {
        $body.find(footerElementSelector).attr("role","contentinfo");
    }

    //add main landmark
    if( typeof mainElementSelector != 'undefined' && mainElementSelector ) {
        $body.find(mainElementSelector).attr("role","main");
    }

    //add navigation landmark
    if( typeof navElementSelector != 'undefined' && navElementSelector ) {
        $body.find(navElementSelector).attr("role","navigation");
    }

    //coockies
    if( typeof Cookies.get('wahFontColor') !='undefined' && typeof Cookies.get('wahBgColor') !='undefined' ){
        jQuery('body :not(.wahcolor), body').css({
            'background-color': Cookies.get('wahBgColor'),
            'color': Cookies.get('wahFontColor')
        });
    }

    //Lights Off
    if( typeof wah_lights_off_selector !='undefined' && wah_lights_off_selector){
        jQuery(".wah-lights-off").click(function(e){
            e.preventDefault();
            if( !jQuery("body").hasClass("wah-lights-off") ) {
                jQuery("body").append('<div class="wah-dark-overlay"></div>');
                jQuery("body").addClass("wah-lights-off");
                jQuery(wah_lights_off_selector).addClass('wah-lights-selector');
            } else {
                jQuery("body .wah-dark-overlay").remove();
                jQuery("body").removeClass("wah-lights-off");
                jQuery(wah_lights_off_selector).removeClass('wah-lights-selector');
            }
        });
    }

    jQuery(".wah-call-clear-cookies").click(function(event){
        event.preventDefault();
        removeAllCookies();
    });

    //highlight_links_setup
    jQuery(".wah-call-highlight-links").click(function(event){
        event.preventDefault();
        $body.toggleClass("highlight_links_on");
    });

    //invert mode
    jQuery(".wah-call-invert").click(function(event){
        event.preventDefault();
        $body.toggleClass("invert_mode_on");
    });

    //remove animations
    jQuery(".wah-call-remove-animations").click(function(event){
        event.preventDefault();
        $body.toggleClass("remove_animations");
    });

    //active button
    jQuery(".wah-action-button").click(function(){
        jQuery(this).toggleClass("active_button");
    });

    //readable font
    jQuery(".readable_fonts .wah-action-button").click(function(event){
        $body.toggleClass("arial_font_on");
    });

    //Keyboard Navigation
    jQuery(".wah-call-keyboard-navigation").click(function(){
        $body.toggleClass("wah_keyboard_access");
    });

    //Fetch scanner params
    if(typeof wah_target_src != 'undefined'){
        wah_target_element = jQuery("body").find("img[src='"+wah_target_src+"']");
        wah_target_element.addClass("wah_scanner_element");
    }
    if(typeof wah_target_link != 'undefined'){
        wah_target_element = jQuery('a[href="'+wah_target_link+'"]');
        wah_target_element.addClass("wah_scanner_link");
    }
});

function setContrastCookie(bg_color,text_color){
    Cookies.set('wahFontColor', text_color, { expires: 14 });
    Cookies.set('wahBgColor', bg_color, { expires: 14 });
}
function removeAllCookies(){
    Cookies.remove('wahFontColor');
    Cookies.remove('wahBgColor');
    location.reload();
}
function wah_font_resizer(){
    var resizable_elements = jQuery("a,p,span,h1,h2,h3,h4,h5,h6");
    //Font++
    jQuery(".font_resizer .larger").click(function(e){
        e.preventDefault();
        resizable_elements.each(function(){
            var el_font_size = parseInt(jQuery(this).css('font-size'));
            jQuery(this).css('font-size',parseInt(el_font_size+1)+'px');
        });
    });
    //Font--
    jQuery(".font_resizer .smaller").click(function(e){
        e.preventDefault();
        resizable_elements.each(function(){
            var el_font_size = parseInt(jQuery(this).css('font-size'));
            if(el_font_size > 12){
                jQuery(this).css('font-size',parseInt(el_font_size-1)+'px');
            }
        });
    });
    //Font reset
    jQuery(".wah-font-reset").click(function(e){
        e.preventDefault();
        resizable_elements.each(function(){
            var el_font_size = parseInt(jQuery(this).css('font-size'));
            jQuery(this).css('font-size',parseInt(jQuery(this).data("wahfont"))+'px');
        });
    });
}
