<?php
/**
 * Instagram carousel section for the front page.
 * Requires: $ig_section (array) with keys:
 *   id, username, title, subtitle, profile_url, profile_pic, posts (array)
 *
 * @package RoadAidEurope
 */

if ( empty( $ig_section ) || empty( $ig_section['posts'] ) ) {
	return;
}

$section_id   = isset( $ig_section['id'] ) ? $ig_section['id'] : 'instagram';
$username     = isset( $ig_section['username'] ) ? $ig_section['username'] : '';
$title        = isset( $ig_section['title'] ) ? $ig_section['title'] : 'Instagram';
$subtitle     = isset( $ig_section['subtitle'] ) ? $ig_section['subtitle'] : '';
$profile_url  = isset( $ig_section['profile_url'] ) ? $ig_section['profile_url'] : ( $username ? 'https://www.instagram.com/' . $username . '/' : '#' );
$track_id     = $section_id . 'Track';
$dots_id      = $section_id . 'Dots';
$ig_icon_svg  = '<svg class="ig-brand-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" stroke="currentColor" stroke-width="1.75"/><circle cx="12" cy="12" r="4.25" stroke="currentColor" stroke-width="1.75"/><circle cx="17.5" cy="6.5" r="1.25" fill="currentColor"/></svg>';
?>
<section id="<?php echo esc_attr( $section_id ); ?>" class="section ig-section" data-ig-user="<?php echo esc_attr( $username ); ?>">
	<div class="container">
		<div class="section-title">
			<div>
				<h2><?php echo esc_html( $title ); ?></h2>
				<?php if ( $subtitle ) : ?>
					<p><?php echo esc_html( $subtitle ); ?></p>
				<?php endif; ?>
			</div>
			<a class="btn ig-profile-btn" href="<?php echo esc_url( $profile_url ); ?>" target="_blank" rel="noopener noreferrer">
				<?php echo $ig_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span>@<?php echo esc_html( $username ); ?></span>
			</a>
		</div>
		<div class="blog-slider-wrap ig-slider-wrap js-snap-slider">
			<button type="button" class="blog-slider-btn blog-slider-prev" aria-label="<?php esc_attr_e( 'Previous posts', 'road-aid-europe' ); ?>">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
			</button>
			<button type="button" class="blog-slider-btn blog-slider-next" aria-label="<?php esc_attr_e( 'Next posts', 'road-aid-europe' ); ?>">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
			</button>
			<div class="blog-slider-track ig-slider-track" id="<?php echo esc_attr( $track_id ); ?>" data-snap-track>
				<?php foreach ( $ig_section['posts'] as $post ) :
					$permalink = isset( $post['url'] ) ? $post['url'] : '';
					$image     = isset( $post['image'] ) ? $post['image'] : ( isset( $post['thumb'] ) ? $post['thumb'] : '' );
					$caption   = isset( $post['caption'] ) ? $post['caption'] : '';
					$excerpt   = $caption ? wp_trim_words( wp_strip_all_tags( $caption ), 18 ) : '';
					$is_video  = ! empty( $post['is_video'] );
					$ts        = isset( $post['timestamp'] ) ? (int) $post['timestamp'] : 0;
					$date_label = $ts ? date_i18n( get_option( 'date_format' ), $ts ) : '';
					if ( ! $permalink || ! $image ) {
						continue;
					}
					?>
					<article class="blog-slide ig-slide card">
						<a href="<?php echo esc_url( $permalink ); ?>" class="blog-slide-link ig-slide-link" target="_blank" rel="noopener noreferrer">
							<div class="ig-slide-media<?php echo $is_video ? ' is-video' : ''; ?>">
								<img
									src="<?php echo esc_url( $image ); ?>"
									alt=""
									loading="lazy"
									decoding="async"
									referrerpolicy="no-referrer"
									width="640"
									height="640"
								/>
								<?php if ( $is_video ) : ?>
									<span class="ig-video-badge" aria-hidden="true">
										<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><polygon points="8,5 20,12 8,19"/></svg>
									</span>
								<?php endif; ?>
							</div>
							<div class="blog-slide-content ig-slide-content">
								<?php if ( $date_label ) : ?>
									<time class="blog-slide-date" datetime="<?php echo esc_attr( gmdate( 'c', $ts ) ); ?>"><?php echo esc_html( $date_label ); ?></time>
								<?php endif; ?>
								<?php if ( $excerpt ) : ?>
									<p class="blog-slide-excerpt"><?php echo esc_html( $excerpt ); ?></p>
								<?php else : ?>
									<p class="blog-slide-excerpt ig-slide-view"><?php esc_html_e( 'View on Instagram', 'road-aid-europe' ); ?></p>
								<?php endif; ?>
								<span class="blog-slide-cta"><?php esc_html_e( 'Open post', 'road-aid-europe' ); ?></span>
							</div>
						</a>
					</article>
				<?php endforeach; ?>
			</div>
			<div class="blog-slider-dots" id="<?php echo esc_attr( $dots_id ); ?>" data-snap-dots aria-hidden="true"></div>
		</div>
	</div>
</section>
