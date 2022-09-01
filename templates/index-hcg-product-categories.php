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
<?php
/*
Template Name: Product Categories Index
*/
?>
<?php get_header(); ?>

	<div id="primary" class="content-area">
		<main id="content" class="site-content" role="main">

			<header class="archive-header">
				<h1 class="archive-title">Products</h1>
			</header><!-- .archive-header -->

<?php $product_categories = get_terms('hcg-product-categories'); ?>

<div class="entry-content">
<ul>
<?php foreach ($product_categories as $category): ?>

	<?php $link = get_term_link(intval($category->term_id), 'hcg-product-categories'); ?>

	<?php if ($category->parent == 0): ?>
		<li><a href="<?=$link;?>"><?=$category->name;?></a></li>
		
		<?php $has_children = FALSE; ?>
		<?php foreach ($product_categories as $cats): ?>
			<?php if ($cats->parent == $category->term_id): ?>
				<?php $sublink = get_term_link(intval($cats->term_id), 'hcg-product-categories'); ?>
				<?php if ( ! $has_children): ?>
					<ul>
					<?php $has_children = TRUE; ?>
				<?php endif; ?>
				<li><a href="<?=$sublink;?>"><?=$cats->name;?></a></li>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php if ($has_children): ?>
		</ul>
		<?php endif; ?>
	<?php endif; ?>

<?php endforeach; ?>
</div>

		</main><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>