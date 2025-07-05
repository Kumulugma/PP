<?php

add_filter( 'walker_nav_menu_start_el', 'add_arrow',10,4);
function add_arrow( $output, $item, $depth, $args ){

//Only add class to 'top level' items on the 'primary' menu.
    if($depth === 0 ){
        if (in_array("menu-item-has-children", $item->classes)) {
            $output .='<span class="nav-click"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-down-fill" viewBox="0 0 16 16">
  <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
</svg></span>';
        }
    }
    return $output;
}