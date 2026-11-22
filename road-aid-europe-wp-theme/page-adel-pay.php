<?php
/**
 * Template Name: Adel Agency Payment Redirect
 * Intermediate page shown before an Adel Agency payment link.
 *
 * @package RoadAidEurope
 */

get_header();

$to_raw = isset( $_GET['to'] ) ? wp_unslash( $_GET['to'] ) : '';
$to_raw = is_string( $to_raw ) ? rawurldecode( $to_raw ) : '';
$to     = esc_url_raw( $to_raw );

$default = function_exists( 'road_aid_get_adel_payment_url' ) ? road_aid_get_adel_payment_url() : '';
if ( ! $to && $default ) {
	$to = esc_url_raw( $default );
}

$allowed = false;
if ( $to && function_exists( 'road_aid_is_adel_payment_url' ) ) {
	$allowed = road_aid_is_adel_payment_url( $to );
}
// Also allow exact match of configured default URL.
if ( $to && $default && untrailingslashit( $to ) === untrailingslashit( $default ) ) {
	$allowed = true;
}

$payments_url = function_exists( 'road_aid_get_payments_refunds_url' ) ? road_aid_get_payments_refunds_url() : home_url( '/payments-refunds/' );
?>

<main id="main" class="site-main">
	<div class="section" id="adel-pay">
		<div class="container" style="max-width:720px;">
			<?php if ( function_exists( 'road_aid_breadcrumbs' ) ) { road_aid_breadcrumbs(); } ?>
			<div class="section-title">
				<div>
					<h2><?php esc_html_e( 'Continue to payment', 'road-aid-europe' ); ?></h2>
					<p><?php esc_html_e( 'Please read this notice before you pay.', 'road-aid-europe' ); ?></p>
				</div>
			</div>

			<?php
			echo function_exists( 'road_aid_adel_payment_notice_html' )
				? road_aid_adel_payment_notice_html( $allowed ? array( 'continue_url' => $to ) : array() )
				: '';
			?>

			<?php if ( ! $allowed ) : ?>
				<div class="card pad" style="margin-top:16px;">
					<p class="note" style="margin:0;">
						<?php esc_html_e( 'No valid Adel Agency payment link was provided. If you received a payment link, open it again from your message, or contact Road Aid Europe for help.', 'road-aid-europe' ); ?>
					</p>
					<p class="note" style="margin:12px 0 0;">
						<a href="<?php echo esc_url( $payments_url ); ?>"><?php esc_html_e( 'Payments & Refunds', 'road-aid-europe' ); ?></a>
					</p>
				</div>
			<?php endif; ?>
		</div>
	</div>
</main>

<?php get_footer(); ?>
