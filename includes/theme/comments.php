<?php


add_action('comment_form_before', 'polskipodarek_enqueue_comment_reply_script');
function polskipodarek_enqueue_comment_reply_script()
{
    if (get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}

function polskipodarek_custom_pings($comment)
{
    ?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>"><?php echo esc_url(comment_author_link()); ?></li>
    <?php
}

add_filter('get_comments_number', 'polskipodarek_comment_count', 0);
function polskipodarek_comment_count($count)
{
    if ( ! is_admin()) {
        global $id;
        $get_comments     = get_comments('status=approve&post_id=' . $id);
        $comments_by_type = separate_comments($get_comments);

        return count($comments_by_type['comment']);
    } else {
        return $count;
    }
}

