<?php
/**
 * American Lanterns Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package AmericanLanterns
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_VERSION', '1.0.0' );

/**
 * Enqueue styles, dequeue ASTRA stylesheet.
 */
function child_enqueue_styles() {

	wp_enqueue_style(
		'zpv-theme-css',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'astra-theme-css' ),
		CHILD_THEME_VERSION,
		'all'
	);

	wp_enqueue_style( 'dashicons' );

	wp_dequeue_style( 'astra-theme-css' );
}
add_action( 'wp_enqueue_scripts', 'child_enqueue_styles' );
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

add_theme_support( 'editor-style' );

/**
 * Registers an editor stylesheet in a sub-directory.
 */
function add_editor_styles_sub_dir() {
	add_editor_style( trailingslashit( get_template_directory_uri() ) . 'editor-style.css' );
}
add_action( 'after_setup_theme', 'add_editor_styles_sub_dir' );


/**
 * Add footer info to the end of each page.
 *
 * @return void
 */
function add_footer_info() {

	?>
</div>
<div class="ast-container alignfull" id="inspire-footer">
		<article class="alignfull" style="min-height: 400px;">
			
		</article>

</div>
	<?php
}
add_action( 'astra_content_bottom', 'add_footer_info' );


/**
 * Add "Confirm Email Address" Field @ WooCommerce Checkout
 *
 */
function bbloomer_add_email_verification_field_checkout( $fields ) {

	$fields['billing']['billing_email']['class'] = array( 'form-row-first' );

	$fields['billing']['billing_em_ver'] = array(
		'label'    => 'Confirm Email',
		'required' => true,
		'class'    => array( 'form-row-last' ),
		'clear'    => true,
		'priority' => 999,
	);

	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'bbloomer_add_email_verification_field_checkout' );

	/**
	 * 3) Generate error message if field values are different.
	 *
	 */
function bbloomer_matching_email_addresses() {
	$email1 = $_POST['billing_email'];
	$email2 = $_POST['billing_em_ver'];
	if ( $email2 !== $email1 ) {
		wc_add_notice( 'Your email addresses do not match', 'error' );
	}
	add_action( 'woocommerce_checkout_process', 'bbloomer_matching_email_addresses' );
}

/**
 * Rename Coupon for Discount Code
 *
 * @param string $translated_text // Translate Text.
 * @param string $text // The text to be translated.
 * @param string $text_domain // The Text domain.
 */
function woocommerce_rename_coupon_field_on_cart( $translated_text, $text, $text_domain ) {

	switch ( $text ) {
		case 'Coupon:':
			$translated_text = 'Discount Code:';
			break;
		case 'Coupons':
			$translated_text = 'Discount Codes';
			break;
		case 'Coupon has been removed.':
			$translated_text = 'Discount code has been removed.';
			break;
		case 'Apply coupon':
			$translated_text = 'Apply Code';
			break;
		case 'Coupon code':
			$translated_text = 'Discount Code';
			break;
		case 'Coupons':
			$translated_text = 'Discount Codes';
			break;
		case 'Add coupon':
			$translated_text = 'Add Discount Code';
			break;
		case 'Add new coupon':
			$translated_text = 'Add new discount code';
			break;
		case 'Coupon type':
			$translated_text = 'Discount type';
			break;
		case 'Coupon amount':
			$translated_text = 'Discount amount';
			break;
		case 'If you have a coupon code, please apply it below.':
			$translated_text = 'If you have a discount code, please apply it below.';
			break;
		case 'Scan this QR code at the event to check in.':
			$translated_text = 'Scan this QR code at the event to check in. Please note: Each attendee will have to have their own ticket with a QR code.';
			break;
		case 'Eventer':
			$translated_text = 'Event';
			break;
		case 'Untitled':
			$translated_text = 'Finger Lakes Festival of Lights';
	}

	return $translated_text;
}


/**
 * Rename the "Have a Coupon?" message on the checkout page
 */
function woocommerce_rename_coupon_message_on_checkout() {
	return 'Have a Discount Code?' . ' <a href="#" class="showcoupon">' . __( 'Click here to enter your code', 'woocommerce' ) . '</a>';
}


/**
 * Rename the "Have a Coupon?" message on the checkout page
 *
 * @param string $err // The string to replace.
 * @param string $err_code // Confirm no error.
 * @param string $something // Checking Somethimg.
 */
function rename_coupon_label( $err, $err_code = null, $something = null ) {

	$err = str_ireplace( 'Coupon', 'Discount Code', $err );

	return $err;
}
add_filter( 'gettext', 'woocommerce_rename_coupon_field_on_cart', 10, 3 );
add_filter( 'gettext', 'woocommerce_rename_coupon_field_on_cart', 10, 3 );
add_filter( 'woocommerce_coupon_error', 'rename_coupon_label', 10, 3 );
add_filter( 'woocommerce_coupon_message', 'rename_coupon_label', 10, 3 );
add_filter( 'woocommerce_cart_totals_coupon_label', 'rename_coupon_label', 10, 1 );
add_filter( 'woocommerce_checkout_coupon_message', 'woocommerce_rename_coupon_message_on_checkout' );


remove_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_again_button' );
