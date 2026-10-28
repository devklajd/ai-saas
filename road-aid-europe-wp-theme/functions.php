<?php
/**
 * Road Aid Europe Theme Functions
 *
 * @package RoadAidEurope
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Road Aid CRM: forms, submissions, WhatsApp, block
$road_aid_crm_file = get_template_directory() . '/inc/road-aid-crm.php';
if ( file_exists( $road_aid_crm_file ) ) {
	require_once $road_aid_crm_file;
}

$road_aid_home_debug_file = get_template_directory() . '/inc/road-aid-home-debug.php';
if ( file_exists( $road_aid_home_debug_file ) ) {
	require_once $road_aid_home_debug_file;
}

/**
 * Check if we're in installation/upgrade mode
 * This prevents theme functions from interfering with WordPress or plugin installation
 */
function road_aid_is_installing() {
	// Check for WordPress installation
	if ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) {
		return true;
	}
	
	// Check for plugin installation (WordPress sets this during plugin install)
	if ( defined( 'WP_SETUP_CONFIG' ) && WP_SETUP_CONFIG ) {
		return true;
	}
	
	// Check if we're in the plugin installation process via AJAX
	// During plugin installation, WordPress uses AJAX
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['action'] ) ) {
		$action = sanitize_text_field( $_REQUEST['action'] );
		// WordPress plugin installation actions
		if ( in_array( $action, array( 'install-plugin', 'activate-plugin', 'update-plugin', 'delete-plugin' ) ) ) {
			return true;
		}
	}
	
	return false;
}

/**
 * Theme Setup
 */
function road_aid_theme_setup() {
	// Don't run during WordPress or plugin installation
	if ( road_aid_is_installing() ) {
		return;
	}
	
	// Add theme support
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 80,
		'width'       => 240,
		'flex-height' => true,
		'flex-width'  => true,
		'header-text' => array( 'site-title', 'site-description' ),
	) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style' ) );
	
	// SEO Support
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	
	// Yoast SEO Breadcrumbs support (only if function exists)
	if ( function_exists( 'add_theme_support' ) ) {
		// This might not be a standard theme support, so we'll skip it if it causes issues
		// add_theme_support( 'yoast-seo-breadcrumbs' );
	}
	
	// Register navigation menus
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'road-aid-europe' ),
		'footer'  => __( 'Footer Menu', 'road-aid-europe' ),
	) );
	
	// Register widget areas
	register_sidebar( array(
		'name'          => __( 'Footer Widget Area', 'road-aid-europe' ),
		'id'            => 'footer-widgets',
		'description'   => __( 'Widgets in this area will appear in the footer.', 'road-aid-europe' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	
	register_sidebar( array(
		'name'          => __( 'Sidebar Widget Area', 'road-aid-europe' ),
		'id'            => 'sidebar-widgets',
		'description'   => __( 'Widgets in this area will appear in the sidebar on pages and posts.', 'road-aid-europe' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	
	// Add image sizes for better SEO
	add_image_size( 'road-aid-featured', 1200, 630, true ); // Open Graph size
	add_image_size( 'road-aid-thumbnail', 300, 300, true );
	
	// Load theme textdomain for translations
	load_theme_textdomain( 'road-aid-europe', get_template_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'road_aid_theme_setup' );

/* ─────────────────────────────────────────────
   Language Switcher
   ───────────────────────────────────────────── */

function road_aid_get_languages() {
	return array(
		'en' => array( 'label' => 'English',   'native' => 'English',   'flag' => '🇬🇧' ),
		'it' => array( 'label' => 'Italian',   'native' => 'Italiano',  'flag' => '🇮🇹' ),
		'es' => array( 'label' => 'Spanish',   'native' => 'Español',   'flag' => '🇪🇸' ),
		'de' => array( 'label' => 'German',    'native' => 'Deutsch',   'flag' => '🇩🇪' ),
		'sq' => array( 'label' => 'Albanian',  'native' => 'Shqip',     'flag' => '🇦🇱' ),
	);
}

function road_aid_get_current_lang() {
	if ( isset( $_GET['lang'] ) && array_key_exists( sanitize_text_field( $_GET['lang'] ), road_aid_get_languages() ) ) {
		return sanitize_text_field( $_GET['lang'] );
	}
	if ( isset( $_COOKIE['road_aid_lang'] ) && array_key_exists( $_COOKIE['road_aid_lang'], road_aid_get_languages() ) ) {
		return $_COOKIE['road_aid_lang'];
	}
	return 'en';
}

function road_aid_set_lang_cookie() {
	if ( isset( $_GET['lang'] ) ) {
		$lang = sanitize_text_field( $_GET['lang'] );
		if ( array_key_exists( $lang, road_aid_get_languages() ) ) {
			setcookie( 'road_aid_lang', $lang, time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
		}
	}
}
add_action( 'init', 'road_aid_set_lang_cookie', 1 );

function road_aid_switch_locale( $locale ) {
	$lang = road_aid_get_current_lang();
	$map  = array(
		'en' => 'en_US',
		'it' => 'it_IT',
		'es' => 'es_ES',
		'de' => 'de_DE',
		'sq' => 'sq',
	);
	return isset( $map[ $lang ] ) ? $map[ $lang ] : $locale;
}
add_filter( 'locale', 'road_aid_switch_locale' );

function road_aid_load_po_translations( $override, $domain, $mofile ) {
	if ( 'road-aid-europe' !== $domain ) {
		return $override;
	}
	$po_file = str_replace( '.mo', '.po', $mofile );
	if ( ! file_exists( $po_file ) ) {
		return $override;
	}
	$content = file_get_contents( $po_file );
	if ( ! $content ) {
		return $override;
	}

	global $l10n;
	$translations = new \Translations();
	preg_match_all( '/msgid\s+"(.+?)"\s+msgstr\s+"(.+?)"/s', $content, $matches, PREG_SET_ORDER );
	foreach ( $matches as $m ) {
		$original    = stripcslashes( $m[1] );
		$translated  = stripcslashes( $m[2] );
		if ( $original && $translated ) {
			$entry = new \Translation_Entry( array(
				'singular'     => $original,
				'translations' => array( $translated ),
			) );
			$translations->add_entry( $entry );
		}
	}

	if ( isset( $l10n[ $domain ] ) ) {
		$l10n[ $domain ]->merge_with( $translations );
	} else {
		$l10n[ $domain ] = $translations;
	}

	return true;
}
add_filter( 'override_load_textdomain', 'road_aid_load_po_translations', 10, 3 );

function road_aid_lang_url( $lang_code ) {
	$url = remove_query_arg( 'lang' );
	return esc_url( add_query_arg( 'lang', $lang_code, $url ) );
}

function road_aid_language_switcher( $class_prefix = '' ) {
	$languages    = road_aid_get_languages();
	$current_lang = road_aid_get_current_lang();
	$current      = $languages[ $current_lang ];
	$prefix       = $class_prefix ? $class_prefix . '-' : '';
	$uid          = 'langSw_' . wp_rand( 1000, 9999 );
	ob_start();
	?>
	<div class="<?php echo esc_attr( $prefix ); ?>lang-switcher" id="<?php echo esc_attr( $uid ); ?>">
		<button class="<?php echo esc_attr( $prefix ); ?>lang-current" type="button" onclick="(function(el){var p=el.closest('.<?php echo esc_attr( $prefix ); ?>lang-switcher');p.classList.toggle('open');event.stopPropagation();})(this)">
			<span class="lang-flag"><?php echo $current['flag']; ?></span>
			<span class="lang-code"><?php echo esc_html( strtoupper( $current_lang ) ); ?></span>
			<svg class="lang-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
		</button>
		<ul class="<?php echo esc_attr( $prefix ); ?>lang-dropdown" role="listbox">
			<?php foreach ( $languages as $code => $lang ) : ?>
				<li role="option"<?php echo $code === $current_lang ? ' aria-selected="true" class="active"' : ''; ?>>
					<a href="<?php echo road_aid_lang_url( $code ); ?>">
						<span class="lang-flag"><?php echo $lang['flag']; ?></span>
						<span class="lang-name"><?php echo esc_html( $lang['native'] ); ?></span>
						<span class="lang-code-label"><?php echo esc_html( strtoupper( $code ) ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * @return string Markup from template-parts/home-blog.php
 */
function road_aid_get_home_blog_section_html() {
	$blog_post_type = apply_filters( 'road_aid_home_blog_post_type', 'post' );
	$blog_query     = new WP_Query(
		array(
			'post_type'           => $blog_post_type,
			'posts_per_page'      => 6,
			'post_status'         => 'publish',
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'suppress_filters'    => true,
		)
	);
	$blog_posts = $blog_query->posts;
	if ( empty( $blog_posts ) ) {
		$blog_posts = get_posts(
			array(
				'post_type'        => $blog_post_type,
				'numberposts'      => 6,
				'post_status'      => 'publish',
				'orderby'          => 'date',
				'order'            => 'DESC',
				'suppress_filters' => true,
			)
		);
	}
	$blog_posts    = apply_filters( 'road_aid_blog_posts', $blog_posts );
	$posts_page_id = (int) get_option( 'page_for_posts' );
	$blog_url      = ( $posts_page_id && get_permalink( $posts_page_id ) ) ? get_permalink( $posts_page_id ) : get_post_type_archive_link( $blog_post_type );
	if ( ! $blog_url ) {
		$blog_url = home_url( '/' );
	}
	// Must require from this scope — locate_template( ..., true ) loads via load_template(),
	// where $blog_posts / $blog_url are out of scope and the template always sees an empty list.
	$template_path = locate_template( 'template-parts/home-blog.php', false, false );
	ob_start();
	if ( $template_path ) {
		require $template_path;
	}
	$html = ob_get_clean();
	wp_reset_postdata();

	$GLOBALS['road_aid_home_blog_debug'] = array(
		'post_type'               => $blog_post_type,
		'queried_count'           => count( $blog_posts ),
		'wp_query_found_posts'    => (int) $blog_query->found_posts,
		'used_get_posts_fallback' => empty( $blog_query->posts ) && ! empty( $blog_posts ),
		'blog_template'           => $template_path ? $template_path : '(missing)',
		'blog_section_bytes'      => strlen( $html ),
		'db_publish_count'        => function_exists( 'road_aid_debug_count_published_posts' ) ? road_aid_debug_count_published_posts( $blog_post_type ) : null,
	);

	return $html;
}

/**
 * Shortcode for the blog section when {{BLOG_SECTION}} cannot be kept in page HTML (e.g. Custom HTML block quirks).
 * Usage: [road_aid_blog_section]
 */
function road_aid_shortcode_blog_section() {
	return road_aid_get_home_blog_section_html();
}
add_shortcode( 'road_aid_blog_section', 'road_aid_shortcode_blog_section' );

/**
 * Bump PHP memory and time limits when uploading images to avoid "server cannot process the image" errors.
 * Image processing (resizing, thumbnails) is memory-heavy; 117KB JPG can be 3000×2000 px and need a lot of RAM.
 */
function road_aid_bump_limits_for_image_upload( $file ) {
	if ( isset( $file['type'] ) && strpos( $file['type'], 'image/' ) === 0 ) {
		@ini_set( 'memory_limit', '512M' );
		@ini_set( 'max_execution_time', '120' );
	}
	return $file;
}
add_filter( 'wp_handle_upload', 'road_aid_bump_limits_for_image_upload', 1 );

/** Bump memory when generating thumbnails (this is when "cannot process image" usually happens). */
function road_aid_bump_limits_before_resize( $sizes ) {
	@ini_set( 'memory_limit', '512M' );
	@ini_set( 'max_execution_time', '120' );
	return $sizes;
}
add_filter( 'intermediate_image_sizes_advanced', 'road_aid_bump_limits_before_resize', 1 );

/** Also when loading the edit image screen or uploading via AJAX. */
function road_aid_bump_limits_for_image_metadata() {
	@ini_set( 'memory_limit', '512M' );
	@ini_set( 'max_execution_time', '120' );
}
add_action( 'load-image.php', 'road_aid_bump_limits_for_image_metadata' );
add_action( 'wp_ajax_upload-attachment', 'road_aid_bump_limits_for_image_metadata', 0 );

/**
 * Enqueue scripts and styles
 */
function road_aid_enqueue_assets() {
	// Theme uses RBNo 3.1 (loaded via @font-face in style.css from assets/fonts/)
	wp_enqueue_style( 'road-aid-style', get_stylesheet_uri(), array(), '1.5.5' );
	
	// Enqueue JavaScript
	wp_enqueue_script( 'road-aid-script', get_template_directory_uri() . '/assets/theme.js', array(), '1.2.0', true );

	// Google reCAPTCHA v2 API (loaded async/defer per Google's recommendation)
	if ( road_aid_recaptcha_enabled() ) {
		wp_enqueue_script( 'road-aid-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), null, true );
	}
	
	// Localize script with theme data
	wp_localize_script( 'road-aid-script', 'roadAidData', array(
		'emergencyPhone' => road_aid_get_emergency_phone(),
		'whatsappPhone'  => road_aid_get_whatsapp_phone(),
		'businessEmail'  => road_aid_get_business_email(),
		'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
		'nonce'          => wp_create_nonce( 'road_aid_nonce' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'road_aid_enqueue_assets' );

/**
 * Output site icon (favicon) – Road Aid Europe Site Icon.svg
 */
function road_aid_site_icon() {
	if ( is_admin() ) {
		return;
	}
	$icon_url = get_template_directory_uri() . '/assets/road aid europe logo new.svg';
	echo '<link rel="icon" href="' . esc_url( $icon_url ) . '" type="image/svg+xml" />' . "\n";
}
add_action( 'wp_head', 'road_aid_site_icon', 0 );

/**
 * Customizer settings
 */
function road_aid_customize_register( $wp_customize ) {
	// Emergency Phone Section
	$wp_customize->add_section( 'road_aid_contact', array(
		'title'    => __( 'Contact Information', 'road-aid-europe' ),
		'priority' => 30,
	) );
	
	// Emergency Phone
	$wp_customize->add_setting( 'emergency_phone', array(
		'default'           => '+355694053525',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'emergency_phone', array(
		'label'   => __( 'Emergency Phone', 'road-aid-europe' ),
		'section' => 'road_aid_contact',
		'type'    => 'text',
	) );
	
	// WhatsApp Phone
	$wp_customize->add_setting( 'whatsapp_phone', array(
		'default'           => '355694053525',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'whatsapp_phone', array(
		'label'   => __( 'WhatsApp Phone', 'road-aid-europe' ),
		'section' => 'road_aid_contact',
		'type'    => 'text',
	) );
	
	// Business Email
	$wp_customize->add_setting( 'business_email', array(
		'default'           => 'info@roadaideurope.com',
		'sanitize_callback' => 'sanitize_email',
	) );
	$wp_customize->add_control( 'business_email', array(
		'label'   => __( 'Business Email', 'road-aid-europe' ),
		'section' => 'road_aid_contact',
		'type'    => 'email',
	) );

	// reCAPTCHA Section
	$wp_customize->add_section( 'road_aid_recaptcha', array(
		'title'       => __( 'Google reCAPTCHA (v2)', 'road-aid-europe' ),
		'description' => __( 'Add spam protection to contact forms. Get your keys at https://www.google.com/recaptcha/admin', 'road-aid-europe' ),
		'priority'    => 31,
	) );

	// reCAPTCHA Site Key
	$wp_customize->add_setting( 'road_aid_recaptcha_site_key', array(
		'default'           => '6LenX0stAAAAANXk_EhYcvVRTc7CepVgqS38y7tc',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'road_aid_recaptcha_site_key', array(
		'label'   => __( 'Site Key', 'road-aid-europe' ),
		'section' => 'road_aid_recaptcha',
		'type'    => 'text',
	) );

	// reCAPTCHA Secret Key
	$wp_customize->add_setting( 'road_aid_recaptcha_secret_key', array(
		'default'           => '6LenX0stAAAAAH0Rs1spaW4TQ1krY0KjL2yx8L0o',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'road_aid_recaptcha_secret_key', array(
		'label'   => __( 'Secret Key', 'road-aid-europe' ),
		'section' => 'road_aid_recaptcha',
		'type'    => 'text',
	) );

}
add_action( 'customize_register', 'road_aid_customize_register' );

/**
 * Get theme option helper
 */
function road_aid_get_option( $option, $default = '' ) {
	return get_theme_mod( $option, $default );
}

/**
 * Get emergency phone
 */
function road_aid_get_emergency_phone() {
	return road_aid_get_option( 'emergency_phone', '+355694053525' );
}

/**
 * Get WhatsApp phone
 */
function road_aid_get_whatsapp_phone() {
	return road_aid_get_option( 'whatsapp_phone', '355694053525' );
}

/**
 * Get business email
 */
function road_aid_get_business_email() {
	return road_aid_get_option( 'business_email', 'info@roadaideurope.com' );
}

/**
 * Get reCAPTCHA v2 site key
 */
function road_aid_get_recaptcha_site_key() {
	return trim( road_aid_get_option( 'road_aid_recaptcha_site_key', '6LenX0stAAAAANXk_EhYcvVRTc7CepVgqS38y7tc' ) );
}

/**
 * Get reCAPTCHA v2 secret key
 */
function road_aid_get_recaptcha_secret_key() {
	return trim( road_aid_get_option( 'road_aid_recaptcha_secret_key', '6LenX0stAAAAAH0Rs1spaW4TQ1krY0KjL2yx8L0o' ) );
}

/**
 * Whether reCAPTCHA is configured (both keys present)
 */
function road_aid_recaptcha_enabled() {
	return road_aid_get_recaptcha_site_key() !== '' && road_aid_get_recaptcha_secret_key() !== '';
}

/**
 * Verify a reCAPTCHA v2 response token with Google's API.
 *
 * @param string $token The g-recaptcha-response value from the form.
 * @return bool True if verification succeeds (or reCAPTCHA is not configured).
 */
function road_aid_verify_recaptcha( $token ) {
	// If not configured, don't block submissions.
	if ( ! road_aid_recaptcha_enabled() ) {
		return true;
	}

	if ( empty( $token ) ) {
		return false;
	}

	$response = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array(
		'timeout' => 10,
		'body'    => array(
			'secret'   => road_aid_get_recaptcha_secret_key(),
			'response' => $token,
			'remoteip' => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
		),
	) );

	if ( is_wp_error( $response ) ) {
		// Network failure verifying – log but do not silently accept spam.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Road Aid reCAPTCHA verify error: ' . $response->get_error_message() );
		}
		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	return ! empty( $body['success'] );
}

/**
 * Send WhatsApp notification
 */
function road_aid_send_whatsapp_notification( $message, $phone_number = null ) {
	if ( empty( $phone_number ) ) {
		$phone_number = road_aid_get_whatsapp_phone();
	}
	
	// Clean phone number (remove + and spaces, keep only digits)
	$phone_number = preg_replace( '/[^0-9]/', '', $phone_number );
	
	if ( empty( $phone_number ) ) {
		return false;
	}
	
	// Try multiple methods to send WhatsApp message
	
	// Method 1: CallMeBot API (free, simple)
	// Note: This requires the phone number to be registered with CallMeBot
	// Visit: https://www.callmebot.com/blog/free-api-whatsapp-messages/
	$callmebot_api_key = get_option( 'road_aid_callmebot_api_key', '' );
	if ( ! empty( $callmebot_api_key ) ) {
		$api_url = "https://api.callmebot.com/whatsapp.php?phone={$phone_number}&text=" . urlencode( $message ) . "&apikey={$callmebot_api_key}";
		$response = wp_remote_get( $api_url, array( 'timeout' => 10 ) );
		if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
			return true;
		}
	}
	
	// Method 2: Custom Webhook/API URL (for Twilio, MessageBird, etc.)
	$whatsapp_webhook_url = get_option( 'road_aid_whatsapp_webhook_url', '' );
	if ( ! empty( $whatsapp_webhook_url ) ) {
		$webhook_data = array(
			'phone'   => $phone_number,
			'message' => $message,
		);
		$response = wp_remote_post( $whatsapp_webhook_url, array(
			'timeout' => 10,
			'body'    => json_encode( $webhook_data ),
			'headers' => array( 'Content-Type' => 'application/json' ),
		) );
		if ( ! is_wp_error( $response ) ) {
			return true;
		}
	}
	
	// Method 3: WhatsApp Web API (using wa.me redirect as fallback - this won't auto-send but creates a link)
	// This is a fallback - we'll log that WhatsApp notification was attempted
	// In a real scenario, you'd want to use a proper API service
	
	return false;
}

/**
 * Handle contact form submission
 */
function road_aid_handle_contact_form() {
	// Verify nonce
	if ( ! isset( $_POST['road_aid_contact_nonce'] ) || ! wp_verify_nonce( $_POST['road_aid_contact_nonce'], 'road_aid_contact_form' ) ) {
		wp_die( 'Security check failed', 'Error', array( 'back_link' => true ) );
	}

	// Verify Google reCAPTCHA (v2)
	$recaptcha_token = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';
	if ( ! road_aid_verify_recaptcha( $recaptcha_token ) ) {
		wp_redirect( add_query_arg( 'contact', 'recaptcha', wp_get_referer() ) );
		exit;
	}

	// Get form ID
	$form_id = sanitize_text_field( $_POST['form_id'] ?? 'default' );
	$form = road_aid_get_contact_form( $form_id );
	
	if ( ! $form ) {
		wp_redirect( add_query_arg( 'contact', 'error', wp_get_referer() ) );
		exit;
	}

	$fields = $form['fields'] ?? array();
	$form_data = array();
	$errors = array();
	
	// Process all form fields
	foreach ( $fields as $field ) {
		$field_id = $field['id'] ?? '';
		$field_type = $field['type'] ?? 'text';
		$field_label = $field['label'] ?? $field_id;
		$field_required = $field['required'] ?? false;
		
		if ( empty( $field_id ) ) {
			continue;
		}
		
		$field_value = '';
		
		// Get field value based on type
		if ( $field_type === 'checkbox' ) {
			// Checkboxes only appear in POST if checked
			$field_value = isset( $_POST['form_field'][ $field_id ] ) ? 'Yes' : 'No';
		} elseif ( isset( $_POST['form_field'][ $field_id ] ) ) {
			if ( $field_type === 'textarea' ) {
				$field_value = sanitize_textarea_field( $_POST['form_field'][ $field_id ] );
			} else {
				$field_value = sanitize_text_field( $_POST['form_field'][ $field_id ] );
			}
		}
		
		// Validate required fields
		if ( $field_required ) {
			if ( $field_type === 'checkbox' ) {
				if ( ! isset( $_POST['form_field'][ $field_id ] ) ) {
					$errors[] = sprintf( __( '%s is required', 'road-aid-europe' ), $field_label );
				}
			} elseif ( empty( $field_value ) ) {
				$errors[] = sprintf( __( '%s is required', 'road-aid-europe' ), $field_label );
			}
		}
		
		// Validate email field
		if ( $field_type === 'email' && ! empty( $field_value ) && ! is_email( $field_value ) ) {
			$errors[] = sprintf( __( '%s must be a valid email address', 'road-aid-europe' ), $field_label );
		}
		
		$form_data[ $field_id ] = array(
			'label' => $field_label,
			'value' => $field_value,
			'type'  => $field_type,
		);
	}
	
	if ( ! empty( $errors ) ) {
		wp_redirect( add_query_arg( 'contact', 'error', wp_get_referer() ) );
		exit;
	}

	// Get email recipient (form-specific or default)
	$to = ! empty( $form['email_recipient'] ) ? sanitize_email( $form['email_recipient'] ) : road_aid_get_business_email();
	
	// Get email and name for headers (if available)
	$email = '';
	$name = '';
	foreach ( $form_data as $field_id => $data ) {
		if ( $data['type'] === 'email' && ! empty( $data['value'] ) ) {
			$email = $data['value'];
		}
		if ( stripos( $data['label'], 'name' ) !== false && ! empty( $data['value'] ) && empty( $name ) ) {
			$name = $data['value'];
		}
	}
	
	// Prepare email subject
	$subject = 'Website Contact - Road Aid Europe';
	foreach ( $form_data as $field_id => $data ) {
		if ( stripos( $data['label'], 'subject' ) !== false && ! empty( $data['value'] ) ) {
			$subject = $data['value'];
			break;
		}
	}
	
	// Prepare email body
	$body = "New contact form submission from Road Aid Europe website:\n\n";
	foreach ( $form_data as $field_id => $data ) {
		if ( ! empty( $data['value'] ) ) {
			$body .= $data['label'] . ': ' . $data['value'] . "\n";
		}
	}

	// Set email headers
	$headers = array();
	if ( ! empty( $email ) ) {
		if ( ! empty( $name ) ) {
			$headers[] = "From: $name <$email>";
		}
		$headers[] = "Reply-To: $email";
	}

	// Send email
	$sent = wp_mail( $to, $subject, $body, $headers );

	// Check if WhatsApp notification is enabled
	$whatsapp_sent = false;
	$whatsapp_enabled = isset( $form['whatsapp_notification'] ) && $form['whatsapp_notification'] === true;
	if ( $whatsapp_enabled ) {
		$whatsapp_message = "🔔 *New Contact Form Submission*\n\n";
		foreach ( $form_data as $field_id => $data ) {
			if ( ! empty( $data['value'] ) && $data['value'] !== 'No' ) {
				$whatsapp_message .= "*" . $data['label'] . "*: " . $data['value'] . "\n";
			}
		}
		$whatsapp_message .= "\n_Form: " . $form['name'] . "_";
		$whatsapp_sent = road_aid_send_whatsapp_notification( $whatsapp_message );
	}

	// Store submission in Road Aid CRM (if function exists)
	if ( function_exists( 'road_aid_crm_save_submission' ) ) {
		road_aid_crm_save_submission(
			$form_id,
			isset( $form['name'] ) ? $form['name'] : $form_id,
			$form_data,
			$sent,
			$whatsapp_sent
		);
	}

	if ( $sent ) {
		wp_redirect( add_query_arg( 'contact', 'success', wp_get_referer() ) );
	} else {
		wp_redirect( add_query_arg( 'contact', 'error', wp_get_referer() ) );
	}
	exit;
}
add_action( 'admin_post_road_aid_contact_submit', 'road_aid_handle_contact_form' );
add_action( 'admin_post_nopriv_road_aid_contact_submit', 'road_aid_handle_contact_form' );

/**
 * Newsletter subscription: save email and redirect
 */
function road_aid_handle_newsletter_subscribe() {
	if ( ! isset( $_POST['road_aid_newsletter_nonce'] ) || ! wp_verify_nonce( $_POST['road_aid_newsletter_nonce'], 'road_aid_newsletter' ) ) {
		wp_safe_redirect( home_url( '/?newsletter=error' ) );
		exit;
	}
	$email = isset( $_POST['newsletter_email'] ) ? sanitize_email( $_POST['newsletter_email'] ) : '';
	if ( ! is_email( $email ) ) {
		wp_safe_redirect( home_url( '/?newsletter=invalid' ) );
		exit;
	}
	$list = get_option( 'road_aid_newsletter_subscribers', array() );
	$list = is_array( $list ) ? $list : array();
	$list[] = array( 'email' => $email, 'subscribed_at' => current_time( 'mysql' ) );
	$list = array_slice( $list, -5000, 5000 );
	update_option( 'road_aid_newsletter_subscribers', $list );
	$redirect = isset( $_POST['_wp_http_referer'] ) ? $_POST['_wp_http_referer'] : home_url( '/' );
	$redirect = add_query_arg( 'newsletter', 'subscribed', $redirect );
	wp_safe_redirect( $redirect );
	exit;
}
add_action( 'admin_post_road_aid_newsletter_subscribe', 'road_aid_handle_newsletter_subscribe' );
add_action( 'admin_post_nopriv_road_aid_newsletter_subscribe', 'road_aid_handle_newsletter_subscribe' );

/**
 * Newsletter subscribe via AJAX (stay on page, show green thank you)
 */
function road_aid_newsletter_ajax_subscribe() {
	if ( ! isset( $_POST['road_aid_newsletter_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['road_aid_newsletter_nonce'] ) ), 'road_aid_newsletter' ) ) {
		wp_send_json_error( array( 'message' => 'error' ) );
		return;
	}
	$email = isset( $_POST['newsletter_email'] ) ? sanitize_email( wp_unslash( $_POST['newsletter_email'] ) ) : '';
	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => 'invalid' ) );
		return;
	}
	$list = get_option( 'road_aid_newsletter_subscribers', array() );
	$list = is_array( $list ) ? $list : array();
	$list[] = array( 'email' => $email, 'subscribed_at' => current_time( 'mysql' ) );
	$list = array_slice( $list, -5000, 5000 );
	update_option( 'road_aid_newsletter_subscribers', $list );
	wp_send_json_success( array( 'message' => 'subscribed' ) );
}
add_action( 'wp_ajax_road_aid_newsletter_ajax_subscribe', 'road_aid_newsletter_ajax_subscribe' );
add_action( 'wp_ajax_nopriv_road_aid_newsletter_ajax_subscribe', 'road_aid_newsletter_ajax_subscribe' );

/**
 * Newsletter shortcode: [road_aid_newsletter]
 */
function road_aid_newsletter_shortcode() {
	$ajax_url = admin_url( 'admin-ajax.php' );
	$ob  = '<div class="section" id="newsletter"><div class="container">';
	$ob .= '<div class="section-title centered"><div><h2>Stay in the loop</h2><p>Get road tips, seasonal reminders, and updates from Road Aid Europe. No spam—just useful info when it matters.</p></div></div>';
	$ob .= '<div id="road-aid-newsletter-card" class="card pad" style="max-width:520px; margin:0 auto;">';
	$ob .= '<form id="road-aid-newsletter-form" class="form road-aid-newsletter-form" method="post" style="display:grid; grid-template-columns:1fr auto; gap:10px; align-items:end;">';
	$ob .= wp_nonce_field( 'road_aid_newsletter', 'road_aid_newsletter_nonce', true, false );
	$ob .= '<div><label for="newsletter-email">Email address</label><input type="email" id="newsletter-email" name="newsletter_email" placeholder="you@example.com" required /></div>';
	$ob .= '<button type="submit" class="btn primary">Subscribe</button></form>';
	$ob .= '<p class="note" style="margin:12px 0 0; font-size:11px;">We only send occasional updates. You can unsubscribe at any time. See our <a href="' . esc_url( get_privacy_policy_url() ?: '#' ) . '">Privacy Policy</a>.</p>';
	$ob .= '</div></div></div>';
	$ob .= '<script>';
	$ob .= '(function(){ var f = document.getElementById("road-aid-newsletter-form"); if(!f) return; var card = document.getElementById("road-aid-newsletter-card"); var ajaxUrl = ' . wp_json_encode( $ajax_url ) . ';';
	$ob .= 'f.addEventListener("submit", function(e){ e.preventDefault(); var btn = f.querySelector("button[type=submit]"); var orig = btn.textContent; btn.disabled = true; btn.textContent = "Subscribing…";';
	$ob .= 'var fd = new FormData(f); fd.set("action", "road_aid_newsletter_ajax_subscribe");';
	$ob .= 'fetch(ajaxUrl, { method: "POST", body: fd, credentials: "same-origin" }).then(function(r){ return r.json(); }).then(function(data){';
	$ob .= 'if(data.success){ card.innerHTML = "<p class=\"note\" style=\"margin:0; padding:16px; background:#0d7d4a; color:#fff; border-radius:6px;\">Thanks for subscribing. You\'ll hear from us when we have updates or tips worth sharing.</p>"; } else { btn.disabled = false; btn.textContent = orig; card.insertAdjacentHTML("afterbegin", "<p class=\"note\" style=\"margin:0 0 12px; padding:12px; background:#f8d7da; color:#721c24; border-radius:6px;\">Please enter a valid email address and try again.</p>"); }';
	$ob .= '}).catch(function(){ btn.disabled = false; btn.textContent = orig; card.insertAdjacentHTML("afterbegin", "<p class=\"note\" style=\"margin:0 0 12px; padding:12px; background:#f8d7da; color:#721c24; border-radius:6px;\">Something went wrong. Please try again.</p>"); }); }); })();';
	$ob .= '</script>';
	return $ob;
}
add_shortcode( 'road_aid_newsletter', 'road_aid_newsletter_shortcode' );

/**
 * Newsletter subscribers admin page (Road Aid CRM → Newsletter)
 */
function road_aid_newsletter_subscribers_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$list = get_option( 'road_aid_newsletter_subscribers', array() );
	$list = is_array( $list ) ? $list : array();

	// Export CSV
	if ( isset( $_GET['export'] ) && $_GET['export'] === 'csv' && check_admin_referer( 'road_aid_newsletter_export' ) ) {
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="road-aid-newsletter-subscribers-' . date( 'Y-m-d' ) . '.csv"' );
		$out = fopen( 'php://output', 'w' );
		fputcsv( $out, array( 'Email', 'Subscribed at' ) );
		foreach ( $list as $row ) {
			fputcsv( $out, array( $row['email'] ?? '', $row['subscribed_at'] ?? '' ) );
		}
		fclose( $out );
		exit;
	}

	$export_url = wp_nonce_url( admin_url( 'admin.php?page=road-aid-crm-newsletter&export=csv' ), 'road_aid_newsletter_export' );
	?>
	<div class="wrap road-aid-crm-wrap">
		<div class="road-aid-crm-header">
			<h1><?php esc_html_e( 'Newsletter subscribers', 'road-aid-europe' ); ?></h1>
			<p><?php esc_html_e( 'Emails collected via the Stay in the loop signup (shortcode [road_aid_newsletter]). Last 5,000 entries kept.', 'road-aid-europe' ); ?></p>
		</div>
		<p><a href="<?php echo esc_url( $export_url ); ?>" class="button button-primary"><?php esc_html_e( 'Export CSV', 'road-aid-europe' ); ?></a></p>
		<div class="road-aid-crm-section">
			<?php if ( empty( $list ) ) : ?>
				<p class="road-aid-crm-empty"><?php esc_html_e( 'No subscribers yet.', 'road-aid-europe' ); ?></p>
			<?php else : ?>
				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Email', 'road-aid-europe' ); ?></th>
							<th><?php esc_html_e( 'Subscribed at', 'road-aid-europe' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( array_reverse( $list ) as $row ) : ?>
							<tr>
								<td><?php echo esc_html( $row['email'] ?? '' ); ?></td>
								<td><?php echo esc_html( $row['subscribed_at'] ?? '' ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Add custom fields meta box for pages
 */
function road_aid_add_page_meta_boxes() {
	add_meta_box(
		'road_aid_page_options',
		__( 'Page Options', 'road-aid-europe' ),
		'road_aid_page_options_callback',
		'page',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'road_aid_add_page_meta_boxes' );

/**
 * Page options meta box callback
 */
function road_aid_page_options_callback( $post ) {
	wp_nonce_field( 'road_aid_save_page_options', 'road_aid_page_options_nonce' );
	
	$hide_title = get_post_meta( $post->ID, '_road_aid_hide_title', true );
	$subtitle   = get_post_meta( $post->ID, '_road_aid_subtitle', true );
	?>
	<p>
		<label>
			<input type="checkbox" name="road_aid_hide_title" value="1" <?php checked( $hide_title, '1' ); ?>>
			<?php _e( 'Hide Page Title', 'road-aid-europe' ); ?>
		</label>
	</p>
	<p>
		<label for="road_aid_subtitle">
			<strong><?php _e( 'Subtitle/Excerpt:', 'road-aid-europe' ); ?></strong><br>
			<input type="text" id="road_aid_subtitle" name="road_aid_subtitle" value="<?php echo esc_attr( $subtitle ); ?>" style="width:100%;" />
			<small><?php _e( 'Optional subtitle that appears below the title', 'road-aid-europe' ); ?></small>
		</label>
	</p>
	<?php
	// Contact Form Selection (for Contact page template)
	if ( get_page_template_slug( $post->ID ) === 'page-contact.php' || get_page_template_slug( $post->ID ) === '' ) {
		$selected_form = get_post_meta( $post->ID, '_road_aid_contact_form_id', true );
		$forms = road_aid_get_all_forms();
		?>
		<p>
			<label for="road_aid_contact_form_id">
				<strong><?php _e( 'Contact Form:', 'road-aid-europe' ); ?></strong><br>
				<select id="road_aid_contact_form_id" name="road_aid_contact_form_id" style="width:100%;">
					<option value="default" <?php selected( $selected_form, 'default' ); ?>>Default Contact Form</option>
					<?php foreach ( $forms as $form_id => $form ) : ?>
						<?php if ( $form_id !== 'default' ) : ?>
							<option value="<?php echo esc_attr( $form_id ); ?>" <?php selected( $selected_form, $form_id ); ?>><?php echo esc_html( $form['name'] ); ?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
				<small><?php _e( 'Select which contact form to display on this page', 'road-aid-europe' ); ?></small>
			</label>
		</p>
		<?php
	}
}

/**
 * Save page options
 */
function road_aid_save_page_options( $post_id ) {
	// Check nonce
	if ( ! isset( $_POST['road_aid_page_options_nonce'] ) || ! wp_verify_nonce( $_POST['road_aid_page_options_nonce'], 'road_aid_save_page_options' ) ) {
		return;
	}

	// Check autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check permissions
	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}

	// Save hide title
	$hide_title = isset( $_POST['road_aid_hide_title'] ) ? '1' : '0';
	update_post_meta( $post_id, '_road_aid_hide_title', $hide_title );

	// Save subtitle
	if ( isset( $_POST['road_aid_subtitle'] ) ) {
		update_post_meta( $post_id, '_road_aid_subtitle', sanitize_text_field( $_POST['road_aid_subtitle'] ) );
	}
	
	// Save contact form ID
	if ( isset( $_POST['road_aid_contact_form_id'] ) ) {
		update_post_meta( $post_id, '_road_aid_contact_form_id', sanitize_text_field( $_POST['road_aid_contact_form_id'] ) );
	}
}
add_action( 'save_post', 'road_aid_save_page_options' );

/**
 * Get page subtitle
 */
function road_aid_get_page_subtitle( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	return get_post_meta( $post_id, '_road_aid_subtitle', true );
}

/**
 * Check if page title should be hidden
 */
function road_aid_is_title_hidden( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	return get_post_meta( $post_id, '_road_aid_hide_title', true ) === '1';
}

/**
 * Contact Form Settings - Admin Menu (now under Road Aid CRM in inc/road-aid-crm.php)
 */
function road_aid_contact_form_admin_menu() {
	// No longer add under Appearance; Road Aid CRM adds "Forms" submenu.
}
add_action( 'admin_menu', 'road_aid_contact_form_admin_menu' );

/**
 * Get all contact forms
 */
function road_aid_get_all_forms() {
	// Don't run during plugin installation or if database isn't ready
	if ( road_aid_is_installing() || ! function_exists( 'get_option' ) ) {
		return array();
	}
	
	$forms = get_option( 'road_aid_contact_forms', array() );
	
	// If no forms exist, create default forms
	if ( empty( $forms ) ) {
		$default_form = road_aid_get_default_forms();
		if ( function_exists( 'update_option' ) && ! defined( 'WP_INSTALLING' ) ) {
			update_option( 'road_aid_contact_forms', $default_form );
		}
		return $default_form;
	}

	// Ensure fleet, provider, and updated default forms always exist
	$seeded = false;
	$defaults = road_aid_get_default_forms();
	foreach ( array( 'fleet_registration', 'provider_application' ) as $key ) {
		if ( ! isset( $forms[ $key ] ) && isset( $defaults[ $key ] ) ) {
			$forms[ $key ] = $defaults[ $key ];
			$seeded = true;
		}
	}
	// Refresh default form if it's missing the subject field
	if ( isset( $forms['default'] ) ) {
		$has_subject = false;
		foreach ( ( $forms['default']['fields'] ?? array() ) as $f ) {
			if ( ( $f['id'] ?? '' ) === 'field_subject' ) { $has_subject = true; break; }
		}
		if ( ! $has_subject ) {
			$forms['default'] = $defaults['default'];
			$seeded = true;
		}
	}
	if ( $seeded && function_exists( 'update_option' ) && ! defined( 'WP_INSTALLING' ) ) {
		update_option( 'road_aid_contact_forms', $forms );
	}
	
	return $forms;
}

/**
 * Default form definitions (seeded on first load)
 */
function road_aid_get_default_forms() {
	return array(
		'default' => array(
			'name'        => 'Default Contact Form',
			'id'          => 'default',
			'fields'      => array(
				array( 'id' => 'field_name',    'type' => 'text',     'label' => 'Name',    'placeholder' => 'Your full name',              'required' => true ),
				array( 'id' => 'field_email',   'type' => 'text',     'label' => 'Email',   'placeholder' => 'your.email@example.com',      'required' => true ),
				array( 'id' => 'field_subject', 'type' => 'select',   'label' => 'Subject', 'placeholder' => 'Select a subject',            'required' => true,
					'options' => array( 'Need help', 'Fleet Registration', 'Become a provider', 'Partnership', 'Other' ) ),
				array( 'id' => 'field_message', 'type' => 'textarea', 'label' => 'Message', 'placeholder' => 'Tell us how we can help you...', 'required' => true ),
			),
			'submit_text'           => 'Send Message',
			'success_msg'           => "✓ Message sent successfully! We'll get back to you soon.",
			'email_recipient'       => '',
			'whatsapp_notification' => false,
		),
		'fleet_registration' => array(
			'name'   => 'Fleet Registration',
			'id'     => 'fleet_registration',
			'fields' => array(
				array( 'id' => 'field_company',       'type' => 'text',     'label' => 'Company Name',        'placeholder' => 'Your company name',              'required' => true ),
				array( 'id' => 'field_contact_name',   'type' => 'text',     'label' => 'Contact Person',      'placeholder' => 'Full name',                      'required' => true ),
				array( 'id' => 'field_email',          'type' => 'text',     'label' => 'Email',               'placeholder' => 'company@example.com',            'required' => true ),
				array( 'id' => 'field_phone',          'type' => 'text',     'label' => 'Phone Number',        'placeholder' => '+355 6X XXX XXXX',               'required' => true ),
				array( 'id' => 'field_fleet_size',     'type' => 'select',   'label' => 'Fleet Size',          'placeholder' => 'Select fleet size',              'required' => true,
					'options' => array( '1-10 vehicles', '11-50 vehicles', '51-200 vehicles', '200+ vehicles' ) ),
				array( 'id' => 'field_vehicle_types',  'type' => 'text',     'label' => 'Vehicle Types',       'placeholder' => 'e.g. Cars, Vans, Trucks',        'required' => false ),
				array( 'id' => 'field_coverage',       'type' => 'text',     'label' => 'Coverage Area',       'placeholder' => 'e.g. Tirana, Durres, Nationwide', 'required' => false ),
				array( 'id' => 'field_message',        'type' => 'textarea', 'label' => 'Additional Details',  'placeholder' => 'Tell us about your fleet and needs...', 'required' => false ),
			),
			'submit_text'           => 'Register Fleet',
			'success_msg'           => "✓ Fleet registration submitted! Our team will contact you within 24 hours.",
			'email_recipient'       => '',
			'whatsapp_notification' => true,
		),
		'provider_application' => array(
			'name'   => 'Provider Application',
			'id'     => 'provider_application',
			'fields' => array(
				array( 'id' => 'field_business_name',  'type' => 'text',     'label' => 'Business Name',       'placeholder' => 'Your business name',              'required' => true ),
				array( 'id' => 'field_owner_name',     'type' => 'text',     'label' => 'Owner / Contact Name', 'placeholder' => 'Full name',                      'required' => true ),
				array( 'id' => 'field_email',          'type' => 'text',     'label' => 'Email',               'placeholder' => 'your.email@example.com',          'required' => true ),
				array( 'id' => 'field_phone',          'type' => 'text',     'label' => 'Phone Number',        'placeholder' => '+355 6X XXX XXXX',                'required' => true ),
				array( 'id' => 'field_service_type',   'type' => 'select',   'label' => 'Service Type',        'placeholder' => 'Select service type',             'required' => true,
					'options' => array( 'Tow Truck', 'Mobile Mechanic', 'Locksmith', 'Fuel Delivery', 'Multiple Services' ) ),
				array( 'id' => 'field_city',           'type' => 'text',     'label' => 'City / Region',       'placeholder' => 'e.g. Tirana, Durres, Vlora',      'required' => true ),
				array( 'id' => 'field_vehicles',       'type' => 'text',     'label' => 'Number of Vehicles',  'placeholder' => 'How many service vehicles?',       'required' => false ),
				array( 'id' => 'field_experience',     'type' => 'textarea', 'label' => 'Experience & Details', 'placeholder' => 'Tell us about your services, equipment, and experience...', 'required' => false ),
			),
			'submit_text'           => 'Apply as Provider',
			'success_msg'           => "✓ Application submitted! We'll review it and get back to you within 48 hours.",
			'email_recipient'       => '',
			'whatsapp_notification' => true,
		),
	);
}

/**
 * Get a specific contact form
 */
function road_aid_get_contact_form( $form_id = 'default' ) {
	$forms = road_aid_get_all_forms();
	return isset( $forms[ $form_id ] ) ? $forms[ $form_id ] : ( isset( $forms['default'] ) ? $forms['default'] : null );
}

/**
 * Contact Form Settings Page
 */
function road_aid_contact_form_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$forms = road_aid_get_all_forms();
	$current_form_id = isset( $_GET['form_id'] ) ? sanitize_text_field( $_GET['form_id'] ) : ( isset( $_GET['edit_form'] ) ? sanitize_text_field( $_GET['edit_form'] ) : 'default' );
	$current_form = isset( $forms[ $current_form_id ] ) ? $forms[ $current_form_id ] : null;
	$is_new = isset( $_GET['new_form'] );
	$is_editing = $current_form && ! $is_new;

	// Handle delete
	if ( isset( $_GET['delete_form'] ) && check_admin_referer( 'delete_form_' . $_GET['delete_form'] ) ) {
		$delete_id = sanitize_text_field( $_GET['delete_form'] );
		if ( $delete_id !== 'default' && isset( $forms[ $delete_id ] ) ) {
			unset( $forms[ $delete_id ] );
			update_option( 'road_aid_contact_forms', $forms );
			echo '<div class="notice notice-success"><p>' . esc_html__( 'Form deleted!', 'road-aid-europe' ) . '</p></div>';
			$current_form_id = 'default';
			$current_form = isset( $forms['default'] ) ? $forms['default'] : null;
		}
	}

	// Save form
	if ( isset( $_POST['road_aid_save_form'] ) && check_admin_referer( 'road_aid_form_nonce' ) ) {
		$form_id = sanitize_text_field( $_POST['form_id'] ?? '' );
		$form_name = sanitize_text_field( $_POST['form_name'] ?? 'Contact Form' );
		
		if ( empty( $form_id ) ) {
			$form_id = sanitize_title( $form_name );
			if ( empty( $form_id ) ) {
				$form_id = 'form_' . time();
			}
		}
		
		// Process dynamic fields
		$form_fields = array();
		if ( isset( $_POST['form_fields'] ) && is_array( $_POST['form_fields'] ) ) {
			foreach ( $_POST['form_fields'] as $index => $field_data ) {
				$field_id = sanitize_text_field( $field_data['id'] ?? 'field_' . $index );
				$field_type = sanitize_text_field( $field_data['type'] ?? 'text' );
				$field_label = sanitize_text_field( $field_data['label'] ?? '' );
				$field_placeholder = sanitize_text_field( $field_data['placeholder'] ?? '' );
				$field_required = isset( $field_data['required'] ) ? true : false;
				
				$field_config = array(
					'id'          => $field_id,
					'type'        => $field_type,
					'label'       => $field_label,
					'placeholder' => $field_placeholder,
					'required'    => $field_required,
				);
				
				// Add options for select/radio fields
				if ( in_array( $field_type, array( 'select', 'radio' ) ) && isset( $field_data['options'] ) ) {
					$options = array();
					$options_raw = explode( "\n", sanitize_textarea_field( $field_data['options'] ) );
					foreach ( $options_raw as $option ) {
						$option = trim( $option );
						if ( ! empty( $option ) ) {
							$options[] = $option;
						}
					}
					$field_config['options'] = $options;
				}
				
				$form_fields[] = $field_config;
			}
		}
		
		$forms[ $form_id ] = array(
			'name'                 => $form_name,
			'id'                   => $form_id,
			'fields'               => $form_fields,
			'submit_text'          => sanitize_text_field( $_POST['form_submit_text'] ?? 'Send Message' ),
			'success_msg'          => sanitize_textarea_field( $_POST['form_success_msg'] ?? "✓ Message sent successfully! We'll get back to you soon." ),
			'email_recipient'      => sanitize_email( $_POST['form_email_recipient'] ?? '' ),
			'whatsapp_notification' => isset( $_POST['form_whatsapp_notification'] ) ? true : false,
		);
		
		update_option( 'road_aid_contact_forms', $forms );
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Form saved!', 'road-aid-europe' ) . '</p></div>';
		
		$current_form = $forms[ $form_id ];
		$current_form_id = $form_id;
		$is_editing = true;
	}

	// If editing and form doesn't exist, create default
	if ( $is_editing && ! $current_form ) {
		$current_form = road_aid_get_contact_form( 'default' );
		$current_form_id = 'default';
	}

	// Enqueue jQuery for admin
	wp_enqueue_script( 'jquery' );
	?>
	<div class="wrap road-aid-crm-wrap">
		<div class="road-aid-crm-header">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<p><?php _e( 'Create and manage multiple contact forms. Each form can have different fields and settings. WhatsApp settings are in Road Aid CRM → WhatsApp.', 'road-aid-europe' ); ?></p>
		</div>
		
		<div class="road-aid-crm-form-grid">
			<div class="road-aid-crm-form-sidebar">
				<h2><?php _e( 'Forms', 'road-aid-europe' ); ?></h2>
				<p><a href="?page=road-aid-contact-form&new_form=1" class="button button-primary"><?php _e( '+ New Form', 'road-aid-europe' ); ?></a></p>
				<ul class="road-aid-crm-form-list">
					<?php foreach ( $forms as $form_id => $form ) : ?>
					<li>
						<a href="?page=road-aid-contact-form&form_id=<?php echo esc_attr( $form_id ); ?>" class="<?php echo $current_form_id === $form_id ? 'current' : ''; ?>">
							<?php echo esc_html( $form['name'] ); ?>
							<?php if ( $form_id === 'default' ) : ?>
								<small style="opacity: 0.7;"> (<?php _e( 'default', 'road-aid-europe' ); ?>)</small>
							<?php endif; ?>
						</a>
						<?php if ( $form_id !== 'default' ) : ?>
							<a href="<?php echo esc_url( wp_nonce_url( '?page=road-aid-contact-form&delete_form=' . $form_id, 'delete_form_' . $form_id ) ); ?>" onclick="return confirm('<?php esc_attr_e( 'Delete this form?', 'road-aid-europe' ); ?>');" style="color: #b32d2e; font-size: 11px; margin-left: 8px;"><?php _e( 'Delete', 'road-aid-europe' ); ?></a>
						<?php endif; ?>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
			
			<div class="road-aid-crm-form-main">
				<?php if ( $is_new || $is_editing ) : ?>
					<h2><?php echo $is_new ? __( 'Create New Form', 'road-aid-europe' ) : __( 'Edit Form', 'road-aid-europe' ); ?></h2>
					
					<form method="post" action="" id="road-aid-form-builder">
						<?php wp_nonce_field( 'road_aid_form_nonce' ); ?>
						
						<?php if ( $is_editing ) : ?>
							<input type="hidden" name="form_id" value="<?php echo esc_attr( $current_form_id ); ?>">
						<?php endif; ?>
						
						<table class="form-table">
							<tr>
								<th><label for="form_name"><?php _e( 'Form Name', 'road-aid-europe' ); ?> *</label></th>
								<td>
									<input type="text" id="form_name" name="form_name" value="<?php echo esc_attr( $current_form['name'] ?? 'Contact Form' ); ?>" class="regular-text" required />
									<p class="description"><?php _e( 'Internal name for this form', 'road-aid-europe' ); ?></p>
								</td>
							</tr>
							<tr>
								<th><label for="form_email_recipient"><?php _e( 'Email Recipient', 'road-aid-europe' ); ?></label></th>
								<td>
									<input type="email" id="form_email_recipient" name="form_email_recipient" value="<?php echo esc_attr( $current_form['email_recipient'] ?? '' ); ?>" class="regular-text" />
									<p class="description"><?php _e( 'Leave empty to use default email from theme settings', 'road-aid-europe' ); ?></p>
								</td>
							</tr>
						</table>
						
						<h3><?php _e( 'Form Fields', 'road-aid-europe' ); ?></h3>
						<p class="description"><?php _e( 'Add, remove, and configure form fields. Drag to reorder.', 'road-aid-europe' ); ?></p>
						
						<div id="form-fields-container" style="margin: 20px 0;">
							<?php
							$fields = $current_form['fields'] ?? array();
							foreach ( $fields as $index => $field ) :
								$field_id = $field['id'] ?? 'field_' . $index;
								$field_type = $field['type'] ?? 'text';
								$field_label = $field['label'] ?? '';
								$field_placeholder = $field['placeholder'] ?? '';
								$field_required = $field['required'] ?? false;
								$field_options = isset( $field['options'] ) ? implode( "\n", $field['options'] ) : '';
							?>
							<div class="form-field-item" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; background: #f9f9f9; position: relative;">
								<div style="display: flex; gap: 10px; align-items: flex-start;">
									<div style="flex: 1;">
										<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
											<div>
												<label><strong>Field ID:</strong></label><br>
												<input type="text" name="form_fields[<?php echo $index; ?>][id]" value="<?php echo esc_attr( $field_id ); ?>" class="regular-text" required />
											</div>
											<div>
												<label><strong>Field Type:</strong></label><br>
												<select name="form_fields[<?php echo $index; ?>][type]" class="field-type-select" style="width: 100%;">
													<option value="text" <?php selected( $field_type, 'text' ); ?>>Text</option>
													<option value="email" <?php selected( $field_type, 'email' ); ?>>Email</option>
													<option value="tel" <?php selected( $field_type, 'tel' ); ?>>Phone</option>
													<option value="textarea" <?php selected( $field_type, 'textarea' ); ?>>Textarea</option>
													<option value="select" <?php selected( $field_type, 'select' ); ?>>Dropdown (Select)</option>
													<option value="radio" <?php selected( $field_type, 'radio' ); ?>>Radio Buttons</option>
													<option value="checkbox" <?php selected( $field_type, 'checkbox' ); ?>>Checkbox</option>
												</select>
											</div>
										</div>
										<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
											<div>
												<label><strong>Label:</strong></label><br>
												<input type="text" name="form_fields[<?php echo $index; ?>][label]" value="<?php echo esc_attr( $field_label ); ?>" class="regular-text" required />
											</div>
											<div>
												<label><strong>Placeholder:</strong></label><br>
												<input type="text" name="form_fields[<?php echo $index; ?>][placeholder]" value="<?php echo esc_attr( $field_placeholder ); ?>" class="regular-text" />
											</div>
										</div>
										<div class="field-options-container" style="display: <?php echo in_array( $field_type, array( 'select', 'radio' ) ) ? 'block' : 'none'; ?>; margin-bottom: 10px;">
											<label><strong>Options (one per line):</strong></label><br>
											<textarea name="form_fields[<?php echo $index; ?>][options]" rows="4" class="large-text" placeholder="Option 1&#10;Option 2&#10;Option 3"><?php echo esc_textarea( $field_options ); ?></textarea>
											<p class="description"><?php _e( 'Enter one option per line. These will be used for dropdown or radio buttons.', 'road-aid-europe' ); ?></p>
										</div>
										<div>
											<label>
												<input type="checkbox" name="form_fields[<?php echo $index; ?>][required]" value="1" <?php checked( $field_required, true ); ?>>
												<strong>Required field</strong>
											</label>
										</div>
									</div>
									<div>
										<button type="button" class="button remove-field" style="color: #b32d2e;"><?php _e( 'Remove', 'road-aid-europe' ); ?></button>
									</div>
								</div>
							</div>
							<?php endforeach; ?>
						</div>
						
						<button type="button" id="add-field-btn" class="button"><?php _e( '+ Add Field', 'road-aid-europe' ); ?></button>
						
						<table class="form-table" style="margin-top: 30px;">
							<tr>
								<th scope="row"><label for="form_submit_text"><?php _e( 'Submit Button Text', 'road-aid-europe' ); ?></label></th>
								<td>
									<input type="text" id="form_submit_text" name="form_submit_text" value="<?php echo esc_attr( $current_form['submit_text'] ?? 'Send Message' ); ?>" style="width:100%; max-width:300px;" />
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="form_success_msg"><?php _e( 'Success Message', 'road-aid-europe' ); ?></label></th>
								<td>
									<textarea id="form_success_msg" name="form_success_msg" rows="3" style="width:100%; max-width:500px;"><?php echo esc_textarea( $current_form['success_msg'] ?? "✓ Message sent successfully! We'll get back to you soon." ); ?></textarea>
									<p class="description"><?php _e( 'Message shown after successful form submission', 'road-aid-europe' ); ?></p>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e( 'WhatsApp Notification', 'road-aid-europe' ); ?></th>
								<td>
									<label>
										<input type="checkbox" name="form_whatsapp_notification" value="1" <?php checked( $current_form['whatsapp_notification'] ?? false, true ); ?>>
										<?php _e( 'Send WhatsApp notification to website owner after form submission', 'road-aid-europe' ); ?>
									</label>
									<p class="description"><?php _e( 'When enabled, the website owner will receive a WhatsApp message with the form submission data. Configure in Road Aid CRM → WhatsApp.', 'road-aid-europe' ); ?></p>
								</td>
							</tr>
						</table>
						
						<?php submit_button( $is_new ? __( 'Create Form', 'road-aid-europe' ) : __( 'Save Form', 'road-aid-europe' ), 'primary', 'road_aid_save_form' ); ?>
						
						<?php if ( $is_editing ) : ?>
							<p style="margin-top: 20px;">
								<strong><?php _e( 'Shortcode:', 'road-aid-europe' ); ?></strong><br>
								<code>[road_aid_contact_form id="<?php echo esc_attr( $current_form_id ); ?>"]</code>
							</p>
						<?php endif; ?>
					</form>
					
					<script>
					jQuery(document).ready(function($) {
						var fieldIndex = <?php echo count( $fields ); ?>;
						
						// Add new field
						$('#add-field-btn').on('click', function() {
							var fieldHtml = '<div class="form-field-item" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; background: #f9f9f9; position: relative;">' +
								'<div style="display: flex; gap: 10px; align-items: flex-start;">' +
									'<div style="flex: 1;">' +
										'<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">' +
											'<div><label><strong>Field ID:</strong></label><br><input type="text" name="form_fields[' + fieldIndex + '][id]" value="field_' + fieldIndex + '" class="regular-text" required /></div>' +
											'<div><label><strong>Field Type:</strong></label><br><select name="form_fields[' + fieldIndex + '][type]" class="field-type-select" style="width: 100%;">' +
												'<option value="text">Text</option>' +
												'<option value="email">Email</option>' +
												'<option value="tel">Phone</option>' +
												'<option value="textarea">Textarea</option>' +
												'<option value="select">Dropdown (Select)</option>' +
												'<option value="radio">Radio Buttons</option>' +
												'<option value="checkbox">Checkbox</option>' +
											'</select></div>' +
										'</div>' +
										'<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">' +
											'<div><label><strong>Label:</strong></label><br><input type="text" name="form_fields[' + fieldIndex + '][label]" value="" class="regular-text" required /></div>' +
											'<div><label><strong>Placeholder:</strong></label><br><input type="text" name="form_fields[' + fieldIndex + '][placeholder]" value="" class="regular-text" /></div>' +
										'</div>' +
										'<div class="field-options-container" style="display: none; margin-bottom: 10px;">' +
											'<label><strong>Options (one per line):</strong></label><br>' +
											'<textarea name="form_fields[' + fieldIndex + '][options]" rows="4" class="large-text" placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>' +
											'<p class="description">Enter one option per line. These will be used for dropdown or radio buttons.</p>' +
										'</div>' +
										'<div><label><input type="checkbox" name="form_fields[' + fieldIndex + '][required]" value="1"> <strong>Required field</strong></label></div>' +
									'</div>' +
									'<div><button type="button" class="button remove-field" style="color: #b32d2e;">Remove</button></div>' +
								'</div>' +
							'</div>';
							$('#form-fields-container').append(fieldHtml);
							fieldIndex++;
						});
						
						// Remove field
						$(document).on('click', '.remove-field', function() {
							if (confirm('<?php echo esc_js( __( 'Remove this field?', 'road-aid-europe' ) ); ?>')) {
								$(this).closest('.form-field-item').remove();
							}
						});
						
						// Toggle options field based on type
						$(document).on('change', '.field-type-select', function() {
							var $container = $(this).closest('.form-field-item').find('.field-options-container');
							if ($(this).val() === 'select' || $(this).val() === 'radio') {
								$container.show();
							} else {
								$container.hide();
							}
						});
					});
					</script>
				<?php else : ?>
					<h2><?php _e( 'Select a Form', 'road-aid-europe' ); ?></h2>
					<p><?php _e( 'Select a form from the list to edit, or create a new form.', 'road-aid-europe' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Get form field by ID
 */
function road_aid_get_form_field( $form_id, $field_id ) {
	$form = road_aid_get_contact_form( $form_id );
	if ( ! $form || ! isset( $form['fields'] ) ) {
		return null;
	}
	
	foreach ( $form['fields'] as $field ) {
		if ( isset( $field['id'] ) && $field['id'] === $field_id ) {
			return $field;
		}
	}
	
	return null;
}

/**
 * Display Contact Form
 */
function road_aid_display_contact_form( $form_id = 'default' ) {
	$form = road_aid_get_contact_form( $form_id );
	if ( ! $form ) {
		return '<p class="note">Form not found. Please create a form in WordPress Admin → Appearance → Contact Forms</p>';
	}
	
	$fields = $form['fields'] ?? array();
	if ( empty( $fields ) ) {
		return '<p class="note">This form has no fields. Please add fields in WordPress Admin → Road Aid CRM → Forms</p>';
	}
	
	ob_start();
	?>
	<div class="card pad">
		<h3 style="margin:0 0 10px;">Send a message</h3>
		
		<?php
		// Display success/error messages
		if ( isset( $_GET['contact'] ) ) {
			if ( $_GET['contact'] === 'success' ) {
				$success_msg = $form['success_msg'] ?? "✓ Message sent successfully! We'll get back to you soon.";
				echo '<div class="card pad" style="background: rgba(78,161,255,.1); border-color: rgba(78,161,255,.3); margin-bottom:16px;">';
				echo '<p style="margin:0; color: #D32F2F;"><strong>' . esc_html( $success_msg ) . '</strong></p>';
				echo '</div>';
			} elseif ( $_GET['contact'] === 'recaptcha' ) {
				echo '<div class="card pad" style="background: rgba(255,0,0,.1); border-color: rgba(255,0,0,.3); margin-bottom:16px;">';
				echo '<p style="margin:0; color: #ff6b6b;"><strong>✗ reCAPTCHA verification failed.</strong> Please tick the "I\'m not a robot" box and try again.</p>';
				echo '</div>';
			} elseif ( $_GET['contact'] === 'error' ) {
				echo '<div class="card pad" style="background: rgba(255,0,0,.1); border-color: rgba(255,0,0,.3); margin-bottom:16px;">';
				echo '<p style="margin:0; color: #ff6b6b;"><strong>✗ Error sending message.</strong> Please try again or call us directly.</p>';
				echo '</div>';
			}
		}
		?>
		
		<form class="form" id="contactForm_<?php echo esc_attr( $form_id ); ?>" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'road_aid_contact_form', 'road_aid_contact_nonce' ); ?>
			<input type="hidden" name="action" value="road_aid_contact_submit">
			<input type="hidden" name="form_id" value="<?php echo esc_attr( $form_id ); ?>">
			
			<?php
			$row_fields = array(); // Fields that should be in a row
			$in_row = false;
			
			foreach ( $fields as $index => $field ) {
				$field_id = $field['id'] ?? 'field_' . $index;
				$field_type = $field['type'] ?? 'text';
				$field_label = $field['label'] ?? '';
				$field_placeholder = $field['placeholder'] ?? '';
				$field_required = $field['required'] ?? false;
				$field_options = $field['options'] ?? array();
				if ( is_string( $field_options ) ) {
					$field_options = array_filter( array_map( 'trim', explode( "\n", $field_options ) ) );
				}
				
				$required_attr = $required_label = '';
				if ( $field_required ) {
					$required_attr = 'required';
					$required_label = ' *';
				}
				
				$field_name_attr = 'form_field[' . esc_attr( $field_id ) . ']';
				$field_html_id = 'form_field_' . esc_attr( $form_id ) . '_' . esc_attr( $field_id );
				
				// Determine if field should be in a row (first two text/email fields)
				$should_be_in_row = ( $field_type === 'text' || $field_type === 'email' ) && count( $row_fields ) < 2 && $field_type !== 'textarea';
				
				if ( $should_be_in_row && ! $in_row ) {
					echo '<div class="row">';
					$in_row = true;
					$row_fields[] = $field_id;
				} elseif ( ! $should_be_in_row && $in_row ) {
					echo '</div>';
					$in_row = false;
					$row_fields = array();
				}
				
				echo '<div>';
				echo '<label for="' . esc_attr( $field_html_id ) . '">' . esc_html( $field_label ) . $required_label . '</label>';
				
				// Render field based on type
				switch ( $field_type ) {
					case 'textarea':
						echo '<textarea id="' . esc_attr( $field_html_id ) . '" name="' . esc_attr( $field_name_attr ) . '" ' . $required_attr . ' placeholder="' . esc_attr( $field_placeholder ) . '"></textarea>';
						break;
						
					case 'select':
						echo '<select id="' . esc_attr( $field_html_id ) . '" name="' . esc_attr( $field_name_attr ) . '" ' . $required_attr . '>';
						if ( ! empty( $field_placeholder ) ) {
							echo '<option value="">' . esc_html( $field_placeholder ) . '</option>';
						}
						foreach ( $field_options as $option ) {
							$option = trim( $option );
							if ( ! empty( $option ) ) {
								echo '<option value="' . esc_attr( $option ) . '">' . esc_html( $option ) . '</option>';
							}
						}
						echo '</select>';
						break;
						
					case 'radio':
						if ( ! empty( $field_options ) ) {
							foreach ( $field_options as $option_index => $option ) {
								$option = trim( $option );
								if ( ! empty( $option ) ) {
									$option_id = $field_html_id . '_' . $option_index;
									echo '<label style="display: block; margin-bottom: 8px; font-weight: normal;">';
									echo '<input type="radio" id="' . esc_attr( $option_id ) . '" name="' . esc_attr( $field_name_attr ) . '" value="' . esc_attr( $option ) . '" ' . $required_attr . ' style="margin-right: 8px;">';
									echo esc_html( $option );
									echo '</label>';
								}
							}
						}
						break;
						
					case 'checkbox':
						echo '<label style="display: block; margin-bottom: 8px; font-weight: normal;">';
						echo '<input type="checkbox" id="' . esc_attr( $field_html_id ) . '" name="' . esc_attr( $field_name_attr ) . '" value="1" ' . $required_attr . ' style="margin-right: 8px;">';
						if ( ! empty( $field_placeholder ) ) {
							echo esc_html( $field_placeholder );
						} else {
							echo esc_html( $field_label );
						}
						echo '</label>';
						break;
						
					default: // text, email, tel
						$input_type = $field_type;
						if ( $field_type === 'text' ) {
							$input_type = 'text';
						}
						echo '<input id="' . esc_attr( $field_html_id ) . '" name="' . esc_attr( $field_name_attr ) . '" type="' . esc_attr( $input_type ) . '" ' . $required_attr . ' placeholder="' . esc_attr( $field_placeholder ) . '" />';
						break;
				}
				
				echo '</div>';
				
				// Close row after second field
				if ( $should_be_in_row && count( $row_fields ) >= 2 ) {
					echo '</div>';
					$in_row = false;
					$row_fields = array();
				}
			}
			
			// Close row if still open
			if ( $in_row ) {
				echo '</div>';
			}
			?>
			
			<?php if ( road_aid_recaptcha_enabled() ) : ?>
			<div style="margin: 6px 0 16px;">
				<div class="g-recaptcha" data-sitekey="<?php echo esc_attr( road_aid_get_recaptcha_site_key() ); ?>"></div>
			</div>
			<?php endif; ?>
			
			<button class="btn primary" type="submit"><?php echo esc_html( $form['submit_text'] ?? 'Send Message' ); ?></button>
			<p class="note" style="margin-top:10px;">We'll respond as soon as possible. For urgent matters, please call us directly.</p>
		</form>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Contact Form Shortcode: [road_aid_contact_form id="default"]
 */
function road_aid_contact_form_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'id' => 'default',
	), $atts, 'road_aid_contact_form' );
	
	return road_aid_display_contact_form( $atts['id'] );
}
add_shortcode( 'road_aid_contact_form', 'road_aid_contact_form_shortcode' );

/**
 * Google Maps Embed - Admin Menu
 */
function road_aid_maps_embed_menu() {
	// Don't run during installation
	if ( defined( 'WP_INSTALLING' ) || ! function_exists( 'add_submenu_page' ) ) {
		return;
	}
	
	add_submenu_page(
		'themes.php',
		__( 'Google Maps Embed', 'road-aid-europe' ),
		__( 'Google Maps', 'road-aid-europe' ),
		'manage_options',
		'road-aid-maps-embed',
		'road_aid_maps_embed_page'
	);
}
add_action( 'admin_menu', 'road_aid_maps_embed_menu' );

/**
 * Google Maps Embed Admin Page
 */
function road_aid_maps_embed_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Save embed code
	if ( isset( $_POST['road_aid_save_map_embed'] ) && check_admin_referer( 'road_aid_map_embed_nonce' ) ) {
		$embed_code = wp_kses_post( $_POST['map_embed_code'] ?? '' );
		update_option( 'road_aid_map_embed_code', $embed_code );
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Map embed code saved!', 'road-aid-europe' ) . '</p></div>';
	}

	$current_embed = get_option( 'road_aid_map_embed_code', '' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p><?php _e( 'Paste your Google Maps embed code here. No API key needed!', 'road-aid-europe' ); ?></p>
		
		<div style="max-width: 800px;">
			<h2><?php _e( 'How to Get Google Maps Embed Code', 'road-aid-europe' ); ?></h2>
			<ol>
				<li><?php _e( 'Go to <a href="https://www.google.com/maps" target="_blank">Google Maps</a>', 'road-aid-europe' ); ?></li>
				<li><?php _e( 'Search for your location or create a map with multiple locations', 'road-aid-europe' ); ?></li>
				<li><?php _e( 'Click the menu (☰) → Share or embed map', 'road-aid-europe' ); ?></li>
				<li><?php _e( 'Click "Embed a map" tab', 'road-aid-europe' ); ?></li>
				<li><?php _e( 'Copy the iframe code', 'road-aid-europe' ); ?></li>
				<li><?php _e( 'Paste it below and save', 'road-aid-europe' ); ?></li>
			</ol>
			
			<form method="post" action="">
				<?php wp_nonce_field( 'road_aid_map_embed_nonce' ); ?>
				
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="map_embed_code"><?php _e( 'Google Maps Embed Code', 'road-aid-europe' ); ?></label>
						</th>
						<td>
							<textarea id="map_embed_code" name="map_embed_code" rows="8" class="large-text code" style="font-family: monospace;"><?php echo esc_textarea( $current_embed ); ?></textarea>
							<p class="description">
								<?php _e( 'Paste the full iframe code from Google Maps. Example:', 'road-aid-europe' ); ?><br>
								<code>&lt;iframe src="https://www.google.com/maps/embed?pb=..."&gt;&lt;/iframe&gt;</code>
							</p>
						</td>
					</tr>
				</table>
				
				<?php submit_button( __( 'Save Map Embed Code', 'road-aid-europe' ), 'primary', 'road_aid_save_map_embed' ); ?>
			</form>
			
			<?php if ( ! empty( $current_embed ) ) : ?>
				<h2 style="margin-top: 30px;"><?php _e( 'Preview', 'road-aid-europe' ); ?></h2>
				<div style="border: 1px solid #ddd; padding: 10px; background: #fff; max-width: 100%;">
					<?php echo $current_embed; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Get Google Maps embed code
 */
function road_aid_get_map_embed_code() {
	return get_option( 'road_aid_map_embed_code', '' );
}

/**
 * Display Google Map Embed
 */
function road_aid_display_map_embed( $height = '500px' ) {
	$embed_code = road_aid_get_map_embed_code();
	
	if ( empty( $embed_code ) ) {
		return '<div class="card pad"><p class="note">Please add your Google Maps embed code in <strong>WordPress Admin → Appearance → Google Maps</strong></p></div>';
	}
	
	// Extract iframe src if full iframe code provided, or use as-is
	$embed = $embed_code;
	
	// If it's just a URL, wrap it in iframe
	if ( ! preg_match( '/<iframe/i', $embed_code ) && filter_var( $embed_code, FILTER_VALIDATE_URL ) ) {
		$embed = '<iframe src="' . esc_url( $embed_code ) . '" width="100%" height="' . esc_attr( $height ) . '" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
	} elseif ( preg_match( '/<iframe/i', $embed_code ) ) {
		// If it's an iframe, ensure height is set
		$embed = preg_replace( '/height="[^"]*"/i', 'height="' . esc_attr( $height ) . '"', $embed_code );
		$embed = preg_replace( '/width="[^"]*"/i', 'width="100%"', $embed );
		if ( ! preg_match( '/height=/i', $embed ) ) {
			$embed = str_replace( '<iframe', '<iframe height="' . esc_attr( $height ) . '"', $embed );
		}
		if ( ! preg_match( '/width=/i', $embed ) ) {
			$embed = str_replace( '<iframe', '<iframe width="100%"', $embed );
		}
	}
	
	ob_start();
	?>
	<div class="card pad" style="padding:0; overflow:hidden;">
		<div style="padding:16px 18px; border-bottom:1px solid rgba(255,255,255,.08); display:flex; justify-content:space-between; align-items:center; gap:10px;">
			<div>
				<b style="font-size:14px;">Coverage Map</b>
				<div class="note">Service locations and coverage areas</div>
			</div>
		</div>
		<div style="position:relative; width:100%; aspect-ratio: 16 / 10; background: rgba(0,0,0,.2);">
			<?php echo $embed; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Shortcode to display map embed: [road_aid_map_embed height="500px"]
 */
function road_aid_map_embed_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'height' => '500px',
	), $atts, 'road_aid_map_embed' );
	
	return road_aid_display_map_embed( $atts['height'] );
}
add_shortcode( 'road_aid_map_embed', 'road_aid_map_embed_shortcode' );

/**
 * Europe SVG map shortcode (same as homepage coverage section)
 * Use [road_aid_europe_map] on Coverage page or anywhere you want the Europe map.
 */
function road_aid_europe_map_shortcode() {
	$europe_svg = get_template_directory() . '/assets/europe.svg';
	if ( ! file_exists( $europe_svg ) ) {
		return '';
	}
	$svg = file_get_contents( $europe_svg );
	$svg = preg_replace( '/<\?xml[^?]+\?>\s*/', '', $svg );
	$svg = str_replace( '<svg ', '<svg class="europe-map-svg" aria-hidden="true" ', $svg );
	$svg = str_replace( ' fill="#ececec"', '', $svg );
	$svg = str_replace( ' stroke="black"', '', $svg );
	$ob = '<div class="europe-map-wrapper">' . $svg . '</div>';
	return $ob;
}
add_shortcode( 'road_aid_europe_map', 'road_aid_europe_map_shortcode' );

/**
 * Inject data-coverage labels on Albania SVG paths (qarks → service line from coverage page).
 *
 * @param string $svg Raw SVG markup.
 * @return string
 */
function road_aid_albania_map_inject_coverage_labels( $svg ) {
	$labels = array(
		'AL10' => __( 'Shkoder — Roadside Assistance & Tow Truck', 'road-aid-europe' ),
		'AL11' => __( 'Tirana — Roadside Assistance & Tow Truck', 'road-aid-europe' ),
		'AL02' => __( 'Durres — Roadside Assistance & Tow Truck', 'road-aid-europe' ),
		'AL04' => __( 'Fier — Roadside Assistance & Tow Truck', 'road-aid-europe' ),
		'AL03' => __( 'Elbasan — Roadside Assistance & Tow Truck', 'road-aid-europe' ),
		'AL07' => __( 'Kukes — Roadside Assistance & Tow Truck', 'road-aid-europe' ),
		'AL01' => __( 'Berat — Roadside Assistance & Tow Truck', 'road-aid-europe' ),
		'AL12' => __( 'Vlora, Saranda & Ksamil — Roadside Assistance & Tow Truck', 'road-aid-europe' ),
		'AL06' => __( 'Korce — Roadside Assistance & Tow Truck', 'road-aid-europe' ),
		'AL05' => __( 'Gjirokaster — Roadside Assistance & Tow Truck', 'road-aid-europe' ),
		'AL08' => __( 'Lezhe — Roadside Assistance & Tow Truck', 'road-aid-europe' ),
		'AL09' => __( 'Diber — Roadside Assistance & Tow Truck', 'road-aid-europe' ),
	);
	foreach ( $labels as $id => $text ) {
		$esc = esc_attr( $text );
		$svg = preg_replace_callback(
			'/<path\s[^>]*\bid="' . preg_quote( $id, '/' ) . '"[^>]*>/',
			function ( $m ) use ( $esc ) {
				$tag = $m[0];
				if ( strpos( $tag, 'data-coverage=' ) !== false ) {
					return $tag;
				}
				return '>' === substr( $tag, -1 ) ? substr( $tag, 0, -1 ) . ' data-coverage="' . $esc . '">' : $tag;
			},
			$svg,
			1
		);
	}
	return $svg;
}

/**
 * Albania counties SVG (Simplemaps) — interactive like Europe map.
 * Use [road_aid_albania_map] on the Coverage page.
 */
function road_aid_albania_map_shortcode() {
	$al_svg = get_template_directory() . '/assets/al.svg';
	if ( ! file_exists( $al_svg ) ) {
		return '';
	}
	$svg = file_get_contents( $al_svg );
	$svg = preg_replace( '/<\?xml[^?]+\?>\s*/', '', $svg );
	$svg = road_aid_albania_map_inject_coverage_labels( $svg );
	$svg = str_replace( '<svg ', '<svg class="albania-map-svg" role="img" aria-label="' . esc_attr__( 'Albania service coverage by county', 'road-aid-europe' ) . '" ', $svg );
	$svg = str_replace( 'viewbox=', 'viewBox=', $svg );
	return '<div class="albania-map-wrapper">' . $svg . '</div>';
}
add_shortcode( 'road_aid_albania_map', 'road_aid_albania_map_shortcode' );

/**
 * Coverage Settings - Admin Menu
 */
function road_aid_coverage_settings_menu() {
	// Don't run during installation
	if ( defined( 'WP_INSTALLING' ) || ! function_exists( 'add_submenu_page' ) ) {
		return;
	}
	
	add_submenu_page(
		'themes.php',
		__( 'Coverage Settings', 'road-aid-europe' ),
		__( 'Coverage Settings', 'road-aid-europe' ),
		'manage_options',
		'road-aid-coverage-settings',
		'road_aid_coverage_settings_page'
	);
}
add_action( 'admin_menu', 'road_aid_coverage_settings_menu' );

/**
 * Coverage Settings Admin Page
 */
function road_aid_coverage_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Save settings
	if ( isset( $_POST['road_aid_save_coverage'] ) && check_admin_referer( 'road_aid_coverage_nonce' ) ) {
		// Coverage subtitle
		if ( isset( $_POST['coverage_subtitle'] ) ) {
			update_option( 'road_aid_coverage_subtitle', sanitize_text_field( $_POST['coverage_subtitle'] ) );
		}
		
		// Coverage description
		if ( isset( $_POST['coverage_description'] ) ) {
			update_option( 'road_aid_coverage_description', sanitize_text_field( $_POST['coverage_description'] ) );
		}
		
		// Coverage areas (one per line)
		if ( isset( $_POST['coverage_areas'] ) ) {
			update_option( 'road_aid_coverage_areas', sanitize_textarea_field( $_POST['coverage_areas'] ) );
		}
		
		// Expansion title
		if ( isset( $_POST['expansion_title'] ) ) {
			update_option( 'road_aid_expansion_title', sanitize_text_field( $_POST['expansion_title'] ) );
		}
		
		// Expansion text
		if ( isset( $_POST['expansion_text'] ) ) {
			update_option( 'road_aid_expansion_text', sanitize_textarea_field( $_POST['expansion_text'] ) );
		}
		
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Settings saved!', 'road-aid-europe' ) . '</p></div>';
	}

	$coverage_subtitle = get_option( 'road_aid_coverage_subtitle', '' );
	$coverage_description = get_option( 'road_aid_coverage_description', 'Service coverage across all Albania municipalities (nationwide dispatch via partner network).' );
	$coverage_areas = get_option( 'road_aid_coverage_areas', '' );
	$expansion_title = get_option( 'road_aid_expansion_title', 'Expansion-ready' );
	$expansion_text = get_option( 'road_aid_expansion_text', 'Build Albania as your operational base, then replicate the provider onboarding + dispatch SOP into Balkans and wider Europe.' );
	
	// If empty, set default areas
	if ( empty( $coverage_areas ) ) {
		$default_areas = "Belsh\nBerat\nBulqize\nCerrik\nDelvine\nDevoll\nDiber\nDivjake\nDropull\nDurres\nElbasan\nFier\nFiniq\nFushe-Arrez\nGjak?\nGjirokaster\nGramsh\nHas\nHimare\nKamez\nKavaje\nKlos\nKolonje\nKonispol\nKorce\nKruje\nKukes\nKurbin\nKuçove\nKëlcyre\nLezhe\nLibohove\nLibrazhd\nLushnje\nMalesi e Madhe\nMaliq\nMallakaster\nMat\nMemaliaj\nMirditë\nPatos\nPeqin\nPermet\nPogradec\nPolican\nPrrenjas\nPuke\nPustec\nRoskovec\nRrogozhine\nSarande\nSelenice\nShijak\nShkoder\nSkrapar\nTepelene\nTirana\nTropoje\nUra Vajgurore\nVau i Dejes\nVlore\nVore";
		$coverage_areas = $default_areas;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p><?php _e( 'Manage coverage areas and expansion information displayed on the Coverage page. You can also edit the page content using the WordPress editor.', 'road-aid-europe' ); ?></p>
		
		<form method="post" action="">
			<?php wp_nonce_field( 'road_aid_coverage_nonce' ); ?>
			
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="coverage_subtitle"><?php _e( 'Page Subtitle/Lead Text', 'road-aid-europe' ); ?></label>
					</th>
					<td>
						<input type="text" id="coverage_subtitle" name="coverage_subtitle" value="<?php echo esc_attr( $coverage_subtitle ); ?>" class="regular-text" />
						<p class="description"><?php _e( 'Subtitle displayed below the page title. Leave empty to use page excerpt or default text.', 'road-aid-europe' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="coverage_description"><?php _e( 'Coverage Description', 'road-aid-europe' ); ?></label>
					</th>
					<td>
						<input type="text" id="coverage_description" name="coverage_description" value="<?php echo esc_attr( $coverage_description ); ?>" class="regular-text" />
						<p class="description"><?php _e( 'Text displayed above the coverage areas list', 'road-aid-europe' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="coverage_areas"><?php _e( 'Coverage Areas', 'road-aid-europe' ); ?></label>
					</th>
					<td>
						<textarea id="coverage_areas" name="coverage_areas" rows="15" class="large-text" style="font-family: monospace;"><?php echo esc_textarea( $coverage_areas ); ?></textarea>
						<p class="description">
							<?php _e( 'Enter one area/city per line. These will appear as pill badges on the Coverage page.', 'road-aid-europe' ); ?><br>
							<?php _e( 'Example:', 'road-aid-europe' ); ?><br>
							<code>Tirana<br>Durres<br>Vlore</code>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="expansion_title"><?php _e( 'Expansion Section Title', 'road-aid-europe' ); ?></label>
					</th>
					<td>
						<input type="text" id="expansion_title" name="expansion_title" value="<?php echo esc_attr( $expansion_title ); ?>" class="regular-text" />
						<p class="description"><?php _e( 'Title for the expansion-ready section (leave empty to hide)', 'road-aid-europe' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="expansion_text"><?php _e( 'Expansion Section Text', 'road-aid-europe' ); ?></label>
					</th>
					<td>
						<textarea id="expansion_text" name="expansion_text" rows="4" class="large-text"><?php echo esc_textarea( $expansion_text ); ?></textarea>
						<p class="description"><?php _e( 'Text for the expansion-ready section (leave empty to hide)', 'road-aid-europe' ); ?></p>
					</td>
				</tr>
			</table>
			
			<?php submit_button( __( 'Save Settings', 'road-aid-europe' ), 'primary', 'road_aid_save_coverage' ); ?>
		</form>
	</div>
	<?php
}

/**
 * Contact Page Settings - Admin Menu
 */
function road_aid_contact_page_settings_menu() {
	// Don't run during installation
	if ( defined( 'WP_INSTALLING' ) || ! function_exists( 'add_submenu_page' ) ) {
		return;
	}
	
	add_submenu_page(
		'themes.php',
		__( 'Contact Page Settings', 'road-aid-europe' ),
		__( 'Contact Page Settings', 'road-aid-europe' ),
		'manage_options',
		'road-aid-contact-page-settings',
		'road_aid_contact_page_settings_page'
	);
}
add_action( 'admin_menu', 'road_aid_contact_page_settings_menu' );

/**
 * Contact Page Settings Admin Page
 */
function road_aid_contact_page_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Save settings
	if ( isset( $_POST['road_aid_save_contact_page'] ) && check_admin_referer( 'road_aid_contact_page_nonce' ) ) {
		// Subtitle/Lead text
		if ( isset( $_POST['contact_subtitle'] ) ) {
			update_option( 'road_aid_contact_subtitle', sanitize_text_field( $_POST['contact_subtitle'] ) );
		}
		
		// Emergency section description
		if ( isset( $_POST['emergency_description'] ) ) {
			update_option( 'road_aid_emergency_description', sanitize_textarea_field( $_POST['emergency_description'] ) );
		}
		
		// Emergency section title
		if ( isset( $_POST['emergency_title'] ) ) {
			update_option( 'road_aid_emergency_title', sanitize_text_field( $_POST['emergency_title'] ) );
		}
		
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Settings saved!', 'road-aid-europe' ) . '</p></div>';
	}

	$contact_subtitle = get_option( 'road_aid_contact_subtitle', '' );
	$emergency_title = get_option( 'road_aid_emergency_title', 'Emergency' );
	$emergency_description = get_option( 'road_aid_emergency_description', 'For breakdown assistance, use the request form above to send structured details.' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p><?php _e( 'Manage content displayed on the Contact page. You can also edit the page content using the WordPress editor.', 'road-aid-europe' ); ?></p>
		
		<form method="post" action="">
			<?php wp_nonce_field( 'road_aid_contact_page_nonce' ); ?>
			
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="contact_subtitle"><?php _e( 'Page Subtitle/Lead Text', 'road-aid-europe' ); ?></label>
					</th>
					<td>
						<input type="text" id="contact_subtitle" name="contact_subtitle" value="<?php echo esc_attr( $contact_subtitle ); ?>" class="regular-text" />
						<p class="description"><?php _e( 'Subtitle displayed below the page title. Leave empty to use page excerpt or default text.', 'road-aid-europe' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="emergency_title"><?php _e( 'Emergency Section Title', 'road-aid-europe' ); ?></label>
					</th>
					<td>
						<input type="text" id="emergency_title" name="emergency_title" value="<?php echo esc_attr( $emergency_title ); ?>" class="regular-text" />
						<p class="description"><?php _e( 'Title for the emergency contact card', 'road-aid-europe' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="emergency_description"><?php _e( 'Emergency Section Description', 'road-aid-europe' ); ?></label>
					</th>
					<td>
						<textarea id="emergency_description" name="emergency_description" rows="4" class="large-text"><?php echo esc_textarea( $emergency_description ); ?></textarea>
						<p class="description"><?php _e( 'Description text displayed in the emergency contact card', 'road-aid-europe' ); ?></p>
					</td>
				</tr>
			</table>
			
			<?php submit_button( __( 'Save Settings', 'road-aid-europe' ), 'primary', 'road_aid_save_contact_page' ); ?>
		</form>
	</div>
	<?php
}

/**
 * ========================================
 * ADMIN DASHBOARD CUSTOMIZATIONS
 * ========================================
 */

/**
 * Enqueue custom admin styles
 * Exclude media/upload and post editor pages to avoid breaking Media Library and Add Media modal.
 */
function road_aid_admin_styles( $hook ) {
	// Don't run during installation
	if ( defined( 'WP_INSTALLING' ) || ! function_exists( 'wp_enqueue_style' ) ) {
		return;
	}
	// Skip on Media Library, Add New Media, and post/page editor (Add Media modal) to prevent conflicts
	$exclude = array( 'upload.php', 'media-new.php', 'post.php', 'post-new.php', 'page.php', 'page-new.php' );
	if ( in_array( $hook, $exclude, true ) ) {
		return;
	}
	wp_enqueue_style( 'road-aid-admin-style', get_template_directory_uri() . '/assets/admin-style.css', array(), '1.0.0' );
}
add_action( 'admin_enqueue_scripts', 'road_aid_admin_styles' );

/**
 * Customize login page
 */
function road_aid_custom_login() {
	wp_enqueue_style( 'road-aid-login-style', get_template_directory_uri() . '/assets/login-style.css', array(), '1.0.0' );
}
add_action( 'login_enqueue_scripts', 'road_aid_custom_login' );

/**
 * Change login logo URL
 */
function road_aid_login_logo_url() {
	return home_url();
}
add_filter( 'login_headerurl', 'road_aid_login_logo_url' );

/**
 * Change login logo title
 */
function road_aid_login_logo_url_title() {
	return get_bloginfo( 'name' );
}
add_filter( 'login_headertext', 'road_aid_login_logo_url_title' );

/* ─────────────────────────────────────────────
   Page Seeder — creates all theme pages in one click
   ───────────────────────────────────────────── */

function road_aid_get_seed_pages() {
	return array(
		array(
			'title'    => 'Home',
			'slug'     => 'home',
			'template' => '',
			'content'  => '',
			'is_front' => true,
		),
		array(
			'title'    => 'Emergency',
			'slug'     => 'emergency',
			'template' => 'page-emergency.php',
			'content'  => '',
		),
		array(
			'title'    => 'About Us',
			'slug'     => 'about',
			'template' => 'page-about.php',
			'content'  => '',
		),
		array(
			'title'    => 'Coverage',
			'slug'     => 'coverage',
			'template' => 'page-coverage.php',
			'content'  => '',
		),
		array(
			'title'    => 'Partners & Business Solutions',
			'slug'     => 'partners',
			'template' => 'page-partners.php',
			'content'  => '',
		),
		array(
			'title'    => 'Contact Us',
			'slug'     => 'contact',
			'template' => 'page-contact.php',
			'content'  => '',
		),
		array(
			'title'    => 'Blog',
			'slug'     => 'blog',
			'template' => 'page-blog.php',
			'content'  => '',
		),
		array(
			'title'    => 'Privacy Policy',
			'slug'     => 'privacy-policy',
			'template' => 'page-privacy.php',
			'content'  => '',
		),
		array(
			'title'    => 'Terms & Conditions',
			'slug'     => 'terms-conditions',
			'template' => 'page-terms.php',
			'content'  => '',
		),
		array(
			'title'    => 'Services',
			'slug'     => 'services',
			'template' => '',
			'content'  => '',
		),
		array(
			'title'    => 'Get Assistance',
			'slug'     => 'assistance',
			'template' => '',
			'content'  => '',
		),
		array(
			'title'    => 'Become a Service Provider',
			'slug'     => 'become-a-service-provider',
			'template' => '',
			'content'  => '',
		),
		array(
			'title'    => 'Rental / Fleet Operators',
			'slug'     => 'corporate',
			'template' => '',
			'content'  => '',
		),
	);
}

function road_aid_seed_pages() {
	$pages   = road_aid_get_seed_pages();
	$created = 0;
	$updated = 0;
	$skipped = 0;

	foreach ( $pages as $page_data ) {
		$existing = get_page_by_path( $page_data['slug'] );

		if ( $existing ) {
			if ( ! empty( $page_data['template'] ) ) {
				$current_tpl = get_post_meta( $existing->ID, '_wp_page_template', true );
				if ( $current_tpl !== $page_data['template'] ) {
					update_post_meta( $existing->ID, '_wp_page_template', $page_data['template'] );
					$updated++;
				} else {
					$skipped++;
				}
			} else {
				$skipped++;
			}

			if ( ! empty( $page_data['is_front'] ) ) {
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $existing->ID );
			}
			continue;
		}

		$page_id = wp_insert_post( array(
			'post_title'   => $page_data['title'],
			'post_name'    => $page_data['slug'],
			'post_content' => $page_data['content'],
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_author'  => get_current_user_id(),
		) );

		if ( ! is_wp_error( $page_id ) ) {
			if ( ! empty( $page_data['template'] ) ) {
				update_post_meta( $page_id, '_wp_page_template', $page_data['template'] );
			}
			if ( ! empty( $page_data['is_front'] ) ) {
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $page_id );
			}
			$created++;
		}
	}

	return compact( 'created', 'updated', 'skipped' );
}

function road_aid_seed_pages_menu() {
	if ( defined( 'WP_INSTALLING' ) || ! function_exists( 'add_submenu_page' ) ) {
		return;
	}
	add_submenu_page(
		'themes.php',
		__( 'Seed Pages', 'road-aid-europe' ),
		__( 'Seed Pages', 'road-aid-europe' ),
		'manage_options',
		'road-aid-seed-pages',
		'road_aid_seed_pages_admin'
	);
}
add_action( 'admin_menu', 'road_aid_seed_pages_menu' );

function road_aid_seed_pages_admin() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Unauthorized' );
	}

	$result = null;
	if ( isset( $_POST['road_aid_seed_pages'] ) && check_admin_referer( 'road_aid_seed_pages_nonce' ) ) {
		$result = road_aid_seed_pages();
	}

	$pages     = road_aid_get_seed_pages();
	$statuses  = array();
	foreach ( $pages as $p ) {
		$existing = get_page_by_path( $p['slug'] );
		if ( $existing ) {
			$tpl = get_post_meta( $existing->ID, '_wp_page_template', true );
			$tpl_match = empty( $p['template'] ) || $tpl === $p['template'];
			$statuses[] = array(
				'title'    => $p['title'],
				'slug'     => $p['slug'],
				'template' => $p['template'] ?: '—',
				'status'   => $tpl_match ? 'ok' : 'needs-update',
				'edit_url' => get_edit_post_link( $existing->ID ),
				'view_url' => get_permalink( $existing->ID ),
			);
		} else {
			$statuses[] = array(
				'title'    => $p['title'],
				'slug'     => $p['slug'],
				'template' => $p['template'] ?: '—',
				'status'   => 'missing',
				'edit_url' => '',
				'view_url' => '',
			);
		}
	}
	?>
	<div class="wrap">
		<h1 style="margin-bottom:6px;"><?php esc_html_e( 'Seed Theme Pages', 'road-aid-europe' ); ?></h1>
		<p style="color:#666;max-width:600px;">Creates all the pages this theme needs and assigns the correct page template to each one. Existing pages are never deleted — only their template is updated if needed.</p>

		<?php if ( $result ) : ?>
			<div class="notice notice-success" style="margin:16px 0;">
				<p>
					<strong>Done!</strong>
					<?php echo esc_html( $result['created'] ); ?> page(s) created,
					<?php echo esc_html( $result['updated'] ); ?> template(s) updated,
					<?php echo esc_html( $result['skipped'] ); ?> already correct.
				</p>
			</div>
		<?php endif; ?>

		<table class="widefat striped" style="max-width:820px;margin-top:16px;">
			<thead>
				<tr>
					<th>Page</th>
					<th>Slug</th>
					<th>Template</th>
					<th>Status</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $statuses as $s ) : ?>
					<tr>
						<td><strong><?php echo esc_html( $s['title'] ); ?></strong></td>
						<td><code>/<?php echo esc_html( $s['slug'] ); ?>/</code></td>
						<td><code><?php echo esc_html( $s['template'] ); ?></code></td>
						<td>
							<?php if ( $s['status'] === 'ok' ) : ?>
								<span style="color:#00a32a;">&#10003; OK</span>
							<?php elseif ( $s['status'] === 'needs-update' ) : ?>
								<span style="color:#dba617;">&#9888; Template mismatch</span>
							<?php else : ?>
								<span style="color:#d63638;">&#10005; Missing</span>
							<?php endif; ?>
						</td>
						<td>
							<?php if ( $s['edit_url'] ) : ?>
								<a href="<?php echo esc_url( $s['edit_url'] ); ?>">Edit</a> |
								<a href="<?php echo esc_url( $s['view_url'] ); ?>" target="_blank">View</a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<form method="post" style="margin-top:20px;">
			<?php wp_nonce_field( 'road_aid_seed_pages_nonce' ); ?>
			<button type="submit" name="road_aid_seed_pages" class="button button-primary button-hero">
				<?php esc_html_e( 'Create / Update All Pages', 'road-aid-europe' ); ?>
			</button>
			<p class="description" style="margin-top:8px;">This will create any missing pages and fix template assignments. Nothing is deleted.</p>
		</form>
	</div>
	<?php
}

/**
 * Custom admin footer text
 */
function road_aid_admin_footer_text() {
	return sprintf(
		__( 'Thank you for using <strong>%s</strong> theme. Powered by <a href="%s" target="_blank">WordPress</a>.', 'road-aid-europe' ),
		get_bloginfo( 'name' ),
		'https://wordpress.org'
	);
}
add_filter( 'admin_footer_text', 'road_aid_admin_footer_text' );

/**
 * Remove WordPress version from admin footer
 */
function road_aid_remove_footer_version() {
	return '';
}
add_filter( 'update_footer', 'road_aid_remove_footer_version', 11 );

/**
 * Customize admin color scheme
 */
function road_aid_admin_color_scheme() {
	// Don't run during installation
	if ( defined( 'WP_INSTALLING' ) ) {
		return;
	}
	
	global $_wp_admin_css_colors;
	
	if ( ! is_array( $_wp_admin_css_colors ) ) {
		$_wp_admin_css_colors = array();
	}
	
	// Define custom color scheme
	$colors = array(
		'#1a1a1a', // Base color (dark)
		'#D32F2F', // Accent red
		'#b71c1c', // Focus color (darker red)
		'#ffffff', // Current color (white)
	);
	
	$_wp_admin_css_colors['road-aid'] = (object) array(
		'name'  => __( 'Road Aid Europe', 'road-aid-europe' ),
		'url'   => '',
		'slug'  => 'road-aid',
		'colors' => $colors,
	);
}
add_action( 'admin_init', 'road_aid_admin_color_scheme' );

/**
 * Set default admin color scheme for new users
 */
function road_aid_set_default_admin_color( $user_id ) {
	$args = array(
		'ID' => $user_id,
		'admin_color' => 'road-aid',
	);
	wp_update_user( $args );
}
add_action( 'user_register', 'road_aid_set_default_admin_color' );

/**
 * Set default admin color scheme for current user if not set
 */
function road_aid_set_current_user_admin_color() {
	// Don't run during installation
	if ( defined( 'WP_INSTALLING' ) || ! function_exists( 'get_current_user_id' ) ) {
		return;
	}
	
	$user_id = get_current_user_id();
	if ( $user_id && function_exists( 'get_userdata' ) && function_exists( 'update_user_meta' ) ) {
		$user = get_userdata( $user_id );
		if ( $user && empty( $user->admin_color ) ) {
			update_user_meta( $user_id, 'admin_color', 'road-aid' );
		}
	}
}
add_action( 'admin_init', 'road_aid_set_current_user_admin_color' );

/**
 * Add custom admin bar menu items
 */
function road_aid_admin_bar_menu( $wp_admin_bar ) {
	// Don't run during installation
	if ( defined( 'WP_INSTALLING' ) || ! is_object( $wp_admin_bar ) ) {
		return;
	}
	
	// Add theme settings link
	$wp_admin_bar->add_menu( array(
		'id'    => 'road-aid-theme-settings',
		'title' => __( 'Road Aid Settings', 'road-aid-europe' ),
		'href'  => admin_url( 'customize.php' ),
		'meta'  => array(
			'title' => __( 'Theme Settings', 'road-aid-europe' ),
		),
	) );
	
	// Add submenu items
	$wp_admin_bar->add_menu( array(
		'parent' => 'road-aid-theme-settings',
		'id'     => 'road-aid-contact-forms',
		'title'  => __( 'Contact Forms', 'road-aid-europe' ),
		'href'   => admin_url( 'themes.php?page=road-aid-contact-form' ),
	) );
	
	$wp_admin_bar->add_menu( array(
		'parent' => 'road-aid-theme-settings',
		'id'     => 'road-aid-coverage-settings',
		'title'  => __( 'Coverage Settings', 'road-aid-europe' ),
		'href'   => admin_url( 'themes.php?page=road-aid-coverage-settings' ),
	) );
	
	$wp_admin_bar->add_menu( array(
		'parent' => 'road-aid-theme-settings',
		'id'     => 'road-aid-contact-page-settings',
		'title'  => __( 'Contact Page Settings', 'road-aid-europe' ),
		'href'   => admin_url( 'themes.php?page=road-aid-contact-page-settings' ),
	) );
	
	$wp_admin_bar->add_menu( array(
		'parent' => 'road-aid-theme-settings',
		'id'     => 'road-aid-google-maps',
		'title'  => __( 'Google Maps', 'road-aid-europe' ),
		'href'   => admin_url( 'themes.php?page=road-aid-maps-embed' ),
	) );
}
add_action( 'admin_bar_menu', 'road_aid_admin_bar_menu', 100 );

/**
 * Add custom dashboard widgets
 */
function road_aid_add_dashboard_widgets() {
	wp_add_dashboard_widget(
		'road_aid_quick_links',
		__( 'Road Aid Europe - Quick Links', 'road-aid-europe' ),
		'road_aid_dashboard_quick_links_widget'
	);
	
	wp_add_dashboard_widget(
		'road_aid_contact_info',
		__( 'Road Aid Europe - Contact Information', 'road-aid-europe' ),
		'road_aid_dashboard_contact_info_widget'
	);
}
add_action( 'wp_dashboard_setup', 'road_aid_add_dashboard_widgets' );

/**
 * Quick Links Dashboard Widget
 */
function road_aid_dashboard_quick_links_widget() {
	?>
	<div style="padding: 10px 0;">
		<ul style="list-style: disc; margin-left: 20px;">
			<li><a href="<?php echo admin_url( 'themes.php?page=road-aid-contact-form' ); ?>"><?php _e( 'Manage Contact Forms', 'road-aid-europe' ); ?></a></li>
			<li><a href="<?php echo admin_url( 'themes.php?page=road-aid-coverage-settings' ); ?>"><?php _e( 'Edit Coverage Settings', 'road-aid-europe' ); ?></a></li>
			<li><a href="<?php echo admin_url( 'themes.php?page=road-aid-contact-page-settings' ); ?>"><?php _e( 'Edit Contact Page Settings', 'road-aid-europe' ); ?></a></li>
			<li><a href="<?php echo admin_url( 'themes.php?page=road-aid-maps-embed' ); ?>"><?php _e( 'Configure Google Maps', 'road-aid-europe' ); ?></a></li>
			<li><a href="<?php echo admin_url( 'customize.php' ); ?>"><?php _e( 'Customize Theme', 'road-aid-europe' ); ?></a></li>
		</ul>
	</div>
	<?php
}

/**
 * Contact Information Dashboard Widget
 */
function road_aid_dashboard_contact_info_widget() {
	$emergency_phone = road_aid_get_emergency_phone();
	$whatsapp_phone = road_aid_get_whatsapp_phone();
	$business_email = road_aid_get_business_email();
	?>
	<div style="padding: 10px 0;">
		<p><strong><?php _e( 'Emergency Phone:', 'road-aid-europe' ); ?></strong><br>
		<a href="tel:<?php echo esc_attr( $emergency_phone ); ?>"><?php echo esc_html( $emergency_phone ); ?></a></p>
		
		<p><strong><?php _e( 'WhatsApp:', 'road-aid-europe' ); ?></strong><br>
		<a href="https://wa.me/<?php echo esc_attr( preg_replace( '/[^0-9]/', '', $whatsapp_phone ) ); ?>" target="_blank"><?php echo esc_html( $whatsapp_phone ); ?></a></p>
		
		<p><strong><?php _e( 'Business Email:', 'road-aid-europe' ); ?></strong><br>
		<a href="mailto:<?php echo esc_attr( $business_email ); ?>"><?php echo esc_html( $business_email ); ?></a></p>
		
		<p style="margin-top: 15px;">
			<a href="<?php echo admin_url( 'customize.php?autofocus[section]=road_aid_contact' ); ?>" class="button button-primary">
				<?php _e( 'Edit Contact Information', 'road-aid-europe' ); ?>
			</a>
		</p>
	</div>
	<?php
}

/**
 * Remove unnecessary dashboard widgets
 */
function road_aid_remove_dashboard_widgets() {
	// Remove default WordPress widgets
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
}
add_action( 'wp_dashboard_setup', 'road_aid_remove_dashboard_widgets', 999 );

/**
 * Add custom admin CSS
 * Skip on Media Library and post editor pages to avoid breaking Add Media functionality.
 */
function road_aid_admin_head() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( $screen && in_array( $screen->id, array( 'upload', 'media', 'post', 'page' ), true ) ) {
		return;
	}
	?>
	<style>
		/* Custom admin styling */
		#wpadminbar {
			background: #1a1a1a !important;
		}
		
		#wpadminbar .ab-item,
		#wpadminbar .ab-empty-item,
		#wpadminbar a.ab-item,
		#wpadminbar > #wp-toolbar > #wp-admin-bar-root-default .ab-item {
			color: #ffffff !important;
		}
		
		#wpadminbar .ab-item:hover,
		#wpadminbar .ab-item:focus,
		#wpadminbar .ab-item:active {
			background: #4ea1ff !important;
		}
		
		/* Customize admin menu */
		#adminmenu .wp-menu-image:before {
			color: #a7aaad;
		}
		
		#adminmenu li.wp-has-current-submenu .wp-menu-image:before,
		#adminmenu li.current .wp-menu-image:before {
			color: #4ea1ff;
		}
		
		/* Customize buttons */
		.button-primary,
		.button.button-primary {
			background: #4ea1ff !important;
			border-color: #4ea1ff !important;
		}
		
		.button-primary:hover,
		.button.button-primary:hover {
			background: #2d5aa0 !important;
			border-color: #2d5aa0 !important;
		}
		
		/* Dashboard widget styling */
		#road_aid_quick_links .inside,
		#road_aid_contact_info .inside {
			background: #f9f9f9;
			border-top: 3px solid #4ea1ff;
		}
	</style>
	<?php
}
add_action( 'admin_head', 'road_aid_admin_head' );

/**
 * Customize login page head
 */
function road_aid_login_head() {
	?>
	<style>
		body.login {
			background: #0E2A47;
		}
		
		.login h1 a {
			background-image: url('<?php echo get_template_directory_uri(); ?>/assets/road aid europe logo new.svg');
			background-size: contain;
			background-repeat: no-repeat;
			background-position: center;
			width: 200px;
			height: 80px;
		}
		
		.login form {
			background: #ffffff;
			border-radius: 8px;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
		}
		
		.login .button-primary {
			background: #D32F2F !important;
			border-color: #D32F2F !important;
			text-shadow: none;
			box-shadow: none;
		}
		
		.login .button-primary:hover {
			background: #b71c1c !important;
			border-color: #b71c1c !important;
		}
		
		.login #nav a,
		.login #backtoblog a {
			color: #D32F2F !important;
		}
		
		.login #nav a:hover,
		.login #backtoblog a:hover {
			color: #b71c1c !important;
		}
	</style>
	<?php
}
add_action( 'login_head', 'road_aid_login_head' );

/**
 * ========================================
 * SECURITY ENHANCEMENTS
 * ========================================
 */

/**
 * Remove WordPress version from head
 */
function road_aid_remove_version() {
	return '';
}
add_filter( 'the_generator', 'road_aid_remove_version' );

/**
 * Disable XML-RPC (if not needed)
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Remove unnecessary header information
 */
function road_aid_remove_header_info() {
	// Don't run during installation
	if ( defined( 'WP_INSTALLING' ) ) {
		return;
	}
	
	if ( function_exists( 'remove_action' ) ) {
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	}
}
add_action( 'init', 'road_aid_remove_header_info' );

/**
 * Add security headers
 */
function road_aid_security_headers() {
	if ( ! is_admin() ) {
		header( 'X-Content-Type-Options: nosniff' );
		header( 'X-Frame-Options: SAMEORIGIN' );
		header( 'X-XSS-Protection: 1; mode=block' );
		header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	}
}
add_action( 'send_headers', 'road_aid_security_headers' );

/**
 * ========================================
 * SEO OPTIMIZATION & YOAST SEO COMPATIBILITY
 * ========================================
 */

/**
 * Add Schema.org structured data (Organization)
 */
function road_aid_schema_organization() {
	// Don't run in admin or during installation
	if ( is_admin() || defined( 'WP_INSTALLING' ) || ! function_exists( 'get_bloginfo' ) ) {
		return;
	}
	
	$schema = array(
		'@context' => 'https://schema.org',
		'@type' => 'Organization',
		'name' => get_bloginfo( 'name' ),
		'url' => home_url(),
		'logo' => get_template_directory_uri() . '/assets/road aid europe logo new.svg',
		'contactPoint' => array(
			'@type' => 'ContactPoint',
			'telephone' => road_aid_get_emergency_phone(),
			'contactType' => 'customer service',
			'areaServed' => 'AL',
			'availableLanguage' => array( 'en', 'sq' ),
		),
		'sameAs' => array(
			// Add social media URLs here if available
		),
	);
	
	// Add email if available
	$email = road_aid_get_business_email();
	if ( ! empty( $email ) ) {
		$schema['email'] = $email;
	}
	
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}
add_action( 'wp_head', 'road_aid_schema_organization', 5 );

/**
 * Add Schema.org structured data for LocalBusiness
 */
function road_aid_schema_local_business() {
	// Don't run in admin or during installation
	if ( is_admin() || defined( 'WP_INSTALLING' ) || ! function_exists( 'is_front_page' ) ) {
		return;
	}
	
	if ( ! is_front_page() ) {
		return;
	}
	
	$schema = array(
		'@context' => 'https://schema.org',
		'@type' => 'LocalBusiness',
		'name' => get_bloginfo( 'name' ),
		'image' => get_template_directory_uri() . '/assets/road aid europe logo new.svg',
		'telephone' => road_aid_get_emergency_phone(),
		'priceRange' => '$$',
		'address' => array(
			'@type' => 'PostalAddress',
			'addressCountry' => 'AL',
		),
	);
	
	$email = road_aid_get_business_email();
	if ( ! empty( $email ) ) {
		$schema['email'] = $email;
	}
	
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}
add_action( 'wp_head', 'road_aid_schema_local_business', 5 );

/**
 * Add Schema.org BreadcrumbList
 */
function road_aid_schema_breadcrumbs() {
	// Don't run in admin
	if ( is_admin() ) {
		return;
	}
	
	// Only add if Yoast breadcrumbs are not active
	if ( function_exists( 'yoast_breadcrumb' ) ) {
		return;
	}
	
	if ( is_front_page() ) {
		return;
	}
	
	$items = array();
	$position = 1;
	
	// Home
	$items[] = array(
		'@type' => 'ListItem',
		'position' => $position++,
		'name' => get_bloginfo( 'name' ),
		'item' => home_url(),
	);
	
	// Current page
	if ( is_page() || is_single() ) {
		$items[] = array(
			'@type' => 'ListItem',
			'position' => $position,
			'name' => get_the_title(),
			'item' => get_permalink(),
		);
	}
	
	if ( count( $items ) > 1 ) {
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'BreadcrumbList',
			'itemListElement' => $items,
		);
		
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
	}
}
add_action( 'wp_head', 'road_aid_schema_breadcrumbs', 5 );

/**
 * Enhanced meta tags for SEO
 */
function road_aid_enhanced_meta_tags() {
	// Don't run in admin or during plugin installation
	if ( is_admin() || defined( 'WP_INSTALLING' ) ) {
		return;
	}
	
	// Only add if Yoast SEO is not active (Yoast handles this)
	if ( defined( 'WPSEO_VERSION' ) ) {
		return;
	}
	
	// Meta description
	if ( is_singular() && has_excerpt() ) {
		$description = get_the_excerpt();
	} elseif ( is_singular() ) {
		$description = wp_trim_words( get_the_content(), 30, '...' );
	} else {
		$description = get_bloginfo( 'description' );
	}
	
	if ( ! empty( $description ) ) {
		echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $description ) ) . '" />' . "\n";
	}
	
	// Open Graph tags
	if ( is_singular() ) {
		$og_title = get_the_title();
		$og_url = get_permalink();
		$og_type = is_single() ? 'article' : 'website';
		
		if ( has_post_thumbnail() ) {
			$og_image = get_the_post_thumbnail_url( get_the_ID(), 'road-aid-featured' );
		} else {
			$og_image = get_template_directory_uri() . '/assets/road aid europe logo new.svg';
		}
		
		$og_description = ! empty( $description ) ? wp_strip_all_tags( $description ) : get_bloginfo( 'description' );
		
		echo '<meta property="og:title" content="' . esc_attr( $og_title ) . '" />' . "\n";
		echo '<meta property="og:type" content="' . esc_attr( $og_type ) . '" />' . "\n";
		echo '<meta property="og:url" content="' . esc_url( $og_url ) . '" />' . "\n";
		echo '<meta property="og:image" content="' . esc_url( $og_image ) . '" />' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( $og_description ) . '" />' . "\n";
		echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '" />' . "\n";
		
		// Twitter Card
		echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
		echo '<meta name="twitter:title" content="' . esc_attr( $og_title ) . '" />' . "\n";
		echo '<meta name="twitter:description" content="' . esc_attr( $og_description ) . '" />' . "\n";
		echo '<meta name="twitter:image" content="' . esc_url( $og_image ) . '" />' . "\n";
	}
}
add_action( 'wp_head', 'road_aid_enhanced_meta_tags', 1 );

/**
 * Breadcrumbs function (fallback if Yoast not active)
 */
function road_aid_breadcrumbs() {
	// Use Yoast breadcrumbs if available
	if ( function_exists( 'yoast_breadcrumb' ) ) {
		yoast_breadcrumb( '<nav class="breadcrumbs" aria-label="Breadcrumb">', '</nav>' );
		return;
	}
	
	// Fallback breadcrumbs
	if ( is_front_page() ) {
		return;
	}
	
	echo '<nav class="breadcrumbs" aria-label="Breadcrumb">';
	echo '<a href="' . esc_url( home_url() ) . '">' . esc_html__( 'Home', 'road-aid-europe' ) . '</a>';
	
	if ( is_page() ) {
		$ancestors = get_post_ancestors( get_the_ID() );
		if ( $ancestors ) {
			$ancestors = array_reverse( $ancestors );
			foreach ( $ancestors as $ancestor ) {
				echo ' <span class="separator">/</span> ';
				echo '<a href="' . esc_url( get_permalink( $ancestor ) ) . '">' . esc_html( get_the_title( $ancestor ) ) . '</a>';
			}
		}
		echo ' <span class="separator">/</span> ';
		echo '<span class="current">' . esc_html( get_the_title() ) . '</span>';
	} elseif ( is_single() ) {
		echo ' <span class="separator">/</span> ';
		$category = get_the_category();
		if ( $category ) {
			echo '<a href="' . esc_url( get_category_link( $category[0]->term_id ) ) . '">' . esc_html( $category[0]->name ) . '</a>';
			echo ' <span class="separator">/</span> ';
		}
		echo '<span class="current">' . esc_html( get_the_title() ) . '</span>';
	}
	
	echo '</nav>';
}

/**
 * Ensure proper heading hierarchy
 */
function road_aid_fix_heading_hierarchy( $content ) {
	if ( ! is_singular() ) {
		return $content;
	}
	
	// This is a helper function - actual heading hierarchy should be maintained in templates
	return $content;
}

/**
 * Add proper alt text to images if missing
 */
function road_aid_image_alt_text( $attr, $attachment, $size ) {
	if ( empty( $attr['alt'] ) ) {
		$attr['alt'] = get_the_title( $attachment->ID );
		if ( empty( $attr['alt'] ) ) {
			$attr['alt'] = get_bloginfo( 'name' ) . ' - ' . __( 'Image', 'road-aid-europe' );
		}
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'road_aid_image_alt_text', 10, 3 );

/**
 * Add rel="nofollow" to external links
 */
function road_aid_nofollow_external_links( $content ) {
	if ( ! is_singular() ) {
		return $content;
	}
	
	$pattern = '/<a(.*?)href=["\'](https?:\/\/[^"\']+)["\'](.*?)>/i';
	$home_url = home_url();
	
	$content = preg_replace_callback( $pattern, function( $matches ) use ( $home_url ) {
		$url = $matches[2];
		$attributes = $matches[1] . $matches[3];
		
		// Check if it's an external link
		if ( strpos( $url, $home_url ) === false && strpos( $url, 'mailto:' ) === false && strpos( $url, 'tel:' ) === false ) {
			// Add nofollow if not already present
			if ( strpos( $attributes, 'rel=' ) === false ) {
				$attributes .= ' rel="nofollow noopener"';
			} elseif ( strpos( $attributes, 'nofollow' ) === false ) {
				$attributes = preg_replace( '/rel=["\']([^"\']*)["\']/', 'rel="$1 nofollow noopener"', $attributes );
			}
		}
		
		return '<a' . $attributes . 'href="' . esc_url( $url ) . '">';
	}, $content );
	
	return $content;
}
// Uncomment if you want to auto-add nofollow to external links
// add_filter( 'the_content', 'road_aid_nofollow_external_links' );

/**
 * Add proper language attributes
 */
function road_aid_language_attributes( $output ) {
	$lang = get_locale();
	if ( empty( $lang ) ) {
		$lang = 'en-US';
	}
	return ' lang="' . esc_attr( $lang ) . '"';
}
add_filter( 'language_attributes', 'road_aid_language_attributes' );

/**
 * Ensure Yoast SEO compatibility
 */
function road_aid_yoast_compatibility() {
	// Safety check - ensure WordPress is loaded
	if ( ! function_exists( 'has_action' ) ) {
		return;
	}
	
	// Remove duplicate meta descriptions if Yoast is active
	if ( defined( 'WPSEO_VERSION' ) ) {
		// Yoast handles meta tags, so we skip our custom meta tags
		// Use wp_loaded hook to ensure action is registered first
		if ( has_action( 'wp_head', 'road_aid_enhanced_meta_tags' ) ) {
			remove_action( 'wp_head', 'road_aid_enhanced_meta_tags', 1 );
		}
		
		// Ensure Yoast breadcrumbs are styled
		if ( function_exists( 'yoast_breadcrumb' ) ) {
			add_filter( 'wpseo_breadcrumb_output', 'road_aid_style_yoast_breadcrumbs' );
		}
	}
}
// Use wp_loaded instead of init to ensure all actions are registered
// Use priority 20 to run after most plugins are loaded
add_action( 'wp_loaded', 'road_aid_yoast_compatibility', 20 );

/**
 * Style Yoast breadcrumbs
 */
function road_aid_style_yoast_breadcrumbs( $output ) {
	// Add custom classes to Yoast breadcrumbs
	$output = str_replace( 'breadcrumbs', 'breadcrumbs road-aid-breadcrumbs', $output );
	return $output;
}

/**
 * Add breadcrumb CSS
 */
function road_aid_breadcrumb_styles() {
	?>
	<style>
		.breadcrumbs,
		.road-aid-breadcrumbs {
			padding: 12px 0;
			font-size: 13px;
			color: var(--muted);
		}
		.breadcrumbs a,
		.road-aid-breadcrumbs a {
			color: var(--muted);
			text-decoration: none;
		}
		.breadcrumbs a:hover,
		.road-aid-breadcrumbs a:hover {
			color: var(--brand);
		}
		.breadcrumbs .separator,
		.road-aid-breadcrumbs .separator {
			margin: 0 8px;
			color: var(--muted);
		}
		.breadcrumbs .current,
		.road-aid-breadcrumbs .current {
			color: var(--text);
			font-weight: 500;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'road_aid_breadcrumb_styles' );

/**
 * Add sitemap hint in robots.txt (if Yoast not active)
 */
function road_aid_robots_txt( $output, $public ) {
	if ( defined( 'WPSEO_VERSION' ) ) {
		return $output; // Yoast handles this
	}
	
	$output .= "\nSitemap: " . esc_url( home_url( '/sitemap.xml' ) ) . "\n";
	return $output;
}
add_filter( 'robots_txt', 'road_aid_robots_txt', 10, 2 );

/**
 * Ensure proper semantic HTML structure
 */
function road_aid_semantic_html() {
	// This function ensures templates use proper semantic HTML
	// Actual implementation is in templates (header, main, article, section, etc.)
}

/**
 * Add proper ARIA labels for accessibility and SEO
 */
function road_aid_aria_labels() {
	// ARIA labels are already added in header.php and templates
	// This is a placeholder for future enhancements
}

/**
 * Optimize images for SEO
 */
function road_aid_image_seo( $html, $id, $caption, $title, $align, $url, $size, $alt ) {
	// Ensure images have proper attributes
	if ( empty( $alt ) && ! empty( $id ) ) {
		$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
		if ( empty( $alt ) ) {
			$alt = get_the_title( $id );
		}
	}
	
	// Add loading="lazy" for better performance
	if ( strpos( $html, 'loading=' ) === false ) {
		$html = str_replace( '<img ', '<img loading="lazy" ', $html );
	}
	
	return $html;
}
add_filter( 'image_send_to_editor', 'road_aid_image_seo', 10, 8 );

/**
 * Add canonical URL support (Yoast handles this, but we add fallback)
 */
function road_aid_canonical_url() {
	// Don't run in admin
	if ( is_admin() ) {
		return;
	}
	
	if ( defined( 'WPSEO_VERSION' ) ) {
		return; // Yoast handles this
	}
	
	if ( is_singular() ) {
		echo '<link rel="canonical" href="' . esc_url( get_permalink() ) . '" />' . "\n";
	} elseif ( is_front_page() ) {
		echo '<link rel="canonical" href="' . esc_url( home_url() ) . '" />' . "\n";
	}
}
add_action( 'wp_head', 'road_aid_canonical_url', 1 );

/**
 * Add preconnect for external resources (performance + SEO)
 */
function road_aid_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.googleapis.com',
		);
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'road_aid_resource_hints', 10, 2 );

/**
 * Ensure proper heading hierarchy in content
 */
function road_aid_content_heading_hierarchy( $content ) {
	if ( ! is_singular() ) {
		return $content;
	}
	
	// This ensures h2-h6 are used properly in content
	// h1 is reserved for page title
	// Content should start with h2
	
	return $content;
}
add_filter( 'the_content', 'road_aid_content_heading_hierarchy' );

/**
 * Add meta robots tag for better SEO control
 */
function road_aid_meta_robots() {
	// Don't run in admin
	if ( is_admin() ) {
		return;
	}
	
	if ( defined( 'WPSEO_VERSION' ) ) {
		return; // Yoast handles this
	}
	
	// Add noindex for specific pages if needed
	if ( is_search() || is_404() ) {
		echo '<meta name="robots" content="noindex, follow" />' . "\n";
	}
}
add_action( 'wp_head', 'road_aid_meta_robots', 1 );

/**
 * ========================================
 * ERROR HANDLING & VALIDATION
 * ========================================
 */

/**
 * Better error handling for form submissions
 */
function road_aid_handle_form_errors( $errors ) {
	if ( ! empty( $errors ) ) {
		// Log errors for debugging (only in development)
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Road Aid Form Errors: ' . print_r( $errors, true ) );
		}
	}
	return $errors;
}

/**
 * Validate email addresses
 */
function road_aid_validate_email( $email ) {
	if ( ! is_email( $email ) ) {
		return false;
	}
	return sanitize_email( $email );
}

/**
 * Sanitize phone numbers
 */
function road_aid_sanitize_phone( $phone ) {
	// Remove all non-numeric characters except + and spaces
	$phone = preg_replace( '/[^0-9+\s]/', '', $phone );
	return sanitize_text_field( trim( $phone ) );
}

/**
 * ========================================
 * PERFORMANCE OPTIMIZATIONS
 * ========================================
 */

/**
 * Defer JavaScript loading
 */
function road_aid_defer_scripts( $tag, $handle, $src ) {
	$defer_scripts = array( 'road-aid-script' );
	
	if ( in_array( $handle, $defer_scripts ) ) {
		return str_replace( ' src', ' defer src', $tag );
	}

	// Google reCAPTCHA should load async + defer per Google's guidance.
	if ( 'road-aid-recaptcha' === $handle ) {
		return str_replace( ' src', ' async defer src', $tag );
	}
	
	return $tag;
}
add_filter( 'script_loader_tag', 'road_aid_defer_scripts', 10, 3 );

/**
 * Remove query strings from static resources
 */
function road_aid_remove_query_strings( $src ) {
	if ( strpos( $src, '?ver=' ) ) {
		$src = remove_query_arg( 'ver', $src );
	}
	return $src;
}
// Uncomment if needed: add_filter( 'script_loader_src', 'road_aid_remove_query_strings', 15, 1 );
// Uncomment if needed: add_filter( 'style_loader_src', 'road_aid_remove_query_strings', 15, 1 );

/**
 * ========================================
 * ACCESSIBILITY IMPROVEMENTS
 * ========================================
 */

/**
 * Add skip to content link
 */
function road_aid_skip_link() {
	echo '<a class="skip-link screen-reader-text" href="#main">' . esc_html__( 'Skip to content', 'road-aid-europe' ) . '</a>';
}
add_action( 'wp_body_open', 'road_aid_skip_link', 5 );

/**
 * Add screen reader text class styles
 */
function road_aid_screen_reader_styles() {
	?>
	<style>
		.screen-reader-text {
			clip: rect(1px, 1px, 1px, 1px);
			position: absolute !important;
			height: 1px;
			width: 1px;
			overflow: hidden;
		}
		.screen-reader-text:focus {
			background-color: #f1f1f1;
			border-radius: 3px;
			box-shadow: 0 0 2px 2px rgba(0, 0, 0, 0.6);
			clip: auto !important;
			color: #21759b;
			display: block;
			font-size: 14px;
			font-weight: bold;
			height: auto;
			left: 5px;
			line-height: normal;
			padding: 15px 23px 14px;
			text-decoration: none;
			top: 5px;
			width: auto;
			z-index: 100000;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'road_aid_screen_reader_styles' );

