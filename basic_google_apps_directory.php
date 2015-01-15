<?php

/**
 * Plugin Name: Google Apps Directory
 * Plugin URI: http://wp-glogin.com/directory/
 * Description: Search your Google Apps domain for employee info from a widget
 * Version: 1.2
 * Author: Dan Lester
 * Author URI: http://wp-glogin.com/
 * License: GPL3
 */

class basic_google_apps_directory {
	
	protected $PLUGIN_VERSION = '1.1';
	
	// Singleton
	private static $instance = null;
	
	public static function get_instance() {
		if (null == self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	// Constructor
	protected function __construct() {
		$this->add_actions();
	}
	
	// Register with GAL
	
	public function gad_gather_serviceacct_reqs($reqs_array) {
		$reqs_array[] = array('Google Apps Directory', 
								array('https://www.googleapis.com/auth/admin.directory.user.readonly'
										 => 'Search for user information in your domain'));
		return $reqs_array;
	}
	
	// Handle AJAX etc
	// ***************

	public function gad_directory_search() {
		if (!isset($_POST['gad_nonce']) || !wp_verify_nonce($_POST['gad_nonce'], 'gad-nonce')) {
			die (json_encode(Array('error'=>'No permission to make AJAX call')));
		}
		
		if (!isset($_POST['gad_search']) || $_POST['gad_search'] == '') {
			die (json_encode(Array('error'=>'Please specify a search string')));
		}
		$searchstr = $_POST['gad_search'];
		
		$outdata = array();
		$msg = '';
		
		if (!function_exists('GoogleAppsLogin')) {
			$msg = "Google Apps Login plugin needs to be activated and configured";
			die (json_encode(Array('error' => $msg)));
		}
		 
		try {
			$gal = GoogleAppsLogin();
			
			if (!method_exists($gal, 'get_Auth_AssertionCredentials')) {
				throw new Exception('Requires version 2.5+ of Google Apps Login');
			}
		
			$cred = $gal->get_Auth_AssertionCredentials(
					array('https://www.googleapis.com/auth/admin.directory.user.readonly'));
				
			$serviceclient = $gal->get_Google_Client();
				
			$serviceclient->setAssertionCredentials($cred);
		
			// Include paths were set when client was created
			if (!class_exists('GoogleGAL_Service_Directory')) {
				require_once( 'Google/Service/Directory.php' );
			}
				
			$userservice = new GoogleGAL_Service_Directory($serviceclient);
				
			$nextToken = '';
				
			do {
					
				$usersresult = $userservice->users->listUsers(Array('query' => $searchstr, 'customer' => 'my_customer',
						'maxResults' => 10, 'pageToken' => $nextToken));
		
				$usersdata = $usersresult->getUsers();
					
				foreach ($usersdata as $u) {
					$user_outdata = array(
						'primaryEmail' => $u->getPrimaryEmail(),
						'fullName' => $u->name->getFullName(),
						'givenName' => $u->name->getGivenName(),
						'familyName' => $u->name->getFamilyName(),
						'thumbnailPhotoUrl' => $u->getThumbnailPhotoUrl()
					);
					$user_outdata = apply_filters('gad_extract_user_data', $user_outdata, $u);
					$outdata[] = $user_outdata;
				}
		
				$nextToken = $usersresult->getNextPageToken();
					
			} while ($nextToken);
		
		} catch (GoogleGAL_Service_Exception $ge) {
			$errors = $ge->getErrors();
			$doneerr = false;
			if (is_array($errors) && count($errors) > 0) {
				if (isset($errors[0]['reason'])) {
					switch ($errors[0]['reason']) {
						case 'insufficientPermissions':
							$msg = 'User had insufficient permission to fetch Google User data';
							$doneerr = true;
							break;
		
						case 'accessNotConfigured':
							$msg = 'You need to enable Admin SDK for your project in Google Cloud Console';
							$doneerr = true;
							break;
							
						case 'forbidden':
							$msg = 'Forbidden - are you sure the user you entered in Service Account settings is a Google Apps admin?';
							$doneerr = true;
							break;
					}
				}
			}
				
			if (!$doneerr) {
				$msg = 'Service Error fetching Google Users: '.$ge->getMessage();
			}
				
		} catch (GoogleGAL_Auth_Exception $ge) {
			$error = $ge->getMessage();
			if (preg_match('/Error refreshing the OAuth2 token.+invalid_grant/s', $error)) {
				/*
				 * When keys don't match etc
				* Error refreshing the OAuth2 token, message: '{ "error" : "invalid_grant" }'
				*/
				$msg = 'Error - please check your private key and service account email are correct in Settings -> Google Apps Login (Service Account settings)';
			}
			else if (preg_match('/Error refreshing the OAuth2 token.+unauthorized_client/s', $error)) {
				/*
				 * When sub is wrong
				* Error refreshing the OAuth2 token, message: '{ "error" : "unauthorized_client", "error_description" : "Unauthorized client or scope in request." }'
				*/
				$msg = 'Error - please check you have named a Google Apps admin\'s email address in Settings -> Google Apps Login (Service Account settings)';
			}
			else if (preg_match('/Error refreshing the OAuth2 token.+access_denied/s', $error)) {
				/*
				 * When scope not entered
				* Google Auth Error fetching Users: Error refreshing the OAuth2 token, message: '{
 				* "error" : "access_denied", "error_description" : "Requested client not authorized."}'
				*/
				$msg = 'Error - please check you have added the required permissions scope to your Google Cloud Console project. See Settings -> Google Apps Login (Service Account settings).';
			}
			else {
				$msg = "Google Auth Error fetching Users: ".$ge->getMessage();
			}
		}
		catch (GAL_Service_Exception $e) {
			$msg = "GAL Error fetching Google Users: ".$e->getMessage();
		}
		catch (Exception $e) {
			$msg = "General Error fetching Google Users: ".$e->getMessage();
		}
		
		if ($msg == '') {
			echo json_encode(Array('users'=>$outdata));
		}
		else {
			echo json_encode(Array('error' => $msg));
			error_log($msg);
		}
		die();
	}
	
	// HOOKS AND FILTERS
	// *****************
	
	protected function add_actions() {
		add_action('init', array($this, 'gad_init'));
		add_action('admin_init', array($this, 'gad_admin_init'));
		add_action('widgets_init', array($this, 'gad_widgets_init'));
		add_action('wp_ajax_gad_directory_search', array($this, 'gad_directory_search'));
		add_filter('gal_gather_serviceacct_reqs',  array($this, 'gad_gather_serviceacct_reqs'));
	}

	public function gad_widgets_init() {
		require_once( plugin_dir_path(__FILE__).'/core/directory_widget.php' );
		register_widget( 'GAD_Widget' );
	}
	
	public function gad_init() {
		wp_register_script( 'gad_widget_js', $this->my_plugin_url().'js/gad-widget.js', array('jquery') );
		wp_register_style( 'gad_widget_css', $this->my_plugin_url().'css/gad-widget.css' );
	}
	
	public function gad_admin_init() {
		// Check Google Apps Login is configured - display warnings if not
		if (apply_filters('gal_get_clientid', '') == '') {
			add_action('admin_notices', Array($this, 'gad_admin_auth_message'));
			if (is_multisite()) {
				add_action('network_admin_notices', Array($this, 'gad_admin_auth_message'));
			}
		}
		else {
			// Is service account configured?
		}
		
	}
	
	public function gad_admin_auth_message() {
	?>
		<div class="error">
        	<p>You will need to install and configure 
        		<a href="http://wp-glogin.com/google-apps-login-premium/?utm_source=Admin%20Configmsg&utm_medium=freemium&utm_campaign=Directory" 
        		target="_blank">Google Apps Login</a>  
        		plugin in order for Google Apps Directory to work. (Requires v2.5+ of Free or Professional)
        	</p>
    	</div> <?php
	}
		
	
	// AUX
	
	public function my_plugin_basename() {
		$basename = plugin_basename(__FILE__);
		if ('/'.$basename == __FILE__) { // Maybe due to symlink
			$basename = basename(dirname(__FILE__)).'/'.basename(__FILE__);
		}
		return $basename;
	}
	
	protected function my_plugin_url() {
		$basename = plugin_basename(__FILE__);
		if ('/'.$basename == __FILE__) { // Maybe due to symlink
			return plugins_url().'/'.basename(dirname(__FILE__)).'/';
		}
		// Normal case (non symlink)
		return plugin_dir_url( __FILE__ );
	}

}

// Global accessor function to singleton
function GoogleAppsDirectory() {
	return basic_google_apps_directory::get_instance();
}

// Initialise at least once
GoogleAppsDirectory();

?>
