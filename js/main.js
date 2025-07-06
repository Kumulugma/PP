jQuery(document).ready(function($){

    // Navigation
    // $('.menu-item-has-children').hover(function(){
    //     $(this).stop().toggleClass('opened');
    // });


    // $('#main_nav>ul>li>a').click(function(event){
    //     event.preventDefault();
    //     $(this).parent('').addClass('open_submenu');
    // });

    // $('#main_nav>ul>li').click(function(){
    //     $('#main_nav>ul>li').removeClass('open_submenu');
    //     $(this).addClass('open_submenu');
    // });

    $('#mm_trigger').click(function(){
        $(this).addClass('selected');
        $('#mm_wrap').addClass('open');
        $('#close_mm').addClass('active');
    });

    $('#search_trigger').click(function(){
        $(this).addClass('selected');
        $('#mm_search').addClass('open');
        $('#close_mm').addClass('active');
    });

    $('#cart_trigger').click(function(){
        $(this).addClass('selected');
        $('#mm_cart').addClass('open');
        $('#close_mm').addClass('active');
    });

    $('#close_mm').click(function(){
        $('.mobile_nav_element').removeClass('open');
        $('.mm_item').removeClass('selected');
        $('#close_mm').removeClass('active');
    });

    $('#full_cart_btn').click(function(){
        $('.mini_cart_wrap').toggleClass('open');
    });

    $('#search_desktop').click(function(){
        $('#mm_search').toggleClass('dt_open');
    });

    // Mobile Filters

    $('#mobile_filters_trigger').click(function(){
        $('#pl_sidebar').addClass('open');
    });
    $('#close_filters').click(function(){
        $('#pl_sidebar').removeClass('open');
    });

    // Side menu
    $('.nav-click').click(function(){
        $(this).parent('.menu-item-has-children').toggleClass('open');
    });

    //
    let $notification = $('.woocommerce-notices-wrapper');

    if ( $notification.length){
        $($notification).delay(5000).fadeOut(800);
    }


    $(window).scroll(function () {
        var sticky = $('#top_bar'),
            scroll = $(window).scrollTop();
        if (scroll >= 1) sticky.addClass('fixed');
        else sticky.removeClass('fixed');
    });
    $(window).scrollTop($(window).scrollTop()-1);

    // FAQ
    var timeout;
    jQuery( function( $ ) {
        $('.woocommerce').on('change', 'input.qty', function(){

            if ( timeout !== undefined ) {
                clearTimeout( timeout );
            }

            timeout = setTimeout(function() {
                $("[name='update_cart']").trigger("click");
            }, 1000 ); // 1 second delay, half a second (500) seems comfortable too

        });
    } );

    $('.sidebar-filter-product').click(function () {
        element = this;

        var paramName = 'filter_pt';
        var paramValue = getQueryParam(paramName) || '';
        var checkboxValue = encodeURIComponent(element.value);

        var values = paramValue.split(',').filter(Boolean);

        var valueIndex = values.indexOf(checkboxValue);

        if (element.checked && valueIndex === -1) {
            values.push(checkboxValue);
        } else if (!element.checked && valueIndex !== -1) {
            values.splice(valueIndex, 1);
        }

        var newParamValue = values.join(',');
        var newUrl = updateQueryStringParameter(window.location.href, paramName, newParamValue);
        window.history.pushState({path:newUrl},'',newUrl);
        window.location.href = newUrl;
    });

    $('.customization-url').each(function () {
        let a = $(this).find('a');
        a.each(function () {
            $(this).attr('href', $(this).attr('href')+'?filter_pt=pers')
        });
    });
});

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

jQuery.event.special.touchstart = {
    setup: function( _, ns, handle ) {
        this.addEventListener("touchstart", handle, { passive: !ns.includes("noPreventDefault") });
    }
};
jQuery.event.special.touchmove = {
    setup: function( _, ns, handle ) {
        this.addEventListener("touchmove", handle, { passive: !ns.includes("noPreventDefault") });
    }
};
jQuery.event.special.wheel = {
    setup: function( _, ns, handle ){
        this.addEventListener("wheel", handle, { passive: true });
    }
};
jQuery.event.special.mousewheel = {
    setup: function( _, ns, handle ){
        this.addEventListener("mousewheel", handle, { passive: true });
    }
};

function getQueryParam(name) {
    var url = window.location.href;
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)');
    var results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

function updateQueryStringParameter(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    if (uri.match(re)) {
        return uri.replace(re, '$1' + key + "=" + value + '$2');
    }
    else {
        return uri + separator + key + "=" + value;
    }
}