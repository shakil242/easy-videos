<?php
//Plugin Settings Page
add_action('admin_menu', 'easy_video_settings');
 
function easy_video_settings() {
    add_submenu_page(
        'edit.php?post_type=video',
        'Settings',
        'Settings',
        'manage_options',
        'easy_video_settings_page',
        'easy_video_settings_callback' );
}
 
function easy_video_settings_callback() {
        echo '<h2>API KEY</h2>';?>
        <?php

        if(isset($_GET['message'])){

?>
    <div class="notice notice-success is-dismissible" style="margin-left: 0; margin-bottom: 15px">
        <p><?php _e(  $_GET['message'], 'easyvideo' ); ?></p>
    </div>

<?php


        }
        ?>
        <form method="POST" id="" style="margin-bottom: 20px;" action="<?php echo admin_url( 'admin.php' ); ?>">
              <label for="esy-youtube-api-key">API Key <span class="description">(required)</span> &nbsp;</label>
<input type="text" name="api_key" class="regular-text" placeholder="AIzaSyC8yg6VK..." value="<?php echo get_option('easy_video_api_key'); ?>
">
				<input type="hidden" name="action"  value="ev_api_key_action">
               <input type="submit" value="Add Key"  class="button-primary">
        </form>
     
<?php


} 



add_action( 'admin_action_ev_api_key_action', 'ev_api_key_action_admin_action' );
function ev_api_key_action_admin_action()
{


	update_option('easy_video_api_key', $_POST['api_key']);

	$message = "API has been saved sussessfully";
    // Do your stuff here
    wp_redirect( $_SERVER['HTTP_REFERER'] .'&message='.$message );

    exit();
}

function easyvideo_plugin_settings_link($links) {

	$url = get_admin_url() . 'edit.php?post_type=video&page=easy_video_settings_page';
	$settings_link = '<a href="'.$url.'">' . __( 'Settings', 'easyvideo' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;

}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'easyvideo_plugin_settings_link' );







