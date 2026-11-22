<?php
/**
 * Template Name: Terms & Conditions
 *
 * @package RoadAidEurope
 */

get_header();
?>

<main id="main" class="site-main">
	<div class="section" id="terms">
		<div class="container">
			<?php if ( function_exists( 'road_aid_breadcrumbs' ) ) { road_aid_breadcrumbs(); } ?>
			<div class="section-title">
				<div>
					<h2><?php esc_html_e( 'Terms and Conditions', 'road-aid-europe' ); ?></h2>
					<p><?php esc_html_e( 'Terms of use for our website and roadside assistance services.', 'road-aid-europe' ); ?></p>
				</div>
			</div>

			<div style="display:grid; gap:20px;">
				<div class="card pad">
					<h3 style="margin:0 0 10px; font-size:15px;"><?php esc_html_e( '1. Acceptance', 'road-aid-europe' ); ?></h3>
					<p class="note" style="margin:0;"><?php esc_html_e( 'By using this website or engaging Road Aid Europe for roadside assistance or related services, you agree to these terms. If you are acting for a company or other organisation, you confirm that you have authority to bind them. If you do not agree, do not use our services.', 'road-aid-europe' ); ?></p>
				</div>

				<div class="card pad">
					<h3 style="margin:0 0 10px; font-size:15px;"><?php esc_html_e( '2. Our services', 'road-aid-europe' ); ?></h3>
					<p class="note" style="margin:0;"><?php esc_html_e( 'Road Aid Europe coordinates roadside assistance (e.g. towing, on-the-spot repairs, locksmith, fuel delivery) through a network of local providers. We act as dispatcher and coordinator; the actual repair or recovery may be carried out by third-party providers. Availability and response times depend on location, demand, and circumstances. We will use reasonable efforts to assist you within our coverage area.', 'road-aid-europe' ); ?></p>
				</div>

				<div class="card pad">
					<h3 style="margin:0 0 10px; font-size:15px;"><?php esc_html_e( '3. Your obligations', 'road-aid-europe' ); ?></h3>
					<p class="note" style="margin:0;"><?php esc_html_e( 'You must: provide accurate contact and location information; cooperate with our team and the attending provider; ensure you are entitled to request assistance for the vehicle in question; and pay any agreed fees or charges. You must not use our services or website for any unlawful purpose or in a way that could harm our systems or reputation.', 'road-aid-europe' ); ?></p>
				</div>

				<div class="card pad">
					<h3 style="margin:0 0 10px; font-size:15px;"><?php esc_html_e( '4. Fees and payment', 'road-aid-europe' ); ?></h3>
					<p class="note" style="margin:0;"><?php esc_html_e( 'Fees depend on the type of service, location, and any pre-agreed terms (e.g. for business or insurance partners). We or the attending provider will inform you of applicable charges where possible before work is carried out. Payment may be due on site, by invoice, or as per your contract. Failure to pay may result in suspension of service and recovery action.', 'road-aid-europe' ); ?></p>
				</div>

				<div class="card pad" id="payment-collection">
					<h3 style="margin:0 0 10px; font-size:15px;"><?php esc_html_e( 'Payment Collection', 'road-aid-europe' ); ?></h3>
					<p class="note" style="margin:0;"><?php
					$payments_page = get_page_by_path( 'payments-refunds' );
					$payments_url  = ( $payments_page && get_permalink( $payments_page ) ) ? get_permalink( $payments_page ) : home_url( '/payments-refunds/' );
					echo wp_kses(
						sprintf(
							/* translators: %s: Payments & Refunds page URL */
							__( 'For transactions identified as being collected by Adel Agency LLC, Adel Agency LLC acts as Road Aid Europe’s payment collection and coordination partner. It collects customer payments and distributes the relevant amounts to the roadside assistance suppliers. Please see our <a href="%s">Payments &amp; Refunds</a> page for further information.', 'road-aid-europe' ),
							esc_url( $payments_url )
						),
						array( 'a' => array( 'href' => array() ) )
					);
					?></p>
				</div>

				<div class="card pad">
					<h3 style="margin:0 0 10px; font-size:15px;"><?php esc_html_e( '5. Limitation of liability', 'road-aid-europe' ); ?></h3>
					<p class="note" style="margin:0;"><?php esc_html_e( 'To the fullest extent permitted by law: (a) we are not liable for delays or failures caused by circumstances beyond our reasonable control (e.g. weather, traffic, provider availability); (b) our liability for direct loss in connection with our coordination services is limited to the amount you paid for that incident (or a reasonable cap if higher); (c) we are not liable for indirect, consequential, or punitive damages; (d) liability for work done by the attending provider may be governed by their terms and applicable law. Nothing in these terms excludes or limits liability that cannot be excluded or limited by law.', 'road-aid-europe' ); ?></p>
				</div>

				<div class="card pad">
					<h3 style="margin:0 0 10px; font-size:15px;"><?php esc_html_e( '6. Website use', 'road-aid-europe' ); ?></h3>
					<p class="note" style="margin:0;"><?php printf( wp_kses( __( 'You may use this website for lawful purposes only. Content is provided for general information; we do not guarantee that it is complete or up to date. We are not responsible for third-party sites linked from this website. Your use of the site is also subject to our <a href="%s">Privacy Policy</a>.', 'road-aid-europe' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( home_url( '/privacy-policy/' ) ) ); ?></p>
				</div>

				<div class="card pad">
					<h3 style="margin:0 0 10px; font-size:15px;"><?php esc_html_e( '7. Complaints and disputes', 'road-aid-europe' ); ?></h3>
					<p class="note" style="margin:0;"><?php esc_html_e( 'If you are unhappy with our service, please contact us with your incident details and we will try to resolve the matter. If a dispute arises, the laws of the country in which Road Aid Europe (or the operating entity) is established shall apply, and the courts of that country shall have jurisdiction, unless mandatory law requires otherwise.', 'road-aid-europe' ); ?></p>
				</div>

				<div class="card pad">
					<h3 style="margin:0 0 10px; font-size:15px;"><?php esc_html_e( '8. Changes', 'road-aid-europe' ); ?></h3>
					<p class="note" style="margin:0;"><?php esc_html_e( 'We may update these terms from time to time. The current version will be published on this page. Continued use of our services after changes constitutes acceptance of the updated terms. For significant changes we may notify you where appropriate.', 'road-aid-europe' ); ?></p>
				</div>

				<div class="card pad">
					<h3 style="margin:0 0 10px; font-size:15px;"><?php esc_html_e( '9. Contact', 'road-aid-europe' ); ?></h3>
					<p class="note" style="margin:0;"><?php esc_html_e( 'For questions about these terms, contact us using the details provided on this website.', 'road-aid-europe' ); ?></p>
				</div>
			</div>
		</div>
	</div>
</main>

<?php get_footer(); ?>
