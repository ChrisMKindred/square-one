<?php get_header(); ?>

	<main class="page-content" role="main">

		<?php // Content: Sub-header
		get_template_part( 'content/header/sub' ); ?>

		<?php // If we have content, output our content block (for panels and odd spacing)
		if( get_the_content() !== '' ) { ?>

			<div class="content-wrap">

				<?php // Content: Page
				get_template_part( 'content/page/default' ); ?>

			</div><!-- .content-wrap -->

		<?php } ?>

		<?php // Panels
		if( function_exists( 'have_panels' ) && have_panels() )
		do_action( 'the_panels' ); ?>

	</main><!-- main -->

	<?php get_sidebar(); ?>	

<?php get_footer(); ?>