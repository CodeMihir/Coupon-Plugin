<?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'coupons_mihir_parmar';
    $id = $_GET["id"];
    
    if(isset($id) && !empty($id))
    {

        $coupons = $wpdb->get_results($wpdb->prepare("SELECT id,title,description,amount , image , category , availability from $table_name where id=%s", $id));
        $av_options =       unserialize($coupons[0]->availability);
        $wpc_title        = sanitize_text_field( $coupons[0]->title );
        $wpc_desc         = sanitize_text_field( $coupons[0]->description );
        $wpc_category     = isset( $coupons[0]->category) ? sanitize_text_field($coupons[0]->category) : 'off';
        $wpc_img          = absint($coupons[0]->image);
        $wpc_amount       = absint($coupons[0]->amount);
        $availability     = !empty($av_options) ? array_map('sanitize_text_field', $av_options) : 'off';
    }

    if (isset($_GET['success'])) {
        $success_message = $_GET['success'];
        echo '<div class="notice notice-success wpc-success-msg is-dismissible"><p> '.$success_message.' </p></div>';
    }
    if (isset($_GET['errors'])) {
        $errors_message = $_GET['errors'];
        _e('<div class="notice notice-error wpc-error-msg is-dismissible"><p> '.$errors_message.'</p></div>','wp-mihir-parmar');	
    }
    ?>
    <div class="wrap">
        <h2><?php _e('coupons','wp-mihir-parmar') ?> <a class="add-new-h2"
            href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=wpc-listing-coupons');?>"><?php _e('back to list', 'wp-mihir-parmar')?></a>
        </h2>


        <form method="post" enctype="multipart/form-data" action="admin-post.php" class="wpc_form">
        <input type="hidden" name="action" value="wpc_update_coupon">
        <input type="hidden" name="coupon_id" value="<?php echo $id; ?>">

    <input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php esc_attr_e($nonce = wp_create_nonce('wpc_coupon_updated_nounce')); ?>" />
    <table class="form-table" >
        <tbody>
            <tr valign="top">
                <th scope="row"><?php _e('Title','wp-mihir-parmar') ?> : 
                    <span class="wpc_required">*</span></th>
                <td>
                    <input type="text" name="wpc_title" class="wpc_field" value="<?php echo esc_attr($wpc_title); ?>">
                    <p><?php _e('Enter the Coupon title.','wp-mihir-parmar') ?></p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                <?php _e('Description','wp-mihir-parmar') ?> :
                </th>
                <td>
                    <textarea name="wpc_desc" id="" cols="30" rows="3" class="wpc_field_textarea"><?php echo esc_attr($wpc_desc); ?></textarea>
                    <p><?php _e('Enter the Coupon description.','wp-mihir-parmar') ?></p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                <?php _e('Coupon amount','wp-mihir-parmar') ?> :
                <span class="wpc_required">*</span></th>
                <td>
                    <input type="number" name="amount_coupon"  value="<?php echo esc_attr($wpc_amount); ?>">
                    <p class="description"><?php _e('Enter the Coupon amount.','wp-mihir-parmar') ?> :</p>
                </td>
            </tr>
            <tr>

                <th scope="row"><?php _e('Coupon Image', 'wp-mihir-parmar'); ?> </th>
                <td>
                <?php if ($wpc_img && $image = wp_get_attachment_image_url($wpc_img, 'medium')) { ?>
                 <div class="wpc-image-container">
                    <a href="#" class="wpc-upload">
                        <img src="<?php echo esc_url($image) ?>" />
                    </a>
                    <a href="#" class="wpc-remove"><?php _e('Remove image','wp-mihir-parmar') ?></a>
                    <input type="hidden" name="wpc_img" value="<?php echo absint($wpc_img) ?>">
                </div>
                <?php } else { ?>
                    <div class="wpc-image-container">
                        <a href="#" class="button wpc-upload"><?php _e('Upload image','wp-mihir-parmar') ?></a>
                        <a href="#" class="wpc-remove" style="display:none"><?php _e('Remove image','wp-mihir-parmar') ?>
                        </a>
                        <input type="hidden" name="wpc_img" value="">
                    </div>
                    <?php } ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Category: </th>
                <td>
                    <select name="wpc_category" id="">
                        <option value="mobile"  <?php echo $wpc_category == 'mobile' ? "selected" : "" ?> selected>
                        <?php _e('mobile','wp-mihir-parmar') ?></option>
                        <option value="computer" <?php echo $wpc_category == 'computer' ? "selected" : "" ?>>
                        <?php _e('computer','wp-mihir-parmar') ?></option>
                        <option value="tablet" <?php echo $wpc_category == 'tablet' ? "selected" : "" ?>>
                        <?php _e('tablet','wp-mihir-parmar') ?></option>
                    </select>
                    <p><?php _e('Select the Coupon category.','wp-mihir-parmar') ?></p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Availability ','wp-mihir-parmar') ?>: </th>
                <td>
                <input type="checkbox" name="av_options[client]" <?php echo isset($availability['client']) ? checked( true , true, true ) : ''; ?>> Client
                <input type="checkbox" name="av_options[distributor]" <?php echo isset($availability['distributor']) ? checked( true , true, true ) : ''; ?>>  <?php _e('Distributor','wp-mihir-parmar') ?>
                <p class="description"><?php _e('Choose the Coupon availability.','wp-mihir-parmar') ?></p>
                </td>
            </tr>
        </tbody>
    </table>
    <input class="button-primary wpc-submit" type="submit" value="<?php _e('update', 'wp-mihir-parmar'); ?>" name="Update_Options">
</form>
</div>