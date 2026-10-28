<?php
/**
 * The front page template
 * Displays the content from the WordPress page editor (shortcodes are processed).
 *
 * @package RoadAidEurope
 */

get_header();

$page_content  = '';
$raw_content   = '';
$front_page_id = 0;
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		$front_page_id = get_the_ID();
		$raw_content   = get_post_field( 'post_content', $front_page_id );
		$raw           = $raw_content;
		if ( $raw ) {
			// Homepage is hand-built HTML; wpautop injects <p>/<br> and breaks layout.
			$had_wpautop         = remove_filter( 'the_content', 'wpautop' );
			$had_shortcode_unautop = remove_filter( 'the_content', 'shortcode_unautop' );
			$page_content = apply_filters( 'the_content', $raw );
			if ( $had_wpautop ) {
				add_filter( 'the_content', 'wpautop' );
			}
			if ( $had_shortcode_unautop ) {
				add_filter( 'the_content', 'shortcode_unautop' );
			}
			break;
		}
	}
}

// Europe map
$europe_map = '';
$europe_svg = get_template_directory() . '/assets/europe.svg';
if ( file_exists( $europe_svg ) ) {
	$svg = file_get_contents( $europe_svg );
	$svg = preg_replace( '/<\?xml[^?]+\?>\s*/', '', $svg );
	$svg = str_replace( '<svg ', '<svg class="europe-map-svg" aria-hidden="true" ', $svg );
	$svg = str_replace( ' fill="#ececec"', '', $svg );
	$svg = str_replace( ' stroke="black"', '', $svg );
	$europe_map = $svg;
}

// Blog section — see road_aid_get_home_blog_section_html() in functions.php
$blog_section = road_aid_get_home_blog_section_html();

// Dynamic URLs for home layout
$assistance_page = get_page_by_path( 'assistance' );
$corporate_page  = get_page_by_path( 'corporate' );
$providers_page  = get_page_by_path( 'providers' ) ?: get_page_by_path( 'become-a-service-provider' );
$assistance_url  = ( $assistance_page && get_permalink( $assistance_page ) ) ? get_permalink( $assistance_page ) : home_url( '/assistance' );
$corporate_url   = ( $corporate_page && get_permalink( $corporate_page ) ) ? get_permalink( $corporate_page ) : home_url( '/corporate' );
$providers_url   = ( $providers_page && get_permalink( $providers_page ) ) ? get_permalink( $providers_page ) : home_url( '/providers' );

$emergency_phone = function_exists( 'road_aid_get_emergency_phone' ) ? road_aid_get_emergency_phone() : '+355 69 405 3525';
$emergency_raw   = preg_replace( '/\D/', '', $emergency_phone );

// Hero image - filter allows Customizer or child theme to override
$hero_image = apply_filters( 'road_aid_hero_image_url', '' );
if ( empty( $hero_image ) ) {
	$hero_image = home_url( '/wp-content/uploads/2026/02/Depositphotos_301461242_ds.jpg' );
}

// Use WordPress front page content and inject dynamic placeholders
$html = $page_content;
$html = str_replace( '{{EUROPE_MAP}}', $europe_map, $html );
$html = str_replace( '{{BLOG_SECTION}}', $blog_section, $html );
$html = str_replace( '{{ASSISTANCE_URL}}', esc_url( $assistance_url ), $html );
$html = str_replace( '{{CORPORATE_URL}}', esc_url( $corporate_url ), $html );
$html = str_replace( '{{PROVIDERS_URL}}', esc_url( $providers_url ), $html );
$html = str_replace( '{{EMERGENCY_PHONE}}', esc_html( $emergency_phone ), $html );
$html = str_replace( '{{EMERGENCY_PHONE_RAW}}', esc_attr( $emergency_raw ), $html );
$html = str_replace( '{{HERO_IMAGE}}', esc_url( $hero_image ), $html );

// Form modals for fleet registration and provider application
$html = str_replace( '{{FLEET_FORM}}', road_aid_display_contact_form( 'fleet_registration' ), $html );
$html = str_replace( '{{PROVIDER_FORM}}', road_aid_display_contact_form( 'provider_application' ), $html );

// Translate all hardcoded English strings in home.html
$home_strings = array(
	'Roadside Assistance Albania'    => __( 'Roadside Assistance Albania', 'road-aid-europe' ),
	'Roadside help.<br>'             => __( 'Roadside help.', 'road-aid-europe' ) . '<br>',
	'<span class="text-brand">Minutes away.</span>' => '<span class="text-brand">' . __( 'Minutes away.', 'road-aid-europe' ) . '</span>',
	'24/7 roadside assistance in Albania. Fast tow truck service, tire change, fuel delivery,' => __( '24/7 roadside assistance in Albania. Fast tow truck service, tire change, fuel delivery,', 'road-aid-europe' ),
	'lockout service and emergency towing. Call ROAD AID Europe now.' => __( 'lockout service and emergency towing. Call ROAD AID Europe now.', 'road-aid-europe' ),
	'>Get Help Now<'                 => '>' . __( 'Get Help Now', 'road-aid-europe' ) . '<',
	'>Call +355 69 405 3525<'        => '>' . sprintf( __( 'Call %s', 'road-aid-europe' ), esc_html( $emergency_phone ) ) . '<',
	'Tow Truck Service &bull; Battery &bull; Flat Tire &bull; Lockout &bull; Fuel &bull; Emergency Roadside Assistance' => __( 'Tow Truck Service', 'road-aid-europe' ) . ' &bull; ' . __( 'Battery', 'road-aid-europe' ) . ' &bull; ' . __( 'Flat Tire', 'road-aid-europe' ) . ' &bull; ' . __( 'Lockout', 'road-aid-europe' ) . ' &bull; ' . __( 'Fuel', 'road-aid-europe' ) . ' &bull; ' . __( 'Emergency Roadside Assistance', 'road-aid-europe' ),
	'>Always available<'             => '>' . __( 'Always available', 'road-aid-europe' ) . '<',
	'>Average arrival time<'         => '>' . __( 'Average arrival time', 'road-aid-europe' ) . '<',
	'>Verified providers<'           => '>' . __( 'Verified providers', 'road-aid-europe' ) . '<',
	'>Coverage area<'                => '>' . __( 'Coverage area', 'road-aid-europe' ) . '<',
	'>Help in 3 simple steps<'       => '>' . __( 'Help in 3 simple steps', 'road-aid-europe' ) . '<',
	'No apps to download. No registration. Just tell us where you are.' => __( 'No apps to download. No registration. Just tell us where you are.', 'road-aid-europe' ),
	'>Share your location<'          => '>' . __( 'Share your location', 'road-aid-europe' ) . '<',
	'Tell us where you are and what happened. Takes 30 seconds.' => __( 'Tell us where you are and what happened. Takes 30 seconds.', 'road-aid-europe' ),
	'>We dispatch roadside help<'    => '>' . __( 'We dispatch roadside help', 'road-aid-europe' ) . '<',
	'Our team will quickly send a tow truck or roadside assistance service to your location.' => __( 'Our team will quickly send a tow truck or roadside assistance service to your location.', 'road-aid-europe' ),
	'>Drivers reach you fast<'       => '>' . __( 'Drivers reach you fast', 'road-aid-europe' ) . '<',
	'It only takes 30 seconds to share your location so our drivers can reach you faster.' => __( 'It only takes 30 seconds to share your location so our drivers can reach you faster.', 'road-aid-europe' ),
	'>What we cover<'                => '>' . __( 'What we cover', 'road-aid-europe' ) . '<',
	'Every common roadside emergency, handled by verified local professionals.' => __( 'Every common roadside emergency, handled by verified local professionals.', 'road-aid-europe' ),
	'>Request Service<'              => '>' . __( 'Request Service', 'road-aid-europe' ) . '<',
	'>Towing Service<'               => '>' . __( 'Towing Service', 'road-aid-europe' ) . '<',
	'Fast and reliable tow truck service to move your vehicle safely to the nearest garage or your preferred location.' => __( 'Fast and reliable tow truck service to move your vehicle safely to the nearest garage or your preferred location.', 'road-aid-europe' ),
	'>Battery Jump Start Service<'   => '>' . __( 'Battery Jump Start Service', 'road-aid-europe' ) . '<',
	'Dead battery? We provide quick battery jump start roadside assistance to start your vehicle and get you moving again.' => __( 'Dead battery? We provide quick battery jump start roadside assistance to start your vehicle and get you moving again.', 'road-aid-europe' ),
	'>Flat Tire Service<'            => '>' . __( 'Flat Tire Service', 'road-aid-europe' ) . '<',
	'Flat tire or puncture? Our team offers quick tire change and roadside assistance to help you continue your journey.' => __( 'Flat tire or puncture? Our team offers quick tire change and roadside assistance to help you continue your journey.', 'road-aid-europe' ),
	'>Car Lockout Service<'          => '>' . __( 'Car Lockout Service', 'road-aid-europe' ) . '<',
	'Locked your keys inside? We offer quick car lockout roadside assistance to help you access your vehicle without damage.' => __( 'Locked your keys inside? We offer quick car lockout roadside assistance to help you access your vehicle without damage.', 'road-aid-europe' ),
	'>Fuel Delivery<'                => '>' . __( 'Fuel Delivery', 'road-aid-europe' ) . '<',
	'Ran out of fuel? We provide fast fuel delivery roadside assistance to get you moving again quickly.' => __( 'Ran out of fuel? We provide fast fuel delivery roadside assistance to get you moving again quickly.', 'road-aid-europe' ),
	'>Accident Assistance<'          => '>' . __( 'Accident Assistance', 'road-aid-europe' ) . '<',
	'Had a collision or went off-road? Our team provides accident support, towing service, and roadside assistance to help you quickly and safely.' => __( 'Had a collision or went off-road? Our team provides accident support, towing service, and roadside assistance to help you quickly and safely.', 'road-aid-europe' ),
	/* Medical Assistance + Fly & Fix */
	'>Traveller support<'            => '>' . __( 'Traveller support', 'road-aid-europe' ) . '<',
	'>Medical Assistance<'           => '>' . __( 'Medical Assistance', 'road-aid-europe' ) . '<',
	'Help accessing medical support during your trip' => __( 'Help accessing medical support during your trip', 'road-aid-europe' ),
	'>Call for Medical Support<'     => '>' . __( 'Call for Medical Support', 'road-aid-europe' ) . '<',
	'If you feel unwell while visiting Albania, Road Aid Europe can help coordinate contact with healthcare professionals and local healthcare services.' => __( 'If you feel unwell while visiting Albania, Road Aid Europe can help coordinate contact with healthcare professionals and local healthcare services.', 'road-aid-europe' ),
	'>Doctor Consultation by Phone<' => '>' . __( 'Doctor Consultation by Phone', 'road-aid-europe' ) . '<',
	'We can help arrange a telephone consultation, subject to doctor availability. The doctor determines whether your concern is suitable for assessment by phone or requires an in-person examination.' => __( 'We can help arrange a telephone consultation, subject to doctor availability. The doctor determines whether your concern is suitable for assessment by phone or requires an in-person examination.', 'road-aid-europe' ),
	'>Finding Local Healthcare<'     => '>' . __( 'Finding Local Healthcare', 'road-aid-europe' ) . '<',
	'Our team can assist with locating a nearby clinic, hospital or pharmacy and coordinating appointment requests.' => __( 'Our team can assist with locating a nearby clinic, hospital or pharmacy and coordinating appointment requests.', 'road-aid-europe' ),
	'>Communication Support<'        => '>' . __( 'Communication Support', 'road-aid-europe' ) . '<',
	'We can assist with English-language communication and practical arrangements for accessing local healthcare.' => __( 'We can assist with English-language communication and practical arrangements for accessing local healthcare.', 'road-aid-europe' ),
	'>Transport Coordination<'       => '>' . __( 'Transport Coordination', 'road-aid-europe' ) . '<',
	'If needed, we can help contact local providers to arrange transport to a healthcare facility, subject to availability.' => __( 'If needed, we can help contact local providers to arrange transport to a healthcare facility, subject to availability.', 'road-aid-europe' ),
	'>How to request assistance<'    => '>' . __( 'How to request assistance', 'road-aid-europe' ) . '<',
	'Call our team, share your location and briefly explain the assistance you need. We will discuss the available options and help coordinate your request.' => __( 'Call our team, share your location and briefly explain the assistance you need. We will discuss the available options and help coordinate your request.', 'road-aid-europe' ),
	'>Please note<'                  => '>' . __( 'Please note', 'road-aid-europe' ) . '<',
	'Road Aid Europe provides assistance and coordination. Medical advice, assessments and treatment are provided by independent healthcare professionals. Services depend on your location, provider availability and the nature of your request.' => __( 'Road Aid Europe provides assistance and coordination. Medical advice, assessments and treatment are provided by independent healthcare professionals. Services depend on your location, provider availability and the nature of your request.', 'road-aid-europe' ),
	'This service is not health insurance or an emergency medical service. In a medical emergency, contact local emergency services immediately rather than waiting for a telephone consultation.' => __( 'This service is not health insurance or an emergency medical service. In a medical emergency, contact local emergency services immediately rather than waiting for a telephone consultation.', 'road-aid-europe' ),
	'>Airport car care<'             => '>' . __( 'Airport car care', 'road-aid-europe' ) . '<',
	'>FLY &amp; FIX<'                => '>' . __( 'FLY & FIX', 'road-aid-europe' ) . '<',
	'You fly. We take care of your car.' => __( 'You fly. We take care of your car.', 'road-aid-europe' ),
	'Leave your car with us at Tirana Airport, and we coordinate its maintenance, cleaning, technical inspection or approved repairs while you travel.' => __( 'Leave your car with us at Tirana Airport, and we coordinate its maintenance, cleaning, technical inspection or approved repairs while you travel.', 'road-aid-europe' ),
	'>Book Your Car Drop-off<'       => '>' . __( 'Book Your Car Drop-off', 'road-aid-europe' ) . '<',
	'>Our Services<'                 => '>' . __( 'Our Services', 'road-aid-europe' ) . '<',
	'>Car Cleaning &amp; Detailing<' => '>' . __( 'Car Cleaning & Detailing', 'road-aid-europe' ) . '<',
	'Interior and exterior washing, deep cleaning and upholstery care.' => __( 'Interior and exterior washing, deep cleaning and upholstery care.', 'road-aid-europe' ),
	'>Routine Maintenance<'          => '>' . __( 'Routine Maintenance', 'road-aid-europe' ) . '<',
	'Oil and filter changes and scheduled servicing.' => __( 'Oil and filter changes and scheduled servicing.', 'road-aid-europe' ),
	'>Vehicle Checks<'               => '>' . __( 'Vehicle Checks', 'road-aid-europe' ) . '<',
	'Tyre, brake, battery and fluid-level checks.' => __( 'Tyre, brake, battery and fluid-level checks.', 'road-aid-europe' ),
	'>Diagnostics<'                  => '>' . __( 'Diagnostics', 'road-aid-europe' ) . '<',
	'Assessment of warning lights and reported vehicle issues.' => __( 'Assessment of warning lights and reported vehicle issues.', 'road-aid-europe' ),
	'>Repairs<'                      => '>' . __( 'Repairs', 'road-aid-europe' ) . '<',
	'Coordination with suitable workshops based on your vehicle’s needs.' => __( 'Coordination with suitable workshops based on your vehicle’s needs.', 'road-aid-europe' ),
	'>Technical Vehicle Inspection<' => '>' . __( 'Technical Vehicle Inspection', 'road-aid-europe' ) . '<',
	'Arrangement of your vehicle’s mandatory physical and technical inspection at DPSHTRR regional centres or authorised technical inspection centres.' => __( 'Arrangement of your vehicle’s mandatory physical and technical inspection at DPSHTRR regional centres or authorised technical inspection centres.', 'road-aid-europe' ),
	'>How It Works<'                 => '>' . __( 'How It Works', 'road-aid-europe' ) . '<',
	'>Book Your Drop-off<'           => '>' . __( 'Book Your Drop-off', 'road-aid-europe' ) . '<',
	'Share your travel dates, vehicle details and the services you need.' => __( 'Share your travel dates, vehicle details and the services you need.', 'road-aid-europe' ),
	'>Leave Your Car with Us<'       => '>' . __( 'Leave Your Car with Us', 'road-aid-europe' ) . '<',
	'Drop off your vehicle at the agreed location near Tirana Airport. We record its condition and confirm your requests.' => __( 'Drop off your vehicle at the agreed location near Tirana Airport. We record its condition and confirm your requests.', 'road-aid-europe' ),
	'>Approve the Work<'             => '>' . __( 'Approve the Work', 'road-aid-europe' ) . '<',
	'We share the proposed services, estimated costs and expected timing. Work begins only after your approval. Any additional work requires your approval separately.' => __( 'We share the proposed services, estimated costs and expected timing. Work begins only after your approval. Any additional work requires your approval separately.', 'road-aid-europe' ),
	'>Collect Your Car<'             => '>' . __( 'Collect Your Car', 'road-aid-europe' ) . '<',
	'We coordinate collection around your return and explain the work completed.' => __( 'We coordinate collection around your return and explain the work completed.', 'road-aid-europe' ),
	'Service availability and completion times depend on workshop and inspection appointments, parts availability and your travel dates. Arrangements and applicable charges are confirmed before proceeding.' => __( 'Service availability and completion times depend on workshop and inspection appointments, parts availability and your travel dates. Arrangements and applicable charges are confirmed before proceeding.', 'road-aid-europe' ),
	'>Get Roadside Help<'                 => '>' . __( 'Get Roadside Help', 'road-aid-europe' ) . '<',
	'>Medical support when you need it.<' => '>' . __( 'Medical support when you need it.', 'road-aid-europe' ) . '<',
	'>Explore Fly &amp; Fix<'             => '>' . __( 'Explore Fly & Fix', 'road-aid-europe' ) . '<',
	'Only approved work. Additional work requires your approval.' => __( 'Only approved work. Additional work requires your approval.', 'road-aid-europe' ),
	'>Drop off your car.<'                => '>' . __( 'Drop off your car.', 'road-aid-europe' ) . '<',
	'>We arrange the approved work.<'     => '>' . __( 'We arrange the approved work.', 'road-aid-europe' ) . '<',
	'>Collect on your return.<'           => '>' . __( 'Collect on your return.', 'road-aid-europe' ) . '<',
	'>Maintenance<'                       => '>' . __( 'Maintenance', 'road-aid-europe' ) . '<',

	'24/7 roadside assistance across Albania — fast tow truck response, tire change, fuel delivery, lockout service and emergency support when you need it.' => __( '24/7 roadside assistance across Albania — fast tow truck response, tire change, fuel delivery, lockout service and emergency support when you need it.', 'road-aid-europe' ),
	'>Battery Assistance<' => '>' . __( 'Battery Assistance', 'road-aid-europe' ) . '<',
	'>Fuel Delivery<' => '>' . __( 'Fuel Delivery', 'road-aid-europe' ) . '<',
	'>Drop off your car<' => '>' . __( 'Drop off your car', 'road-aid-europe' ) . '<',
	'>We arrange the approved work<' => '>' . __( 'We arrange the approved work', 'road-aid-europe' ) . '<',
	'>Collect on your return<' => '>' . __( 'Collect on your return', 'road-aid-europe' ) . '<',
	'Share your travel dates and leave your vehicle near Tirana Airport.' => __( 'Share your travel dates and leave your vehicle near Tirana Airport.', 'road-aid-europe' ),
	'Maintenance and repairs are coordinated only with your approval.' => __( 'Maintenance and repairs are coordinated only with your approval.', 'road-aid-europe' ),
	'Pick up your car when you land — we coordinate around your flight.' => __( 'Pick up your car when you land — we coordinate around your flight.', 'road-aid-europe' ),
	'Leave your car with us at Tirana Airport. While you travel, we arrange the maintenance or repairs you approve. Collect your car when you return.' => __( 'Leave your car with us at Tirana Airport. While you travel, we arrange the maintenance or repairs you approve. Collect your car when you return.', 'road-aid-europe' ),

	'Roadside assistance across Albania.' => __( 'Roadside assistance across Albania.', 'road-aid-europe' ),
	'>Coverage<'                     => '>' . __( 'Coverage', 'road-aid-europe' ) . '<',
	'Roadside assistance and tow truck services across major Albanian cities.' => __( 'Roadside assistance and tow truck services across major Albanian cities.', 'road-aid-europe' ),
	'>For businesses<'               => '>' . __( 'For businesses', 'road-aid-europe' ) . '<',
	'Rental companies, fleet operators, and insurers — we handle roadside for your clients so you don\'t have to.' => __( 'Rental companies, fleet operators, and insurers — we handle roadside for your clients so you don\'t have to.', 'road-aid-europe' ),
	'>Rental &amp; Fleet Operators<' => '>' . __( 'Rental & Fleet Operators', 'road-aid-europe' ) . '<',
	'Your renters and drivers get 24/7 roadside assistance. We dispatch, track, and report — you get clean summaries and zero headaches.' => __( 'Your renters and drivers get 24/7 roadside assistance. We dispatch, track, and report — you get clean summaries and zero headaches.', 'road-aid-europe' ),
	'>Dedicated dispatch for your fleet<' => '>' . __( 'Dedicated dispatch for your fleet', 'road-aid-europe' ) . '<',
	'>Transparent per-case reporting<'    => '>' . __( 'Transparent per-case reporting', 'road-aid-europe' ) . '<',
	'>No subscription — pay per use<'     => '>' . __( 'No subscription — pay per use', 'road-aid-europe' ) . '<',
	'>Register Your Fleet<'          => '>' . __( 'Register Your Fleet', 'road-aid-europe' ) . '<',
	'>Become a Service Provider<'    => '>' . __( 'Become a Service Provider', 'road-aid-europe' ) . '<',
	'Own a tow truck, garage, or mobile repair service? Join our verified network and receive dispatched jobs in your area.' => __( 'Own a tow truck, garage, or mobile repair service? Join our verified network and receive dispatched jobs in your area.', 'road-aid-europe' ),
	'>Get dispatched jobs directly<'      => '>' . __( 'Get dispatched jobs directly', 'road-aid-europe' ) . '<',
	'>Flexible — accept what you can handle<' => '>' . __( 'Flexible — accept what you can handle', 'road-aid-europe' ) . '<',
	'>Grow your business with us<'        => '>' . __( 'Grow your business with us', 'road-aid-europe' ) . '<',
	'>Apply as Provider<'            => '>' . __( 'Apply as Provider', 'road-aid-europe' ) . '<',
	'>Emergency<'                    => '>' . __( 'Emergency', 'road-aid-europe' ) . '<',
	'Our team is available <strong>24/7</strong> to provide fast <strong>tow truck and roadside help anywhere in Albania.</strong>' => sprintf( __( 'Our team is available %1$s24/7%2$s to provide fast %1$stow truck and roadside help anywhere in Albania.%2$s', 'road-aid-europe' ), '<strong>', '</strong>' ),
	'<strong>Our emergency services include:</strong>' => '<strong>' . __( 'Our emergency services include:', 'road-aid-europe' ) . '</strong>',
	'<li>Tow Truck Service</li>'          => '<li>' . __( 'Tow Truck Service', 'road-aid-europe' ) . '</li>',
	'<li>Car Towing</li>'                 => '<li>' . __( 'Car Towing', 'road-aid-europe' ) . '</li>',
	'<li>Tire Change</li>'                => '<li>' . __( 'Tire Change', 'road-aid-europe' ) . '</li>',
	'<li>Fuel Delivery</li>'              => '<li>' . __( 'Fuel Delivery', 'road-aid-europe' ) . '</li>',
	'<li>Battery Jump Start</li>'         => '<li>' . __( 'Battery Jump Start', 'road-aid-europe' ) . '</li>',
	'<li>Car Lockout Assistance</li>'     => '<li>' . __( 'Car Lockout Assistance', 'road-aid-europe' ) . '</li>',
	'<strong>Important:</strong> If there is an accident with injuries or a dangerous situation, please contact <strong>local emergency authorities first</strong>.' => '<strong>' . __( 'Important:', 'road-aid-europe' ) . '</strong> ' . __( 'If there is an accident with injuries or a dangerous situation, please contact', 'road-aid-europe' ) . ' <strong>' . __( 'local emergency authorities first', 'road-aid-europe' ) . '</strong>.',
);
foreach ( $home_strings as $en => $translated ) {
	$html = str_replace( $en, $translated, $html );
}

// If the homepage HTML never received the blog block (no placeholder/shortcode in the page), append it once. Detect by section id, not raw token, so shortcode + append does not duplicate.
if ( strpos( $html, 'id="blog"' ) === false && strpos( $html, "id='blog'" ) === false && $blog_section !== '' ) {
	$html .= $blog_section;
}

if ( function_exists( 'road_aid_render_home_debug_panel' ) ) {
	road_aid_render_home_debug_panel(
		array(
			'front_page_id'            => $front_page_id,
			'raw_content_len'          => strlen( (string) $raw_content ),
			'raw_has_placeholder'      => strpos( (string) $raw_content, '{{BLOG_SECTION}}' ) !== false,
			'filtered_has_placeholder' => strpos( $page_content, '{{BLOG_SECTION}}' ) !== false,
			'filtered_has_shortcode'   => strpos( $page_content, '[road_aid_blog_section' ) !== false,
			'output_has_blog_id'       => strpos( $html, 'id="blog"' ) !== false || strpos( $html, "id='blog'" ) !== false,
			'page_content_empty'       => $page_content === '',
		)
	);
}

echo $html;

get_footer();
