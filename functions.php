<?php
/*
 * Customizations for TDReplica.com site and style.
 * June 2016, Stephen Houser <stephenhouser@gmail.com>
 */

/* 
 * Add local, TDReplica style.css to list of used style sheets. 
 */
add_action( 'wp_enqueue_scripts', 'tdr_enqueue_styles' );
function tdr_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
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
 * Include any content from Register page onto the register page
 * ...I know, seems obvious but buddypress does not show it!
 */
add_action('bp_before_account_details_fields', 'tdr_registration_text');
function tdr_registration_text() {
	$page = get_page_by_title('Register');
	$content = apply_filters('the_content', $page->post_content);
	echo $content;
}

/*
 * Add a secret invitation code to the register page.
 * This is to help prevent spammers. Unless you enter the secret
 * pass phrase you cannot register as a new user.
 */
$invitation_code = 'tdreplica';

/* Add fields to the registration page, and show any errors */ 
add_action('bp_account_details_fields', 'registration_invitation', 10);
function registration_invitation() {
	$invitation = (!empty($_POST['signup_invitation'])) ? sanitize_text_field($_POST['signup_invitation'] ) : '';
	?>
		<label for="signup_invitation">
			<?php _e('Invitation Code', 'registration_invitation') ?> 
			<?php _e( '(required)', 'registration_invitation' ); ?>
		</label>		
		<?php do_action('bp_signup_invitation_errors'); ?>
		<input type="text" name="signup_invitation" id="signup_invitation" 
				class="input" value="<?php echo esc_attr($invitation); ?>" size="25" />
	<?php
}

/* Validate the invitation code in Buddypress' signup process, return errors if failed */
add_action('bp_signup_validate', 'registration_invitation_validate');
function registration_invitation_validate() {
	global $invitation_code;
	$bp = buddypress();
	if (empty($_POST['signup_invitation']) || (!empty($_POST['signup_invitation']) && trim($_POST['signup_invitation']) != $invitation_code)) {
    	$bp->signup->errors['signup_invitation'] = __('You must include a correct invitation code.', 'registration_invitation');
	}
}
?>
