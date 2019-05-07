<?php
/**
* PO_TopUserMenu
* Author: JOELINJATOVO
*/
class PO_TopMenuUser extends \WP_Widget {

	public function __construct() {
		parent::__construct(
			'PO_TopMenuUser', // Base ID
			__('PO TopUserMenu', 'plumtree'), // Name
			array('description' => __( "Handy special widget. A menu with drop down submenu.", 'plumtree' ), )
		);
	}

	public function form($instance) {
		$defaults = array(
			'title' => 'Top Menu',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title: ', 'plumtree' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
	<?php
	}

	public function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	public function widget($args, $instance) {
		extract($args);

		$title = apply_filters('widget_title', $instance['title'] );
		$inline = ( isset($instance['inline']) ? $instance['inline'] : false );

		echo $before_widget;
		if ($title) { echo $before_title . $title . $after_title; }
		?>

		<?php if ( is_user_logged_in() ) { ?>
            <?php
            $vendor_id      = get_current_user_id();
            $store_icon_src = wp_get_attachment_image_src( get_user_meta( $vendor_id, '_wcv_store_icon_id', true ), array( 100, 100 ) );
            $store_icon 	= '';
            $shop_name   	= get_user_meta( $vendor_id, 'pv_shop_name', true );

            // see if the array is valid
            if ( is_array( $store_icon_src ) ) {
                $store_icon 	= '<img src="'.esc_url($store_icon_src[0]).'" alt="'.esc_attr($shop_name).'" class="store-icon" />';
            }
            
            ?>
            <div id="top-header">
                <ul>
                    <li class="add_product"><a class="login_button inline" href="<?php echo site_url('dashboard/product/edit'); ?>"><?php _e('Vends tes articles', 'plumtree'); ?></a></li>
                    <?php if (function_exists('fep_query_url')) : ?>
                        <?php echo '<li class="fep_notification"><a class="notification" href="'. fep_query_url( 'messagebox' ) .'"><i class="fa fa-envelope-o" aria-hidden="true" style="font-size: 20px;"></i> ' . fep_get_new_message_button() . '</a></li>'; ?>
                    <?php endif; ?>
                    <li class="dropdown">
                        <a class="dropbtn">
                            <?php 
                                 if ($store_icon) { 
                                     echo $store_icon; 
                                 } else { 
                                    $avatar = get_avatar( $vendor_id, 70 );
                                     if($avatar){
                                         echo $avatar;
                                     }else{
                                         echo '<img width="269" height="199" style="width:30px;" src="'.site_url('/wp-content/themes/handystore-child/assets/img/vendor.png').'" alt="'.esc_attr($shop_name).'" class="store-icon" />'; 
                                     }
                                 } 
                            ?>
                            <span>
                                <?php 
                                    if($shop_name){
                                        echo $shop_name;
                                    }else{
                                        _e('MON PROFIL', 'ksk');
                                    }
                                ?>
                            </span>
                            <i id="header-down-arrow" class="fa fa-sort-desc"></i>
                        </a>
                        <div class="dropdown-content">
                            <?php if( class_exists('WCV_Vendors') && \WCV_Vendors::is_vendor( $vendor_id )){ ?>
                                <a class="link" href="<?php echo site_url('dashboard/product/edit'); ?>"> <?php echo __( 'Ajouter un article', 'plumtree' ); ?></a>
                                <?php if ( class_exists('WooCommerce') ) : ?>
                                    <a class="link" href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('Mon Profil', 'plumtree'); ?>"><?php _e('Mon Profil', 'plumtree'); ?></a>
                                <?php endif; ?>
                                <a class="link" href="<?php echo site_url('dashboard/settings'); ?>"><?php _e('Mes Paramètres', 'plumtree'); ?></a>
                                <a class="link" href="<?php echo WCV_Vendors::get_vendor_shop_page($vendor_id); ?>"><?php _e('Voir ma boutique', 'plumtree'); ?></a>
                                <a class="link" href="<?php echo site_url('dashboard/product'); ?>"><?php _e('Mes articles', 'plumtree'); ?></a>
                            <?php } ?>
                            <a class="link" href="<?php echo site_url('wishlist'); ?>"><?php _e('Mes favoris', 'plumtree'); ?></a>
                            <a class="link" href="<?php echo site_url('comment-ca-marche'); ?>"><?php _e('Comment ça marche ?', 'plumtree'); ?></a>
                            <a class="link" href="<?php echo wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ); ?>" title="<?php _e('Log out of this account', 'handy-feature-pack');?>" style="color: #F03E53;"><?php _e('Log out', 'handy-feature-pack');?></a>
                        </div>
                    </li>
                </ul>
        <?php } else { ?>
			<form id="login" class="ajax-auth" method="post">
				<h3><?php _e('New to site? ', 'handy-feature-pack');?><a id="pop_signup" href=""><?php _e('Create an Account', 'handy-feature-pack');?></a></h3>
				<h1><?php _e('Login', 'handy-feature-pack');?></h1>
				<p class="status"></p>
				<?php wp_nonce_field('ajax-login-nonce', 'security'); ?>
				<p>
					<label for="username"><?php _e('Username', 'handy-feature-pack');?><span class="required">*</span></label>
					<input id="username" type="text" class="required" name="username" required aria-required="true">
				</p>
				<p>
					<label for="password"><?php _e('Password', 'handy-feature-pack');?><span class="required">*</span></label>
					<input id="password" type="password" class="required" name="password" required aria-required="true">
				</p>
                <div style="width: 100%">
                    <input class="submit_button" type="submit" value="<?php esc_html_e('Login', 'handy-feature-pack'); ?>">
                    <a class="text-link" href="<?php echo wp_lostpassword_url(); ?>"><?php _e('Lost password?', 'handy-feature-pack');?></a>
                    <a class="close" href=""><?php _e('(close)', 'handy-feature-pack');?></a>
                </div>
                <div style="clearfix: both;"></div>
                <br>
                <br>
                <br>
                <div style="width: 100%; text-align: center; padding-left: 10px;">
                    <?php 
                    if ( function_exists('oa_social_login_render_login_form') ) {
                        echo '<div class="social-networks-login">';
                        do_action('oa_social_login');
                        echo '</div>';
                    }else{
                        echo do_shortcode('[nextend_social_login provider="facebook"]');
                    } 
                    ?>
                </div>
			</form>

			<form id="register" class="ajax-auth" method="post">
				<h3><?php _e('Already have an account? ', 'handy-feature-pack');?><a id="pop_login"  href=""><?php _e('Login', 'handy-feature-pack');?></a></h3>
				<h1><?php _e('Signup', 'handy-feature-pack');?></h1>
				<p class="status"></p>
				<?php wp_nonce_field('ajax-register-nonce', 'signonsecurity'); ?>
				<p>
					<label for="signonname"><?php _e('Username', 'handy-feature-pack');?><span class="required">*</span></label>
					<input id="signonname" type="text" name="signonname" class="required" required aria-required="true" pattern="<?php echo apply_filters('register_form_username_pattern', '[a-zA-Z0-9 ]+'); ?>" title="<?php esc_html_e('Digits and Letters only.', 'handy-feature-pack'); ?>">
				</p>
				<p>
					<label for="email"><?php _e('Email', 'handy-feature-pack');?><span class="required">*</span></label>
					<input id="email" type="text" class="required email" name="email" required aria-required="true">
				</p>
				<p>
					<label for="signonpassword"><?php _e('Password', 'handy-feature-pack');?><span class="required">*</span></label>
					<input id="signonpassword" type="password" class="required" name="signonpassword" required aria-required="true">
				</p>
				<?php // Apply to become a vendor
				if ( class_exists('WC_Vendors') && class_exists( 'WooCommerce' ) ) :
					if ( WC_Vendors::$pv_options->get_option( 'show_vendor_registration' ) ) :
						$terms_page = WC_Vendors::$pv_options->get_option( 'terms_to_apply_page' ); ?>
						<input class="input-checkbox" id="apply_for_vendor_widget" <?php checked( isset( $_POST[ 'apply_for_vendor_widget' ] ), true ) ?> type="checkbox" name="apply_for_vendor_widget" value="1"/>
						<label for="apply_for_vendor_widget" class="checkbox-label">
							<?php echo apply_filters('wcvendors_vendor_registration_checkbox', __( 'Apply to become a vendor? ', 'handy-feature-pack' )); ?>
						</label>
					<?php if ( $terms_page && $terms_page !='' ) : ?>
						<p class="agree-to-terms" style="display:none">
							<input class="input-checkbox" id="agree_to_terms_widget" <?php checked( isset( $_POST[ 'agree_to_terms_widget' ] ), true ) ?> type="checkbox" name="agree_to_terms_widget" value="1"/>
							<label for="agree_to_terms_widget" class="checkbox-label">
								<?php printf( __( 'I have read and accepted the <a target="_blank" href="%s">terms and conditions</a>', 'handy-feature-pack' ), get_permalink( $terms_page ) ); ?>
							</label>
						</p>
					<?php  endif; ?>
						<script type="text/javascript">
							jQuery(function () {
								if (jQuery('#apply_for_vendor_widget').is(':checked')) {
									jQuery('.agree-to-terms').show();
								}
								jQuery('#apply_for_vendor_widget').on('click', function () {
									jQuery('.agree-to-terms').slideToggle();
								});
							})
						</script>
					<?php endif; ?>
				<?php endif; ?>
				<input class="submit_button" type="submit" value="<?php esc_html_e('Register', 'handy-feature-pack'); ?>">
				<a class="close" href="#"><?php _e('(close)', 'handy-feature-pack');?></a>
                
				<?php 
                    if ( function_exists('oa_social_login_render_login_form') ) {
                        echo '<div class="social-networks-login">';
                        do_action('oa_social_login');
                        echo '</div>';
                    }else{
                        echo do_shortcode('[nextend_social_login provider="facebook"]');
                    } 
                ?>
			</form>

            <a class="login_button inline" id="show_login" href=""><i class="fa fa-user"></i><?php _e('Login', 'handy-feature-pack'); ?></a>
            <a class="login_button inline" id="show_signup" href=""><i class="fa fa-pencil"></i><?php _e('Register', 'handy-feature-pack'); ?></a>
            
        <?php } ?>

		<?php
		echo $after_widget;
	}

}