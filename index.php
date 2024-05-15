<?php
/**
 * Plugin Name:       Dolls Custom Plugin
 * Description:       Contains custom code added for dynamic pricing and product addition
 * Version:           1.0
 * Author:            Abdul Jalil Mian
 * Author URI:        http://www.abduljalil.dev
 */

include('simple_html_dom.php');

function get_live_gold_price() {
	$data_url = "http://www.bullionbypost.co.uk/gold-price/latest-gold-price/";
	$html = file_get_html($data_url);

	$holder = $html->find(".ratio-table-holder");
	$data_content = $holder[0]->find("td[title='Current Gold Price'] span[name='current_price_field']");

	$value = $data_content[0]->innertext;

	return (float)$value;
}

function add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
	$product = wc_get_product( $product_id );
	$categories = $product->get_category_ids();
	
	$live_price = get_live_gold_price();
	
	if( in_array(154, $categories) ) { // gold bars

		if( in_array(168, $categories) ) { // 12.5 kilograms
			$cart_item_data['modified_price'] = 12500 * ($live_price + ( $live_price * (0.023) ) ); 
		} else if ( in_array(167, $categories) ) { // 1 kilogram
			$cart_item_data['modified_price'] = 1000 * ($live_price + ( $live_price * (0.02) ) ); 
		} else if ( in_array(166, $categories) ) { // 500 grams
			$cart_item_data['modified_price'] = 500 * ($live_price + ( $live_price * (0.025) ) ); 
		} else if ( in_array(165, $categories) ) { // 250 grams
			$cart_item_data['modified_price'] = 250 * ($live_price + ( $live_price * (0.03) ) ); 
		} else if ( in_array(164, $categories) ) { // 100 grams
			$cart_item_data['modified_price'] = 100 * ($live_price + ( $live_price * (0.04) ) ); 
		} else if ( in_array(163, $categories) ) { // 50 grams
			$cart_item_data['modified_price'] = 50 * ($live_price + ( $live_price * (0.05) ) ); 
		} else if ( in_array(162, $categories) ) { // 20 grams
			$cart_item_data['modified_price'] = 20 * ($live_price + ( $live_price * (0.08) ) ); 
		} else if ( in_array(161, $categories) ) { // 10 grams
			$cart_item_data['modified_price'] = 10 * ($live_price + ( $live_price * (0.13) ) ); 
		} else if ( in_array(160, $categories) ) { // 5 grams
			$cart_item_data['modified_price'] = 5 * ($live_price + ( $live_price * (0.15) ) ); 
		} else if ( in_array(159, $categories) ) { // 2 grams
			$cart_item_data['modified_price'] = 2 * ($live_price + ( $live_price * (0.4) ) ); 
		} else if ( in_array(158, $categories) ) { // 1 gram
			$cart_item_data['modified_price'] = 1 * ($live_price + ( $live_price * (0.5) ) ); 
		} else {
			$price = $product->get_price();
			$cart_item_data['modified_price'] = $price;
		}
	}

	return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'add_cart_item_data', 10, 3 );

function before_calculate_totals( $cart_obj ) {
	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}

	foreach( $cart_obj->get_cart() as $key=>$value ) {
		if( isset( $value['modified_price'] ) ) {
			if( $value['modified_price'] === 0 ) {
				// $cart_obj->remove_cart_item( $value('key') );
			} else {
				$price = $value['modified_price'];
				$value['data']->set_price( ( $price ) );
			}
		}
	}
}
add_action( 'woocommerce_before_calculate_totals', 'before_calculate_totals', 10, 1 );
