<?php

if(!class_exists('wpc_settings'))
{
    class wpc_settings
    {
        public function __construct(){
            update_option( 'correct_data', array());
            add_action( 'admin_menu', array('wpc_settings','register_admin_page'));
            add_action( 'admin_post_wpc_update_coupon',  array('wpc_settings','admin_wpc_update_coupon'));

        }
        static function admin_wpc_update_coupon(){

            global $wpdb;
            $table_name = $wpdb->prefix . 'coupons_mihir_parmar';
            $id = $_POST['coupon_id'];

            if (isset($_POST['Update_Options'])) {
                $av_options = isset($_POST['av_options']) ? $_POST['av_options'] : array();
                $av_options = array_map('sanitize_text_field', $av_options);
                $av_options_serialized = serialize($av_options);
        
                if(empty($_POST['wpc_title'])){
                    wp_redirect( admin_url("admin.php?page=coupon-update&id=".$id."&errors=title cannot be empty") );
                    die();
                }
                else
                {
                    $wpc_duplicate = wpc_get_records($_POST['wpc_title']);
                    if($wpc_duplicate > 1){
                        wp_redirect( admin_url("admin.php?page=coupon-update&id=".$id."&errors=duplicate coupon found") );
                        die();
                    }
                }
                if(empty($_POST['amount_coupon'])){
                    wp_redirect( admin_url("admin.php?page=coupon-update&id=".$id."&errors=amount cannot be empty") );
                    die();
                }

                $result = $wpdb->update(
                        $table_name, //table
                        array(
                            'title'           => sanitize_text_field($_POST['wpc_title']),
                            'description'     => sanitize_text_field($_POST['wpc_desc']),
                            'amount'          => absint($_POST['amount_coupon']),
                            'image'           => sanitize_text_field($_POST['wpc_img']),
                            'category'        => sanitize_text_field($_POST['wpc_category']),
                            'availability'    => $av_options_serialized,
                        ),
                        array('id' => $id), //where
                        array('%s') //where format
                );
        
                if($result){
                    wp_redirect( admin_url("admin.php?page=wpc-listing-coupons&id=".$id."&success=coupon updated") );
                    exit;
                }
                else{
                    wp_redirect( admin_url("admin.php?page=coupon-update&id=".$id."&errors=please update one or more field") );
                    exit;
                }
                if ($wpdb->last_error) {
                    echo "Database Error: " . $wpdb->last_error;
                }
            }
        }
        
        static function register_admin_page() {

            add_menu_page( __('Products','wp-mihir-parmar'), __('Products','wp-mihir-parmar'), 'manage_options', 'wpc-products', array('wpc_settings','wpc_add_coupon'));
            add_submenu_page( 'wpc-products', __('Coupons','wp-mihir-parmar'), __('Coupons','wp-mihir-parmar'), 'manage_options', 'wpc-listing-coupons', array('wpc_settings','wpc_add_coupon') );
            add_submenu_page( 'wpc-products', __('Add Coupon','wp-mihir-parmar'), __('Add Coupon','wp-mihir-parmar'), 'manage_options', 'wpc-add-coupon', array('wpc_settings','wpc_admin_callback') );
            add_submenu_page(null, __('Update Coupon','wp-mihir-parmar'),__('Update Coupon','wp-mihir-parmar'), 'manage_options', 'coupon-update', array('wpc_settings','wpc_coupon_update'));
            remove_submenu_page('wpc-products', 'wpc-products');

        }
        static function wpc_coupon_update(){
            require_once  WPC_PLUGIN_DIR_PATH.'admin/coupon-update.php'; 
        }
        static function wpc_add_coupon(){
            if (!class_exists('WP_List_Table')) {  
                require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
            }
            require_once  WPC_PLUGIN_DIR_PATH.'admin/listing.php';              
            $table = new wpc_coupon_listing();
            $table->prepare_items();
            if (isset($_GET['success'])) {
                    $success_message = $_GET['success'];
                    _e('<div class="notice notice-success  is-dismissible"><p> '.$success_message.'</p></div>','wp-mihir-parmar');	
            } ?>
            <div class="wrap">
            <h2><?php _e('coupons', 'wp-mihir-parmar')?>
            <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=wpc-add-coupon');?>"><?php _e('Add new coupon ', 'wp-mihir-parmar')?></a>
            </h2>
                <form id="persons-table" method="GET">
                    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
                    <?php $table->display() ?>
                </form> 
            </div>
            <?php

        }
        static function wpc_admin_callback() {
            if(! current_user_can( 'administrator' ) && !current_user_can( 'manage_options' ) ){
                wp_die( __('You do not have sufficient permissions to access this page.', 'wp-mihir-parmar'));
            } 
            require_once WPC_PLUGIN_DIR_PATH.'admin/submit_data.php';
        }
    }
    new wpc_settings();
}