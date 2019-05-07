<?php
/**
* PO_DashboardVendor
* Author: JOELINJATOVO
*/
class PO_DashboardVendor extends WP_Widget {

	// constructor
	public function __construct() {
		parent::__construct(
			'PO_DashboardVendor', // Base ID
			__('PO DashboardVendor', 'plumtree'), // Name
			array('description' => __( "Handy special widget. A sidebar menu.", 'plumtree' ), )
		);
	}

	public function form($instance) {
		$defaults = array(
			'title' => 'PO_DashboardVendor',
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
            $user_info = get_userdata($vendor_id);
            $name      = $user_info->display_name;
            
            $shop_name = $shop_url = '';
            if( class_exists('WCV_Vendors') ){
                $shop_name = \WCV_Vendors::get_vendor_sold_by($vendor_id);
                $shop_url  = \WCV_Vendors::get_vendor_shop_page( $vendor_id );
            }
            
            $message_count = 0;
            if ( function_exists('fep_query_url') ) :
                $result = $wpdb->get_row($wpdb->prepare("SELECT count(id) as count FROM ".FEP_MESSAGES_TABLE." WHERE (to_user = %d AND parent_id = 0 AND to_del = 0 AND status = 0 AND last_sender <> %d) OR (from_user = %d AND parent_id = 0 AND from_del = 0 AND status = 0 AND last_sender <> %d)", $vendor_id, $vendor_id, $vendor_id, $vendor_id));
                $message_count = (int) $result->count;
            endif;

            $wish_count = 0;
            if (defined( 'YITH_WCWL' )){
                $result = $wpdb->get_row($wpdb->prepare("SELECT count(ID) as count FROM {$wpdb->prefix}yith_wcwl WHERE user_id = %d ;", $vendor_id));
                $wish_count = (int) $result->count;
            }

            $product_count = 0;
            $results = array();
            $args = array(
                'posts_per_page'   => -1,
                'post_type'        => 'product',
                'author'	       => $vendor_id,
                'post_status'      => 'publish',
                'suppress_filters' => true 
            );
            $results = get_posts( $args );
            $product_count = count($results);
            
            echo $before_title;
            echo sprintf(__('Bonjour %s', 'plumtree'), $name);
            echo $after_title;
            
            echo '<ul class="nav-items">';
                if( class_exists('WCV_Vendors') && \WCV_Vendors::is_vendor( $vendor_id )){
                    echo '<li class="nav-item"><a class="button1" href="'.site_url('dashboard/product/edit').'"><i class="fa fa-plus" aria-hidden="true"></i> '.__( 'Ajouter un article', 'plumtree' ).'</a></li>';
                    if (class_exists('WooCommerce') ) :
                        echo '<li class="nav-item"><a class="button2" href="'.get_permalink(get_option('woocommerce_myaccount_page_id')).'"><i class="fa fa-user" aria-hidden="true"></i> '.__('Mon profil','plumtree').'</a></li>';
                    endif;
                    echo '<li class="nav-item"><a class="button4" href="'.$shop_url.'"><i class="fa fa-gift" aria-hidden="true"></i> '.__( 'Voir ma boutique', 'plumtree' ).'</a></li>';
                    echo '<li class="nav-item"><a class="button4" href="'.site_url('dashboard/product').'"><i class="fa fa-gift" aria-hidden="true"></i> '.__( 'Mes articles', 'plumtree' ).' ('.$product_count.')</a></li>';
                    echo '<li class="nav-item"><a class="button4" href="'.site_url('dashboard/settings').'"><i class="fa fa-cogs" aria-hidden="true"></i> '.__( 'Mes paramètres', 'plumtree' ).'</a></li>';
                }
                if ( function_exists('fep_query_url') ) :
                    echo '<li class="nav-item"><a class="button4" href="'. fep_query_url( 'messagebox' ) .'"><i class="fa fa-envelope-o" aria-hidden="true"></i> ' . sprintf(__('Voir mes messages %s', 'plumtree'), fep_get_new_message_button() ) . '</a></li>';
                endif;
                echo '<li class="nav-item"><a class="button4" href="'.site_url('wishlist').'"><i class="fa fa-heart" aria-hidden="true"></i> '.__( 'Mes favoris', 'plumtree' ).' ('.$wish_count.')</a></li>';
                echo '<li class="nav-item"><a class="button4" href="'.site_url('comment-ca-marche').'"><i class="fa fa-question-circle" aria-hidden="true"></i> '.__( 'Comment ça marche ?', 'plumtree' ).'</a></li>';
                echo '<li class="nav-item"><a class="logout button4" href="'.wp_logout_url(apply_filters('the_permalink', get_permalink())).'"><i class="fa fa-sign-out" aria-hidden="true"></i> '.__( 'Log out', 'handy-feature-pack' ).'</a></li>';
            echo '</ul>';
        }
        echo $after_widget;

    }
}