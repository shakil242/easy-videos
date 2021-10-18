<?php
/*
Plugin Name: Easy Video
Plugin URI: https://easyvideo.com/
Description: Plugin developed by shakeel ahmed for test task.
Version: 1.0
Author: Shakeel Ahmed
Author URI: https://easyvideo.com/
License: GPLv2 or later
Text Domain: easyvideo
*/


// Exit if accessed directly /
if ( ! defined( 'ABSPATH' ) ) exit;

// ---------------------------------------------------------
// Define Plugin Folders Path
// ---------------------------------------------------------
define( "EV_PLUGIN_PATH", plugin_dir_path( __FILE__ ) );
define( "EV_PLUGIN_URL", plugin_dir_url( __FILE__ ) );



// Registering Custom Post Type Video
add_action( 'init', 'easy_youtube_videos', 20 );
function easy_youtube_videos() {
    $labels = array(

        'name' => __( 'Easy Videos' , 'easyvideo' ),

        'singular_name' => __( 'Easy Video' , 'easyvideo' ),

        'add_new' => __( 'New Easy Video' , 'easyvideo' ),

        'add_new_item' => __( 'Add New Easy Video' , 'easyvideo' ),

        'edit_item' => __( 'Edit Easy Video' , 'easyvideo' ),

        'new_item' => __( 'New Easy Video' , 'easyvideo' ),

        'view_item' => __( 'View Easy Video' , 'easyvideo' ),

        'search_items' => __( 'Search Easy Video' , 'easyvideo' ),

        'not_found' =>  __( 'No Easy Video Found' , 'easyvideo' ),

        'not_found_in_trash' => __( 'No Easy Video found in Trash' , 'easyvideo' ),

    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'description' => 'Easy Youtube Video',
        'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'post-formats', 'custom-fields' ),
        'taxonomies' => array( 'video_category'),
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-video-alt3',
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => array( 'slug' => 'videos' ),
        'public' => true,
        'has_archive' => 'video_category',
        'capability_type' => 'post',
        'show_in_rest' => true,
    );
    register_post_type( 'video', $args ); // max 20 character cannot contain capital letters and spaces
}

function easy_youtube_videos_taxonomy() {
    register_taxonomy(
        'video_category',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
        array('video'),
        array(
            'hierarchical' => true,
            'public' => true,
            'label' => 'Video Category',
            'show_admin_column' => true,
            'show_ui'           => true,
            'show_in_rest' => true,
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'video_category',    // This controls the base slug that will display before each term
                'with_front' => false  // Don't display the category base before
            )
        )
    );
}
add_action( 'init', 'easy_youtube_videos_taxonomy');

//---------- Create Video Import Page

add_action('admin_menu', 'easy_youtube_videos_register_import_page');
function easy_youtube_videos_register_import_page() {
    add_submenu_page(
        'edit.php?post_type=video',
        'Easy Youtubed Video Import',
        'Import Videos',
        'manage_options',
        'easy-youtube-import-videos',
        'easy_youtube_videos_register_import_page_callback'

    );
}

function easy_youtube_videos_register_import_page_callback() { ?>

    <h2><?php esc_attr_e( 'Easy Youtube Videos', 'easyvideo' ); ?></h2>

    <div class="wrap">

        <div id="icon-options-general" class="icon32"></div>
        <h1><?php esc_attr_e( 'Import Youtube Videos', 'easyvideo' ); ?></h1>

        <div id="poststuff">

            <div id="post-body" class="metabox-holder">

                <!-- main content -->
                <div id="post-body-content">

                    <div class="meta-box-sortables ui-sortable">

                            <div class="inside">

                                <form method="POST" id="easy_import_videos" style="margin-bottom: 20px;">
                                    <label for="esy-youtube-api">Search Video Title <span class="description">(required)</span> &nbsp;</label>
                                    <input type="text" name="video_query" class="regular-text" />
                                    <input type="hidden" name="easy_video_import_ajax_nonce" id="easy-video-import-ajax-nonce" value="<?php echo wp_create_nonce( 'video_import_nonce' ); ?>" />
                                    <input type="submit" value="Import!" id="import_videos" class="button-primary"  />

                                </form>


                                <form method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
                                <table class="widefat fixed" cellspacing="0">
                                    <thead>
                                    <tr>
                                        <td id="cb" class="manage-column column-cb check-column">
                                            <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
                                            <input id="cb-select-all-1" type="checkbox">
                                        </td>
                                        <th scope="col" id="image" class="manage-column column-image column-primary">Image</th>
                                        <th scope="col" id="title" class="manage-column column-title">Title</th>
                                        <th scope="col" id="title" class="manage-column column-title">Category</th>

                                    </tr>
                                    </thead>

                                    <tbody class="video_search_results">

                                    </tbody>


                                </table>
                                    <input type="hidden" name="action" value="easy_import_video_form" />
                                    <input type="submit" class="button-primary" style="margin-top: 20px;">
                                </form>
                            </div>
                            <!-- .inside -->


                    </div>
                    <!-- .meta-box-sortables .ui-sortable -->

                </div>
                <!-- post-body-content -->


                <!-- #postbox-container-1 .postbox-container -->

            </div>
            <!-- #post-body .metabox-holder .columns-2 -->

            <br class="clear">
        </div>
        <!-- #poststuff -->

    </div> <!-- .wrap -->

<?php }
add_action( 'admin_footer', 'easy_video_import_admin_ajax' ); // Write our JS below here

function easy_video_import_admin_ajax() { ?>
    <script type="text/javascript" >
        var $ = jQuery;

        $(document).on('click', '#import_videos', function(e) {
                e.preventDefault();
                var formData = $('#easy_import_videos').serialize(); //serialized
                $.ajax({
                    url: '<?php echo site_url(); ?>/wp-admin/admin-ajax.php', // <-- point to server-side PHP script
                    data: formData + '&action=easy_video_import_videos_funtion', //this was the problem
                    type: 'post',
                    success: function(result){
                        $('.video_search_results').html(result);
                        console.log(result);

                    },
                    error: function(result) {
                        alert("some error");
                    }
                });
            });



    </script> <?php
}

//-------------- end form ----

function easy_video_import_videos_funtion(){

    if ( ! isset( $_POST['easy_video_import_ajax_nonce'] )
        || ! wp_verify_nonce( $_POST['easy_video_import_ajax_nonce'], 'video_import_nonce' )
    ) {
        print 'Sorry, your nonce did not verify.';
        exit;
    } else {
//

        $videoQuery = $_POST['video_query'];

        $get_api_key = get_option('easy_video_api_key');

        $curlURL = 'https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults=20&q='.$videoQuery.'&type=video&key='.$get_api_key;

        $curl = curl_init($curlURL);
        curl_setopt($curl, CURLOPT_URL, $curlURL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $data = curl_exec($curl);
        curl_close($curl);

        $results = json_decode($data, true);
//        echo '<pre>';
//        print_r($results);
//        echo '</pre>';
//        exit;


        $html ='';
        $count = 1;
        foreach ($results['items'] as $item)
        {
           $title = $item['snippet']['title'];
           $category =  $item['snippet']['channelTitle'];
           $imageUrl = $item['snippet']['thumbnails']['default']['url'];
           $videoId = $item['id']['videoId'];
           $videoUrl = "https://www.youtube.com/watch?v=".$item['id']['videoId'];


            $html .='<tr>';
            $html .='<td scope="row" class="check-column"> <input style="margin-left: 10px;" id="video_'.$videoId.'" type="checkbox" name="video['.$count.'][id]" value="'.$videoId.'"></td>';
            $html .='<td class="image column-image has-row-actions column-primary" data-colname="Image"><a href="'.$videoUrl.'" target="_blank"><img src="'.$imageUrl.'"></a> <input type="hidden" name="video['.$count.'][image]" value="'.$imageUrl.'"></td>';
            $html .='<td class="title column-title" data-colname="Title"><a href="'.$videoUrl.'" target="_blank">'.$title.'</a><input type="hidden" name="video['.$count.'][title]" value="'.$title.'"></td>';
            $html .='<td class="category column-category" data-colname="category"><a href="'.$videoUrl.'" target="_blank">'.$category.'</a><input type="hidden" name="video['.$count.'][category]" value="'.$category.'"> </td>';
            $html .='</tr>';
            $count++;
        }

        echo $html;

        die();


    }
}
// creating Ajax call for WordPress
add_action( 'wp_ajax_nopriv_easy_video_import_videos_funtion', 'easy_video_import_videos_funtion' );
add_action( 'wp_ajax_easy_video_import_videos_funtion', 'easy_video_import_videos_funtion' );

function easy_youtube_videos_register_settings() {
    add_option( 'easy_youtube_videos_option_name', 'This is my option value.');
    register_setting( 'easy_youtube_videos_options_group', 'easy_youtube_videos_option_name', 'easy_youtube_videos_callback' );
}
add_action( 'admin_init', 'easy_youtube_videos_register_settings' );
function easy_youtube_videos_register_options_page() {
    add_submenu_page('Page Title', 'Plugin Menu', 'manage_options', 'easy_youtube_videos', 'easy_youtube_videos_options_page');
}
add_action('admin_menu', 'easy_youtube_videos_register_options_page');




// include necessary files
require_once EV_PLUGIN_PATH . 'includes/save_imported_videos.php';
require_once EV_PLUGIN_PATH . 'includes/search_videos.php';
require_once EV_PLUGIN_PATH . 'includes/settings.php';