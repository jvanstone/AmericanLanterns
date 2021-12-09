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
<div class="ast-container" id="inspire-footer">
		<article>
			<div class="before-footer">
				<h2>Our lantern festivals light up the night and inspire fantasy, imagination and celebration!</h2>
				<p>People of all ages can wander through a dazzling array of larger-than-life illuminated silk lanterns and light festival displays while taking in the fresh air and unparalleled majestic scenery of the Finger Lakes in New York. Groups of all sizes are welcome</p>
			</div>
		</article>

</div>
	<?php
}
//add_action( 'astra_content_bottom', 'add_footer_info' );


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


/**
 * Make the Payment for Tickets Complete
 *
 * @param number $order_id // The Order Id.
 */
function alf_auto_complete_by_payment_method( $order_id ) {

	if ( ! $order_id ) {
		return;
	}

	global $product;
	$order = wc_get_order( $order_id );

	if ( 'processing' === $order->data['status'] ) {
		$payment_method = $order->get_payment_method();
		if ( 'cod' !== $payment_method ) {
			$order->update_status( 'completed' );
		}
	}

}
add_action( 'woocommerce_order_status_changed', 'alf_auto_complete_by_payment_method' );

function fooevents_display_date_on_single() {
	global $post;
	$product = wc_get_product( $post->ID );
	$start_date = $product->get_meta( 'WooCommerceEventsDate' );
	$end_date = $product->get_meta( 'WooCommerceEventsEndDate' );
	if ( $end_date ) {
	  printf(
		'<h3>%s</h3>',
		esc_html( $start_date . ' - ' . $end_date )
	  );
	} else if( $start_date ) {
	  printf(
		'<h3>%s</h3>',
		esc_html( $start_date )
	  );
	}
  }
  add_action( 'woocommerce_single_product_summary', 'fooevents_display_date_on_single' );
  

/**
 * Display variations in a table format.
 */
function woocommerce_variable_add_to_cart() {
	global $product, $post;

	// Enter a comma separated list of product ID's that should display variations in table format. Leave empty to display all product ( e.g array() )
	$products = array( ); 
	if ( in_array( $post->ID, $products ) || empty( $products ) ) {
 
		$variations = find_valid_variations();
 
		// Check if the special 'price_grid' meta is set, if it is, load the default template.
		if ( get_post_meta( $post->ID, 'price_grid', true ) ) {
 
			// Enqueue variation scripts.
			wp_enqueue_script( 'wc-add-to-cart-variation' );
 
			// Load the template
			wc_get_template(
				'single-product/add-to-cart/variable.php',
				array(
					'available_variations' => $product->get_available_variations(),
					'attributes'           => $product->get_variation_attributes(),
					'selected_attributes'  => $product->get_variation_default_attributes(),
				)
			);

			return;
		}
		// Cool, lets do our own template!
		?>
			<table class="variations variations-grid" cellspacing="0">
				<tbody>
			<?php
			$variation = 0;
			foreach ( $variations as $key => $value ) {
				if ( ! $value['variation_is_visible'] ) {
					continue;
				}
				if ( $variation != $value['variation_id'] ) {
					?>

					<tr>
						<td>
						<?php

						foreach ( $value['attributes'] as $key => $val ) {
							$val = str_replace( array( '-', '_' ), ' ', $val );
							printf( '<div class="attr attr-%s">%s</div>', $key, ucwords( $val ) );
						}
						?>
						</td>
						<td>
						<?php echo $value['price_html']; ?>
						</td>

						<?php if ( $value['is_in_stock'] ) { ?>
							<form class="cart"  action="<?php echo esc_url( $product->add_to_cart_url() ); ?>" method="post" enctype='multipart/form-data'>
								<td id="variation_<?php echo $value['variation_id']; ?>">
									<?php woocommerce_quantity_input(); ?>
									<?php
									$variation_minimum_quantity  = get_post_meta( $value['variation_id'], 'variation_minimum_allowed_quantity', true );
									$variation_maximum_quantity  = get_post_meta( $value['variation_id'], 'variation_maximum_allowed_quantity', true );
									$variation_group_of_quantity = get_post_meta( $value['variation_id'], 'variation_group_of_quantity', true );
									?>
									<?php if ( isset( $variation_group_of_quantity ) ) { ?>
										<script>
										jQuery(document).ready(function($){
											$( "#variation_<?php echo $value['variation_id']; ?> .qty" ).attr( "step", <?php echo $variation_group_of_quantity; ?> );
											$( "#variation_<?php echo $value['variation_id']; ?> .qty" ).attr( "value", <?php echo $variation_group_of_quantity; ?> );
											$( "#variation_<?php echo $value['variation_id']; ?> .qty" ).attr( "min", <?php echo $variation_minimum_quantity; ?> );
											$( "#variation_<?php echo $value['variation_id']; ?> .qty" ).attr( "max", <?php echo $variation_maximum_quantity; ?> );
										});
										</script>                                 
									<?php } ?>
								</td>
								<td>
									<?php
									if ( ! empty( $value['attributes'] ) ) {
										foreach ( $value['attributes'] as $attr_key => $attr_value ) {
											?>
											<input type="hidden" name="<?php echo $attr_key; ?>" value="<?php echo $attr_value; ?>">
											<?php
										}
									}
									?>
									<button type="submit" class="single_add_to_cart_button btn btn-primary"><span class="glyphicon glyphicon-tag"></span> Add to cart</button>
								</td>
								<input type="hidden" name="variation_id" value="<?php echo $value['variation_id']; ?>" />
								<input type="hidden" name="product_id" value="<?php echo esc_attr( $post->ID ); ?>" />
								<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $post->ID ); ?>" />
							</form>
							<?php } else { ?>
								<td colspan="2">
									<p class="stock out-of-stock"><?php _e( 'This item is currently out of stock and unavailable.', 'woocommerce' ); ?></p>
								</td>
							<?php } ?>
					</tr>
					<?php
					$variation = $value['variation_id'];
				}
			}
			?>
			</tbody>
			</table>

		<?php
	} else {

		// Enqueue variation scripts.
		wp_enqueue_script( 'wc-add-to-cart-variation' );

		// Load the template
		wc_get_template(
			'single-product/add-to-cart/variable.php',
			array(
				'available_variations' => $product->get_available_variations(),
				'attributes'           => $product->get_variation_attributes(),
				'selected_attributes'  => $product->get_variation_default_attributes(),
			)
		);
		return;
	}
}

function find_valid_variations() {
	global $product, $post;

		$variations   = $product->get_available_variations();
		$attributes   = $product->get_attributes();
		$new_variants = array();

		// Loop through all variations
	foreach ( $variations as $variation ) {

		// Peruse the attributes.

		// 1. If both are explicitly set, this is a valid variation
		// 2. If one is not set, that means any, and we must 'create' the rest.

		$valid = true; // so far
		foreach ( $attributes as $slug => $args ) {
			if ( array_key_exists( "attribute_$slug", $variation['attributes'] ) && ! empty( $variation['attributes'][ "attribute_$slug" ] ) ) {
				// Exists

			} else {
				// Not exists, create
				$valid = false; // it contains 'anys'
				// loop through all options for the 'ANY' attribute, and add each
				foreach ( explode( '|', $attributes[ $slug ]['value'] ) as $attribute ) {
					$attribute                                      = trim( $attribute );
					$new_variant                                    = $variation;
					$new_variant['attributes'][ "attribute_$slug" ] = $attribute;
					$new_variants[]                                 = $new_variant;
				}
			}
		}

		// This contains ALL set attributes, and is itself a 'valid' variation.
		if ( $valid ) {

			$new_variants[] = $variation;
		}
	}

		return $new_variants;
}


//Remove WooCommerce Tabs - this code removes all 3 tabs - to be more specific just remove actual unset lines 

add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

function woo_remove_product_tabs( $tabs ) {

    unset( $tabs['description'] );      	// Remove the description tab
    unset( $tabs['reviews'] ); 			// Remove the reviews tab
   // unset( $tabs['additional_information'] );  	// Remove the additional information tab

    return $tabs;

}

// Display variations dropdowns on shop page for variable products
add_filter( 'woocommerce_loop_add_to_cart_link', 'njengah_display_variation_dropdown_on_shop_page' );
function njengah_display_variation_dropdown_on_shop_page() {
   global $product;
   if( $product->is_type( 'variable' )) {
	   $attribute_keys = array_keys( $product->get_attributes() );
   ?>
	   <form class="variations_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->id ); ?>" data-product_variations="<?php echo htmlspecialchars( json_encode( $product->get_available_variations() ) ) ?>">
	   <?php do_action( 'woocommerce_before_variations_form' ); ?>
			   <?php if ( empty( $product->get_available_variations() ) && false !== $product->get_available_variations() ) : ?>
		   <p class="stock out-of-stock"><?php _e( 'This product is currently out of stock and unavailable.', 'woocommerce' ); ?></p>
	   <?php else : ?>
		   <table class="variations" cellspacing="0">
			   <tbody>
				 <?php foreach ( $product->get_variation_attributes() as $attribute_name => $options ) : ?>
				   <tr>
						   <td class="label"><label for="<?php echo sanitize_title( $attribute_name ); ?>"><?php echo wc_attribute_label( $attribute_name ); ?></label></td>
						   <td class="value">
							   <?php
								   $selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( urldecode( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ) : $product->get_variation_default_attribute( $attribute_name );
   wc_dropdown_variation_attribute_options( array( 'options' => $options, 'attribute' => $attribute_name, 'product' => $product, 'selected' => $selected ) );
								   echo end( $attribute_keys ) === $attribute_name ? apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . __( 'Clear', 'woocommerce' ) . '</a>' ) : '';
							   ?>
						   </td>
					   </tr>
				   <?php endforeach;?>
			   </tbody>
		   </table>
			   <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
			   <div class="single_variation_wrap">
			   <?php
				   /**
					* woocommerce_before_single_variation Hook.
					*/
				   do_action( 'woocommerce_before_single_variation' );
					   /**
					* woocommerce_single_variation hook. Used to output the cart button and placeholder for variation data.
					* @since 2.4.0
					* @hooked woocommerce_single_variation - 10 Empty div for variation data.
					* @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
					*/
				   do_action( 'woocommerce_single_variation' );
	
				   /**
					* woocommerce_after_single_variation Hook.
					*/
				   do_action( 'woocommerce_after_single_variation' );
			   ?>
		   </div>
			   <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	   <?php endif; ?>
		   <?php do_action( 'woocommerce_after_variations_form' ); ?>
   </form>
	   <?php } else {
	   echo sprintf( '<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</a>',
		   esc_url( $product->add_to_cart_url() ),
		   esc_attr( isset( $quantity ) ? $quantity : 1 ),
		   esc_attr( $product->id ),
		   esc_attr( $product->get_sku() ),
		   esc_attr( isset( $class ) ? $class : 'button' ),
		   esc_html( $product->add_to_cart_text() )
	   );
	   }
	}

	add_action( 'woocommerce_single_product_summary', 'hide_add_to_cart_button_variable_product', 1, 0 );
	function hide_add_to_cart_button_variable_product() {
	
		// Removing add to cart button and quantities only
		remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
	}
	