<?php
/**
* Extra files & functions are hooked here.
*
* Displays all of the head element and everything up until the "site-content" div.
*
* @package HandyStore Child
* @subpackage Core
* @since 1.0
*/

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style('child-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style('dropzone-style', get_theme_root_uri() . '/handystore-child/assets/css/dropzone.css' );
    wp_enqueue_style('dropzone-basic-style', get_theme_root_uri() . '/handystore-child/assets/css/basic.css' );
}

add_action( 'wp_enqueue_scripts', 'passion_enqueue_scripts' );
function passion_enqueue_scripts() {
    global $wp;
    
    $uri = trim($wp->request, '/');
    if(strpos($uri, 'dashboard/product/edit')!==false){
        wp_enqueue_script('dropzone',get_theme_root_uri() . '/handystore-child/assets/js/dropzone.js', array('jquery'));
        wp_enqueue_script('my-script',get_theme_root_uri() . '/handystore-child/assets/js/script.js',array('jquery','dropzone'));
    }
    $drop_param = array(
      'upload'              => admin_url( 'admin-ajax.php?action=handle_dropped_media' ),
      'delete'              => admin_url( 'admin-ajax.php?action=handle_deleted_media' ),
      'jsonError'  => __( 'Reponse invalide.', 'pulmtree'),
      'dictMaxFilesExceeded'  => __( 'Vous ne pouvez plus télécharger de fichiers.', 'pulmtree'),
      'dictDefaultMessage'  => __( 'Add Images to Product Gallery', 'wcvendors-pro'),
      'dictFileTooBig'  => __( 'Cette image est trop grande. Nous n\'autorisons que 2Mo ou moins.', 'pulmtree'),
      'dictInvalidFileType' => __( 'Ce type de fichier est invalide', 'pulmtree'),
      'dictRemoveFile'      => __( 'Supprimer', 'pulmtree'),
      'dictRemoveFileConfirmation' => __( 'Voulez-vous vraiment supprimer le fichier?', 'pulmtree'),
      'dictCancelUpload'    => __( 'Annuler', 'pulmtree'),
      'dictUploadCanceled'  => __( 'Téléversement annulé', 'pulmtree'),
      'dictCancelUploadConfirmation'  => __( 'Téléversement annulé', 'pulmtree'),
      'dictCancelUploadConfirmation'  => __( "Voulez-vous vraiment annuler le Téléversement?", 'pulmtree'),
    );
    wp_localize_script('my-script','dropParam', $drop_param);
}

include('inc/top-header.php');
include('inc/account-sidebar.php');
include('inc/product-sidebar-widget.php');

/**
 * Capture user login and add it as timestamp in user meta data
 */
function passion_user_last_login( $user_login, $user ) {
    update_user_meta($user->ID, 'last_login', time() );
}
add_action('wp_login', 'passion_user_last_login', 10, 2 );

function passion_vendor_box_shortcode_2($atts){
    $defaults = [
        'button'     => true,  
        'email'      => 0,  
        'phone'      => true,  
        'last_visit' => true,  
    ];
    $atts = shortcode_atts($defaults, $atts, 'passion_vendor_box_2');
    extract($atts);
    
    
    if(class_exists('WCV_Vendors')){
        global $post;
        
        ob_start();
        echo '<div class="vendor-container">';
            echo '<div>';
                echo '<div class="passion_vendor_box" style="position: relative; padding-bottom: 90px; min-height: 135px;">';
                $vendor_id = \WCV_Vendors::get_vendor_from_product($post->ID);
                if(\WCV_Vendors::is_vendor( $vendor_id )){
                    $store_icon_src = wp_get_attachment_image_src( get_user_meta( $vendor_id, '_wcv_store_icon_id', true ), 'pt-vendor-main-logo' );
                    $store_icon     = '';
                    $user_data      = get_userdata($vendor_id);
                    $store_name     = get_user_meta( $vendor_id, 'pv_shop_name', true );
                    $last_login     = get_user_meta($vendor_id, 'last_login', true);
                    $phone_number   = get_user_meta($vendor_id, '_wcv_store_phone', true);
                    $the_login_date = human_time_diff($last_login);

                    // Get Vendor address
                    $address1 	    = get_user_meta($vendor_id, '_wcv_store_address1', true );
                    $city 			= get_user_meta($vendor_id, '_wcv_store_city', true );
                    $state 			= get_user_meta($vendor_id, '_wcv_store_state', true );
                    $store_postcode = get_user_meta($vendor_id, '_wcv_store_postcode', true );
                    $address 		= ( $address1 != '') ? $address1 .', ' . $city .', '. $state .', '. $store_postcode : '';
                    $address_labels = [];
                    if($address1 != ''){
                        $address_labels[] = $address1;
                    }
                    if($city != ''){
                        $address_labels[] = $city;
                    }
                    if($state != ''){
                        $address_labels[] = $state;
                    }
                    if($store_postcode != ''){
                        $address_labels[] = $store_postcode;
                    }
                    $address_label = implode($address_labels, ', ');

                    // Get Vendor socials
                    $twitter_username 	= get_user_meta( $vendor_id , '_wcv_twitter_username', true );
                    $instagram_username = get_user_meta( $vendor_id , '_wcv_instagram_username', true );
                    $facebook_url 		= get_user_meta( $vendor_id , '_wcv_facebook_url', true );
                    $linkedin_url       = get_user_meta( $vendor_id , '_wcv_linkedin_url', true );
                    $youtube_url 	    = get_user_meta( $vendor_id , '_wcv_youtube_url', true );
                    $googleplus_url	    = get_user_meta( $vendor_id , '_wcv_googleplus_url', true );
                    $pinterest_url 	    = get_user_meta( $vendor_id , '_wcv_pinterest_url', true );

                    // Social list
                    $social_icons_list = '<ul class="social-icons" style="margin-bottom: 10px;text-align: center;">';
                    if ( $facebook_url != '') { $social_icons_list .= '<li><a href="'.$facebook_url.'" target="_blank"><i class="fa fa-facebook"></i></a></li>'; }
                    if ( $instagram_username != '') { $social_icons_list .= '<li><a href="//instagram.com/'.$instagram_username.'" target="_blank"><i class="fa fa-instagram"></i></a></li>'; }
                    if ( $twitter_username != '') { $social_icons_list .= '<li><a href="//twitter.com/'.$twitter_username.'" target="_blank"><i class="fa fa-twitter"></i></a></li>'; }
                    if ( $googleplus_url != '') { $social_icons_list .= '<li><a href="'.$googleplus_url.'" target="_blank"><i class="fa fa-google-plus"></i></a></li>'; }
                    if ( $youtube_url != '') { $social_icons_list .= '<li><a href="'.$youtube_url.'" target="_blank"><i class="fa fa-youtube"></i></a></li>'; }
                    if ( $linkedin_url != '') { $social_icons_list .= '<li><a href="'.$linkedin_url.'" target="_blank"><i class="fa fa-linkedin"></i></a></li>'; }
                    $social_icons_list .= '</ul>';
                    $social_icons = empty( $twitter_username ) && empty( $instagram_username ) && empty( $facebook_url ) && empty( $linkedin_url ) && empty( $youtube_url ) && empty( $googleplus_url ) && empty( $pinterst_url ) ? false : true;

                    echo '<div class="passion_vendor-image">';
                        // see if the array is valid
                        if ( is_array( $store_icon_src ) ) {
                            echo '<a href="'.WCV_Vendors::get_vendor_shop_page( $vendor_id ).'">';
                                echo '<img src="'. esc_url($store_icon_src[0]) .'" alt="'. esc_attr($store_name) .'" class="store-icon" />';
                            echo '</a>';
                        }else{
                            echo get_avatar($vendor_id);
                        }
                    echo '</div>';

                }else{
                    echo get_bloginfo( 'name' );
                }

                echo '<div class="vendor-message-seller-container">';
                  if(\WCV_Vendors::is_vendor( $vendor_id )){
                      echo '<div class="vendor-name">';
                            printf('<a href="%s"><h3>%s</h3></a>', WCV_Vendors::get_vendor_shop_page( $vendor_id ), WCV_Vendors::get_vendor_sold_by( $vendor_id ) );
                            do_action( 'wcv_before_vendor_store_rating' );
                            echo '<div class="rating-container">';
                            if ( !WCVendors_Pro::get_option( 'ratings_management_cap' )) echo WCVendors_Pro_Ratings_Controller::ratings_link( $vendor_id, true );
                            echo '</div>';
                            do_action( 'wcv_after_vendor_store_rating' );
                            echo '<div class="vendor-desc">';
                                if($last_visit==true){
                                    printf(__('Dernière visite : %s', 'pulmtree'), $the_login_date);
                                }

                                if($address==true){
                                    echo '<br><i class="fa fa-map-marker" aria-hidden="true"></i> '.$address_label;
                                }

                                if($email==true){
                                    echo '<br><a href="mailto:'.$user_data->user_email.'"><i class="fa fa-envelope-o" aria-hidden="true"></i> '.$user_data->user_email.'</a>';
                                }

                                if(!empty($phone_number)){
                                    echo '<br><a href="tel:'.$phone_number.'"><i class="fa fa-phone" aria-hidden="true"></i> '.$phone_number.'</a>';
                                }
                            echo '</div>';
                      echo '</div>';
                  }
                echo '</div>';

                echo '<div id="po-vendor-message-seller-2" class="po-vendor-message-seller-form">';
                    echo do_shortcode('[fep_shortcode_new_message_form to="{current-post-author}" subject="{current-post-title}"]');
                echo '</div>';
                ?>

                <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    "use strict";

                    // Close popup
                    $(document).on('click', '.messenger_overlay, .close', function (e) {
                            $('#po-vendor-message-seller-2').fadeOut(300);
                            $('.messenger_overlay').css('opacity', '0');
                            $('.messenger_overlay').remove();
                            e.preventDefault();
                    });

                    // Show the login/signup popup on click
                    $('.po-message-seller').on('click', function (e) {
                        var overlay = '<div class="messenger_overlay"></div>';
                        $('body').prepend($(overlay).css('opacity', '0.5'));
                        $('#po-vendor-message-seller-2').fadeIn(300);
                        e.preventDefault();
                    });
                });
                </script>

            <?php
                echo '</div>'; // END .passion_vendor_box
                echo '<div class="vendor-buttons">';
                    echo '<a class="button po-message-seller" id="po-message-seller" href="#" rel="nofollow">';
                        echo '<i class="fa fa-envelope-o" aria-hidden="true"></i>Contacter le vendeur';
                    echo '</a>';
                echo '</div>'; // END .vendor-buttons
            echo '</div>';
        echo '</div>'; // END .vendor-container
        
        $ret_str=ob_get_contents();
        ob_end_clean();
        
        return $ret_str;
    }
    
    return "";
}
add_shortcode('passion_vendor_box_2', 'passion_vendor_box_shortcode_2');

function passion_vendor_box_shortcode($atts){
    $defaults = [
        'button'     => true,  
        'email'      => 0,  
        'phone'      => true,  
        'last_visit' => true,  
    ];
    $atts = shortcode_atts($defaults, $atts, 'passion_vendor_box');
    extract($atts);
    
    
    if(class_exists('WCV_Vendors')){
        global $post;
        
        ob_start();
        echo '<div class="passion_vendor_box" style="position: relative; padding-bottom: 90px;">';
        $vendor_id = \WCV_Vendors::get_vendor_from_product($post->ID);
        if(\WCV_Vendors::is_vendor( $vendor_id )){
            $store_icon_src = wp_get_attachment_image_src( get_user_meta( $vendor_id, '_wcv_store_icon_id', true ), 'pt-vendor-main-logo' );
            $store_icon     = '';
            $user_data      = get_userdata($vendor_id);
			$store_name     = get_user_meta( $vendor_id, 'pv_shop_name', true );
            $last_login     = get_user_meta($vendor_id, 'last_login', true);
            $phone_number   = get_user_meta($vendor_id, '_wcv_store_phone', true);
            $the_login_date = human_time_diff($last_login);
            
            // Get Vendor address
            $address1 	    = get_user_meta($vendor_id, '_wcv_store_address1', true );
            $city 			= get_user_meta($vendor_id, '_wcv_store_city', true );
            $state 			= get_user_meta($vendor_id, '_wcv_store_state', true );
            $store_postcode = get_user_meta($vendor_id, '_wcv_store_postcode', true );
            $address 		= ( $address1 != '') ? $address1 .', ' . $city .', '. $state .', '. $store_postcode : '';
            $address_labels = [];
            if($address1 != ''){
                $address_labels[] = $address1;
            }
            if($city != ''){
                $address_labels[] = $city;
            }
            if($state != ''){
                $address_labels[] = $state;
            }
            if($store_postcode != ''){
                $address_labels[] = $store_postcode;
            }
            $address_label = implode($address_labels, ', ');

            // Get Vendor socials
            $twitter_username 	= get_user_meta( $vendor_id , '_wcv_twitter_username', true );
            $instagram_username = get_user_meta( $vendor_id , '_wcv_instagram_username', true );
            $facebook_url 		= get_user_meta( $vendor_id , '_wcv_facebook_url', true );
            $linkedin_url       = get_user_meta( $vendor_id , '_wcv_linkedin_url', true );
            $youtube_url 	    = get_user_meta( $vendor_id , '_wcv_youtube_url', true );
            $googleplus_url	    = get_user_meta( $vendor_id , '_wcv_googleplus_url', true );
            $pinterest_url 	    = get_user_meta( $vendor_id , '_wcv_pinterest_url', true );
            
            // Social list
            $social_icons_list = '<ul class="social-icons" style="margin-bottom: 10px;text-align: center;">';
            if ( $facebook_url != '') { $social_icons_list .= '<li><a href="'.$facebook_url.'" target="_blank"><i class="fa fa-facebook"></i></a></li>'; }
            if ( $instagram_username != '') { $social_icons_list .= '<li><a href="//instagram.com/'.$instagram_username.'" target="_blank"><i class="fa fa-instagram"></i></a></li>'; }
            if ( $twitter_username != '') { $social_icons_list .= '<li><a href="//twitter.com/'.$twitter_username.'" target="_blank"><i class="fa fa-twitter"></i></a></li>'; }
            if ( $googleplus_url != '') { $social_icons_list .= '<li><a href="'.$googleplus_url.'" target="_blank"><i class="fa fa-google-plus"></i></a></li>'; }
            if ( $youtube_url != '') { $social_icons_list .= '<li><a href="'.$youtube_url.'" target="_blank"><i class="fa fa-youtube"></i></a></li>'; }
            if ( $linkedin_url != '') { $social_icons_list .= '<li><a href="'.$linkedin_url.'" target="_blank"><i class="fa fa-linkedin"></i></a></li>'; }
            $social_icons_list .= '</ul>';
            $social_icons = empty( $twitter_username ) && empty( $instagram_username ) && empty( $facebook_url ) && empty( $linkedin_url ) && empty( $youtube_url ) && empty( $googleplus_url ) && empty( $pinterst_url ) ? false : true;

            echo '<div class="passion_vendor-image">';
                // see if the array is valid
                if ( is_array( $store_icon_src ) ) {
                    echo '<img src="'. esc_url($store_icon_src[0]) .'" alt="'. esc_attr($store_name) .'" class="store-icon" />';
                }else{
                    echo get_avatar($vendor_id);
                    /*echo '<img src="'.site_url('/wp-content/themes/handystore-child/assets/img/vendor-logo.png').'" class="store-icon" />';*/
                }
            echo '</div>';
            
            echo '<div class="passion_vendor-info">';
                echo '<div style="text-align: center;">';
                    printf('<a href="%s"><h2>%s</h2></a>', WCV_Vendors::get_vendor_shop_page( $vendor_id ), WCV_Vendors::get_vendor_sold_by( $vendor_id ) );
                echo '</div>';
                if ($social_icons) { 
                    echo $social_icons_list;
                }
                do_action( 'wcv_before_vendor_store_rating' );
                echo '<div class="rating-container">';
                if ( !WCVendors_Pro::get_option( 'ratings_management_cap' )) echo WCVendors_Pro_Ratings_Controller::ratings_link( $vendor_id, true );
                echo '</div>';
                do_action( 'wcv_after_vendor_store_rating' );
            
                if($last_visit==true){
                    printf(__('Dernière visite : %s', 'pulmtree'), $the_login_date);
                }
            
                if($address==true){
                    echo '<br><i class="fa fa-map-marker" aria-hidden="true"></i> '.$address_label;
                }
            
                if($email==true){
                    echo '<br><a href="mailto:'.$user_data->user_email.'"><i class="fa fa-envelope-o" aria-hidden="true"></i> '.$user_data->user_email.'</a>';
                }
            
                if(!empty($phone_number)){
                    echo '<br><a href="tel:'.$phone_number.'"><i class="fa fa-phone" aria-hidden="true"></i> '.$phone_number.'</a>';
                }
            echo '</div>';
            
        }else{
            echo get_bloginfo( 'name' );
        }
        
        echo '<div class="vendor-message-seller-container">';
          echo '<a class="button po-message-seller-right" id="po-message-seller" href="#" rel="nofollow">';
                echo '<i class="fa fa-envelope-o" aria-hidden="true"></i>Contacter le vendeur';
            echo '</a>';
        echo '</div>';
        
        echo '<div id="po-vendor-message-seller" class="po-vendor-message-seller-form">';
            echo do_shortcode('[fep_shortcode_new_message_form to="{current-post-author}" subject="{current-post-title}"]');
        echo '</div>';
        ?>

        <script type="text/javascript">
        jQuery(document).ready(function ($) {
            "use strict";

            // Close popup
            $(document).on('click', '.messenger_overlay, .close', function (e) {
                    $('#po-vendor-message-seller').fadeOut(300);
                    $('.messenger_overlay').css('opacity', '0');
                    $('.messenger_overlay').remove();
                    e.preventDefault();
            });

            // Show the login/signup popup on click
            $('.po-message-seller-right').on('click', function (e) {
                var overlay = '<div class="messenger_overlay"></div>';
                $('body').prepend($(overlay).css('opacity', '0.5'));
                $('#po-vendor-message-seller').fadeIn(300);
                e.preventDefault();
            });
        });
        </script>

        <?php
        /*
        if (function_exists('pt_message_sender_form')) {
            pt_message_sender_form($vendor_id);
        }
        */
        
        echo '</div>';
        
        $ret_str=ob_get_contents();
        ob_end_clean();
        
        return $ret_str;
    }
    
    return "";
}
add_shortcode('passion_vendor_box', 'passion_vendor_box_shortcode');

function passion_body_class($classes){
	if(is_user_logged_in()){
		global $current_user;
		$roles = [];
		foreach($current_user->roles as $role){
			$roles[] = 'role-'.$role;
		}
		return array_merge($classes, $roles);
	}
	return $classes;
}
add_filter('body_class', 'passion_body_class');

function passion_before_wcv_store_phone($html){
    $html .= po_country_selector('TN');
    return $html;
}

function passion_add_frontend_vendor_pro_fields(){
	$user_id = get_current_user_id(); 
    ?>

    <hr style="clear: both;" />
    <h2><?php esc_attr( bloginfo( 'name' ) ); _e(' extra Settings', 'plumtree'); ?></h2>

    <?php
    $featured_carousel_value = get_user_meta( $user_id, 'pt_vendor_featured_carousel', true );
    WCVendors_Pro_Form_Helper::input( array(
        'id' => 'pt_vendor_featured_carousel',
        'label' => esc_html__( 'Check if you want to add carousel with featured products to your shop page', 'plumtree' ),
        'desc_tip' => false,
        'description' => '',
        'type' => 'hidden',
        'value'	=> true,
        )
    );

    $question_form_value = get_user_meta( $user_id, 'pt_vendor_question_form', true );
    WCVendors_Pro_Form_Helper::input( array(
        'id' => 'pt_vendor_question_form',
        'label' => esc_html__( 'Check if you want to add "Ask a question about this product" form to "Seller Tab" on each of your products', 'plumtree' ),
        'desc_tip' => false,
        'description' => '',
        'type' => 'checkbox',
        'value'	=> $question_form_value,
        )
    );

    $sender_form_value = get_user_meta( $user_id, 'pt_vendor_message_sender', true );
    WCVendors_Pro_Form_Helper::input( array(
        'id' => 'pt_vendor_message_sender',
        'label' => esc_html__( 'Check if you want to add "Send a Message" form to your shop page header', 'plumtree' ),
        'desc_tip' => false,
        'description' => '',
        'type' => 'checkbox',
        'value'	=> $sender_form_value,
        )
    );
}

function passion_wp_render_title_tag(){
    $vendor_shop = urldecode( get_query_var( 'vendor_shop' ) );
    $vendor_id  = \WCV_Vendors::get_vendor_id( $vendor_shop ); 
    $store_name  = get_user_meta($vendor_id, 'pv_shop_name', true );
    echo '<title>'.$store_name. ' - ' .get_bloginfo( 'name', 'display' ).'</title>';
}

function passion_after_setup_theme(){
    remove_action( 'wcv_after_variations_tab', 'pt_add_frontend_vendor_pro_product_fields' );
    if(function_exists( 'pt_store_title' ) ) {
        remove_action( 'woocommerce_before_main_content', 'pt_store_title', 5 );
        add_action( 'woocommerce_before_main_content', 'passion_store_title', 5 );
    }
    remove_action('wcvendors_settings_after_vacation_mode', 'pt_add_frontend_vendor_pro_fields' );
    add_action('wcvendors_settings_after_vacation_mode', 'passion_add_frontend_vendor_pro_fields' );
    //add_filter( 'wcv_wp_input_start__wcv_store_phone', 'passion_before_wcv_store_phone', 10, 1 );
    
}
add_action('after_setup_theme', 'passion_after_setup_theme');

/**
* Changer meta title Vendor Page
*/
function passion_wp_loaded(){
    $vendor_shop = urldecode( get_query_var( 'vendor_shop' ) );
    if(!empty($vendor_shop)){
        $vendor_id  = \WCV_Vendors::get_vendor_id( $vendor_shop ); 
        if(\WCV_Vendors::is_vendor( $vendor_id )){
            remove_action( 'wp_head', '_wp_render_title_tag', 1 );
            add_action('wp_head', 'passion_wp_render_title_tag', 1 );
        }
    }
}
add_action('wp', 'passion_wp_loaded');

/**
* Changer page title Vendor Page
*/
if ( ! function_exists( 'passion_store_title' ) ) {
    function passion_store_title(){
        if ( (is_shop() || is_product_category() || is_product_tag()) && !is_front_page() ) {
            $vendor_shop 		= urldecode( get_query_var( 'vendor_shop' ) );
            $title = get_the_title( get_option( 'woocommerce_shop_page_id' ) );
            if(!empty($vendor_shop)){
                $vendor_id  = \WCV_Vendors::get_vendor_id( $vendor_shop ); 
                if(\WCV_Vendors::is_vendor( $vendor_id )){
                    $store_name = get_user_meta( $vendor_id, 'pv_shop_name', true );
                    $title      = $store_name;
                }
            }
            
            echo '<div class="col-md-4 col-sm-6 col-xs-12"><div class="page-title">'.esc_attr( $title ).'</div></div>';
        }
    }
}

/**
* No header button chat
*/
function po_fep_menu_buttons( $menu )
{
    return [];
}
add_filter( 'fep_menu_buttons', 'po_fep_menu_buttons', 99 );

/**
* Hide location data
*/
function passion_wcv_vendor_store_input_hidden( $array )
{
    $array['type'] = 'hidden';
    return $array;
}
add_filter( 'wcv_vendor_store_address2', 'passion_wcv_vendor_store_input_hidden', 100, 1 );
add_filter( 'wcv_vendor_store_city', 'passion_wcv_vendor_store_input_hidden', 100, 1 );
add_filter( 'wcv_vendor_store_state', 'passion_wcv_vendor_store_input_hidden', 100, 1 );
add_filter( 'wcv_vendor_store_postcode', 'passion_wcv_vendor_store_input_hidden', 100, 1 );

function passion_categories_box_shortcode($atts) {
    if(class_exists('PT_Cats_List_Walker')){
        ob_start();

        $defaults = array(
            'title' => 'Categories',
            'show_count' => true,
            'cats_count' => true,
            'show_img' => true,
            'hierarchical' => true,
            'collapsing' => true,
            'cats_type' => 'category',
            'sortby' => 'name',
            'order' => 'DESC',
            'exclude_cats' => '',
        );

        $atts = shortcode_atts($defaults, $atts, 'passion_category_box');
        extract($atts);

        global $wp_query;

        // Setup Current Category
        $current_cat   = false;
        $cat_ancestors = array();

        if (is_tax('product_cat') || is_category() ) {
            $current_cat   = $wp_query->queried_object;
            $cat_ancestors = get_ancestors($current_cat->term_id, $cats_type);
        }

        $catsWalker = new \PT_Cats_List_Walker();
        $catsWalker->show_img = $show_img;
        $catsWalker->collapsing = $collapsing;

        $args = array(
            'orderby'            => $sortby,
            'order'              => $order,
            'style'              => 'list',
            'show_count'         => $show_count,
            'hide_empty'         => true,
            'exclude'            => $exclude_cats,
            'hierarchical'       => $hierarchical,
            'title_li'           => '',
            'show_option_none'   => __( 'No categories', 'handy-feature-pack' ),
            'taxonomy'           => $cats_type,
        );
        $args['walker'] = $catsWalker;
        $args['current_category'] = ( $current_cat ) ? $current_cat->term_id : '';
        $args['current_category_ancestors'] = $cat_ancestors;

        echo '<div id="pt_categories" class="widget widget_pt_categories widget_passion_categories">';
            if(!empty($title)) echo '<h3 class="widget-title itemprop=" name"="">'.$title.'</h3>';
            echo '<ul class="pt-categories">';

            wp_list_categories( $args );

            echo '</ul>';
        echo '</div>';

        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
    
    return 'No class PT_Cats_List_Walker';
}
add_shortcode('passion_category_box', 'passion_categories_box_shortcode');

function passion_account_shortcode($atts) {
    ob_start();
    if(!is_user_logged_in()){
        // do nothing
    }else{
        $vendor_id = get_current_user_id();
        if(\WCV_Vendors::is_vendor( $vendor_id )){
            include('inc/mon-profil.php');
        }
    }
    $html = ob_get_contents();
    ob_end_clean();

    return $html;
}
add_shortcode('po_account', 'passion_account_shortcode');

function passion_wcv_vendor_company_url($arr) {
    $arr['placeholder'] = __('Lien URL de votre entreprise ou blog', 'plumtree');
    $arr['description'] = '';
    return $arr;
}
add_filter('wcv_vendor_company_url', 'passion_wcv_vendor_company_url', 10, 1);

add_action('widgets_init', function(){register_widget('PO_TopMenuUser');});
add_action('widgets_init', function(){register_widget('PO_DashboardVendor');});
add_action('widgets_init', function(){register_widget('PO_ProductSidebar');});

function passion_template_whish_count(){
    if(function_exists( 'yith_wcwl_count_add_to_wishlist' ) ){
        global $post;
        
        $count = yith_wcwl_count_add_to_wishlist($post->ID);
        echo '<span class="wish-count"><i class="fa fa-heart" aria-hidden="true"></i> '.$count.'</span>';
    }
}
add_action('woocommerce_after_shop_loop_item_title', 'passion_template_whish_count', 15 );

function passion_form_product_attributes_path($path){
    return __DIR__.'/partials/wcvendors-pro-attributes.php';
}
add_filter('wcvendors_pro_product_form_product_attributes_path', 'passion_form_product_attributes_path');

function passion_product_table_actions_path($path){
    return __DIR__.'/partials/product/wcvendors-pro-table-actions.php';
}
add_filter('wcv_product_table_actions_path', 'passion_product_table_actions_path');

function passion_product_description(){
    echo '<div class="entry-content">';
        the_content();
    echo '</div>';
    
    echo '<div class="entry-passion_vendor_box hidden-desktop">';
        echo do_shortcode('[passion_vendor_box_2]');
    echo '</div>';
}
add_action('woocommerce_single_product_summary', 'passion_product_description', 29);

// Remove the description tab
function passion_remove_product_tabs( $tabs ) {
    if(isset($tabs['reviews'])&&isset($tabs['description'])){
        $tabs['reviews']['priority'] = $tabs['description']['priority'];
    }
    unset($tabs['description']);
    unset($tabs['fep_wc_contact_seller']);
    return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'passion_remove_product_tabs', 101 );

function passion_vendor_login_redirect_args( $array ) {
    $array['boutique'] = __( 'Boutique',  'pulmtree' );
    return $array;
}
add_filter( 'wcvendors_vendor_login_redirect_args', 'passion_vendor_login_redirect_args', 101, 1 );

function passion_redirect_url_after_register( $redirect_url ) {
    return site_url('boutique');
}
add_filter( 'handy-redirect-url-after-register', 'passion_redirect_url_after_register', 10, 1 );

function passion_set_as_sold() {
    if(function_exists('wc_update_product_stock_status')){
        $post_id = (int) get_query_var('product_sold_id');
        if(!$post_id || ! isset( $_POST[ '_po-save_product_sold' ] ) || !wp_verify_nonce( $_POST[ '_po-save_product_sold' ], '_po-save_product_sold' ) || !is_user_logged_in() ) {
            return;
        }

        $text = array( 'notice' => '', 'type' => 'success' );

        $post = get_post($post_id);
        if($post == null){
            $text[ 'notice' ] = __( 'Ce produit n\'existe pas.', 'pulmtree' );
            $text[ 'type' ]   = 'error';   
        }elseif($post->post_type == 'product'){
            if($post->post_author != get_current_user_id()){
                $text[ 'notice' ] = __( 'Ce produit ne vous appartient pas', 'pulmtree' );
                $text[ 'type' ]   = 'error';    
            }else{
                update_post_meta($post_id, '_manage_stock', 'no');
                
                $stock_status = isset($_POST['stock_status'])?$_POST['stock_status']:'outofstock';
                wc_update_product_stock_status($post_id, $stock_status);
                if($stock_status=='outofstock'){
                    $text[ 'notice' ] = __( 'Produit marqué comme vendu avec succès.', 'pulmtree' );
                }else{
                    $text[ 'notice' ] = __( 'Produit marqué comme disponible avec succès.', 'pulmtree' );
                }
                
            }
        }else{
            $text[ 'notice' ] = __( 'Ce produit n\'existe pas', 'pulmtree' );
            $text[ 'type' ]   = 'error';
        }

        wc_add_notice( $text[ 'notice' ], $text[ 'type' ] );

        wp_safe_redirect(get_permalink($post_id));
        exit;
    }
}
add_action('template_redirect', 'passion_set_as_sold' );

function passion_rewrite_rules_array($rules){
    $newrules = array();
    $newrules['product-sold/([0-9]+)/?$'] = 'index.php?pagename=product-sold&product_sold_id=$matches[1]';
    return $newrules + $rules;
}
add_action('rewrite_rules_array', 'passion_rewrite_rules_array' );

// do not use on live/production servers
function passion_wp_loaded_2() {							
    $rules = get_option( 'rewrite_rules' );			
    if (!isset($rules['product-sold/([0-9]+)/?$'])){
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }
}
add_action( 'wp_loaded', 'passion_wp_loaded_2' );

function passion_query_vars( $query_vars ){
    array_push($query_vars, 'product_sold_id');
    return $query_vars;
}
add_filter('query_vars', 'passion_query_vars' );


function passion_product_store_sold(){
    global $post;
    global $product;
    if (!$product->is_in_stock() ) {
        echo '<p class="stock sold">Vendu</p>';
    }
}
add_action('woocommerce_single_product_summary', 'passion_product_store_sold', 30);

function passion_login_redirect($redirect_to, $request, $user ) {
    //is there a user to check?
    if (isset($user->roles) && is_array($user->roles)) {
        //check for vendor
        if (in_array('vendor', $user->roles)) {
            // redirect them to another URL, in this case, the homepage 
            $redirect_to =  site_url('boutique');
        }
    }
    return $redirect_to;
}
add_filter('login_redirect', 'passion_login_redirect', 10, 3 );

add_filter( 'fep_form_fields', function( $fields ) {
    unset( $fields['message_title']['minlength'] );
    unset( $fields['message_title']['maxlength'] );
    unset( $fields['message_content']['minlength'] );
    unset( $fields['message_content']['maxlength'] );
    return $fields;
});

add_filter('login_form_bottom', function($content, $args ) {
    $content .= do_shortcode('[nextend_social_login provider="facebook"]');
    return $content;
}, 10, 2);

add_filter('wcvendors_pro_table_product_search_meta_keys', function($meta_search) {
    if(is_array($meta_search) && isset($meta_search[0]['key']) && ($meta_search[0]['key'] == '_sku')){
        if($meta_search[0]['value']==''){
            return [];
        }
    }
    return $meta_search;
}, 10, 1);

/**
 * Change a currency symbol
 */
add_filter('woocommerce_currency_symbol', 'passion_change_existing_currency_symbol', 10, 2);
function passion_change_existing_currency_symbol( $currency_symbol, $currency ) {
     switch( $currency ) {
          case 'TND': $currency_symbol = 'DT'; break;
     }
     return $currency_symbol;
}

/**
 * Output a the product images and hook into media uploader on front end
 *
 * @since      1.1.3
 * @param      int     $post_id      the post id for the files being uploaded
 * @todo       add filters to allow the field to be hooked into this should not echo html but return it.
 */
function passion_product_media_uploader( $post_id ) {

    if ( 'yes' !== get_option( 'wcvendors_hide_product_media_featured' ) ) {

        do_action( 'wcv_form_product_media_uploader_before_product_media_uploader', $post_id );

        $post_thumb = has_post_thumbnail($post_id);
        echo '<input type="hidden" id="_featured_image_id" name="_featured_image_id" value="'.( $post_thumb ? get_post_thumbnail_id($post_id) : '' ). '" />';

        if ( 'yes' !== get_option( 'wcvendors_hide_product_media_gallery' ) ) {

            if (metadata_exists('post', $post_id, '_product_image_gallery' ) ) {
                $product_image_gallery = get_post_meta( $post_id, '_product_image_gallery', true );
            } else {
                // Backwards compat
                if ( !empty( $post_id ) ) {
                    $attachment_ids = get_posts( 'post_parent=' . $post_id . '&numberposts=-1&post_type=attachment&orderby=menu_order&order=ASC&post_mime_type=image&fields=ids&meta_key=_woocommerce_exclude_image&meta_value=0' );
                } else {
                    $attachment_ids = array();
                }

                $attachment_ids = array_diff($attachment_ids, array(get_post_thumbnail_id($post_id)));
                $product_image_gallery = implode( ',', $attachment_ids );
            }

            // Output the image gallery if there are any images.
            $attachment_ids		= array_filter( explode( ',', $product_image_gallery ) );

            $max_gallery_count	= get_option( 'wcvendors_product_max_gallery_count' );

            $max_gallery_count	= $max_gallery_count ? $max_gallery_count: 4;

            $gallery_options = apply_filters( 'wcv_product_gallery_options', array(
                    'max_upload'			=> $max_gallery_count,
                    'notice'				=> __( 'You have reached the maximum number of gallery images.', 'wcvendors-pro' ),
                    'max_selected_notice'	=> sprintf( __( 'Maximum number of gallery images selected. Please select a maximum of %1$d images.', 'wcvendors-pro' ), $max_gallery_count ),
                )
            );

            echo '<div class="all-100 small-100 tiny-100" >';

            echo '<h6>'.__('Images du produit', 'pulmtree').'</h6>';

            echo '<div id="media-uploader" class="dropzone">';
                echo '<div id="product_images_container" data-gallery_max_upload="'. $gallery_options[ 'max_upload' ] .'" data-gallery_max_notice="'.$gallery_options[ 'notice' ].'" data-gallery_max_selected_notice="' . $gallery_options[ 'max_selected_notice' ] . '">';
                    echo '<ul class="product_images inline">';
                    if ( $post_thumb ) {
                        $attachment_id = get_post_thumbnail_id($post_id);
                        echo '<li class="wcv-gallery-image" data-attachment_id="' . $attachment_id . '">';
                        echo wp_get_attachment_image($attachment_id, array(150,150) );
                        echo '<ul class="actions">';
                        echo '<li><a href="#" class="po_delete" title="delete"><i class="fa fa-times"></i></a></li>';
                        echo '</ul>';
                        echo '</li>';
                    }
                    if ( sizeof( $attachment_ids ) > 0 ) {
                        foreach( $attachment_ids as $attachment_id ) {
                            echo '<li class="wcv-gallery-image" data-attachment_id="' . $attachment_id . '">';
                            echo wp_get_attachment_image( $attachment_id, array(150,150) );
                            echo '<ul class="actions">';
                            echo '<li><a href="#" class="po_delete" title="delete"><i class="fa fa-times"></i></a></li>';
                            echo '</ul>';
                            echo '</li>';
                        }
                    }
                    echo '</ul>';
                    echo '<input type="hidden" id="product_image_gallery" name="product_image_gallery" value="'. (( sizeof( $attachment_ids ) > 0 ) ? $product_image_gallery : '' ). '">';
                echo '</div>';
                echo '<span class="wcv_gallery_msg"></span>';
            echo '</div>'; // dropzone
            echo '</div>';

        }
        

        do_action( 'wcv_form_product_media_uploader_after_product_media_uploader', $post_id );

    }

} // media_uploader ()

add_action( 'wp_ajax_handle_dropped_media', 'po_handle_dropped_media' );
function po_handle_dropped_media() {
    status_header(200);
    
    // These files need to be included as dependencies when on the front end.
	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );

    $upload_dir = wp_upload_dir();
    $upload_path = $upload_dir['path'] . DIRECTORY_SEPARATOR;
    $num_files = count($_FILES['file']['tmp_name']);

    $newupload = 0;

    if ( !empty($_FILES) ) {
        $files = $_FILES;
        foreach($files as $file) {
            $newfile = array (
                'name'     => $file['name'],
                'type'     => $file['type'],
                'tmp_name' => $file['tmp_name'],
                'error'    => $file['error'],
                'size'     => $file['size']
            );

            $_FILES = array('upload'=>$newfile);
            
            foreach($_FILES as $file => $array) {
                $newupload = media_handle_upload($file, 0);
                //$newupload = media_handle_upload($file, 0, [], ['test_type'=>0, 'test_form' => false]);
            }
        }
    }

    if(is_wp_error($newupload)){
        echo json_encode(array(
            'status'  => 0,
            'message' => esc_html( $newupload->get_error_message() ),
        ));
    }else{
        echo json_encode(array(
            'status'  => 1,
            'attachment_id' => $newupload,
        ));
    } 
    die();
}

add_action( 'wp_ajax_handle_deleted_media', 'po_handle_deleted_media' );
function po_handle_deleted_media(){
    if( isset($_REQUEST['media_id']) ){
        $post_id = absint( $_REQUEST['media_id'] );
        $status = wp_delete_attachment($post_id, true);
        if( $status )
            echo json_encode(array('status' => 'OK'));
        else
            echo json_encode(array('status' => 'FAILED'));
    }
    die();
}

function po_wp_handle_upload($data, $action){
    return $data;
}
add_filter('wp_handle_upload', 'po_wp_handle_upload', 10, 2);

function po_wp_check_filetype_and_ext($data, $file, $filename, $mimes, $real_mime){
    if(empty($data['ext']) && empty($data['type'])){
        if(strpos($filename, 'jpeg')!==false){
            $data['ext'] = 'jpeg';
            $data['type'] = 'image/jpeg';
        }
        if(strpos($filename, 'jpg')!==false){
            $data['ext'] = 'jpg';
            $data['type'] = 'image/jpg';
        }
        if(strpos($filename, 'png')!==false){
            $data['ext'] = 'png';
            $data['type'] = 'image/png';
        }
        if(strpos($filename, 'gif')!==false){
            $data['ext'] = 'gif';
            $data['type'] = 'image/gif';
        }
    }
    
    return $data;
}
add_filter('wp_check_filetype_and_ext', 'po_wp_check_filetype_and_ext', 1000, 5);