<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="nav">
	<div class="container">
		<div class="nav-inner">
			<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Road Aid Europe">
				<?php
				$logo_url = get_template_directory_uri() . '/assets/road aid europe logo new.svg';
				if ( has_custom_logo() ) {
					the_custom_logo();
				} else {
					echo '<img src="' . esc_url( $logo_url ) . '" alt="Road Aid Europe" class="logo-img" />';
				}
				?>
			</a>

			<nav class="menu" aria-label="Main navigation">
				<?php echo road_aid_primary_nav_links(); ?>
			</nav>

		<div class="nav-cta">
			<?php echo road_aid_language_switcher(); ?>
			<a class="btn desktop-provider-btn" href="<?php echo esc_url( get_permalink( get_page_by_path( 'become-a-service-provider' ) ) ?: home_url( '/providers' ) ); ?>" title="<?php esc_attr_e( 'Become a Provider', 'road-aid-europe' ); ?>"><?php esc_html_e( 'Become a Provider', 'road-aid-europe' ); ?></a>
			<a class="btn primary" href="<?php echo esc_url( get_permalink( get_page_by_path( 'emergency' ) ) ?: home_url( '/emergency' ) ); ?>" id="needHelpNowBtn" title="<?php esc_attr_e( 'Need Help Now', 'road-aid-europe' ); ?>"><?php esc_html_e( 'Need Help Now', 'road-aid-europe' ); ?></a>
			<button class="hamburger" id="hamburger" aria-controls="mobileMenu" aria-expanded="false" aria-label="Toggle menu">
				<span class="hamburger-line"></span>
				<span class="hamburger-line"></span>
				<span class="hamburger-line"></span>
			</button>
		</div>
		</div>

		<div class="mobile-menu" id="mobileMenu">
			<?php echo road_aid_primary_nav_links(); ?>
			<a class="btn mobile-provider-btn" href="<?php echo esc_url( get_permalink( get_page_by_path( 'become-a-service-provider' ) ) ?: home_url( '/providers' ) ); ?>" title="<?php esc_attr_e( 'Become a Provider', 'road-aid-europe' ); ?>"><?php esc_html_e( 'Become a Provider', 'road-aid-europe' ); ?></a>
			<?php echo road_aid_language_switcher( 'mobile' ); ?>
		</div>
	</div>
</header>

<script>
document.addEventListener('click',function(e){
	var switchers = document.querySelectorAll('.lang-switcher, .mobile-lang-switcher');
	for(var i=0;i<switchers.length;i++){
		if(!switchers[i].contains(e.target)){
			switchers[i].classList.remove('open');
		}
	}
});
</script>

<?php

/**
 * Primary nav for homepage redesign: Emergency, Fly & Fix, Medical, About, Coverage.
 */
function road_aid_primary_nav_links() {
	$home = trailingslashit( home_url( '/' ) );
	$items = array(
		array( __( 'Emergency', 'road-aid-europe' ), get_permalink( get_page_by_path( 'emergency' ) ) ?: home_url( '/emergency/' ) ),
		array( __( 'Fly & Fix', 'road-aid-europe' ), $home . '#fly-and-fix' ),
		array( __( 'Medical Assistance', 'road-aid-europe' ), $home . '#medical-assistance' ),
		array( __( 'About Us', 'road-aid-europe' ), get_permalink( get_page_by_path( 'about' ) ) ?: home_url( '/about/' ) ),
		array( __( 'Coverage', 'road-aid-europe' ), get_permalink( get_page_by_path( 'coverage' ) ) ?: home_url( '/coverage/' ) ),
	);
	$html = '';
	foreach ( $items as $item ) {
		$html .= '<a href="' . esc_url( $item[1] ) . '" class="menu-item">' . esc_html( $item[0] ) . '</a>';
	}
	return $html;
}

/**
 * Fallback menu if no menu is set
 */
function road_aid_fallback_menu() {
	$pages = array(
		'Get Assistance' => 'assistance',
		'Services' => 'services',
		'Rental / Fleet Operators' => 'corporate',
		'Become a Service Provider' => 'providers',
		'Coverage' => 'coverage',
		'About' => 'about',
		'Contact' => 'contact',
	);
	
	foreach ( $pages as $title => $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page ) {
			echo '<a href="' . esc_url( get_permalink( $page ) ) . '">' . esc_html( $title ) . '</a>';
		}
	}
}

/**
 * Custom menu walker for simple menu output
 */
class Road_Aid_Menu_Walker extends Walker_Nav_Menu {
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;
		
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		
		$output .= '<a href="' . esc_url( $item->url ) . '" class="' . esc_attr( $class_names ) . '">' . esc_html( $item->title ) . '</a>';
	}
}
?>

