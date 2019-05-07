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
    wp_enqueue_style( 'child-style', get_template_directory_uri() . '/style.css' );
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
          echo '<a class="button" id="po-message-seller" href="#" rel="nofollow">';
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
            $('#po-message-seller').on('click', function (e) {
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

$countryArray = array(
	'AD'=>array('name'=>'ANDORRA','code'=>'376'),
	'AE'=>array('name'=>'UNITED ARAB EMIRATES','code'=>'971'),
	'AF'=>array('name'=>'AFGHANISTAN','code'=>'93'),
	'AG'=>array('name'=>'ANTIGUA AND BARBUDA','code'=>'1268'),
	'AI'=>array('name'=>'ANGUILLA','code'=>'1264'),
	'AL'=>array('name'=>'ALBANIA','code'=>'355'),
	'AM'=>array('name'=>'ARMENIA','code'=>'374'),
	'AN'=>array('name'=>'NETHERLANDS ANTILLES','code'=>'599'),
	'AO'=>array('name'=>'ANGOLA','code'=>'244'),
	'AQ'=>array('name'=>'ANTARCTICA','code'=>'672'),
	'AR'=>array('name'=>'ARGENTINA','code'=>'54'),
	'AS'=>array('name'=>'AMERICAN SAMOA','code'=>'1684'),
	'AT'=>array('name'=>'AUSTRIA','code'=>'43'),
	'AU'=>array('name'=>'AUSTRALIA','code'=>'61'),
	'AW'=>array('name'=>'ARUBA','code'=>'297'),
	'AZ'=>array('name'=>'AZERBAIJAN','code'=>'994'),
	'BA'=>array('name'=>'BOSNIA AND HERZEGOVINA','code'=>'387'),
	'BB'=>array('name'=>'BARBADOS','code'=>'1246'),
	'BD'=>array('name'=>'BANGLADESH','code'=>'880'),
	'BE'=>array('name'=>'BELGIUM','code'=>'32'),
	'BF'=>array('name'=>'BURKINA FASO','code'=>'226'),
	'BG'=>array('name'=>'BULGARIA','code'=>'359'),
	'BH'=>array('name'=>'BAHRAIN','code'=>'973'),
	'BI'=>array('name'=>'BURUNDI','code'=>'257'),
	'BJ'=>array('name'=>'BENIN','code'=>'229'),
	'BL'=>array('name'=>'SAINT BARTHELEMY','code'=>'590'),
	'BM'=>array('name'=>'BERMUDA','code'=>'1441'),
	'BN'=>array('name'=>'BRUNEI DARUSSALAM','code'=>'673'),
	'BO'=>array('name'=>'BOLIVIA','code'=>'591'),
	'BR'=>array('name'=>'BRAZIL','code'=>'55'),
	'BS'=>array('name'=>'BAHAMAS','code'=>'1242'),
	'BT'=>array('name'=>'BHUTAN','code'=>'975'),
	'BW'=>array('name'=>'BOTSWANA','code'=>'267'),
	'BY'=>array('name'=>'BELARUS','code'=>'375'),
	'BZ'=>array('name'=>'BELIZE','code'=>'501'),
	'CA'=>array('name'=>'CANADA','code'=>'1'),
	'CC'=>array('name'=>'COCOS (KEELING) ISLANDS','code'=>'61'),
	'CD'=>array('name'=>'CONGO, THE DEMOCRATIC REPUBLIC OF THE','code'=>'243'),
	'CF'=>array('name'=>'CENTRAL AFRICAN REPUBLIC','code'=>'236'),
	'CG'=>array('name'=>'CONGO','code'=>'242'),
	'CH'=>array('name'=>'SWITZERLAND','code'=>'41'),
	'CI'=>array('name'=>'COTE D IVOIRE','code'=>'225'),
	'CK'=>array('name'=>'COOK ISLANDS','code'=>'682'),
	'CL'=>array('name'=>'CHILE','code'=>'56'),
	'CM'=>array('name'=>'CAMEROON','code'=>'237'),
	'CN'=>array('name'=>'CHINA','code'=>'86'),
	'CO'=>array('name'=>'COLOMBIA','code'=>'57'),
	'CR'=>array('name'=>'COSTA RICA','code'=>'506'),
	'CU'=>array('name'=>'CUBA','code'=>'53'),
	'CV'=>array('name'=>'CAPE VERDE','code'=>'238'),
	'CX'=>array('name'=>'CHRISTMAS ISLAND','code'=>'61'),
	'CY'=>array('name'=>'CYPRUS','code'=>'357'),
	'CZ'=>array('name'=>'CZECH REPUBLIC','code'=>'420'),
	'DE'=>array('name'=>'GERMANY','code'=>'49'),
	'DJ'=>array('name'=>'DJIBOUTI','code'=>'253'),
	'DK'=>array('name'=>'DENMARK','code'=>'45'),
	'DM'=>array('name'=>'DOMINICA','code'=>'1767'),
	'DO'=>array('name'=>'DOMINICAN REPUBLIC','code'=>'1809'),
	'DZ'=>array('name'=>'ALGERIA','code'=>'213'),
	'EC'=>array('name'=>'ECUADOR','code'=>'593'),
	'EE'=>array('name'=>'ESTONIA','code'=>'372'),
	'EG'=>array('name'=>'EGYPT','code'=>'20'),
	'ER'=>array('name'=>'ERITREA','code'=>'291'),
	'ES'=>array('name'=>'SPAIN','code'=>'34'),
	'ET'=>array('name'=>'ETHIOPIA','code'=>'251'),
	'FI'=>array('name'=>'FINLAND','code'=>'358'),
	'FJ'=>array('name'=>'FIJI','code'=>'679'),
	'FK'=>array('name'=>'FALKLAND ISLANDS (MALVINAS)','code'=>'500'),
	'FM'=>array('name'=>'MICRONESIA, FEDERATED STATES OF','code'=>'691'),
	'FO'=>array('name'=>'FAROE ISLANDS','code'=>'298'),
	'FR'=>array('name'=>'FRANCE','code'=>'33'),
	'GA'=>array('name'=>'GABON','code'=>'241'),
	'GB'=>array('name'=>'UNITED KINGDOM','code'=>'44'),
	'GD'=>array('name'=>'GRENADA','code'=>'1473'),
	'GE'=>array('name'=>'GEORGIA','code'=>'995'),
	'GH'=>array('name'=>'GHANA','code'=>'233'),
	'GI'=>array('name'=>'GIBRALTAR','code'=>'350'),
	'GL'=>array('name'=>'GREENLAND','code'=>'299'),
	'GM'=>array('name'=>'GAMBIA','code'=>'220'),
	'GN'=>array('name'=>'GUINEA','code'=>'224'),
	'GQ'=>array('name'=>'EQUATORIAL GUINEA','code'=>'240'),
	'GR'=>array('name'=>'GREECE','code'=>'30'),
	'GT'=>array('name'=>'GUATEMALA','code'=>'502'),
	'GU'=>array('name'=>'GUAM','code'=>'1671'),
	'GW'=>array('name'=>'GUINEA-BISSAU','code'=>'245'),
	'GY'=>array('name'=>'GUYANA','code'=>'592'),
	'HK'=>array('name'=>'HONG KONG','code'=>'852'),
	'HN'=>array('name'=>'HONDURAS','code'=>'504'),
	'HR'=>array('name'=>'CROATIA','code'=>'385'),
	'HT'=>array('name'=>'HAITI','code'=>'509'),
	'HU'=>array('name'=>'HUNGARY','code'=>'36'),
	'ID'=>array('name'=>'INDONESIA','code'=>'62'),
	'IE'=>array('name'=>'IRELAND','code'=>'353'),
	'IL'=>array('name'=>'ISRAEL','code'=>'972'),
	'IM'=>array('name'=>'ISLE OF MAN','code'=>'44'),
	'IN'=>array('name'=>'INDIA','code'=>'91'),
	'IQ'=>array('name'=>'IRAQ','code'=>'964'),
	'IR'=>array('name'=>'IRAN, ISLAMIC REPUBLIC OF','code'=>'98'),
	'IS'=>array('name'=>'ICELAND','code'=>'354'),
	'IT'=>array('name'=>'ITALY','code'=>'39'),
	'JM'=>array('name'=>'JAMAICA','code'=>'1876'),
	'JO'=>array('name'=>'JORDAN','code'=>'962'),
	'JP'=>array('name'=>'JAPAN','code'=>'81'),
	'KE'=>array('name'=>'KENYA','code'=>'254'),
	'KG'=>array('name'=>'KYRGYZSTAN','code'=>'996'),
	'KH'=>array('name'=>'CAMBODIA','code'=>'855'),
	'KI'=>array('name'=>'KIRIBATI','code'=>'686'),
	'KM'=>array('name'=>'COMOROS','code'=>'269'),
	'KN'=>array('name'=>'SAINT KITTS AND NEVIS','code'=>'1869'),
	'KP'=>array('name'=>'KOREA DEMOCRATIC PEOPLES REPUBLIC OF','code'=>'850'),
	'KR'=>array('name'=>'KOREA REPUBLIC OF','code'=>'82'),
	'KW'=>array('name'=>'KUWAIT','code'=>'965'),
	'KY'=>array('name'=>'CAYMAN ISLANDS','code'=>'1345'),
	'KZ'=>array('name'=>'KAZAKSTAN','code'=>'7'),
	'LA'=>array('name'=>'LAO PEOPLES DEMOCRATIC REPUBLIC','code'=>'856'),
	'LB'=>array('name'=>'LEBANON','code'=>'961'),
	'LC'=>array('name'=>'SAINT LUCIA','code'=>'1758'),
	'LI'=>array('name'=>'LIECHTENSTEIN','code'=>'423'),
	'LK'=>array('name'=>'SRI LANKA','code'=>'94'),
	'LR'=>array('name'=>'LIBERIA','code'=>'231'),
	'LS'=>array('name'=>'LESOTHO','code'=>'266'),
	'LT'=>array('name'=>'LITHUANIA','code'=>'370'),
	'LU'=>array('name'=>'LUXEMBOURG','code'=>'352'),
	'LV'=>array('name'=>'LATVIA','code'=>'371'),
	'LY'=>array('name'=>'LIBYAN ARAB JAMAHIRIYA','code'=>'218'),
	'MA'=>array('name'=>'MOROCCO','code'=>'212'),
	'MC'=>array('name'=>'MONACO','code'=>'377'),
	'MD'=>array('name'=>'MOLDOVA, REPUBLIC OF','code'=>'373'),
	'ME'=>array('name'=>'MONTENEGRO','code'=>'382'),
	'MF'=>array('name'=>'SAINT MARTIN','code'=>'1599'),
	'MG'=>array('name'=>'MADAGASCAR','code'=>'261'),
	'MH'=>array('name'=>'MARSHALL ISLANDS','code'=>'692'),
	'MK'=>array('name'=>'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF','code'=>'389'),
	'ML'=>array('name'=>'MALI','code'=>'223'),
	'MM'=>array('name'=>'MYANMAR','code'=>'95'),
	'MN'=>array('name'=>'MONGOLIA','code'=>'976'),
	'MO'=>array('name'=>'MACAU','code'=>'853'),
	'MP'=>array('name'=>'NORTHERN MARIANA ISLANDS','code'=>'1670'),
	'MR'=>array('name'=>'MAURITANIA','code'=>'222'),
	'MS'=>array('name'=>'MONTSERRAT','code'=>'1664'),
	'MT'=>array('name'=>'MALTA','code'=>'356'),
	'MU'=>array('name'=>'MAURITIUS','code'=>'230'),
	'MV'=>array('name'=>'MALDIVES','code'=>'960'),
	'MW'=>array('name'=>'MALAWI','code'=>'265'),
	'MX'=>array('name'=>'MEXICO','code'=>'52'),
	'MY'=>array('name'=>'MALAYSIA','code'=>'60'),
	'MZ'=>array('name'=>'MOZAMBIQUE','code'=>'258'),
	'NA'=>array('name'=>'NAMIBIA','code'=>'264'),
	'NC'=>array('name'=>'NEW CALEDONIA','code'=>'687'),
	'NE'=>array('name'=>'NIGER','code'=>'227'),
	'NG'=>array('name'=>'NIGERIA','code'=>'234'),
	'NI'=>array('name'=>'NICARAGUA','code'=>'505'),
	'NL'=>array('name'=>'NETHERLANDS','code'=>'31'),
	'NO'=>array('name'=>'NORWAY','code'=>'47'),
	'NP'=>array('name'=>'NEPAL','code'=>'977'),
	'NR'=>array('name'=>'NAURU','code'=>'674'),
	'NU'=>array('name'=>'NIUE','code'=>'683'),
	'NZ'=>array('name'=>'NEW ZEALAND','code'=>'64'),
	'OM'=>array('name'=>'OMAN','code'=>'968'),
	'PA'=>array('name'=>'PANAMA','code'=>'507'),
	'PE'=>array('name'=>'PERU','code'=>'51'),
	'PF'=>array('name'=>'FRENCH POLYNESIA','code'=>'689'),
	'PG'=>array('name'=>'PAPUA NEW GUINEA','code'=>'675'),
	'PH'=>array('name'=>'PHILIPPINES','code'=>'63'),
	'PK'=>array('name'=>'PAKISTAN','code'=>'92'),
	'PL'=>array('name'=>'POLAND','code'=>'48'),
	'PM'=>array('name'=>'SAINT PIERRE AND MIQUELON','code'=>'508'),
	'PN'=>array('name'=>'PITCAIRN','code'=>'870'),
	'PR'=>array('name'=>'PUERTO RICO','code'=>'1'),
	'PT'=>array('name'=>'PORTUGAL','code'=>'351'),
	'PW'=>array('name'=>'PALAU','code'=>'680'),
	'PY'=>array('name'=>'PARAGUAY','code'=>'595'),
	'QA'=>array('name'=>'QATAR','code'=>'974'),
	'RO'=>array('name'=>'ROMANIA','code'=>'40'),
	'RS'=>array('name'=>'SERBIA','code'=>'381'),
	'RU'=>array('name'=>'RUSSIAN FEDERATION','code'=>'7'),
	'RW'=>array('name'=>'RWANDA','code'=>'250'),
	'SA'=>array('name'=>'SAUDI ARABIA','code'=>'966'),
	'SB'=>array('name'=>'SOLOMON ISLANDS','code'=>'677'),
	'SC'=>array('name'=>'SEYCHELLES','code'=>'248'),
	'SD'=>array('name'=>'SUDAN','code'=>'249'),
	'SE'=>array('name'=>'SWEDEN','code'=>'46'),
	'SG'=>array('name'=>'SINGAPORE','code'=>'65'),
	'SH'=>array('name'=>'SAINT HELENA','code'=>'290'),
	'SI'=>array('name'=>'SLOVENIA','code'=>'386'),
	'SK'=>array('name'=>'SLOVAKIA','code'=>'421'),
	'SL'=>array('name'=>'SIERRA LEONE','code'=>'232'),
	'SM'=>array('name'=>'SAN MARINO','code'=>'378'),
	'SN'=>array('name'=>'SENEGAL','code'=>'221'),
	'SO'=>array('name'=>'SOMALIA','code'=>'252'),
	'SR'=>array('name'=>'SURINAME','code'=>'597'),
	'ST'=>array('name'=>'SAO TOME AND PRINCIPE','code'=>'239'),
	'SV'=>array('name'=>'EL SALVADOR','code'=>'503'),
	'SY'=>array('name'=>'SYRIAN ARAB REPUBLIC','code'=>'963'),
	'SZ'=>array('name'=>'SWAZILAND','code'=>'268'),
	'TC'=>array('name'=>'TURKS AND CAICOS ISLANDS','code'=>'1649'),
	'TD'=>array('name'=>'CHAD','code'=>'235'),
	'TG'=>array('name'=>'TOGO','code'=>'228'),
	'TH'=>array('name'=>'THAILAND','code'=>'66'),
	'TJ'=>array('name'=>'TAJIKISTAN','code'=>'992'),
	'TK'=>array('name'=>'TOKELAU','code'=>'690'),
	'TL'=>array('name'=>'TIMOR-LESTE','code'=>'670'),
	'TM'=>array('name'=>'TURKMENISTAN','code'=>'993'),
	'TN'=>array('name'=>'TUNISIA','code'=>'216'),
	'TO'=>array('name'=>'TONGA','code'=>'676'),
	'TR'=>array('name'=>'TURKEY','code'=>'90'),
	'TT'=>array('name'=>'TRINIDAD AND TOBAGO','code'=>'1868'),
	'TV'=>array('name'=>'TUVALU','code'=>'688'),
	'TW'=>array('name'=>'TAIWAN, PROVINCE OF CHINA','code'=>'886'),
	'TZ'=>array('name'=>'TANZANIA, UNITED REPUBLIC OF','code'=>'255'),
	'UA'=>array('name'=>'UKRAINE','code'=>'380'),
	'UG'=>array('name'=>'UGANDA','code'=>'256'),
	'US'=>array('name'=>'UNITED STATES','code'=>'1'),
	'UY'=>array('name'=>'URUGUAY','code'=>'598'),
	'UZ'=>array('name'=>'UZBEKISTAN','code'=>'998'),
	'VA'=>array('name'=>'HOLY SEE (VATICAN CITY STATE)','code'=>'39'),
	'VC'=>array('name'=>'SAINT VINCENT AND THE GRENADINES','code'=>'1784'),
	'VE'=>array('name'=>'VENEZUELA','code'=>'58'),
	'VG'=>array('name'=>'VIRGIN ISLANDS, BRITISH','code'=>'1284'),
	'VI'=>array('name'=>'VIRGIN ISLANDS, U.S.','code'=>'1340'),
	'VN'=>array('name'=>'VIET NAM','code'=>'84'),
	'VU'=>array('name'=>'VANUATU','code'=>'678'),
	'WF'=>array('name'=>'WALLIS AND FUTUNA','code'=>'681'),
	'WS'=>array('name'=>'SAMOA','code'=>'685'),
	'XK'=>array('name'=>'KOSOVO','code'=>'381'),
	'YE'=>array('name'=>'YEMEN','code'=>'967'),
	'YT'=>array('name'=>'MAYOTTE','code'=>'262'),
	'ZA'=>array('name'=>'SOUTH AFRICA','code'=>'27'),
	'ZM'=>array('name'=>'ZAMBIA','code'=>'260'),
	'ZW'=>array('name'=>'ZIMBABWE','code'=>'263')
);

/*
* Country Array to HTML Select List
*
* Usage:
*   echo po_country_selector(); // Basic
*   echo po_country_selector("IN"); // Set default Country with its code
*   echo po_country_selector("IN", "my-country", "my-country", "form-control"); // With full Options
*
*/
function po_country_selector($defaultCountry = "", $id = "", $name = "", $classes = ""){
    global $countryArray; // Assuming the array is placed above this function
    
    $output = "<select id='".$id."' name='".$name."' class='".$classes."'>";
	
	foreach($countryArray as $code => $country){
		$countryName = ucwords(strtolower($country["name"])); // Making it look good
		$output .= "<option value='".$code."' ".(($code==strtoupper($defaultCountry))?"selected":"").">".$code." - ".$countryName." (+".$country["code"].")</option>";
	}
	
	$output .= "</select>";
	
	return $output; // or echo $output; to print directly
}

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
        echo do_shortcode('[passion_vendor_box]');
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

function passion_rewrites_init(){
    add_rewrite_rule(
        'product-sold/([0-9]+)/?$',
        'index.php?pagename=product-sold&product_sold_id=$matches[1]',
        'top' );
}
add_action('init', 'passion_rewrites_init' );

// do not use on live/production servers
function passion_rewrite_rules() {
	$ver = filemtime( __FILE__ ); // Get the file time for this file as the version number
	$defaults = array( 'version' => 0, 'time' => time() );
	$r = wp_parse_args( get_option( __CLASS__ . '_flush', array() ), $defaults );

	if ( $r['version'] != $ver || $r['time'] + 172800 < time() ) { // Flush if ver changes or if 48hrs has passed.
		flush_rewrite_rules();
		// trace( 'flushed' );
		$args = array( 'version' => $ver, 'time' => time() );
		if ( ! update_option( __CLASS__ . '_flush', $args ) )
			add_option( __CLASS__ . '_flush', $args );
	}
}
add_action( 'init', 'passion_rewrite_rules' );

function passion_query_vars( $query_vars ){
    $query_vars[] = 'product_sold_id';
    return $query_vars;
}
add_filter('query_vars', 'passion_query_vars' );


function passion_product_store_sold(){
    global $product;
    if ( !$product->is_in_stock() ) {
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
