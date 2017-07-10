<?php
/*
 * Customizations for TDReplica.com site and style.
 * June 2016, Stephen Houser <stephenhouser@gmail.com>
 */

/* 
 * Add local, TDReplica style.css to list of used style sheets. 
 */
function my_theme_enqueue_styles() {
    $parent_style = 'twentyfourteen-style'; 
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/tdreplica.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

/* 
 * Only show the admin bar when logged in
 */
add_filter( 'show_admin_bar', '__return_true' , 1000 );

function mytheme_admin_bar_render() {
    global $wp_admin_bar;
    if ( !is_user_logged_in() ) {
        global $wp_admin_bar;
    }
}        

add_action( 'wp_before_admin_bar_render', 'mytheme_admin_bar_render' );

/* 
 * Add MG Safety Fast logo to login screen 
 */
function tdreplica_login_logo() { 
?>
    <style type="text/css">
        .login h1 a {
            background-image: url(/wp-content/uploads/MGSafetyFast.png) !important;
            padding-bottom: 30px;
        }
    </style>
<?php 
}
add_action( 'login_enqueue_scripts', 'tdreplica_login_logo' );

//* Add custom message to WordPress login page
function tdreplica_login_message( $message ) {
    if ( empty($message) ){
        //return "<p><strong>Welcome to SmallEnvelop. Please login to continue</strong></p>";
        return "<p align=\"center\"><strong>If you have not logged in since June 2016 your
        password will not work.</strong>
        <br/>
        Use <a href=\"/wp-login.php?action=lostpassword\">Lost your password</a> to set a new one.</p>";
    } else {
        return $message;
    }
}

add_filter( 'login_message', 'tdreplica_login_message' );

/*
 * Add @mentionname after bbpress forum author details
 */
function mentionname_to_bbpress () {
    $user = get_userdata( bbp_get_reply_author_id() );
    if ( !empty( $user->user_nicename ) ) {
        $user_nicename = $user->user_nicename;
        echo '<p class="bbp-user-nicename">@'.$user_nicename.'</p>';
        /*echo bbp_get_user_reply_count_raw(bbp_get_reply_author_id())." topics";
        echo bbp_get_user_reply_count_raw(bbp_get_reply_author_id())." replies";
        echo " since ".date("Y/m/d", strtotime(get_userdata(bbp_get_reply_author_id())->user_registered));   
        */
    }
}

add_action( 'bbp_theme_after_reply_author_details', 'mentionname_to_bbpress' );

/* 
 * Change FROM address and name for outgoing email 
 */
function new_mail_from($old) {
	return 'webmaster@tdreplica.com';
}
function new_mail_from_name($old) {
	return 'TDReplica.com Wordpress';
}

add_filter('wp_mail_from', 'new_mail_from');
add_filter('wp_mail_from_name', 'new_mail_from_name');

/*
 * Add user id to users list
 */
add_filter('manage_users_columns', 'pippin_add_user_id_column');
function pippin_add_user_id_column($columns) {
    $columns['user_id'] = 'User ID';
    return $columns;
}

add_action('manage_users_custom_column',  'pippin_show_user_id_column_content', 10, 3);
function pippin_show_user_id_column_content($value, $column_name, $user_id) {
    $user = get_userdata( $user_id );
        if ( 'user_id' == $column_name )
                return $user_id;
    return $value;
}
?>
