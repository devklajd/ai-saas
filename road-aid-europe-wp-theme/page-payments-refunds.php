<?php
/**
 * Template Name: Payments & Refunds
 *
 * @package RoadAidEurope
 */

get_header();

$payments_url = home_url( '/payments-refunds/' );
$address      = function_exists( 'road_aid_get_adel_address' ) ? road_aid_get_adel_address() : '';
$email        = function_exists( 'road_aid_get_adel_payments_email' ) ? road_aid_get_adel_payments_email() : '';
$phone        = function_exists( 'road_aid_get_adel_payments_phone' ) ? road_aid_get_adel_payments_phone() : '';
$policy       = function_exists( 'road_aid_get_adel_refund_policy' ) ? road_aid_get_adel_refund_policy() : '';
$has_contact  = ( $address !== '' || $email !== '' || $phone !== '' );
$has_policy   = ( $policy !== '' );
?>

<main id="main" class="site-main">
	<div class="section" id="payments-refunds">
		<div class="container">
			<?php if ( function_exists( 'road_aid_breadcrumbs' ) ) { road_aid_breadcrumbs(); } ?>
			<div class="section-title">
				<div>
					<h2><?php esc_html_e( 'Payments & Refunds', 'road-aid-europe' ); ?></h2>
					<p><?php esc_html_e( 'How Adel Agency LLC collects payments for Road Aid Europe services.', 'road-aid-europe' ); ?></p>
				</div>
			</div>

			<div style="display:grid; gap:20px; max-width:820px;">
				<div class="card pad">
					<h3 style="margin:0 0 10px; font-size:15px;"><?php esc_html_e( 'Payment Collection Partner', 'road-aid-europe' ); ?></h3>
					<p class="note" style="margin:0 0 12px;"><?php esc_html_e( 'Adel Agency LLC is a US-based company working in partnership with Road Aid Europe.', 'road-aid-europe' ); ?></p>
					<p class="note" style="margin:0 0 12px;"><?php esc_html_e( 'For payments handled under this arrangement, Adel Agency LLC acts as an intermediary: it collects customer payments for roadside assistance arranged through Road Aid Europe’s supplier network and subsequently distributes the relevant amounts to the suppliers.', 'road-aid-europe' ); ?></p>
					<p class="note" style="margin:0;"><?php esc_html_e( 'Before payment, customers will be informed of the collecting entity, the service being arranged and the applicable charges.', 'road-aid-europe' ); ?></p>
				</div>

				<div class="card pad">
					<h3 style="margin:0 0 10px; font-size:15px;"><?php esc_html_e( 'Payment Questions, Cancellations & Refunds', 'road-aid-europe' ); ?></h3>
					<p class="note" style="margin:0;"><?php esc_html_e( 'Please contact us using the details below for questions about a payment, cancellation or refund. Include your name, service reference, payment date and amount. Do not send full card details.', 'road-aid-europe' ); ?></p>
				</div>

				<?php if ( $has_contact ) : ?>
				<div class="card pad">
					<h3 style="margin:0 0 10px; font-size:15px;"><?php esc_html_e( 'Adel Agency LLC — contact for payment & refund enquiries', 'road-aid-europe' ); ?></h3>
					<?php if ( $address !== '' ) : ?>
						<p class="note" style="margin:0 0 10px;"><strong><?php esc_html_e( 'Registered business address', 'road-aid-europe' ); ?>:</strong><br><?php echo nl2br( esc_html( $address ) ); ?></p>
					<?php endif; ?>
					<?php if ( $email !== '' ) : ?>
						<p class="note" style="margin:0 0 10px;"><strong><?php esc_html_e( 'Email', 'road-aid-europe' ); ?>:</strong>
							<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
						</p>
					<?php endif; ?>
					<?php if ( $phone !== '' ) : ?>
						<p class="note" style="margin:0;"><strong><?php esc_html_e( 'Telephone', 'road-aid-europe' ); ?>:</strong>
							<a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
						</p>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<?php if ( $has_policy ) : ?>
				<div class="card pad">
					<h3 style="margin:0 0 10px; font-size:15px;"><?php esc_html_e( 'Cancellation and refund policy', 'road-aid-europe' ); ?></h3>
					<div class="note" style="margin:0;"><?php echo nl2br( esc_html( $policy ) ); ?></div>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</main>

<?php get_footer(); ?>
