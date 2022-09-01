<?php
/**
 ** WARNING! DO NOT EDIT!
 **
 ** These templates are part of the core hcg-products files
 ** and will be overwritten when upgrading hcg-products.
 **
 ** This template was automatically copied into your site's active
 ** theme directory (in the hcg-products/ subdirectory) when the 
 ** plugin was activated. Please edit that copy of the template.
 **
 **/
?>

<?php get_header(); ?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

		<?php if ( have_posts() ) : ?>
			<header class="archive-header">
				<h1 class="archive-title"><?php $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); echo $term->name; ?></h1>
			</header><!-- .archive-header -->

			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<header class="entry-header">
					<?php the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h1>' ); ?>
					<div class="entry-meta">
						<?php edit_post_link( __( 'Edit', 'twentyfourteen' ), '<span class="edit-link">', '</span>' ); ?>
					</div>
				</header>
				<div class="entry-content">
					<?php the_content(); ?>

					<p><?=get_post_meta($post->ID, 'teaser', TRUE);?></p>

					<p><?=get_post_meta($post->ID, 'long_description', TRUE);?></p>

				</div>
				
			<?php endwhile; ?>

		<?php else : ?>
		    <p>No content found.</p>
		<?php endif; ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>