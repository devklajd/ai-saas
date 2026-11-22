<style>
.footer { border-top: 1px solid var(--line); margin-top: 26px; }
.footer-main { padding: 40px 0 32px; }
.footer-grid { display: grid; grid-template-columns: 1.4fr 1fr 1fr; gap: 32px; align-items: start; }
.footer-brand { max-width: 320px; }
.footer-logo { margin-bottom: 12px; }
.footer-logo-img, .footer-brand .custom-logo-link img { height: 42px; width: auto; max-width: 180px; object-fit: contain; display: block; }
.footer-tagline { color: var(--muted); font-size: 14px; line-height: 1.5; margin: 0 0 16px; }
.footer-phone { font-weight: 600; font-size: 15px; color: var(--brand) !important; text-decoration: none; display: inline-block; transition: opacity 0.2s ease; }
.footer-phone:hover { color: var(--brand2) !important; opacity: 0.9; }
.footer-column { display: flex; flex-direction: column; }
.footer-title { margin: 0 0 16px; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text); }
.footer-links { display: flex; flex-direction: column; gap: 2px; }
.footer-links a { color: var(--muted); font-size: 14px; padding: 8px 0; display: block; text-decoration: none; transition: color 0.2s ease; }
.footer-links a:hover { color: var(--text); }
.footer-bottom { padding: 20px 0; border-top: 1px solid var(--line); background: rgba(0, 0, 0, 0.15); }
.footer-copyright { margin: 0; font-size: 13px; color: var(--muted); text-align: center; }
.footer {
  padding: 30px 0 0px;}
@media (max-width: 900px) { .footer-grid { grid-template-columns: 1fr; gap: 24px; } }
@media (max-width: 768px) { .footer-main { padding: 32px 0 24px; } .footer-grid { gap: 28px; } .footer-bottom { padding: 16px 0; } .footer-copyright { font-size: 12px; } }
</style>
<footer class="footer">
	<div class="footer-main">
		<div class="container">
			<div class="footer-grid">
				<div class="footer-brand">
					<div class="footer-logo">
						<?php
						$logo_url = get_template_directory_uri() . '/assets/road aid europe logo new.svg';
						if ( has_custom_logo() ) {
							the_custom_logo();
						} else {
							echo '<img src="' . esc_url( $logo_url ) . '" alt="Road Aid Europe" class="footer-logo-img" />';
						}
						?>
					</div>
					<p class="footer-tagline"><?php esc_html_e( '24/7 roadside assistance in Albania with expansion across the Balkans and Europe.', 'road-aid-europe' ); ?></p>
					<?php if ( function_exists( 'road_aid_get_emergency_phone' ) ) : ?>
					<a class="footer-phone" href="tel:<?php echo esc_attr( road_aid_get_emergency_phone() ); ?>"><?php echo esc_html( road_aid_get_emergency_phone() ); ?></a>
					<?php endif; ?>
				</div>
				<div class="footer-column">
					<h4 class="footer-title"><?php esc_html_e( 'Quick Links', 'road-aid-europe' ); ?></h4>
					<nav class="footer-nav" aria-label="Quick links">
						<?php
						wp_nav_menu( array(
							'theme_location' => 'primary',
							'container'      => false,
							'fallback_cb'    => 'road_aid_footer_fallback',
							'items_wrap'     => '<div class="footer-links">%3$s</div>',
							'walker'         => new Road_Aid_Footer_Menu_Walker(),
						) );
						?>
					</nav>
				</div>
				<div class="footer-column">
					<h4 class="footer-title"><?php esc_html_e( 'Legal', 'road-aid-europe' ); ?></h4>
					<div class="footer-links">
						<?php
						$privacy_page  = get_page_by_path( 'privacy-policy' );
						$terms_page    = get_page_by_path( 'terms-conditions' );
						$payments_page = get_page_by_path( 'payments-refunds' );
						$privacy_url   = ( $privacy_page && get_permalink( $privacy_page ) ) ? get_permalink( $privacy_page ) : home_url( '/privacy-policy/' );
						$terms_url     = ( $terms_page && get_permalink( $terms_page ) ) ? get_permalink( $terms_page ) : home_url( '/terms-conditions/' );
						$payments_url  = ( $payments_page && get_permalink( $payments_page ) ) ? get_permalink( $payments_page ) : home_url( '/payments-refunds/' );
						?>
						<a href="<?php echo esc_url( $privacy_url ); ?>"><?php esc_html_e( 'Privacy Policy', 'road-aid-europe' ); ?></a>
						<a href="<?php echo esc_url( $terms_url ); ?>"><?php esc_html_e( 'Terms & Conditions', 'road-aid-europe' ); ?></a>
						<a href="<?php echo esc_url( $payments_url ); ?>"><?php esc_html_e( 'Payments & Refunds', 'road-aid-europe' ); ?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="footer-bottom">
		<div class="container">
			<p class="footer-copyright">&copy; <?php echo esc_html( date( 'Y' ) ); ?> Road Aid Europe. All rights reserved.</p>
		</div>
	</div>
</footer>

<div class="sticky-help" aria-label="Quick actions">
	<button class="fab whatsapp" id="stickyWhatsApp">WhatsApp</button>
	<a class="fab call" id="stickyCall" href="tel:<?php echo esc_attr( road_aid_get_emergency_phone() ); ?>">Call</a>
</div>

<?php wp_footer(); ?>

</body>
</html>

<?php
/**
 * Footer menu fallback
 */
function road_aid_footer_fallback() {
	$pages = array(
		'Get Assistance' => 'assistance',
		'Services' => 'services',
		'Rental / Fleet Operators' => 'corporate',
		'Become a Provider' => 'providers',
		'Coverage' => 'coverage',
	);
	
	foreach ( $pages as $title => $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page ) {
			echo '<a href="' . esc_url( get_permalink( $page ) ) . '">' . esc_html( $title ) . '</a>';
		}
	}
}

/**
 * Footer menu walker
 */
class Road_Aid_Footer_Menu_Walker extends Walker_Nav_Menu {
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$output .= '<a href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . '</a>';
	}
}
?>

