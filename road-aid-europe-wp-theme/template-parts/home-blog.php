<?php
/**
 * Blog section for front page
 * Requires: $blog_posts (array), $blog_url (string)
 *
 * @package RoadAidEurope
 */

$blog_posts = isset( $blog_posts ) ? $blog_posts : array();
if ( empty( $blog_posts ) ) {
	echo '<section id="blog" class="section blog-section"><div class="container">';
	echo '<div class="section-title"><div><h2>Latest from the blog</h2><p>Tips, updates, and stories from the road.</p></div></div>';
	echo '<div class="blog-slider-empty"><p>No posts yet. Add posts in WordPress to see them here.</p></div>';
	echo '</div></section>';
	return;
}
?>
<section id="blog" class="section blog-section">
	<div class="container">
		<div class="section-title">
			<div>
				<h2>Latest from the blog</h2>
				<p>Tips, updates, and stories from the road.</p>
			</div>
			<a class="btn" href="<?php echo esc_url( home_url( '/index.php/blog/' ) ); ?>">View all posts</a>
		</div>
		<div class="blog-slider-wrap">
			<button type="button" class="blog-slider-btn blog-slider-prev" aria-label="Previous posts">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
			</button>
			<button type="button" class="blog-slider-btn blog-slider-next" aria-label="Next posts">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
			</button>
			<div class="blog-slider-track" id="blogSliderTrack">
				<?php
				foreach ( $blog_posts as $post ) {
					setup_postdata( $post );
					$thumb = get_the_post_thumbnail_url( $post->ID, 'road-aid-featured' );
					if ( ! $thumb ) {
						$thumb = get_the_post_thumbnail_url( $post->ID, 'large' );
					}
					$excerpt = get_the_excerpt( $post );
					$excerpt_text = $excerpt ? wp_trim_words( $excerpt, 15 ) : wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $post ) ), 15 );
					?>
					<article class="blog-slide card">
						<a href="<?php echo esc_url( get_permalink( $post ) ); ?>" class="blog-slide-link">
							<?php if ( $thumb ) : ?>
								<div class="blog-slide-image" style="background-image: url(<?php echo esc_url( $thumb ); ?>);"></div>
							<?php else : ?>
								<div class="blog-slide-image blog-slide-image-placeholder"></div>
							<?php endif; ?>
							<div class="blog-slide-content">
								<time class="blog-slide-date" datetime="<?php echo esc_attr( get_the_date( 'c', $post ) ); ?>"><?php echo esc_html( get_the_date( '', $post ) ); ?></time>
								<h3 class="blog-slide-title"><?php echo esc_html( get_the_title( $post ) ); ?></h3>
								<p class="blog-slide-excerpt"><?php echo esc_html( $excerpt_text ); ?></p>
								<span class="blog-slide-cta">Read more</span>
							</div>
						</a>
					</article>
					<?php
				}
				wp_reset_postdata();
				?>
			</div>
			<div class="blog-slider-dots" id="blogSliderDots" aria-hidden="true"></div>
		</div>
	</div>
</section>
