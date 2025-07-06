<?php


add_action('widgets_init', 'polskipodarek_widgets_init');

function polskipodarek_widgets_init()
{
    register_sidebar(array(
        'name'          => esc_html__('Sidebar Widget Area', 'polskipodarek'),
        'id'            => 'primary-widget-area',
        'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
        'after_widget'  => '</li>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
