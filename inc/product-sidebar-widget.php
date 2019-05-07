<?php
/**
* PO_ProductSidebar
* Author: JOELINJATOVO
*/
class PO_ProductSidebar extends WP_Widget {

	// constructor
	public function __construct() {
		parent::__construct(
			'PO_ProductSidebar', // Base ID
			__('PO ProductSidebar', 'plumtree'), // Name
			array('description' => __( "Handy special widget. A sidebar menu.", 'plumtree' ), )
		);
	}

	public function form($instance) {
		$defaults = array(
			'title' => 'PO_ProductSidebar',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title: ', 'plumtree' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
	<?php
	}

	public  function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

    public function widget($args, $instance) {
        global $wpdb;
        extract($args);
        
        // these are the widget options
        $title = apply_filters('widget_title', $instance['title']);
        
        echo $before_widget;
        
        if(!is_user_logged_in()){
            // not logged
        }else{
            // logged
            $vendor_id = get_current_user_id();
            $shop_name = \WCV_Vendors::get_vendor_sold_by($vendor_id);
            $shop_url  = WCV_Vendors::get_vendor_shop_page( $vendor_id );
            
            global $post;
            global $product;
            
            if( \WCV_Vendors::is_vendor( $vendor_id ) && ($post->post_author == $vendor_id) ){
                echo '<ul class="nav-items">';
                    echo '<li class="nav-item">';
                        echo '<form action="'.site_url('product-sold/'.$post->ID).'" method="post">';
                            if ( $product->is_in_stock() ) {
                                echo'<input type="submit" class="button4" value="'.__( 'Marquer comme vendu', 'plumtree' ).'" />';
                                echo '<input type="hidden" name="stock_status" value="outofstock">';
                            }else{
                                echo'<input type="submit" class="button4" value="'.__( 'Marquer comme disponible', 'plumtree' ).'" />';
                                echo '<input type="hidden" name="stock_status" value="instock">';
                            }
                            echo '<input type="hidden" name="post_id" value="'.$product->get_id().'">';
                            echo '<input type="hidden" name="action" value="save_product_as_sold">';
                            wp_nonce_field( '_po-save_product_sold', '_po-save_product_sold' );
                        echo '</form>';
                    echo '</li>';
                    
                    echo '<li class="nav-item"><a class="button4" href="'.WCVendors_Pro_Dashboard::get_dashboard_page_url('product/edit/'.$post->ID.'/').'"><i class="fa fa-pencil" aria-hidden="true"></i> '.__( 'Modifier l\'article', 'plumtree' ).'</a></li>';
                    echo '<li class="nav-item"><a class="button4 confirm_delete_widget"  data-confirm_text="'.__( 'Delete product?', 'wcvendors-pro').'" href="'.WCVendors_Pro_Dashboard::get_dashboard_page_url('product/delete/'.$post->ID.'/').'"><i class="fa fa-trash" aria-hidden="true"></i> '.__( 'Supprimer l\'article', 'plumtree' ).'</a></li>';
                echo '</ul>';
            }
            
        }
        echo $after_widget;
        
?>
<script type="text/javascript">
jQuery(function($){
    $('.confirm_delete_widget').on( 'click', function(e) { 
        if ( ! confirm( $( this ).data('confirm_text') ) ) e.preventDefault(); 
    }); 
}); 
</script>
<?php

    }
}