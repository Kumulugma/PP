jQuery(document).ready(function ($) {
    let $active = $('.faq_cat_list li button.active');
    if ($active.length) {
        $active.removeClass('active');
        $active.addClass('active');
        let $faq_cat = $active.attr("data-category");

        $('.faq_answers').addClass('filtered_answers');
        $('[data-answer-category=' + $faq_cat + ']').addClass('active_answer');
        $('[data-question-category]').addClass('d-none');
        $('[data-question-category=' + $faq_cat + ']').removeClass('d-none');
    }

    const sidebar = document.querySelector('.faq_sticky');
    const sidebarTop = sidebar.offsetTop;
    const width = sidebar.offsetWidth;
    
    window.addEventListener('scroll', () => {
        if (window.scrollY >= sidebarTop) {
            sidebar.style.position = 'fixed';
            sidebar.style.top = '120px';
            sidebar.style.width = width+'px';
        } else {
            sidebar.style.position = 'static';
        }
    });


    // faq repeater field filter
    $('.faq_cat_list li button').click(function () {
        // e.preventDefault();
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            let $faq_cat = $(this).attr("data-category");

            $('.faq_answers').removeClass('filtered_answers');
            $('[data-answer-category=' + $faq_cat + ']').removeClass('active_answer');
            $('[data-question-category]').removeClass('d-none');
        } else {
            $('.faq_cat_list li button').removeClass('active');
            $('[data-answer-category]').removeClass('active_answer');

            $(this).addClass('active');
            let $faq_cat = $(this).attr("data-category");

            $('.faq_answers').addClass('filtered_answers');
            $('[data-answer-category=' + $faq_cat + ']').addClass('active_answer');
            $('[data-question-category]').addClass('d-none');
            $('[data-question-category=' + $faq_cat + ']').removeClass('d-none');
        }
    });

    $('.faq_questions [data-question]').click(function () {
        $('.faq_questions [data-question]').removeClass('font-weight-bold');
        $(this).addClass('font-weight-bold');
        let $anchor = $(this).html();

        var root = $('html, body');
        var offset = $('.faq_answers h5:contains('+$anchor+')').offset();
        root.scrollTop(offset.top - 200);
    });

    $('#faq').on('keyup', '.faq_search_input', function (event) {
        // get our elements
        let $faq = $(event.delegateTarget);

        // get the search filter value
        let search = $(this).val().replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&');

        // get the repeater list in faq object
        let $questions = $('.faq_questions', $faq);
        let $answers = $('.faq_answers', $faq);

        // items visible counter
        let itemsVisible = 0;

        // return false early if we dont have a list
        if ($questions.length < 1) return false;

        // loop through each item in the repeater list
        $("li:not('.filter-no-results')", $questions).each(function (key, item) {
            // store our q and a text
            let text = $(this).text();

            // check if the item is visible using regular expression match
            let itemVisible = text.match(new RegExp(search, 'i'));

            // remove these classes
            $(item).removeClass('filter-visible filter-hidden');

            // check if we have a match
            $(item).addClass('filter-' + (itemVisible ? 'visible' : 'hidden'));

            // if item is visible, increment our count
            if (itemVisible) ++itemsVisible;
        });

        $("li:not('.filter-no-results')", $answers).each(function (key, item) {
            // store our q and a text
            let text = $(this).text();

            // check if the item is visible using regular expression match
            let itemVisible = text.match(new RegExp(search, 'i'));

            // remove these classes
            $(item).removeClass('filter-visible filter-hidden');

            // check if we have a match
            $(item).addClass('filter-' + (itemVisible ? 'visible' : 'hidden'));

            // if item is visible, increment our count
            if (itemVisible) ++itemsVisible;
        });

        // check if we have no items visible then show hide no results message
        if (itemsVisible === 0) {
            $('.filter-no-results', $questions).show();
            $('.filter-no-results', $answers).show();
        } else {
            $('.filter-no-results', $questions).hide();
            $('.filter-no-results', $answers).hide();
        }
    });
});
