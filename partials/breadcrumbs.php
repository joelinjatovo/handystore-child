<?php
// ----- Plumtree Blog Breadcrumbs Function

	global $post;

	if ( !is_front_page() || !is_page_template( 'page-templates/front-page.php' ) ) : ?>
	<div class="breadcrumbs-wrapper col-md-12 col-sm-12 col-xs-12"><!-- Breadcrumbs-wrapper -->
		<div class="container">
			<div class="row">
					<div class="col-md-4 col-sm-6 col-xs-12">
						<?php if ( is_single() && handy_get_option('post_pagination')=='on' && !is_attachment() ) { ?>
							<nav class="navigation post-navigation"><!-- Post Nav -->
								<h1 class="screen-reader-text"><?php echo __( 'Post navigation', 'plumtree' ); ?></h1>
								<div class="nav-links">
									<?php previous_post_link( '%link', '<i class="fa fa-angle-left"></i>'. __( ' Previous Post', 'plumtree' ) ); ?>
									<?php next_post_link( '%link', __( 'Next Post ', 'plumtree' ) . '<i class="fa fa-angle-right"></i>' ); ?>
								</div>
							</nav><!-- end of Post Nav -->
						<?php } //end of post nav
							elseif ( is_single() && handy_get_option('post_pagination')=='on' && is_attachment() ) {
								$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
								$next     = get_adjacent_post( false, '', false );
								if ( ! $next && ! $previous ) {
									return false;
								} else { ?>
									<nav class="navigation post-navigation"><!-- Image Nav -->
										<h1 class="screen-reader-text"><?php echo __( 'Post navigation', 'plumtree' ); ?></h1>
										<div class="nav-links">
											<?php previous_image_link( '%link', '<i class="fa fa-angle-left"></i>'.__( ' Previous Image', 'plumtree' ) ); ?>
											<?php next_image_link( '%link', __( 'Next Image ', 'plumtree' ).'<i class="fa fa-angle-right"></i>' ); ?>
										</div>
									</nav><!-- end of Image Nav -->
								<?php }
							} // end of images nav
							 else {
								 pt_output_page_title();
							} ?>
						</div>
						<div class="col-md-8 col-sm-6 col-xs-12">
							<?php if ( function_exists('yoast_breadcrumb') ) {
									if ( !is_front_page() && !is_single() ) {
										yoast_breadcrumb('<p id="breadcrumbs" class="breadcrumbs">','</p>');
									} elseif ( is_single() && handy_get_option('post_breadcrumbs')=='on' ) {
										yoast_breadcrumb('<p id="breadcrumbs" class="breadcrumbs">','</p>');
									}
								} ?>
						</div>
	</div></div></div><!-- end of Breadcrumbs -->
<?php endif;
