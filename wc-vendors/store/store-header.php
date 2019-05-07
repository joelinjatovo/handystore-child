<?php
/**
 * The Template for displaying a store header
 *
 * Override this template by copying it to yourtheme/wc-vendors/store
 *
 * @package    WCVendors_Pro
 * @version    1.3.5
 */

$store_icon_src 	= wp_get_attachment_image_src( get_user_meta( $vendor_id, '_wcv_store_icon_id', true ), 'pt-vendor-main-logo' );
$store_icon 		  = '';
$store_banner_src = wp_get_attachment_image_src( get_user_meta( $vendor_id, '_wcv_store_banner_id', true ), 'full');
$store_banner 		= '';

//$store_name = '';
$store_name = get_user_meta( $vendor_id, 'pv_shop_name', true );
$store_description = get_user_meta( $vendor_id, 'pv_shop_description', true );

// see if the array is valid
if ( is_array( $store_icon_src ) ) {
	$store_icon 	= '<img src="'. esc_url($store_icon_src[0]) .'" alt="'. esc_attr($store_name) .'" class="store-icon" />';
}

// Verified vendor
$verified_vendor = ( array_key_exists( '_wcv_verified_vendor', $vendor_meta ) ) ? $vendor_meta[ '_wcv_verified_vendor' ] : false;
$verified_vendor_label = WCVendors_Pro::get_option( 'verified_vendor_label' );
// $verified_vendor_icon_src 	= WCVendors_Pro::get_option( 'verified_vendor_icon_src' );

// Get store details including social, adddresses and phone number
$twitter_username 	= get_user_meta( $vendor_id , '_wcv_twitter_username', true );
$instagram_username = get_user_meta( $vendor_id , '_wcv_instagram_username', true );
$facebook_url 		  = get_user_meta( $vendor_id , '_wcv_facebook_url', true );
$linkedin_url 		  = get_user_meta( $vendor_id , '_wcv_linkedin_url', true );
$youtube_url 		  = get_user_meta( $vendor_id , '_wcv_youtube_url', true );
$googleplus_url  	  = get_user_meta( $vendor_id , '_wcv_googleplus_url', true );
$pinterest_url 		  = get_user_meta( $vendor_id , '_wcv_pinterest_url', true );
$snapchat_username 	  = get_user_meta( $vendor_id , '_wcv_snapchat_username', true );
$address1 			  = ( array_key_exists( '_wcv_store_address1', $vendor_meta ) ) ? $vendor_meta[ '_wcv_store_address1' ] : '';
$address2 		 	  = ( array_key_exists( '_wcv_store_address2', $vendor_meta ) ) ? $vendor_meta[ '_wcv_store_address2' ] : '';
$city	 			  = ( array_key_exists( '_wcv_store_city', $vendor_meta ) ) ? $vendor_meta[ '_wcv_store_city' ]  : '';
$state	 			  = ( array_key_exists( '_wcv_store_state', $vendor_meta ) ) ? $vendor_meta[ '_wcv_store_state' ] : '';
$phone				  = ( array_key_exists( '_wcv_store_phone', $vendor_meta ) ) ? $vendor_meta[ '_wcv_store_phone' ]  : '';
$store_postcode		  = ( array_key_exists( '_wcv_store_postcode', $vendor_meta ) ) ? $vendor_meta[ '_wcv_store_postcode' ]  : '';
$address 			  = ( $address1 != '') ? $address1 .', '. $city .', '. $state .', '. $store_postcode : '';

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

// Extra classes for header wrappers
$message_sender_form = get_user_meta( $vendor_id , 'pt_vendor_message_sender', true );
$store_brand_class = $store_author_class = ' col-xs-12 col-sm-3';
$store_description_class = ' col-xs-12 col-sm-6';
if ( $message_sender_form === 'yes' ) {
	$store_description_class = ' col-xs-12 col-sm-9';
}

// Social list
$social_icons_list = '<ul class="social-icons">';
if ( $facebook_url != '') { $social_icons_list .= '<li><a href="'.$facebook_url.'" target="_blank"><i class="fa fa-facebook"></i></a></li>'; }
if ( $instagram_username != '') { $social_icons_list .= '<li><a href="//instagram.com/'.$instagram_username.'" target="_blank"><i class="fa fa-instagram"></i></a></li>'; }
if ( $twitter_username != '') { $social_icons_list .= '<li><a href="//twitter.com/'.$twitter_username.'" target="_blank"><i class="fa fa-twitter"></i></a></li>'; }
if ( $googleplus_url != '') { $social_icons_list .= '<li><a href="'.$googleplus_url.'" target="_blank"><i class="fa fa-google-plus"></i></a></li>'; }
if ( $youtube_url != '') { $social_icons_list .= '<li><a href="'.$youtube_url.'" target="_blank"><i class="fa fa-youtube"></i></a></li>'; }
if ( $linkedin_url != '') { $social_icons_list .= '<li><a href="'.$linkedin_url.'" target="_blank"><i class="fa fa-linkedin"></i></a></li>'; }
if ( $snapchat_username != '') { $social_icons_list .= '<li><a href="//www.snapchat.com/add/'.$snapchat_username.'" target="_blank"><i class="fa fa-snapchat" aria-hidden="true"></i></a></li>'; }
$social_icons_list .= '</ul>';

$social_icons = empty( $twitter_username ) && empty( $instagram_username ) && empty( $facebook_url ) && empty( $linkedin_url ) && empty( $youtube_url ) && empty( $googleplus_url ) && empty( $pinterst_url ) && empty( $snapchat_username ) ? false : true; ?>

<?php do_action( 'wcv_before_vendor_store_header' ); ?>

<div class="header-container col-xs-12">
	<?php if( is_array( $store_banner_src ) ) {
        echo '<div id="banner-wrap" style="background: url('.esc_url($store_banner_src[0]).') repeat left top transparent;">';
    } else {
        //  Getting default banner
        $default_banner_src = WCVendors_Pro::get_option( 'default_store_banner_src' );
        echo '<div id="banner-wrap" style="background: url('.esc_url($default_banner_src).') repeat left top transparent;">';
    } ?>

        <div id="inner-element" class="row">

            <?php if ($store_icon != '') { ?>
                <div class="store-brand<?php echo esc_attr($store_brand_class); ?>">
                    <div class="store-brand-inner">
                        <?php echo $store_icon; ?>
                        <?php if ( function_exists('pt_output_favourite_button') ) pt_output_favourite_button($vendor_id); ?>
                    </div>
                </div>
            <?php } ?>

            <div class="store-info<?php echo esc_attr($store_description_class); ?>">
                <div class="store-info-inner">

                    <?php do_action( 'wcv_before_vendor_store_title' ); ?>
                        <h3><?php if ( is_product() ) {
                            echo '<a href="'. WCV_Vendors::get_vendor_shop_page( $post->post_author ).'">'. esc_attr($store_name) . '</a>';
                        } else { echo esc_attr($store_name); } ?></h3>
                        <?php if ( $verified_vendor ) : ?>
                            <div class="wcv-verified-vendor">
                                <i class="fa fa-check-circle-o fa-lg" aria-hidden="true"></i> &nbsp; <?php echo $verified_vendor_label; ?>
                            </div>
                        <?php endif; ?>
                    <?php do_action( 'wcv_after_vendor_store_title' ); ?>

                    <?php do_action( 'wcv_before_vendor_store_rating' ); ?>
                    <div class="rating-container">
                      <?php if ( ! WCVendors_Pro::get_option( 'ratings_management_cap' ) ) echo WCVendors_Pro_Ratings_Controller::ratings_link( $vendor_id, true ); ?>
                    </div>
                    <?php do_action( 'wcv_after_vendor_store_rating' ); ?>

                    <?php if ( ($address_label != '') || ($phone != '') ) { ?>
                        <div class="store-address-container">
                            <div class="store-address">
                                <?php if ( $address_label != '' ) {  ?><a target="_blank" href="http://maps.google.com/maps?&q=<?php echo $address; ?>"><address><i class="fa fa-map-marker" aria-hidden="true"></i><?php echo $address_label; ?></address></a><?php } ?>
                            </div>
                            <div class="store-phone">
                                <?php if ($phone != '')  { ?><a href="tel:<?php echo $phone; ?>"><i class="fa fa-phone"></i><?php echo $phone; ?></a><?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <?php do_action( 'wcv_before_vendor_store_description' ); ?>
                    <div class="entry-excerpt">
                        <?php echo $store_description; ?>
                    </div>
                    <?php if ( $social_icons ) echo $social_icons_list; ?>
                    <?php do_action( 'wcv_after_vendor_store_description' ); ?>

                </div>
            </div>

            <?php if ( $message_sender_form === 'yes' ) { ?>
                <div class="store-aurhor<?php echo esc_attr($store_author_class); ?>">
                    <div class="store-aurhor-inner">
                    <?php echo get_avatar( $vendor_id, 70 ); ?>
                    <?php $user_info = get_userdata( $vendor_id );
                        //echo '<span>'. $user_info->first_name .'&nbsp;'. $user_info->last_name .'</span>';
                        echo '<div class="vendor-message-seller-container">';
                          echo '<a class="button" id="po-message-seller" href="#" rel="nofollow" title="Contacter le vendeur">';
                                echo '<i class="fa fa-envelope-o" aria-hidden="true"></i>Contacter le vendeur';
                            echo '</a>';
                        echo '</div>';

                        echo '<div id="po-vendor-message-seller" class="po-vendor-message-seller-form">';
                            echo do_shortcode('[fep_shortcode_new_message_form to="{current-post-author}" subject="{current-post-title}"]');
                        echo '</div>';
                        /*
                        if (function_exists('pt_message_sender_form')) {
                            pt_message_sender_form($vendor_id);
                        }
                         */
                        ?>
                    </div>
                </div>
            <?php } ?>

        </div>
    <?php echo '</div>'; ?>
</div>
<?php do_action( 'wcv_after_vendor_store_header' ); ?>
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
