<?php


add_shortcode('product_type_filter', function (){
	$pt = explode(',', filter_input( INPUT_GET, "filter_pt", FILTER_SANITIZE_STRING ));

	$str = '<ul class="wc-block-checkbox-list wc-block-components-checkbox-list">
        <li>
            <div class="wc-block-components-checkbox wc-block-checkbox-list__checkbox">
                <label for="ptf1">
                    <input name="filter_pt" value="pers" id="ptf1" class="wc-block-components-checkbox__input sidebar-filter-product" type="checkbox" aria-invalid="false"'.(in_array('pers', $pt) ? ' checked' : '').'>
                    <svg class="wc-block-components-checkbox__mark" aria-hidden="true"
                         xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 20">
                        <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"></path>
                    </svg>
                    <span class="wc-block-components-checkbox__label">Możliwość personalizacji</span>
                </label>
            </div>
        </li>
        <li>
            <div class="wc-block-components-checkbox wc-block-checkbox-list__checkbox">
                <label for="ptf2">
                    <input name="filter_pt" value="sellout" id="ptf2" class="wc-block-components-checkbox__input sidebar-filter-product" type="checkbox" aria-invalid="false"'.(in_array('sellout', $pt) ? ' checked' : '').'>
                    <svg class="wc-block-components-checkbox__mark" aria-hidden="true"
                         xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 20">
                        <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"></path>
                    </svg>
                    <span class="wc-block-components-checkbox__label">Wyprzedaż</span>
                </label>
            </div>
        </li>
        <li>
            <div class="wc-block-components-checkbox wc-block-checkbox-list__checkbox">
                <label for="ptf3">
                    <input name="filter_pt" value="sale" id="ptf3" class="wc-block-components-checkbox__input sidebar-filter-product" type="checkbox" aria-invalid="false"'.(in_array('sale', $pt) ? ' checked' : '').'>
                    <svg class="wc-block-components-checkbox__mark" aria-hidden="true"
                         xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 20">
                        <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"></path>
                    </svg>
                    <span class="wc-block-components-checkbox__label">Promocja</span>
                </label>
            </div>
        </li>
    </ul>';
	return $str;
});