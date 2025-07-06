jQuery(document).ready(function($){
    // Owl Carousel

    $('#product_carousel').owlCarousel({
        loop:true,
        margin:20,
        nav:false,
        dots: true,
        items: 5,
        responsiveClass:true,
        responsive:{
            0:{
                items:1,
                margin:10,
            },
            576:{
                items:2,
                margin:10,
            },
            768:{
                items:3,
                margin:10,
            },
            992:{
                items:4
            },
            1199: {
                items: 5
            }
        }
    });

// Single Product - Carousel

    $('#single_product_carousel').owlCarousel({
        loop: true,
        margin: 10,
        mouseDrag: true,
        // animateIn: 'fadeIn',
        // animateOut: 'fadeOut',
        dotsData: true,
        autoHeight: true,
        nav: true,
        navText: ["<img src='/images/nav_prev.png'>", "<img src='/images/nav_next.png'>"],
        dots: false,
        autoplay: false,
        dotsContainer: '.dC_thumbs',
        items: 1,
        responsiveClass: true
    });

    $('.ocd_sale').on('click', function () {
        $('#single_product_carousel').trigger('to.owl.carousel', [$(this).index(), 500]);
        $('.ocd_sale').removeClass('active');
        $(this).addClass('active');
    });


//    $('[data-bs-toggle="tooltip"]').tooltip();
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})


// $("#addtowl").click(function(){
//     $(".add_to_cart_wrap p .fw-button").click();
//     return false;
// });

    $('.counter-input').keyup(function (e) {
        $(this).closest('div').find('.counter-display').html($(this).val().length);
    });

    if($("#alcohol_cookie").length > 0) {
        $("#alcohol_cookie").click(function (event) {
            event.preventDefault();
            setCookie('alcohol', '1', 7);
            $('.alcohol_box').hide();
        });
    }
});

