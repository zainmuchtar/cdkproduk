<?php
if ( !class_exists( "contactbuddy_admin" ) ) {
    class contactbuddy_admin {
	
		function __construct(&$parent) {
			$this->_parent = &$parent;
			$this->_var = &$parent->_var;
			$this->_name = &$parent->_name;
			$this->_options = &$parent->_options;
			$this->_pluginPath = &$parent->_pluginPath;
			$this->_pluginURL = &$parent->_pluginURL;
			$this->_selfLink = &$parent->_selfLink;

			add_action('admin_menu', array(&$this, 'admin_menu')); // Add menu in admin.
			// SHORTCODE BUTTON
			add_action( 'media_buttons_context', array( &$this, 'add_post_button' ) );

			if ( is_admin() ) {
				add_action( 'admin_init', array( &$this, 'cb_run_on_init' ) );
			}
		}
		function alert( $arg1, $arg2 = false ) {
			$this->_parent->alert( $arg1, $arg2 );
		}
		function nonce() {
			wp_nonce_field( $this->_parent->_var . '-nonce' );
		}
		function savesettings() {
			check_admin_referer( $this->_parent->_var . '-nonce' );
			if(empty($_POST[$this->_var . '-recipemail'])) {
				$this->_parent->_errors[] = 'recipemail';
				$this->_parent->_showErrorMessage( 'Recipient email address is empty' );
			}
			if(empty($_POST[$this->_var . '-subject'])) {
				$this->_parent->_errors[] = 'subject';
				$this->_parent->_showErrorMessage( 'Subject is empty' );
			}
			if($_POST[$this->_var . '-recaptcha'] == '1') {
				if(empty($_POST[$this->_var . '-recaptcha-pubkey'])) {
					$this->_parent->_errors[] = 'recaptcha-pubkey';
					$this->_parent->_showErrorMessage( 'If you are using recaptcha you must input a reCAPTCHA public key' );
				}
				if(empty($_POST[$this->_var . '-recaptcha-privkey'])) {
					$this->_parent->_errors[] = 'recaptcha-privkey';
					$this->_parent->_showErrorMessage( 'If you are using recaptcha you must input a reCAPTCHA private key' );
				}
			}
			
			if ( isset( $this->_parent->_errors ) ) {
				$this->_parent->_showErrorMessage( 'Please correct the ' . ngettext( 'error', 'errors', count( $this->_parent->_errors ) ) . ' in order to save changes.' );
			} else {

				$this->_options['recipemail'] = $_POST[$this->_var . '-recipemail'];
				$this->_options['subject'] = $_POST[$this->_var . '-subject'];
				$this->_options['recaptcha'] = $_POST[$this->_var . '-recaptcha'];
				$this->_options['recaptcha-pubkey'] = $_POST[$this->_var . '-recaptcha-pubkey'];
				$this->_options['recaptcha-privkey'] = $_POST[$this->_var . '-recaptcha-privkey'];
				$this->_options['defaultcss'] = $_POST[$this->_var . '-defaultcss'];
				
				$this->_parent->save();
				$this->alert( 'Settings saved...' );
			}
		}

		
		
		function admin_scripts() {
			wp_enqueue_script( 'pluginbuddy-tooltip-js', $this->_parent->_pluginURL . '/js/tooltip.js', array( 'jquery' ) );
			wp_print_scripts( 'pluginbuddy-tooltip-js' );
			wp_enqueue_script( 'pluginbuddy-'.$this->_var.'-admin-js', $this->_parent->_pluginURL . '/js/admin.js', array( 'jquery' ) );
			wp_print_scripts( 'pluginbuddy-'.$this->_var.'-admin-js' );
			wp_enqueue_script( 'cb-lightbox-js', $this->_parent->_pluginURL . '/js/lightbox.min.js', array( 'jquery' ) );
			wp_print_scripts( 'cb-lightbox-js' );
			echo '<link rel="stylesheet" href="'.$this->_pluginURL . '/css/admin.css" type="text/css" media="all" />';
			echo '<link rel="stylesheet" href="'.$this->_pluginURL . '/css/lightbox.css" type="text/css" media="all" />';
		}

		/**
		 *	get_feed()
		 *
		 *	Gets an RSS or other feed and inserts it as a list of links...
		 *
		 *	$feed		string		URL to the feed.
		 *	$limit		integer		Number of items to retrieve.
		 *	$append		string		HTML to include in the list. Should usually be <li> items including the <li> code.
		 *	$replace	string		String to replace in every title returned. ie twitter includes your own username at the beginning of each line.
		 */
		function get_feed( $feed, $limit, $append = '', $replace = '' ) {
			require_once(ABSPATH.WPINC.'/feed.php');  
			$rss = fetch_feed( $feed );
			if (!is_wp_error( $rss ) ) {
				$maxitems = $rss->get_item_quantity( $limit ); // Limit 
				$rss_items = $rss->get_items(0, $maxitems); 
				
				echo '<ul class="pluginbuddy-nodecor">';

				$feed_html = get_transient( md5( $feed ) );
				if ( $feed_html == '' ) {
					foreach ( (array) $rss_items as $item ) {
						$feed_html .= '<li>- <a href="' . $item->get_permalink() . '">';
						$title =  $item->get_title(); //, ENT_NOQUOTES, 'UTF-8');
						if ( $replace != '' ) {
							$title = str_replace( $replace, '', $title );
						}
						if ( strlen( $title ) < 30 ) {
							$feed_html .= $title;
						} else {
							$feed_html .= substr( $title, 0, 32 ) . ' ...';
						}
						$feed_html .= '</a></li>';
					}
					set_transient( md5( $feed ), $feed_html, 300 ); // expires in 300secs aka 5min
				}
				echo $feed_html;
				
				echo $append;
				echo '</ul>';
			} else {
				echo 'Temporarily unable to load feed...';
			}
		}

		function add_post_button( $content ){
			return $content . '
				<a onclick="cb_add_post();" title="Add simple contact form"><img src="' . $this->_pluginURL . '/images/atsymbol.png" alt="Add simple contact form" /></a>
				<script>
					function cb_add_post() {
						var win = window.dialogArguments || opener || parent || top;
						win.send_to_editor( \'[contactbuddy]\' );
					}
				</script>';
		}
		
		function view_settings() {
			$this->_parent->load();
			$this->admin_scripts();
			/*
			echo '<pre>';
			print_r($this->_options);
			echo '</pre>';
			*/
			if ( !empty( $_POST['save_recip'] ) ) {
				$this->savesettings();
			}
			
			?>
			<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery( '#cb-defaultcss' ).change(function(e) {
						var select_layout = jQuery('#cb-defaultcss').find(":selected").text().replace(/ /g,'').toLowerCase();
						var img_src = '<?php echo $this->_pluginURL; ?>/images/' + select_layout + '-preview.png';
						var img_name = select_layout + '-preview.png';
						jQuery('.cb_preview').attr('href', img_src);
						jQuery('.cb_preview').attr('data-lightbox', img_name);

					});
				});
			</script>
			<div class="wrap">
				<h2><?php echo $this->_name; ?> Settings</h2>
					<div id="poststuff">
						<div class="postbox">
							<h3 id="global-contactbuddy-settings">Global Settings</h3>
							<div class="inside" >
								<form method="post" action="<?php echo $this->_selfLink; ?>">
									<?php // Ex. for saving in a group you might do something like: $this->_options['groups'][$_GET['group_id']['settings'] which would be: ['groups'][$_GET['group_id']['settings'] ?>
									<input type="hidden" name="savepoint" value="" />
									<table class="form-table">
										<tr>
											<td><strong>Check out how to setup ContactBuddy <a href="http://ithemes.com/?p=26008" >here.</a></strong></td>
										</tr>
										<tr>
											<td><label for="first_name">Recipient email address <a class="pluginbuddy_tip" title=" - Enter the email for the form submissions to go to.">i</a></label></td>
											<td><input type="text" name="<?php echo $this->_var; ?>-recipemail" size="45" maxlength="45" value="<?php echo $this->_options['recipemail']; ?>" /></td>
										</tr>
										<tr>
											<td><label for="last_name">Subject <a class="pluginbuddy_tip" title=" - Enter the email subject.">i</a></label></td>
											<td><input type="text" name="<?php echo $this->_var; ?>-subject" size="45" maxlength="45" value="<?php echo $this->_options['subject']; ?>" /></td>
										</tr>
										<tr>
											<td>
												<label for="cb-defaultcss">Form Style Options <a class="pluginbuddy_tip" title=" -  Choose a from multiple styles for your contact form. CSS tip: the class used to style the ContactBuddy form is 'contactbuddy-form'. To preview a layout select it from the dropdown and click the preview link.">i</a></label>
											</td>
											<td>
												<?php $cbstylelist = array(
													'default'	=> 'Default',
													'skinny'	=> 'Skinny',
													'light'		=> 'Light',
													'dark'		=> 'Dark'
												); ?>
												<select name="<?php echo $this->_var; ?>-defaultcss" id="cb-defaultcss">
												<?php
													foreach( $cbstylelist as $stylekey => $styleval ) {
														$select = '';
														if ( isset( $this->_options['defaultcss'] ) ) {								
															if ( $this->_options['defaultcss'] == $stylekey) { $select = " selected "; }
														}
														echo '<option value="' . $stylekey . '"' . $select . '>' . $styleval . '</option>';
													}
												?>
												</select>
												<?php 
													$img_name = $this->_options['defaultcss'] . '-preview.png';
													$imgprev_path = $this->_pluginURL . '/images/' . $this->_options['defaultcss'] . '-preview.png'; 
												?>
												&nbsp;&nbsp;<strong><a class="cb_preview" data-lightbox="<?php echo $img_name; ?>" href="<?php echo $imgprev_path; ?>" >Click for layout preview.</a></strong>
											</td>
										</tr>
										<tr>
											<td>
												<label for="recaptcha">
													Enable reCAPTCHA <a class="pluginbuddy_tip" title=" - [Default: disabled] - When enabled, a reCAPTCHA input will be added to your contact form to insure entries weren't made by a robot. Note: If you have have reCAPTCHA enabled you can only have one Contactbuddy form on each page.">i</a>
												</label>
											</td>
											<input type="hidden" name="<?php echo $this->_var; ?>-recaptcha" value="0" />
											<?php
												if (($this->_options['recaptcha'] == '1') || (isset($_POST[$this->_var . '-recaptcha']) && ($_POST[$this->_var . '-recaptcha'] == '1'))) {
													$checked = 'checked';
												}
												else {
													$checked = '';
												}
											?>
											<td><input class="option_toggle" type="checkbox" name="<?php echo $this->_var; ?>-recaptcha" id="recaptcha" value="1" <?php echo $checked; ?> /></td>
										</tr>
										<?php
											// Check if recaptcha is checked
											if (($this->_options['recaptcha'] != '1') || (isset($_POST[$this->_var . '-recaptcha']) && ($_POST[$this->_var . '-recaptcha'] != '1'))) {
												$keyshow = 'style="display: none;"';
											}
											else {
												$keyshow = '';
											}
										?>
										<table class="form-table recaptcha_toggle" <?php echo $keyshow; ?>>
										<h3 class="recaptcha_toggle" <?php echo $keyshow; ?>>reCaptcha Options</h3><hr class="recaptcha_toggle" <?php echo $keyshow; ?>>
										<tr class="recaptcha_toggle" <?php echo $keyshow; ?>>
											<td><label for="recaptcha-pubkey">reCAPTCHA public key <a class="pluginbuddy_tip" title=" - Enter your reCAPTCHA public key from https://www.google.com/recaptcha">i</a></label></td>
											<td><input type="text" name="<?php echo $this->_var; ?>-recaptcha-pubkey" id="recaptcha-pubkey" size="45" maxlength="45" value="<?php if ( isset( $this->_options['recaptcha-pubkey'] ) ) { echo $this->_options['recaptcha-pubkey']; } ?>" /></td>
										</tr>
										<tr class="recaptcha_toggle" <?php echo $keyshow; ?>>
											<td><label for="recaptcha-privkey">reCAPTCHA private key <a class="pluginbuddy_tip" title=" - Enter your reCAPTCHA private key from https://www.google.com/recaptcha">i</a></label></td>
											<td><input type="text" name="<?php echo $this->_var; ?>-recaptcha-privkey" id="recaptcha-privkey" size="45" maxlength="45" value="<?php if ( isset( $this->_options['recaptcha-privkey'] ) ) { echo $this->_options['recaptcha-privkey']; } ?>" /></td>
										</tr>
										<tr class="recaptcha_toggle" <?php echo $keyshow; ?>>
											<td colspan="2"><h3><a href="https://www.google.com/recaptcha" target="_blank">Click here to get <span style="color: #E9B346;">reCAPTCHA</span> keys</a></h3></td>
										</tr>
									</table>					
									</table>
									<p class="submit"><input type="submit" name="save_recip" value="Save Settings" class="button-primary" id="save" /></p>
									<?php $this->nonce(); ?>
								</form>
						</div>
					</div>
				</div>
			</div>
			<?php
		}

		function view_more() {
			?>
			<h2>Redirecting...</h2>
			<script type="text/javascript">
				window.location.href = "<?php echo $_SERVER['HTTP_REFERER']; ?>"; 
			</script>
			<?php 
		}

		function cb_run_on_init() {
			$this->_parent->load();
			global $blog_id;

			if ( is_multisite() && ( $blog_id != 1 || ! current_user_can( 'manage_network_options' ) ) ) { //only display to network admin if in multisite
				return;
			}
			if ( ! function_exists( 'it_cb_activation_message' ) ) {
				function it_cb_activation_message() { 
					$notice_path = $_SERVER['HTTP_REFERER'] . '&cbsetup=no';

					?>
					<div class="updated" id="cb_setup_notice" >
						ContactBuddy is almost ready. Finish up the settings here!<a href="#" onclick="document.location.href='?page=contactbuddy&cbsetup=yes'" class="cb-notice-button">ContactBuddy Settings</a><a href="#" onclick="document.location.href='<?php echo $notice_path; ?>'" class="cb-notice-hide" >&times;</a>
					</div>
					<?php
				}
			}
			if ( true == $this->_options['set_nag'] ) {
				if ( is_multisite() ) {
					add_action( 'network_admin_notices', 'it_cb_activation_message' );
				} else {
					add_action( 'admin_notices', 'it_cb_activation_message' );
				}
			}
			if ( isset( $_GET['cbsetup'] ) ) {
				if ( 'no' == $_GET['cbsetup'] ) {
					$this->_options['set_nag'] = false;
					$this->_parent->save();
					if ( is_multisite() ) {
						remove_action( 'network_admin_notices', 'it_cb_activation_message' );
					} else {
						remove_action( 'admin_notices', 'it_cb_activation_message' );
					}

					wp_redirect( $_SERVER['HTTP_REFERER'], '302' );
				}
			}

			if ( isset( $_GET['cbsetup'] ) ) {
				if ( 'yes' == $_GET['cbsetup'] ) {	
					$this->_options['set_nag'] = false;	
					$this->_parent->save();
					if ( is_multisite() ) {
						remove_action( 'network_admin_notices', 'it_cb_activation_message' );
					} else {
						remove_action( 'admin_notices', 'it_cb_activation_message' );
					}
					$redirect_path = admin_url('admin.php?page=contactbuddy');
					wp_redirect( $redirect_path, '302' );
				}
			}
		}
		
		
		/** admin_menu()
		 *
		 * Initialize menu for admin section.
		 *
		 */		
		function admin_menu() {
			add_menu_page($this->_parent->_name.' Settings', $this->_parent->_name, apply_filters( 'contactbuddy_capability', 'switch_themes' ), $this->_parent->_var, array(&$this, 'view_settings'), $this->_parent->_pluginURL.'/images/contactbuddy.png');
			add_submenu_page( $this->_parent->_var, $this->_parent->_name.' Settings', 'Settings', apply_filters( 'contactbuddy_capability', 'switch_themes' ), $this->_parent->_var, array(&$this, 'view_settings'));
			add_submenu_page( $this->_parent->_var, $this->_parent->_name.' Get More', 'Get More', apply_filters( 'contactbuddy_capability', 'switch_themes' ), $this->_parent->_var . '-view_more', array( &$this, 'view_more' ) );
		}


    } // End class
	
	$contactbuddy_admin = new contactbuddy_admin($this); // Create instance
}
