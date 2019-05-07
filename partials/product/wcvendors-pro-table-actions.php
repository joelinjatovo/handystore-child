<?php

/**
 * Product Table Main Actions
 *
 * This file is used to add the table actions before and after a table
 *
 * @link       http://www.wcvendors.com
 * @since      1.2.4
 * @version    1.4.4
 *
 * @package    WCVendors_Pro
 * @subpackage WCVendors_Pro/public/partials/product
 */

?>

<div class="wcv_dashboard_table_header wcv-cols-group wcv-search">

	<div class="all-50 small-100">
		<?php if ( strlen( $search ) > 0 ) : ?>
			<span class="wcv_search_results"><?php printf( __( 'Search results for "%s" ...', 'wcvendors-pro' ), $search ); ?></span>
		<?php endif; ?>
	</div>

	<div class="all-50 small-100" style="float:right;">
		<form class="wcv-search-form" method="get">
			<div class="column-group horizontal-gutters">
				<div class="control-group" style="float:right;">
		            <div class="control append-button" role="search">
		                <span><input style="width: 150px;" type="text" name="wcv-search" id="wcv-search" value="<?php echo $search; ?>" placeholder="<?php echo __( 'Recherche', 'pulmtree' ); ?>"></span>
		                <button class="wcv-button" title="<?php echo __( 'Search', 'wcvendors-pro' ); ?>"><i class="fa fa-search"></i></button>
		            </div>
				</div>
			</div>
		</form>
	</div>
</div>

<?php if ( ! $lock_new_products ) : ?>

<div class="wcv_actions wcv-cols-group" style="margin-top: 10px;">
	<div class="all-50 small-100">
	<?php foreach ( $template_overrides as $key => $template_data ) : ?>
		<a href="<?php echo $template_data[ 'url' ]; ?>" class="wcv-button button"><?php echo sprintf( __( 'Add %s ', 'wcvendors-pro' ), $template_data[ 'label' ] );  ?></a>
        <?php break; ?>
	<?php endforeach; ?>
	</div>

	<div class="all-50 small-100" style="float:right">
			<?php
				echo $pagination_wrapper[ 'wrapper_start' ];
				echo paginate_links( apply_filters( 'wcv_product_pagination_args', array(
					    'base' 			=> add_query_arg( 'paged', '%#%' ),
					    'format' 		=> '',
					    'current' 		=> ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1,
					    'total' 		=> $this->max_num_pages,
					    'prev_next'    	=> true,
					    'type'         	=> 'list',
						), ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1, $this->max_num_pages
					) );
				echo $pagination_wrapper[ 'wrapper_end' ];
			?>

	</div>
</div>

<?php endif; ?>
