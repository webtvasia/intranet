<?php
/**
 * Simple product add to cart
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}

?>

<?php
	// Availability
	$availability      = $product->get_availability();
	$availability_html = empty( $availability['availability'] ) ? '' : '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>';

	echo apply_filters( 'woocommerce_stock_html', $availability_html, $availability['availability'], $product );
?>

<?php if ( $product->is_in_stock() ) : ?>

	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form class="cart" method="post" enctype='multipart/form-data'>
	 	<?php

	    /**
		 * @since 2.1.0.
		 */
	    do_action( 'woocommerce_before_add_to_cart_button' );

	    /**
		 * @since 3.0.0.
		 */
	    do_action( 'woocommerce_before_add_to_cart_quantity' );

        woocommerce_quantity_input( array(
            'min_value'   => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
            'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product ),
            'input_value' => ( isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : 1 )
        ) );

	    /**
		 * @since 3.0.0.
		 */
	    do_action( 'woocommerce_after_add_to_cart_quantity' );
	 	?>

		<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

		<?php
		/**
		 * @since 2.1.0.
		 */
		do_action( 'woocommerce_after_add_to_cart_button' );
		?>
	</form>

	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php endif; ?>