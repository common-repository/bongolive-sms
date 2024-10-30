<?php
/*
Plugin Name: Bongolive SMS
Plugin URI: http://wordpress.org/extend/plugins/bongolive-sms/
Description: Bongolive SMS lets you communicate with all of your customers, freinds and people who visit your page via mobile sms using <strong>Bongolive SMS services.</strong><br/> This plugin lets you quickly automate the process of communicating with subscribers and website visitors via sms. The plugin user must have a vendor account with Bongo Live. To create an account visit <a href="http://bongolive.co.tz/">Bongolive</a> and Sign-up as a vendor/broadcaster.<br/> Once complete follow instructions on the web page on how to purchase credits and get more sender names.<br/> Revisit your plugin and start sending SMS to all of your subscribers and other people.
Version: 1.0.5
Author: Michael Shaka
Author URI: http://profiles.wordpress.org/mikonasta007/
License: GPL2
*/

	//load_plugin_textdomain('bongolive-sms', 'wp-content/plugins/bongolive-sms/langs');

	global $wp_bongolive_db_version, $wpdb;
	$wp_bongolive_db_version = "1.0.0";
	define('DEFAULTGROUP','1');
	define('BALANCE','balance');
	define('SENDER','sender');
	define('DEFAULTGROUP_ID','1');
	define('SHORTURL_FIELD_NAME', 'Short URL');

	// The opening page once the plugin is installed
	function wp_bongolive_page() {

		if (function_exists('add_options_page')) {

			add_menu_page(__('Bongolive SMS', 'bongolive-sms'), __('Bongolive SMS', 'bongolive-sms'), 'manage_options', __FILE__, 'wp_bongolive_setting_page', plugin_dir_url( __FILE__ ).'/images/sms.png');
			add_submenu_page(__FILE__, __('SMS Setting', 'bongolive-sms'), __('SMS Setting', 'bongolive-sms'), 'manage_options', __FILE__, 'wp_bongolive_setting_page');
			add_submenu_page(__FILE__, __('Send SMS', 'bongolive-sms'), __('Send SMS', 'bongolive-sms'), 'manage_options', 'bongolive-sms/send', 'wp_bongolive_send_sms_page');
			add_submenu_page(__FILE__, __('View Subscribers', 'bongolive-sms'), __('View Subscribers', 'bongolive-sms'), 'manage_options', 'bongolive-sms/subscribe', 'wp_bongolive_subscribes_page');
			add_submenu_page(__FILE__, __('About Plugin', 'bongolive-sms'), __('About Plugin', 'bongolive-sms'), 'manage_options', 'bongolive-sms/about', 'wp_bongolive_about_setting_page');

		}

	}
	add_action('admin_menu', 'wp_bongolive_page');
	// check the options available in wordpress and instatiate an object of bongolive class
	// if the user has not provided any settings the object cannot be instantiated
	include_once("inc/bongolive.class.php");
	$obj = new bongolive;
	if ((get_option('choose_service')) == 'local'){
		if ((get_option('wp_bongolive_password')!= "") && (get_option('wp_bongolive_apikey')!= "") && (get_option('wp_bongolive_username')!= "") ){
			$obj->set_credentials();
		}	

	
		if ((get_option('wp_bongolive_password')!= "") && (get_option('wp_bongolive_apikey')!= "") && (get_option('wp_bongolive_username')!= "") && (isset($_GET['settings-updated']))){
			$obj->set_credentials();
			update_option('bongolive_sms_credits', $obj->get_account(BALANCE));
			update_option('sender_names',$obj->get_account(SENDER));
		}
	}elseif((get_option('choose_service')) == 'international'){
	
		if ((get_option('wp_bongolive_password')!= "") && (get_option('typed_sender_names')!= "") && (get_option('wp_bongolive_username')!= "") ){
			$obj->set_credentials();
		}	

	
		if ((get_option('wp_bongolive_password')!= "") && (get_option('typed_sender_names')!= "") && (get_option('wp_bongolive_username')!= "") && (isset($_GET['settings-updated']))){
			
			update_option('bongolive_sms_credits',"");
			update_option('sender_names',"");
			$obj->set_credentials();
		}
	}
	
// this function ADDS a shortcode which can be used to place the widget at any place on the theme
	function wp_bongolive_subscribes() {

		include_once("newsletter/form.php");

	}
	add_shortcode('bongolive', 'wp_bongolive_subscribes');
	
	
	// Create the components on the ADMIN menu Bar
	function wp_bongolive_menu() {

		global $wp_admin_bar;
		$get_last_credit = (int)get_option('bongolive_sms_credits');
// This menue Lets you see the available credits on the admin bar
		if(is_super_admin() || is_admin_bar_showing()) {

			if($get_last_credit) {

				global $obj;
				$wp_admin_bar->add_menu(array
					(
						'id'		=>	'wp-credit-sms',
						'title'		=>	'<img src="'.plugin_dir_url(__FILE__).'images/money_coin.png" align="bottom"/> ' . number_format($get_last_credit) . ' ' . $obj->unit,
						'href'		=>	get_bloginfo('url').'/wp-admin/admin.php?page=bongolive-sms/bongolive-sms.php'
					));
			}
			$wp_admin_bar->add_menu(array
				(
					'id'		=>	'wp-send-sms',
					'parent'	=>	'new-content',
					'title'		=>	'Bongolive SMS',
					'href'		=>	get_bloginfo('url').'/wp-admin/admin.php?page=bongolive-sms/send'
				));
		} else {
			return false;
		}
	}
	add_action('admin_bar_menu', 'wp_bongolive_menu');
	
	// a function to print out an ERROR if there are no credits.
	function wp_bongolive_enable() {
		global $obj;
		$get_bloginfo_url = get_admin_url() . "admin.php?page=bongolive-sms/bongolive-sms.php";
		$local_settings = false;
		$international_settings = false;
		// Check the sender name for international use
		
		if ((get_option('wp_bongolive_username') != "") && (get_option('typed_sender_names') != "") && (get_option('wp_bongolive_password') !="")){
			$international_settings = true;
		}
		if ((get_option('wp_bongolive_username') != "") && (get_option('wp_bongolive_apikey') != "") && (get_option('wp_bongolive_password') !="")){
			$local_settings = true;
		}		
		if ((!$local_settings || !$international_settings )&&(isset($_GET['settings-updated']))){
			echo '<div class="error"><p>'.__('All account fields must be completed', 'bongolive-sms').'</p></div>';
		}elseif (!get_option('bongolive_sms_credits') && (get_option('choose_service') == 'local')) {
		    echo '<div class="error"><p><img src="'.plugin_dir_url(__FILE__).'/images/exclamation.png" alt="Bottom" align="top"/> '.__('You dont have enough SMS credits check SMS settings or Internet connectivity', 'bongolive-sms').'</p></div>';
		}elseif(($obj->prep_sender_name(get_option('typed_sender_names'))) == ""){
		    echo "<div class='error'><p>" . __('You entered an invalid sender name', 'bongolive-sms') . "</div></p>";
		}elseif ((get_option('bongolive_sms_credits') > 0) && (isset($_GET['settings-updated'])) && (get_option('bongolive_sender') == "")){
			echo "<div class='updated'><p>" . __('Settings were successfully updated. You can now select a sender name and update', 'bongolive-sms') . "</div></p>";
		}elseif ((get_option('bongolive_sender') == "") &&(get_option('bongolive_sms_credits') > 0)){
			echo "<div class='error'><p>" . __('Please update your Sender Name in the Account Settings!', 'bongolive-sms') . "</div></p>";
		}elseif ((get_option('bongolive_sms_credits') > 0) && (isset($_GET['settings-updated'])) && (get_option('bongolive_sender') != "")){
			"<div class='updated'><p>" . __('All settings updated. You can now view settings and send SMS', 'bongolive-sms') . "</div></p>";
		}
		

	}
	// The wp_bongolive_enable function is deactivated for now
	if(!get_option('bongolive_sms_credits') || (isset($_GET['settings-updated'])) || (get_option('bongolive_sender') != "")) {

		add_action('admin_notices', 'wp_bongolive_enable');

	}

	function wp_bongolive_rightnow_content() {
		global $wpdb, $table_prefix;
		$users = $wpdb->get_var("SELECT COUNT(*) FROM {$table_prefix}bongolive_subscribes WHERE status = 1");
		echo "<tr><td class='b'><a href='".get_bloginfo('url')."/wp-admin/admin.php?page=bongolive-sms/subscribe'>".$users."</a></td><td><a href='".get_bloginfo('url')."/wp-admin/admin.php?page=bongolive-sms/subscribe'>".__('Active Subscribers', 'bongolive-sms')."</a></td></tr>";
	}
	add_action('right_now_content_table_end', 'wp_bongolive_rightnow_content');
	
// This function run when the Plugin ia activated

	function wp_bongolive_install() {
		global $wp_sms_db_version, $table_prefix, $wpdb;
		$subscribes_table= $table_prefix . "bongolive_subscribes";
		$groups_table = $table_prefix . "bongolive_groups";

		$create_subscribes_table = ("CREATE TABLE ".$subscribes_table."(
			ID int(10) NOT NULL auto_increment,
			date DATETIME,
			name VARCHAR(20),
			mobile VARCHAR(20) NOT NULL,
			status tinyint(1),
			group_ID int(10),
			PRIMARY KEY (ID)) CHARSET=utf8
		");
		$create_groups_table = ("CREATE TABLE ".$groups_table."(
			ID int(10) NOT NULL auto_increment,
			date DATETIME,
			group_name VARCHAR(20),
			PRIMARY KEY (ID)) CHARSET=utf8
		");

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($create_subscribes_table);
		dbDelta($create_groups_table);
		add_option('wp_bongolive_db_version', $wp_bongolive_db_version);
		add_option('bongolive_sms_credits','');
		add_option('tempo_activation','');
		add_option('tempo_msg','');
		add_option('sender_names','');
		add_option('numbers','');
		global $wp_bongolive_db_version, $wpdb; 
		$get_current_date = date('Y-m-d H:i:s' ,current_time('timestamp',0));
		$check_group= $wpdb->query("SELECT * FROM {$table_prefix}bongolive_groups WHERE group_name = 'General'");
		if (!$check_group){
		$query = $wpdb->query("INSERT INTO {$table_prefix}bongolive_groups (date, group_name) VALUES ('".$get_current_date."', 'General')");
		}
	}
	register_activation_hook(__FILE__,'wp_bongolive_install');
	//reset the temporary mobile and name options
	add_option('bongolive_add_mobile','');
	add_option('bongolive_add_subscribe','');
	update_option('tempo_msg','');
	update_option('numbers','');
	// Allows a Widget to be registered to the side bar
	function wp_bongolive_widget() {

		wp_register_sidebar_widget('wp_bongolive', __('Subscribe to Bongolive SMS', 'bongolive-sms'), 'wp_bongolive_subscribe_show_widget', array('description'	=>	__('Subscribe to SMS', 'bongolive-sms')));
		wp_register_widget_control('wp_bongolive', __('Subscribe to Bongolive SMS', 'bongolive-sms'), 'wp_bongolive_subscribe_control_widget');

	}
	add_action('plugins_loaded', 'wp_bongolive_widget');
	// Displays the Widget for allowing blog viewers to subscribe
	function wp_bongolive_subscribe_show_widget($args) {

		extract($args);
			$title = get_option('wp_bongolive_widget_name');
			echo $before_title.$title.$after_title;
			include("newsletter/form.php");

	}

	function wp_bongolive_subscribe_control_widget() {

		if($_POST['wp_bongolive_submit_widget']) {
			update_option('wp_bongolive_widget_name', $_POST['wp_bongolive_widget_name']);
		}

		include_once('widget.php');

	}
// Enable a meta box for sending posts to Subscribers
	function wp_bongolive_subscribe_meta_box() {
		add_meta_box('subscribe-meta-box', __('Bongolive SMS', 'bongolive-sms'), 'wp_bongolive_subscribe_post', 'post', 'normal', 'high');
		
	}
	add_action('add_meta_boxes', 'wp_bongolive_subscribe_meta_box');

		

	function wp_bongolive_subscribe_post($post) {

		$values = get_post_custom($post->ID);
		$selected = isset( $values['subscribe_post'] ) ? esc_attr( $values['subscribe_post'][0] ) : '';
		wp_nonce_field('subscribe_box_nonce', 'meta_box_nonce');

		include_once('setting/meta-box.php');
	}

	function wp_bongolive_subscribe_post_save($post_id) {

		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if(!isset($_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'], 'subscribe_box_nonce')) return;
		if(!current_user_can('edit_post')) return;

		if( isset( $_POST['subscribe_post'] ) ){
		update_post_meta($post_id, 'subscribe_post', esc_attr($_POST['subscribe_post']));
		}
	}
	add_action('save_post', 'wp_bongolive_subscribe_post_save');

	function wp_bongolive_subscribe_send($post_ID) {
		$sending_method = get_option('choose_service');
		if( !strstr($_POST['_wp_bongolive_http_referer'], "action=edit")) {
			if((get_post_meta($post_ID, "subscribe_post", true) == 'yes')) {
				global $wpdb, $table_prefix, $obj;
				$number_to_send = $wpdb->get_col("SELECT mobile FROM {$table_prefix}bongolive_subscribes WHERE status = 1");
				//$shortlink = get_permalink( $post_ID );
                $obj->set_credentials();
				$shortlink = wp_get_shortlink($post_ID);
				if ($shortlink == ""){
					$shortlink = get_permalink( $post_ID );
				}
				$obj->msg = get_the_title($post_ID).' '.$shortlink;
				foreach( $number_to_send as $mobile){
					$reciever[] = $obj->check_mobile($mobile);						
				}
                
                /*if ($sending_method == 'international'){
					
					$obj->sender = get_option('typed_sender_names');	
				}*/
				foreach ($reciever as $mobile){
					$obj->to = $mobile;
					if ($sending_method == 'local'){
						$response = $obj->send_sms();
					}else{
						$response = $obj->send_sms_international();
					}
					
				}
				if ($sending_method == 'local'){
					update_option('bongolive_sms_credits', $obj->get_account(BALANCE));	
				}
				
				return $post_ID;
			}
		}

	}
	add_action('publish_post', 'wp_bongolive_subscribe_send');


	/*if(get_option('wp_bongolive_notification_new_version')) {

		$update = get_site_transient('update_core');
		$update = $update->updates;
		
		if($update[1]->current > $wp_bongolive_version) {

			if(get_option('wp_last_send_notification') == false) {

				$obj->to = array(get_option('wp_bongolive_admin_mobile'));
				$obj->msg = sprintf(__('WordPress %s is available! Please update now', 'bongolive-sms'), $update[1]->current);

				$obj->send_sms();

				update_option('wp_last_send_notification', true);

			}
		} else {
			update_option('wp_last_send_notification', false);
		}
	}*/

	function wp_bongolive_setting_page() {
		global $obj;

		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));

			settings_fields('wp_options');
		}
		include_once("inc/bongolive.class.php");
		$obj = new bongolive;
		
		
		if ((get_option('choose_service')) == 'local'){
			if ((get_option('wp_bongolive_password')!= "") && (get_option('wp_bongolive_apikey')!= "") && (get_option('wp_bongolive_username')!= "") ){
				$obj->set_credentials();
				update_option('bongolive_sms_credits', $obj->get_account(BALANCE));
				update_option('sender_names',$obj->get_account(SENDER));
				//update_option('typed_sender_names',"");
			}	

		
			if ((get_option('wp_bongolive_password')!= "") && (get_option('wp_bongolive_apikey')!= "") && (get_option('wp_bongolive_username')!= "") && (isset($_GET['settings-updated']))){
				//$obj->set_credentials();
				//update_option('bongolive_sms_credits', $obj->get_account(BALANCE));
				//update_option('sender_names',$obj->get_account(SENDER));
			}
		}elseif((get_option('choose_service')) == 'international'){
		
			if ((get_option('wp_bongolive_password')!= "") && (get_option('typed_sender_names')!= "") && (get_option('wp_bongolive_username')!= "") ){
				$obj->set_credentials();
			}	

		
			if ((get_option('wp_bongolive_password')!= "") && (get_option('typed_sender_names')!= "") && (get_option('wp_bongolive_username')!= "") && (isset($_GET['settings-updated']))){
				
				update_option('bongolive_sms_credits',"");
				//update_option('bongolive_sender',"");
				//update_option('sender_names',"");
				$obj->set_credentials();
			}
		}
		include_once('setting/setting.php');
	
	}

	function wp_bongolive_send_sms_page() {
		global $obj;
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		include_once('setting/send-sms.php');
	}

	function wp_bongolive_subscribes_page() {
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}

		$name = trim($_POST['new_bongolive_subscribe_name']);
		$mobile = trim($_POST['new_bongolive_subscribe_mobile']);
		$group = trim($_POST['new_bongolive_group_name']);
		$date = date('Y-m-d H:i:s' ,current_time('timestamp',0));
		update_option('bongolive_add_subscribe',$name);
		update_option('bongolive_add_mobile',$mobile);
		
		if ($_POST['new_bongolive_subscribe_group']){
			if (($group) && (strlen($group) <= 15)){
				global $wpdb, $table_prefix;
				$check_group = $wpdb->query("SELECT group_name FROM {$table_prefix}bongolive_groups WHERE groups='".$group."'");
				if(!$check_group) {
					$check = $wpdb->query("INSERT INTO {$table_prefix}bongolive_groups (date, group_name) VALUES ('".$date."','".ucfirst($group)."')");
					if ($check){
					echo "<div class='updated'><p>" . __('Group was successfully created. Select to a group to add members', 'bongolive-sms') . "</div></p>";
					} 
				}else{
					echo "<div class='error'><p>" . __('Group already exists', 'bongolive-sms') . "</div></p>";
				}
			}else{
			echo "<div class='error'><p>" . __('Enter a valid group name not more than 15 characters', 'bongolive-sms') . "</div></p>";
			}
		}
		
		if($_POST['new_bongolive_subscribe']) {
			if($name && $mobile) {
			global $obj;
				if( $mobile = $obj->check_mobile($mobile)){
					global $wpdb, $table_prefix;
					
					$check_mobile = $wpdb->query("SELECT mobile FROM {$table_prefix}bongolive_subscribes WHERE mobile='".$mobile."'");

					if(!$check_mobile) {
						$check = $wpdb->query("INSERT INTO {$table_prefix}bongolive_subscribes (date, name, mobile, status, group_ID) VALUES ('".$date."', '".$name."', '".$mobile."', '1','".$_GET['group_id']."')");

						if($check) {
							echo "<div class='updated'><p>" . __('Subscriber added to the Group', 'bongolive-sms') . "</div></p>";
							update_option('bongolive_add_mobile',"");
							update_option('bongolive_add_subscribe',"");
						}
					} else {
						echo "<div class='error'><p>" . __('Phone number is repeated', 'bongolive-sms') . "</div></p>";
						
					}
				} else {
					echo "<div class='error'><p>" . __('Please enter a valid mobile number', 'bongolive-sms') . "</div></p>";
					
				}
			} else {
				echo "<div class='error'><p>" . __('Please complete all fields', 'bongolive-sms') . "</div></p>";
			}
		}

		if($_POST['subscribeaction'] || $_POST['groupaction']) {
			global $wpdb, $table_prefix;
		if (isset($_POST['column_ID']) && $_POST['column_ID'] != ""){
			$get_IDs = implode(",", $_POST['column_ID']);
			if ($_POST['subscribeaction']){
				$where = "ID IN (".$get_IDs.")";
			}else{
				$where = "group_ID IN (".$get_IDs.")";
			}
			$check_ID = $wpdb->query("SELECT * FROM {$table_prefix}bongolive_subscribes WHERE ". $where);

			switch($_POST['action']) {
				case 'trash':
					if($check_ID) {
						$wpdb->query("DELETE FROM {$table_prefix}bongolive_subscribes WHERE ".$where);
						// if he has also selected the group then delete the group
						if ($_POST['groupaction']){
						//this is to make sure he doesnt delete the default group
							$id_array = explode(',',$get_IDs);
							$new_IDs = array();
							foreach($id_array as $id){
								if ($id == 1){
									continue;
								}
								$new_IDs[] = $id;
							}
							$get_IDs = implode(",",$new_IDs);
							$wpdb->query("DELETE FROM {$table_prefix}bongolive_groups WHERE ID IN (".$get_IDs.")");
						}
						echo "<div class='updated'><p>" . __('Subscriber successfully deleted', 'bongolive-sms') . "</div></p>";
					} else {
						echo "<div class='error'><p>" . __('Please check to select a row', 'bongolive-sms') . "</div></p>";
					}
				break;

				case 'active':
					if($check_ID) {
						$wpdb->query("UPDATE {$table_prefix}bongolive_subscribes SET `status` = '1' WHERE ".$where);
						echo "<div class='updated'><p>" . __('Subscriber Activated.', 'bongolive-sms') . "</div></p>";
					} else {
						echo "<div class='error'><p>" . __('Please Check to select a row', 'bongolive-sms') . "</div></p>";
					}
				break;

				case 'deactive':
					if($check_ID) {
						$wpdb->query("UPDATE {$table_prefix}bongolive_subscribes SET `status` = '0' WHERE ".$where);
						echo "<div class='updated'><p>" . __('Subscriber De-activated.', 'bongolive-sms') . "</div></p>";
					} else {
						echo "<div class='error'><p>" . __('Please check to select a row', 'bongolive-sms') . "</div></p>";
					}
				break;
			}

		}
	}
		include_once('setting/subscribes.php');
	}

	function wp_bongolive_about_setting_page() {
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}

		include_once('setting/about.php');
	}
?>