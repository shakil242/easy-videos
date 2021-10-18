<?php
add_action( 'admin_action_easy_import_video_form', 'easy_import_video_form_admin_action' );
function easy_import_video_form_admin_action()
{
    $videos_list = $_POST['video'];
    $created_posts = array();

    foreach ($videos_list as $key => $video) {
        if(!empty($video['id'])) {
            $term_id = check_category($video['category']);
            $title = wp_strip_all_tags($video['title']);
            $videoUrl = "https://www.youtube.com/watch?v=" . $video['id'];
            $imagePath = $video['image'];

            $post = array(
                'post_title' => $title,
                'post_content' => $videoUrl,
                'post_type' => 'video',
                'post_status' => 'publish',
            );

            // Insert the post into the database
            $post_id = wp_insert_post($post);

            wp_set_post_terms($post_id, $term_id, 'video_category');

            $attach_id = easyVideoUploadRemoteImageAndAttach($imagePath, $post_id);
            set_post_thumbnail($post_id, $attach_id);
        }
    }
    $url=   admin_url( "edit.php?post_type=video" );
  //  // Do your stuff here
    wp_redirect( $url );
    //exit();
}
function easyVideoUploadRemoteImageAndAttach($image_url, $parent_id){

    $image = $image_url;

    $get = wp_remote_get( $image );

    $type = wp_remote_retrieve_header( $get, 'content-type' );

    if (!$type)
        return false;

    $mirror = wp_upload_bits( basename( $image ), '', wp_remote_retrieve_body( $get ) );

    $attachment = array(
        'post_title'=> basename( $image ),
        'post_mime_type' => $type
    );

    $attach_id = wp_insert_attachment( $attachment, $mirror['file'], $parent_id );

    require_once(ABSPATH . 'wp-admin/includes/image.php');

    $attach_data = wp_generate_attachment_metadata( $attach_id, $mirror['file'] );

    wp_update_attachment_metadata( $attach_id, $attach_data );

    return $attach_id;

}
function check_category($term_name){

    $term = term_exists( $term_name, 'video_category' );

    if ( $term !== 0 && $term !== null ) {
        $term_id = $term['term_id'];

    }else{

        global $wpdb;

        $term_table = $wpdb->prefix . "terms";
        $taxonomy_table = $wpdb->prefix . "term_taxonomy";


        $wpdb->insert($term_table , array(
            'name' => $term_name,
            'slug' => sanitize_title($term_name),
            'term_group' => 0, // ... and so on
        ));

        $term_id =  $wpdb->insert_id;

        $wpdb->insert($taxonomy_table , array(
            'term_id' => $term_id,
            'taxonomy' => 'video_category',
        ));

    }
    return  @$term_id;

}