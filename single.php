<?php
get_header();
?>
<article class="single">
	<?php while ( have_posts() ) : the_post(); ?>
		<h1 class="entry-title"><?php the_title(); ?></h1>
		<p class="entry-date"><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></p>
		<div class="entry-content"><?php the_content(); ?></div>
		<nav class="post-nav" aria-label="<?php esc_attr_e( 'Posts', 'gallery-home-nails-style' ); ?>">
			<span><?php previous_post_link( '%link', __( 'Older', 'gallery-home-nails-style' ) ); ?></span>
			<span><?php next_post_link( '%link', __( 'Newer', 'gallery-home-nails-style' ) ); ?></span>
		</nav>
	<?php endwhile; ?>
</article>
<?php
get_footer();
