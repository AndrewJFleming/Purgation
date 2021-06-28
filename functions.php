<?php
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css');
	wp_enqueue_style( 'wpb-google-fonts', 'https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Source+Sans+Pro:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700;1,900&display=swap', false ); 
}

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

add_action( 'woocommerce_after_single_product_summary', 'woocommerce_template_single_meta', 30 );

// Rename reviews tab title on Single product page.
add_filter( 'woocommerce_product_tabs', 'purgation_product_tabs', 100);
function purgation_product_tabs( $tabs ) {
	$tabs['reviews']['title'] = __("Product Reviews");

	return $tabs;
}

// Clarifying the Dollar Symbol
add_filter('woocommerce_currency_symbol', 'purgation_currency_symbol', 30, 2);
function purgation_currency_symbol( $currency_symbol, $currency ) {
	$currency_symbol = 'USD $';
	return $currency_symbol;
} 

// Change number of products per page
add_filter( 'loop_shop_per_page', 'purgation_products_per_page', 20 );
function purgation_products_per_page( $num_products ) {
	return 15;
};

// Change number of columns
add_filter( 'loop_shop_columns', 'purgation_number_columns', 20 );
function purgation_number_columns( $cols ) {
	return 3;
};

// Display Percentage Saved
add_filter( 'woocommerce_format_sale_price', 'woocommerce_custom_sales_price', 10, 3 );
function woocommerce_custom_sales_price( $price, $regular_price, $sale_price ) {
    // Getting the clean numeric prices (without html and currency)
    $_reg_price = floatval( strip_tags($regular_price) );
    $_sale_price = floatval( strip_tags($sale_price) );

    // Percentage calculation and text
    $percentage = round( ( $_reg_price - $_sale_price ) / $_reg_price * 100 ).'%';
    $percentage_txt = ' ' . __(' Save ', 'woocommerce' ) . $percentage;

    $formatted_regular_price = is_numeric( $regular_price ) ? wc_price( $regular_price ) : $regular_price;
    $formatted_sale_price    = is_numeric( $sale_price )    ? wc_price( $sale_price )    : $sale_price;

    echo '<del>' . $formatted_regular_price . '</del> <ins>' . $formatted_sale_price . $percentage_txt . '</ins>';
}

// Set and unset some shop page orderby dropdown options
add_filter( 'woocommerce_catalog_orderby', 'purgation_catalog_orderby', 20 );
function purgation_catalog_orderby( $orderby ){
	// print_r($orderby);
	unset($orderby['rating']);
	$orderby['date'] = __('Sort by date: newest to oldest', 'woocommerce');
	$orderby['oldest_to_recent'] = __( 'Sort by date: oldest to newest', 'woocommerce' );
	return $orderby;
}

// make oldest to recent dropdown option sort ascending
add_filter( 'woocommerce_get_catalog_ordering_args', 'purgation_get_catalog_ordering_args', 20 );
function purgation_get_catalog_ordering_args( $args ){
	 $orderby_value = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ));

	if ( 'oldest_to_recent' == $orderby_value ){
		$args['orderby'] = 'date';
		$args['order'] = 'ASC';
	}

	return $args;
}

// Add Empty Cart button
add_action( 'woocommerce_cart_actions', 'purgation_empty_cart_button' );
function purgation_empty_cart_button() {
	echo "<a class='button' href='?empty-cart=true'>" . __( 'Empty Cart', 'woocommerce') . "</a>";
}

// Empty cart functionality
add_action( 'init', 'purgation_empty_cart' );
function purgation_empty_cart(){
	global $woocommerce;
	if ( isset($_GET['empty-cart'])) {
		$woocommerce->cart->empty_cart();
	}
}

// Remove phone field from checkout
add_filter( 'woocommerce_checkout_fields', 'purgation_checkout_fields', 20);
function purgation_checkout_fields( $fields ){
	unset($fields['billing']['billing_phone']);
	return $fields;
}

// Add "how did you hear about us" checkout field
add_filter( 'woocommerce_checkout_fields', 'purgation_hear_about_us', 30);
function purgation_hear_about_us( $fields ){
	$fields['order']['hear_about_us'] = array(
		'type' => 'select',
		'class' => array( 'form-row-wide' ),
		'label' => 'How did you hear about us?',
		'options' => array(
			'default' => '-- select --',
			'airbnb' => 'Airbnb flyer',
			'ad' => 'Online ad',
			'organic' => 'Google search results',
			'friend' => 'Friend or family member',
		)
	);
	return $fields;
}