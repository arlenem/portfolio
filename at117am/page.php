<?php
/**
 * The main template file
 *
 *
 * @package WordPress
 * @subpackage Arlene_Theme
 * @since Arlene Theme 1.0
 */

$p = get_post();
get_header('basic'); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

		<section>
			<article>
				<div>
					<h2><?php echo $p->post_title</h2>
					<?php echo $p->post_content; ?>
				</div>
			</article>
		</section>

	</main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>