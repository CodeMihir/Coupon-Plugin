<?php 

if (isset($_POST['Save_Options'])) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'coupons_mihir_parmar'; 
    $errors = array();
    update_option( 'correct_data', $_POST );

    if(empty($_POST['wpc_title'])){
        $errors['wpc_title'] = 'title cannot be empty';
    }
    else{
        $wpc_duplicate = wpc_get_records($_POST['wpc_title']);
        if($wpc_duplicate > 0)
        {
            $errors['wpc_title'] = 'coupon already exist';
        }
    }
    if(empty($_POST['amount_coupon']))
    {   
        $errors['wpc_amount_coupon'] = 'amount cannot be empty';
    }
    if(empty($_POST['wpc_category']))
    { 
        $errors['wpc_category'] = 'Category must be assigned.';
    }


    require_once( ABSPATH . 'wp-admin/includes/file.php' ); 

    $wpc_title        = sanitize_text_field( $_POST['wpc_title'] );
    $wpc_desc         = sanitize_text_field( $_POST['wpc_desc'] );
    $wpc_category     = sanitize_text_field( $_POST['wpc_category'] );
    $wpc_client       = isset( $_POST['wpc_client']) ? sanitize_text_field($_POST['wpc_client']) : 'off';
    $av_options = isset($_POST['av_options']) ? $_POST['av_options'] : array();

    $av_options = array_map('sanitize_text_field', $av_options);
    $av_options_serialized = serialize($av_options);
    $wpc_img          = absint($_POST['wpc_img']);
    $wpc_amount       = absint($_POST['amount_coupon']);
    $nonce            = $_POST['_wpnonce'];
    if (wp_verify_nonce($nonce, 'wpc_coupon_nounce') && empty($errors)) {
        $result = $wpdb->insert(
            $table_name,
            array(
                'title'           => $wpc_title,
                'description'     => $wpc_desc,
                'amount'          => $wpc_amount,
                'image'           => $wpc_img,
                'category'        => $wpc_category,
                'availability'    => $av_options_serialized,
                )
            );
            if($result){
                _e('<div class="notice notice-success is-dismissible"><p> coupon created </p></div>','wp-mihir-parmar');	
                update_option( 'correct_data', array() );
            }
            else{
                echo $wpdb->last_error;
            }
    } else {
        _e('<div class="notice notice-error wpc-error-msg is-dismissible"><p>Unable to save data!</p></div>','wp-mihir-parmar');	
    }
}
$wpc_correct_data = get_option( 'correct_data' );


?>
<div class="wrap">

    <h2><?php _e('Add Coupon', 'wp-mihir-parmar') ?>  <a class="add-new-h2"
            href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=wpc-listing-coupons');?>"><?php _e('back to list', 'wp-mihir-parmar')?></a>
        </h2>

    <form method="post" enctype="multipart/form-data" class="wpc_form">
        <input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php esc_attr_e($nonce = wp_create_nonce('wpc_coupon_nounce')); ?>" />
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><?php _e('Title: ', 'wp-mihir-parmar');  ?> <span class="wpc_required">*</span></th>
                    <td>
                  <?php 

                        $error_exists = $wpc_correct_data && isset($wpc_correct_data['wpc_title']) ? $wpc_correct_data['wpc_title'] : '';

                        echo '<input type="text" name="wpc_title" class="wpc_field" value="'.$error_exists.'">';

                        if(!empty($errors) && isset($errors['wpc_title'])){
                            echo '<p class="wpc_error">' . esc_html__($errors['wpc_title'], 'wp-mihir-parmar') . '</p>';
                        }
                        else{ 
                            echo '<p>' . esc_html__('Enter the Coupon title.', 'wp-mihir-parmar') . '</p>';
                        }
                        ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Description:', 'wp-mihir-parmar'); ?> </th>
                    <td>
                        <textarea name="wpc_desc" class="wpc_field_textarea" cols="30" rows="3"><?php if($wpc_correct_data && isset($wpc_correct_data['wpc_desc']))
                         { echo $wpc_correct_data['wpc_desc']; } 
                         ?></textarea>                                                    
                            <p> <?php esc_html_e('Enter the Coupon description.', 'wp-mihir-parmar'); ?> </p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e('Coupon amount:', 'wp-mihir-parmar'); ?><span class="wpc_required"> *</span> </th>
                    <td>
                    <?php 
                        if($wpc_correct_data && isset($wpc_correct_data['amount_coupon'])){ 
                            echo '<input type="number" name="amount_coupon" checked value="'.$wpc_correct_data['amount_coupon'].'">';
                        }
                        else{
                            echo '<input type="number" name="amount_coupon" checked>';
                        }
                        
                        if(!empty($errors) && isset($errors['wpc_amount_coupon'] )){   
                            echo '<p class="wpc_error">' . esc_html__($errors['wpc_amount_coupon'], 'wp-mihir-parmar') . '</p>';
                        }
                        else{
                            echo '<p class="amount">'.esc_html__('Enter the Coupon amount.', 'wp-mihir-parmar').'</p>';
                        }
                        ?>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php _e('Coupon Image', 'wp-mihir-parmar'); ?> </th>
                    <td><?php
                    
                    $image_id = '';
                    $saved_image_id = isset($wpc_correct_data['wpc_img']) ? $wpc_correct_data['wpc_img'] : '' ; 
                    if ($saved_image_id && $image = wp_get_attachment_image_url($saved_image_id, 'medium')) { ?>

                    <div class="wpc-image-container">
                        <a href="#" class="wpc-upload">
                            <img src="<?php echo esc_url($image) ?>" />
                        </a>
                        <a href="#" class="wpc-remove"><?php _e('Remove image', 'wp-mihir-parmar'); ?></a>
                        <input type="hidden" name="wpc_img" value="<?php echo absint($saved_image_id) ?>">
                    </div>

                    <?php }
                    else{

                        if( $image = wp_get_attachment_image_url( $image_id, 'medium' ) && ($image_id !== null) ) { ?>
	                    <div class="wpc-image-container">
                            <a href="#" class="wpc-upload">
                                <img src="<?php echo esc_url( $image ) ?>" />
                            </a>
                            <a href="#" class="wpc-remove"><?php _e('Remove image', 'wp-mihir-parmar'); ?></a>
                            <input type="hidden" name="wpc_img" value="<?php echo absint( $image_id ) ?>">
                        </div>
                        <?php }else{ ?>
                            <div class="wpc-image-container">
                                <a href="#" class="button wpc-upload"><?php _e('Upload image', 'wp-mihir-parmar'); ?></a>
                                <a href="#" class="wpc-remove" style="display:none"><?php _e('Remove image', 'wp-mihir-parmar'); ?>
                            </a>
                                <input type="hidden" name="wpc_img" value="">
                            </div>
                        <?php }
                    } ?>
                    </td>
                        
                </tr>
                
                
                <tr valign="top">
                    <th scope="row"><?php _e('Category:', 'wp-mihir-parmar'); ?> </th>
                    <td>
                        <select name="wpc_category" id="">
                            <option value="mobile" <?php if($wpc_correct_data && isset($wpc_correct_data['wpc_category'])) {
                             selected( $wpc_correct_data['wpc_category'], 'mobile' ); }?> selected>
                             <?php _e('mobile', 'wp-mihir-parmar'); ?></option>
                            <option value="computer" <?php if($wpc_correct_data && isset($wpc_correct_data['wpc_category'])) {
                             selected( $wpc_correct_data['wpc_category'], 'computer' ); }?>>
                             <?php _e('computer', 'wp-mihir-parmar'); ?>
                            </option>
                            <option value="tablet" <?php if($wpc_correct_data && isset($wpc_correct_data['wpc_category'])) {
                             selected( $wpc_correct_data['wpc_category'], 'tablet' ); }?>>
                             <?php _e('tablet', 'wp-mihir-parmar'); ?>
                            </option>
                        </select>
                        <?php 
                        if(!empty($errors) && isset($errors['wpc_category'] )){   
                                echo '<p class="wpc_error">' . esc_html__($errors['wpc_category'], 'wp-mihir-parmar') . '</p>';
                        }
                        else{
                                echo '<p>' . esc_html__('Select the Coupon category.', 'wp-mihir-parmar') . '</p>';
                        }
                        ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Availability:', 'wp-mihir-parmar'); ?> </th>
                    <td>
                        <?php 

                        if($wpc_correct_data && isset($wpc_correct_data['av_options']['client']))
                        { 
                            $wpc_client_checked = checked($wpc_correct_data['av_options']['client'] == 'on', true, false);
                            echo '<div class="wpc_availability  wpc_availability_client">
                            <input type="checkbox" name="av_options[client]" ' . $wpc_client_checked . '> ' . __("Client", "wp-mihir-parmar")
                            .'</div>';
                        }
                        else
                        {
                            echo '<input type="checkbox" name="av_options[client]">'. __("Client","wp-mihir-parmar");
                        }


                        if($wpc_correct_data && isset($wpc_correct_data['av_options']['distributor']))
                        { 
                            $wpc_client_checked = checked($wpc_correct_data['av_options']['distributor'] == 'on', true, false);
                            echo '<div class="wpc_availability  wpc_availability_distributor">
                                <input type="checkbox" name="av_options[distributor]" ' . $wpc_client_checked . '> ' . __("Distributor", "wp-mihir-parmar").'
                                </div>';
                        }
                        else
                        {
                            echo '<input type="checkbox" name="av_options[distributor]" class="av_option_al"> '. __("Distributor","wp-mihir-parmar");;
                        }
                        ?>
                        <p class="description"><?php _e('Choose the Coupon availability.', 'wp-mihir-parmar'); ?></p>
                    </td>
                </tr>     
            </tbody>
        </table>
        <input class="button-primary wpc-submit" type="submit" value="<?php _e('Save', 'wp-mihir-parmar'); ?>" name="Save_Options">
    </form>
</div>