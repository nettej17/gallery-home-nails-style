<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header">
	<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
	<?php $description = get_bloginfo( 'description', 'display' ); ?>
	<?php if ( $description ) : ?>
		<p class="site-description"><?php echo esc_html( $description ); ?></p>
	<?php endif; ?>
</header>
<main>
