<?php
/**
 *
 * Plugin Name: ContactBuddy
 * Plugin URI: https://wordpress.org/plugins/contactbuddy-by-pluginbuddycom/
 * Description: A plugin that allows you to add a simple contact form anywhere in your site.
 * Version: 1.1.8
 * Author: iThemes
 * Author URI: http://ithemes.com
 *
 * Installation:
 * 
 * 1. Download and unzip the latest release zip file.
 * 2. If you use the WordPress plugin uploader to install this plugin skip to step 4.
 * 3. Upload the entire ContactBuddy directory to your `/wp-content/plugins/` directory.
 * 4. Activate the plugin through the 'Plugins' menu in WordPress Administration.
 * 
 * Usage:
 * 
 * 1. Navigate to the ContactBuddy menu in the Wordpress Administration Panel.
 * 2. Go to the ContactBuddy Settings and add your information.
 * 3. Add to your site using widgets or the shortcode button.
 *
 */


if (!class_exists("contactbuddy")) {
	class contactbuddy {
		var $_version = '1.1.8';
		var $_instance = 0;
		var $_var = 'contactbuddy';
		var $_name = 'ContactBuddy';
		var $_timeformat = '%b %e, %Y, %l:%i%p';	// mysql time format
		var $_timestamp = 'M j, Y, g:iA';		// php timestamp format
		
		// Default constructor. This is run when the plugin first runs.
		function __construct() {
			$this->_defaults['recipemail'] = get_option('admin_email');
			$this->_defaults['subject'] = get_option('blogname') . ' // Contact Form';
			$this->_defaults['recaptcha'] = '0';
			$this->_defaults['defaultcss'] = 'default';
			$this->_defaults['set_nag'] = true;
			$this->_pluginPath = dirname( __FILE__ );
			$this->_pluginRelativePath = ltrim( str_replace( '\\', '/', str_replace( rtrim( ABSPATH, '\\\/' ), '', $this->_pluginPath ) ), '\\\/' );
			$this->_pluginURL = site_url() . '/' . $this->_pluginRelativePath;
			if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) { $this->_pluginURL = str_replace( 'http://', 'https://', $this->_pluginURL ); }
			$selflinkvar = explode( '?', $_SERVER['REQUEST_URI'] );
			$this->_selfLink = array_shift( $selflinkvar ) . '?page=' . $this->_var;
			
			require_once( dirname( __FILE__ ) . '/classes/widget.php' );
			// load icon fonts submodule for menu icon
			require_once( dirname( __FILE__ ) . '/lib/icon-fonts/load.php' );
			if ( is_admin() ) { // Runs when an admin is in the dashboard.
				require_once( $this->_pluginPath . '/classes/admin.php');
				register_activation_hook( $this->_pluginPath, array( &$this, 'activate' ) ); // Run some code when plugin is activated in dashboard.
				add_action( 'admin_enqueue_scripts', array( &$this, 'cb_add_admin_scripts' ) );
			} else { // Runs when in non-dashboard parts of the site.
				add_action( 'template_redirect', array( &$this, 'init_public') );
				add_shortcode( 'contactbuddy', array( &$this, 'shortcode' ) );
				add_action( $this->_var . '-widget', array( &$this, 'widget' ), 10, 2 ); // Add action to run widget function.
				add_action( 'wp_print_styles', array( &$this, 'cb_styles' ) );
			}
		}

		function cb_styles() {
			wp_enqueue_style('contactbuddy_css', $this->_pluginURL . "/css/contactbuddy.css");
		}

		function cb_add_admin_scripts() {
			wp_register_style( 'it_cb_notice_css', $this->_pluginURL . '/css/cb-notice.css' );
			wp_enqueue_style( 'it_cb_notice_css' );

			wp_enqueue_script( 'itcb-menu-link', $this->_pluginURL . '/js/cb-menu-link.js', array( 'jquery' ) );
			wp_print_scripts( 'itcb-menu-link' );

		}
		
		/**
		 *	alert()
		 *
		 *	Displays a message to the user at the top of the page when in the dashboard.
		 *
		 *	$message		string		Message you want to display to the user.
		 *	$error			boolean		OPTIONAL! true indicates this alert is an error and displays as red. Default: false
		 *	$error_code		int		OPTIONAL! Error code number to use in linking in the wiki for easy reference.
		 */
		function alert( $message, $error = false, $error_code = '' ) {
			echo '<div id="message" class="';
			if ( $error == false ) {
				echo 'updated fade';
			} else {
				echo 'error';
			}
			if ( $error_code != '' ) {
				$message .= '<p><a href="http://ithemes.com/codex/page/' . $this->_name . ':_Error_Codes#' . $error_code . '" target="_new"><i>' . $this->_name . ' Error Code ' . $error_code . ' - Click for more details.</i></a></p>';
			}
			echo '"><p><strong>'.$message.'</strong></p></div>';
		}
		
		
		
		/**
		 * activate()
		 *
		 * Run on plugin activation. Useful for setting up initial stuff.
		 *
		 */
		function activate() {
			

		}

		
		
		/**
		 * init_public()
		 *
		 * Run on on public pages (non-dashboard).
		 *
		 */
		function init_public() {
			require_once(dirname( __FILE__ ).'/classes/public.php');
		}
		
		// OPTIONS STORAGE //
		
		
		function save() {
			add_option($this->_var, $this->_options, '', 'no'); // 'No' prevents autoload if we wont always need the data loaded.
			update_option($this->_var, $this->_options);
			return true;
		}
		
		
		function load() {
			$this->_options=get_option($this->_var);
			$options = array_merge( $this->_defaults, (array)$this->_options );

			if ( $options !== $this->_options ) {
				// Defaults existed that werent already in the options so we need to update their settings to include some new options.
				$this->_options = $options;
				$this->save();
			}

			return true;
		}
		
		
		function shortcode() {
			return $this->_insertCBform();
		}

		function josh_print( $what_to_print, $print_op = true ) { 
			if ( isset( $what_to_print ) )
				echo '<pre>' . print_r( $what_to_print, $print_op ) . '</pre>';
			else 
				echo 'What you are trying to pring is not currently set. Check yo self before you wreck yo self!';
		}
		
		
		function widget() {
			echo $this->_insertCBform();
		}
		function _insertCBform() {
			$this->load();

			$this->_instance++;
			if ( (isset($this->_options['defaultcss'])) && ($this->_options['defaultcss'] == 'off')) {
			} else {
				if ( $this->_options['defaultcss'] == 'on' ) {
					$stylesheet = 'contactbuddy';
				} else {
					$stylesheet = 'contact-' . $this->_options['defaultcss'];
				}
				if ( !wp_style_is('contactbuddy_css_new') ) {
					wp_enqueue_style('contactbuddy_css_new', $this->_pluginURL . '/css/'. $stylesheet . '.css');
					wp_print_styles('contactbuddy_css_new');
				}
			}
			$return = '';

			if ( !empty($_POST[$this->_var . '-submit']) ) {
				if ($this->_instance == $_POST[$this->_var . '-instancenum'])  {


					$this->_sendEntry();
					if( isset($this->_errors) ) {
						$form['name'] = $_POST[$this->_var . '-name'];
						$form['email'] = $_POST[$this->_var . '-email'];
						$form['subject'] = $_POST[$this->_var . '-subject'];
						$form['message'] = $_POST[$this->_var . '-message'];
						if(in_array('name', $this->_errors)) { $cberror['name'] = 'fail'; }
						if(in_array('email', $this->_errors)) { $cberror['email'] = 'fail'; }
						if(in_array('subject', $this->_errors)) { $cberror['subject'] = 'fail'; }
						if(in_array('message', $this->_errors)) { $cberror['message'] = 'fail'; }
						if(in_array('recaptcha', $this->_errors)) { $cberror['recaptcha'] = 'fail'; }
					}
					if( isset($this->_success) ) {
						$scsuccess['success'] = 'it works';
					}
				}
			}
			
			$return .= '<a name="' . $this->_var . '-' . $this->_instance . '"></a>';
			$return .= '<form method="post" action="#' . $this->_var . '-' . $this->_instance . '" class="contactbuddy-form" id="contactbuddy-' . $this->_instance . '">';
				$return .= '<input type="hidden" name="' . $this->_var . '-instancenum" value="' . $this->_instance . '" />';
				$return .= '<ul>';
					$return .= '<li class="contactbuddy-name-label"><label>Name: </label>';
					if(isset($cberror['name'])) { $return .= '<span class="cberror"><strong>required</strong></span>'; } 
					$return .= '</li>';
					if (isset($form['name'])){ $name = $form['name'];} else { $name = ''; }
					$return .= '<li class="contactbuddy-name-input"><input type="text" class="cbfit" name="' . $this->_var . '-name" value="' . $name . '" /></li>';
					
					$return .= '<li class="contactbuddy-email-label"><label>Email: </label>';
					if(isset($cberror['email'])) { $return .= '<span class="cberror"><strong>required</strong></span>'; }
					$return .= '</li>';
					if (isset($form['email'])){ $email = $form['email'];} else { $email = ''; }
					$return .= '<li class="contactbuddy-email-input"><input type="text" class="cbfit" name="' . $this->_var . '-email" value="' . $email . '" /></li>';
					
					$return .= '<li class="contactbuddy-subject-label"><label>Subject: </label>';
					if(isset($cberror['subject'])) { $return .= '<span class="cberror"><strong>required</strong></span>'; }
					$return .= '</li>';
					if (isset($form['subject'])){ $subject = $form['subject'];} else { $subject = '';}
					$return .= '<li class="contactbuddy-subject-input"><input type="text" class="cbfit" name="' . $this->_var . '-subject" value="' . $subject . '" /></li>';
					
					$return .= '<li class="contactbuddy-message-label"><label>Message: </label>';
					if(isset($cberror['message'])) { $return .= '<span class="cberror"><strong>required</strong></span>'; }
					$return .= '</li>';
					if (isset($form['message'])){ $message = $form['message'];} else { $message = ''; }
					$return .= '<li class="contactbuddy-message-input"><textarea class="cbfit" name="' . $this->_var . '-message" rows="8">' . $message . '</textarea></li>';
					
					if($this->_options['recaptcha'] == '1'){
						$return .= '<li class="contactbuddy-recaptcha-label"><label>reCAPTCHA: </label>';
						if(isset($cberror['recaptcha'])) { $return .= '<span class="cberror"><strong>required</strong></span>'; }
						$return .= '</li>';
						$return .= '<li class="contactbuddy-recaptcha-input">';
						require_once('_recaptchalib.php');
						$publickey = $this->_options['recaptcha-pubkey']; // public key from recaptcha.com
						$return .= recaptcha_get_html($publickey);
						$return .= '</li>';
					}
					
					$return .= '<li class="contactbuddy-submit"><input type="submit" name="' . $this->_var . '-submit" value="submit" /></li>';
				$return .= '</ul>';
			$return .= '</form>';
			
			if (isset($cberror)) {
				$return .= '<span class="cberror"><strong>Please correct the above errors in order to send email.</strong></span>';
			}
			if (isset($scsuccess)) {
				$return .= '<div><p class="cbstatus">Email sent successfully.</p></div>';
			}

			unset($scerror);
			unset($form);
			
			return $return;
		}
		function _sendEntry() {
			$this->load();
			
			foreach( $_POST as $key => $val) {
				$pos = strpos( $key, $this->_var);
				if(($pos !== false) && ($key != ($this->_var . '-submit'))) {
					$label = str_replace($this->_var . '-', '', $key);
					if ( empty($_POST[$key]) ) {
						$this->_errors[] = $label;
					}
				}
			}
			
			
			// RECAPTCHA VALIDATION
			require_once('_recaptchalib.php');
			if ($this->_options['recaptcha'] == '1') {
				$privatekey = $this->_options['recaptcha-privkey']; // private key from recaptcha.com
				$resp = recaptcha_check_answer($privatekey,$_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]);
				if (!$resp->is_valid) {
					$this->_errors[] = 'recaptcha';
				}
			}
			
			if ( isset( $this->_errors ) ) {
				// fail
			} else {
				/* STORING CONTACT SUMISSIONS IN DATABASE
				// Get index for new entry by adding 1 to the largest index currently in the entries. Put in $newID
				if ( is_array( $this->_options['entries'] ) && !empty( $this->_options['entries'] ) ) {
					$newID = max( array_keys( $this->_options['entries'] ) ) + 1;
				} else {
					$newID = 0;
				}
							
				$this->_options['entries'][$newID]['name'] = $_POST[$this->_var . '-name'];
				$this->_options['entries'][$newID]['email'] = $_POST[$this->_var . '-email'];
				$this->_options['entries'][$newID]['subject'] = $subject = $_POST[$this->_var . '-subject'];
				$this->_options['entries'][$newID]['message'] = $_POST[$this->_var . '-message'];
				
				$this->save();
				*/

				/* EMAIL FUNCTION */
				$to = $this->_options['recipemail'];
				$subject = $this->_options['subject'] . ':' . $_POST[$this->_var . '-subject'];
				$message = $_POST[$this->_var . '-message'];
				$headers = 'From: ' . $_POST[$this->_var . '-name'] . ' <' . $_POST[$this->_var . '-email'] . '>' . "\r\n";
				
				wp_mail( $to, $subject, $message, $headers );

				$_POST[$this->_var . '-name'] = '';
				$_POST[$this->_var . '-email'] = '';
				$_POST[$this->_var . '-subject'] = '';
				$_POST[$this->_var . '-message'] = '';
				
				$this->_success[] = 'success';
			}
		}

		// PUBLIC DISPLAY OF MESSAGES ////////////////////////
	
		function _showStatusMessage( $message ) {
			echo '<div id="message" class="updated fade"><p><strong>'.$message.'</strong></p></div>';			
		}
		function _showErrorMessage( $message ) {
			echo '<div id="message" class="error"><p><strong>'.$message.'</strong></p></div>';
		}
		function _cbStatusMessage( $message ) {
			return '<div><p class="cbstatus">' . $message . '</p></div>';			
		}
		function _cbErrorMessage( $message ) {
			return '<span class="cberror"><strong>' . $message . '</strong></span>';
		}
		
    } // End class

	$contactbuddy = new contactbuddy(); // Create instance
	//require_once( dirname( __FILE__ ) . '/classes/widget.php');
}



?>
