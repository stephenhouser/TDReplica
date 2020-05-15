<?php
/*
 * Customizations for TDReplica.com site and style.
 * June 2016, Stephen Houser <stephenhouser@gmail.com>
 */

/* 
 * Add local, TDReplica style.css to list of used style sheets. 
 */
add_action('wp_enqueue_scripts', 'tdr_enqueue_styles');
function tdr_enqueue_styles() {
    wp_enqueue_style('tdr-style', get_template_directory_uri() . '/style.css');
}

/* 
 * Add local, TDReplica admin-style.css to list of used style sheets for admin interface
 */
add_action('admin_enqueue_scripts', 'tdr_enqueue_admin_styles');
function tdr_enqueue_admin_styles() {
    wp_enqueue_style('tdr-admin-style', get_stylesheet_directory_uri() . '/admin-style.css');
}


/* 
 * Only show the admin bar when logged in
 */
add_filter( 'show_admin_bar', '__return_true' , 1000 );

add_action( 'wp_before_admin_bar_render', 'tdr_admin_bar_render' );
function tdr_admin_bar_render() {
    global $wp_admin_bar;
    if ( !is_user_logged_in() ) {
        global $wp_admin_bar;
    }
}        

/* 
 * Add MG Safety Fast logo to login screen 
 */
add_action( 'login_enqueue_scripts', 'tdr_login_logo' );
function tdr_login_logo() { 
	?>
		<style type="text/css">
			.login h1 a {
				background-image: url(/wp-content/uploads/MGSafetyFast.png) !important;
				padding-bottom: 30px;
			}
		</style>
	<?php 
}

/*
 * Add @mentionname after bbpress forum author details
 */
add_action( 'bbp_theme_after_reply_author_details', 'tdr_mentionname_to_bbpress' );
function tdr_mentionname_to_bbpress () {
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


/* 
 * Change FROM address and name for outgoing email 
 */
add_filter('wp_mail_from', 'tdr_new_mail_from');
function tdr_new_mail_from($old) {
	return 'webmaster@tdreplica.com';
}

add_filter('wp_mail_from_name', 'tdr_new_mail_from_name');
function tdr_new_mail_from_name($old) {
	return 'TDReplica.com Wordpress';
}


/*
 * Add user id to users list
 */
add_filter('manage_users_columns', 'tdr_add_user_id_column');
function tdr_add_user_id_column($columns) {
    $columns['user_id'] = 'User ID';
    return $columns;
}

add_action('manage_users_custom_column',  'tdr_show_user_id_column_content', 10, 3);
function tdr_show_user_id_column_content($value, $column_name, $user_id) {
    $user = get_userdata( $user_id );
        if ( 'user_id' == $column_name )
                return $user_id;
    return $value;
}

/*
 * Include any content from Activate page onto the activate page
 * ...I know, seems obvious but buddypress does not show it!
 */
add_action('bp_after_registration_confirmed', 'tdr_confirmed_text');
function tdr_confirmed_text() {
	$page = get_page_by_title('Activate');
	$content = apply_filters('the_content', $page->post_content);
	echo $content;
}

/* Activate
If you don't see the email, please check your SPAM or Junk folder first, then feel free to drop us an email for help at webmaster@tdreplica.com and we will try to get you registered and activated. Include your chosen username in your message to speed things along.

We look forward to seeing you on the forums and in our community!
*/

/*
 * Include any content from Register page onto the register page
 * ...I know, seems obvious but buddypress does not show it!
 */
add_action('bp_before_account_details_fields', 'tdr_registration_text');
function tdr_registration_text() {
	$page = get_page_by_title('Register');
	$content = apply_filters('the_content', $page->post_content);
	echo $content;
}

/* Register
Because our primary goal is the promotion of our hobby and the enjoyment of these great cars, we **do not collect dues** or have bunch of rules to follow (safety fast after all). To become a member, we only have three simple requests:

1. **Help us foster an active, supportive, and family friendly environment**. Always treat everyone with dignity and respect. We are not in competition for prizes or glory.

2. **Share, help, and support**. If someone gives you useful advice or help, return the favor and help someone else. If not for the support and advice of others, many of these cars would never see the road.  Where past kit car manufacturers lacked support and nearly destroyed the hobby, we fill in that gap and ensure others get to enjoy the full benefit of owning a fun little car.

3. **Have Fun**. We’ve received story after story about heart break and disappointment when a project doesn’t seem to work out as planned. Remain vigilant in the idea that this all about having fun and not for profit.

Website hosting is a relatively inexpensive cost and all development and maintenance is done strictly on members’ own time with no compensation.
*/

?>
