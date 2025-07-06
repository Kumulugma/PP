<?php


add_filter('document_title_separator', 'polskipodarek_document_title_separator');
function polskipodarek_document_title_separator($sep)
{
    $sep = '|';

    return $sep;
}

add_filter('the_title', 'polskipodarek_title');
function polskipodarek_title($title)
{
    if ($title == '') {
        return '...';
    } else {
        return $title;
    }
}
