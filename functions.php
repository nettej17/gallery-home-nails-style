<?php
/**
 * Gallery Home Nails Style: theme setup and home grid.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gallery_home_setup() {
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
}
add_action( 'after_setup_theme', 'gallery_home_setup' );

function gallery_home_assets() {
	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	wp_enqueue_style(
		'gallery-home-nails-style',
		get_stylesheet_uri(),
		array(),
		filemtime( $dir . '/style.css' )
	);

	wp_enqueue_script(
		'gallery-home-strips',
		$uri . '/assets/strips.js',
		array(),
		filemtime( $dir . '/assets/strips.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'gallery_home_assets' );

// Show 18 posts per page on the home page.
function gallery_home_query_size( $query ) {
	if ( ! is_admin() && $query->is_main_query() && ( $query->is_home() || $query->is_front_page() ) ) {
		$query->set( 'posts_per_page', 18 );
	}
}
add_action( 'pre_get_posts', 'gallery_home_query_size' );

/**
 * Collects the featured image plus every <img> in the post body, without duplicates.
 */
function gallery_home_images( $post_id ) {
	$images = array();
	$seen   = array();

	if ( has_post_thumbnail( $post_id ) ) {
		$thumb = get_the_post_thumbnail( $post_id, 'large', array( 'loading' => 'lazy' ) );
		if ( $thumb ) {
			if ( preg_match( '/src=["\']([^"\']+)["\']/i', $thumb, $s ) ) {
				$seen[] = $s[1];
			}
			$images[] = $thumb;
		}
	}

	$content = get_post_field( 'post_content', $post_id );
	if ( preg_match_all( '/<img[^>]+>/i', $content, $matches ) ) {
		foreach ( $matches[0] as $tag ) {
			if ( preg_match( '/src=["\']([^"\']+)["\']/i', $tag, $s ) ) {
				if ( in_array( $s[1], $seen, true ) ) {
					continue;
				}
				$seen[] = $s[1];
			}
			$images[] = $tag;
		}
	}

	return $images;
}

/**
 * Renders the home grid for the current query.
 */
function gallery_home_render() {
	if ( ! have_posts() ) {
		echo '<p class="has-text-align-center">' . esc_html__( 'No posts yet.', 'gallery-home-nails-style' ) . '</p>';
		return;
	}

	echo '<div class="gallery">';
	while ( have_posts() ) {
		the_post();
		$url    = esc_url( get_permalink() );
		$title  = esc_html( get_the_title() );
		$images = gallery_home_images( get_the_ID() );

		echo '<article class="card">';
		if ( $images ) {
			echo '<div class="tile"><div class="strip">';
			foreach ( $images as $img ) {
				printf(
					'<a class="slide" href="%1$s" aria-label="%2$s">%3$s</a>',
					$url,
					$title,
					$img // Image markup from the WordPress attachment or post content.
				);
			}
			echo '</div></div>';
			printf( '<a class="card-title" href="%1$s">%2$s</a>', $url, $title );
		} else {
			// No images at all: the title fills the square tile instead.
			printf( '<a class="tile tile-text" href="%1$s">%2$s</a>', $url, $title );
		}
		echo '</article>';
	}
	echo '</div>';

	the_posts_pagination(
		array(
			'mid_size'  => 1,
			'prev_text' => __( 'Newer', 'gallery-home-nails-style' ),
			'next_text' => __( 'Older', 'gallery-home-nails-style' ),
		)
	);
}
