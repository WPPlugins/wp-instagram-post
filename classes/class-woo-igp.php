<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * WPIGP Base Class
 *
 * All functionality pertaining to core functionality of the WP Instagram Post plugin.
 *
 * @package WordPress
 * @subpackage WPIGP
 * @author qsheeraz
 * @since 0.0.1
 *
 */

class Woo_IGP {
	public $version;
	private $file;

	private $token;
	private $prefix;

	private $plugin_url;
	private $assets_url;
	private $plugin_path;
	
	public $igp;
	public $fb_user_profile = array();
	public $fb_user_pages = array();
	
	private $ig_user_name;
	private $ig_password;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct ( $file ) {
		$this->version = '';
		$this->file = $file;
		$this->prefix = 'woo_igp_';
		$this->wigp_username = get_option( 'wigp_username');
		$this->wigp_password = get_option( 'wigp_password');

		/* Plugin URL/path settings. */
		$this->plugin_url = str_replace( '/classes', '', plugins_url( plugin_basename( dirname( __FILE__ ) ) ) );
		$this->plugin_path = str_replace( 'classes', '', plugin_dir_path( __FILE__ ));
		$this->assets_url = $this->plugin_url . '/assets';
		
		$this->igp = new \InstagramAPI\Instagram(true, false);
		try {
		    $this->igp->setUser($this->wigp_username, $this->wigp_password);
    		$this->igp->login();
		} catch (\Exception $e) {
    		//echo 'Something went wrong: '.$e->getMessage()."\n";
    		//exit(0);
		}
		
	} // End __construct()

	/**
	 * init function.
	 *
	 * @access public
	 * @return void
	 */
	public function init () {
		add_action( 'init', array( $this, 'load_localisation' ) );

		add_action( 'admin_init', array( $this, 'wooigp_admin_init' ) );
		add_action( 'admin_menu', array( $this, 'wooigp_admin_menu' ) );
		
		add_action( 'wp_ajax_wigp_save_user', array( $this, 'wigp_save_user' ));
		add_action( 'wp_ajax_wigp_save_meta_box', array( $this, 'wigp_save_meta_box' ));
		add_action( 'save_post', array( $this, 'wigp_instagram_post' ));

		add_action( 'post_submitbox_misc_actions', array( $this, 'wigp_meta_box' ) );


		// Run this on activation.
		register_activation_hook( $this->file, array( $this, 'activation' ) );
	} // End init()
	
	function pa($arr){

		echo '<pre>';
		print_r($arr);
		echo '</pre>';
	}


	/**
	 * wigp_meta_box function.
	 *
	 * @access public
	 * @return void
	 */
	public function wigp_meta_box() {
		global $post; global $post_type;
		$post_id = get_the_ID();
		
		?>
		<div id="wpigp" class="misc-pub-section misc-pub-section-last">
		<?php

			echo '<img src="'.$this->assets_url.'/instagram.png" >&nbsp;&nbsp;&nbsp;';
			echo "<b>";_e( 'WP Instagram:', 'wpigp' ); echo "</b>";
			$ig_post = metadata_exists('post', $post_id, '_wigp_ig') ? get_post_meta( $post_id, '_wigp_ig', true ) : 'checked';
			$ig_msg = ( get_post_meta( $post_id, '_wigp_msg', true ) ? get_post_meta( $post_id, '_wigp_msg', true ) : $post->title );
			?>
			<div id="wigp-form" style="display: none;">
            	<br />
                <input type="checkbox" name="chk_ig" id="chk-ig" <?php echo $ig_post; ?> />
                <label for="chk-ig"><b><?php _e( 'Post to Instagram?', 'wpigp' ); ?></b></label><br />
				<label for="wigp-custom-msg"><?php _e( 'Custom Message: (No html tags)', 'wpigp' ); ?></label>
				<textarea name="wigp_custom_msg" id="wigp-custom-msg"><?php echo $ig_msg; ?></textarea>
				<a href="#" id="wigp-form-ok" class="button"><?php _e( 'Save', 'wpigp' ); ?></a>
				<a href="#" id="wigp-form-hide"><?php _e( 'Cancel', 'wpigp' ); ?></a>
                <input type="hidden" name="postid" id="postid" value="<?php echo get_the_ID()?>" />
			</div>
             &nbsp; <a href="#" id="wigp-form-edit"><?php _e( 'Edit', 'wpigp' ); ?></a>
		</div> 
        
		<script type="text/javascript"><!--
        jQuery(document).ready(function($){
                $("#wigp-form").hide();
                
            $("#wigp-form-edit").click(function(){
				$("#wigp-form-edit").hide();
                $("#wigp-form").show(1000);
            });
            
            $("#wigp-form-hide").click(function(){
                $("#wigp-form").hide(1000);
				$("#wigp-form-edit").show();
            });
           
		    $("#wigp-form-ok").click(function(){
				var custom_msg;
       			custom_msg = $("#wigp-custom-msg").val();
				var data = {
					action: 'wigp_save_meta_box',
					ig_msg: custom_msg,
					postid: $("#postid").val(),
					chk_ig: $("#chk-ig").attr("checked"),
				};
				$.post(ajaxurl, data, function(response) {
					console.log('Got this from the server: ' + response);
				});
                $("#wigp-form").hide(1000);
				$("#wigp-form-edit").show();
            });

        });
		//-->
        </script>
		<?php 
		
	}

	/**
	 * wigp_save_meta_box function.
	 *
	 * @access public
	 * @return void
	 */	
	public function wigp_save_meta_box($post) {

		if ( isset( $_POST['postid'] ) && (int)$_POST['postid'] ) {
			$ig_message = sanitize_text_field( $_POST['ig_msg'] );
			$ig_post_cb = sanitize_text_field( $_POST['chk_ig'] );
			$postid = (int)$_POST['postid'];

			update_post_meta ($postid, '_wigp_msg', $ig_message );
			update_post_meta ($postid, '_wigp_ig', $ig_post_cb );
		}

		die(0);
	}
	
	/**
	 * wooigp_admin_init function.
	 *
	 * @access public
	 * @return void
	 */		
	public function wooigp_admin_init() {
       /* Register stylesheet. */
        wp_register_style( 'wooigpStylesheet', $this->plugin_url.'/wpigp.css' );
		
		register_setting( 'wooigp_options', 'wooigp_settings' );
	
		add_settings_section(
			'wooigp_options_section', 
			__( 'WP Instagram Options', 'wpigp' ), 
			array($this, 'wooigp_settings_section_callback'), 
			'wooigp_options'
		);
	
		add_settings_field( 
			'wooigp_checkbox_post_update', 
			__( 'Post to Instagram every time on post update?', 'wpigp' ), 
			array($this, 'wooigp_checkbox_post_update'), 
			'wooigp_options', 
			'wooigp_options_section' 
		);
	
		add_settings_field( 
			'wooigp_checkbox_notifications', 
			__( 'Get error notifications by email?', 'wpigp' ), 
			array($this, 'wooigp_checkbox_notifications'), 
			'wooigp_options', 
			'wooigp_options_section' 
		);

		add_settings_field( 
			'wooigp_checkbox_post_types', 
			__( 'Select post types to post to Instagram!', 'wpigp' ), 
			array($this, 'wooigp_checkbox_post_types'), 
			'wooigp_options', 
			'wooigp_options_section' 
		);
    }

	/**
	 * wooigp_options function.
	 *
	 * @access public
	 * @return void
	 */		
	public function wooigp_options () {
		
	?>
	<form action='options.php' method='post'>
		
		<h2>WP Instagram Post</h2>
		
		<?php
		settings_fields( 'wooigp_options' );
		do_settings_sections( 'wooigp_options' );
		submit_button();
		?>
		
	</form>
	<?php

	}

	function wooigp_checkbox_post_update(  ) { 
		$options = get_option( 'wooigp_settings' );
		if ( !isset ( $options['wooigp_checkbox_post_update'] ) )
			$options['wooigp_checkbox_post_update'] = 0;
		?>
		<input type='checkbox' name='wooigp_settings[wooigp_checkbox_post_update]' <?php checked( $options['wooigp_checkbox_post_update'], 1 ); ?> value='1'>
		<?php
	
	}
	
	
	function wooigp_checkbox_notifications(  ) { 
		$options = get_option( 'wooigp_settings' );
		if ( !isset ( $options['wooigp_checkbox_notifications'] ) )
			$options['wooigp_checkbox_notifications'] = 0;
		?>
		<input type='checkbox' name='wooigp_settings[wooigp_checkbox_notifications]' <?php checked( $options['wooigp_checkbox_notifications'], 1 ); ?> value='1'>
		<?php
	
	}

	function wooigp_checkbox_post_types(  ) { 
		$options = get_option( 'wooigp_settings' );
		if ( !isset ( $options['wooigp_checkbox_post_types'] ) )
			$options['wooigp_checkbox_post_types'] = array();
		
		foreach ( get_post_types( '', 'names' ) as $post_type ) {
		?>
		<input type='checkbox' 
			   name='wooigp_settings[wooigp_checkbox_post_types][<?php echo $post_type ?>]' 
			   id = '<?php echo $post_type ?>'
			   <?php checked( isset($options['wooigp_checkbox_post_types'][$post_type]) ); ?> 
			   value='<?php echo $post_type ?> '> 
		<label for="<?php echo $post_type ?>"><b><?php echo ucwords($post_type) ?></b></label><br />
		<?php
		}
	}

	function wooigp_settings_section_callback(  ) { 
	
		echo __( 'Settings', 'wpigp' );
	
	}

	/**
	 * socialize_post function.
	 *
	 * @access public
	 * @return void
	 */		
	public function wigp_instagram_post($post_id){
		
		$options = get_option( 'wooigp_settings' );
		if( get_post_status($post_id) == "publish" && isset($options['wooigp_checkbox_post_types'][get_post_type( $post_id )]) != '' ){
			
			$ig_post = metadata_exists('post', $post_id, '_wigp_ig') ? get_post_meta( $post_id, '_wigp_ig', true ) : 'checked';
			$ig_posted = metadata_exists('post', $post_id, '_wigp_ig_posted') ? get_post_meta( $post_id, '_wigp_ig_posted', true ) : '';
			//$options = get_option( 'wooigp_settings' );
			$repost = !$ig_posted ? true : $options['wooigp_checkbox_post_update'];

			if ( $ig_post && $repost ) {
		
				$message = get_the_title($post_id);
				$message.= metadata_exists('post', $post_id, '_wigp_msg') ? " - ".get_post_meta( $post_id, '_wigp_msg', true ) : '';
				
				if( get_post_type( $post_id ) == "product" ){
				
					$_pf = new WC_Product_Factory();
					$_product = $_pf->get_product($post_id);

					$post_desc = strip_tags( get_post_field( 'post_content', $post_id ) );
					$curr_symb = get_woocommerce_currency_symbol();
					$message.= "\n" . __( 'Price: ', 'wpigp') 
							. html_entity_decode($curr_symb, ENT_COMPAT, "UTF-8") 
							. $_product->get_price() . "\n" 
							. $post_desc . "\n" . __( 'Link: ', 'wpigp') 
							. get_permalink( $post_id );

					//$photoFilename = wp_get_attachment_url(get_post_thumbnail_id( $post_id ) );
				} else {
					$post_desc = strip_tags( wp_trim_words( get_post_field( 'post_content', $post_id ), 200) );
					$message.= "\n" . $post_desc;
					$message.= "\n" . get_permalink( $post_id );
					
				}
				$photoFilename = wp_get_attachment_url(get_post_thumbnail_id( $post_id ) );

				try {

				    if ($photoFilename)
				    	$this->igp->uploadPhoto($photoFilename, $message);

	      		} 
				catch (\Exception $e) {
					if ( $options['wooigp_checkbox_notifications'] ){
						$admin_email = get_option( 'admin_email' );
						if ( empty( $admin_email ) ) {
							$current_user = wp_get_current_user();
							$admin_email = $current_user->user_email;
						}
						
						$msg = __('Dear user,', 'wpigp') . "\r\n";
						$msg.= __('Your post ID ', 'wpigp') . $socio_post->ID . __(' not posted on Instragram due to following reason.', 'wpigp') . "\r\n";
						$msg.= $e->getMessage();
						
						wp_mail($admin_email, __('WP Instagram - Notification', 'wpigp'), $msg, $this->wigp_headers());
					}
					return false;
					//console.log($e->getType());
	      		}
	      	}
		}
	}

	/**
	 * wooigp_admin_menu function.
	 *
	 * @access public
	 * @return void
	 */		
	public function wooigp_admin_menu () {
		add_menu_page( 'WP Instagram', 'WP Instagram', 'manage_options', 'wpigp', '', $this->assets_url.'/instagram.png', 52 );
		$page_logins   = add_submenu_page( 'wpigp', 'Logins', 'Logins', 'manage_options', 'wpigp', array( $this, 'wooigp_logins_page' ) );
		$page_options  = add_submenu_page( 'wpigp', 'Options', 'Options', 'manage_options', 'wooigp_options', array( $this, 'wooigp_options' ) );
		add_action( 'admin_print_styles-' . $page_logins, array( $this, 'wooigp_admin_styles' ) );
		add_action( 'admin_print_styles-' . $page_options, array( $this, 'wooigp_admin_styles' ) );
	}

	/**
	 * wooigp_admin_styles function.
	 *
	 * @access public
	 * @return void
	 */			
	public function wooigp_admin_styles() {
       /*
        * It will be called only on plugin admin page, enqueue stylesheet here
        */
       wp_enqueue_style( 'wooigpStylesheet' );
   }


	/**
	 * wooigp_logins_page function.
	 *
	 * @access public
	 * @return void
	 */		
	public function wooigp_logins_page () {
		
		$filepath = $this->plugin_path.'wpigp.logins.php';
		if (file_exists($filepath))
			include_once($filepath);
		else
			die('Could not load file '.$filepath);
	}


	/**
	 * creating email headers.
	 *
	 * @access public
	 */
	public function wigp_headers(){
		$admin_email = get_option( 'admin_email' );
		if ( empty( $admin_email ) ) {
			$admin_email = 'support@' . $_SERVER['SERVER_NAME'];
		}

		$from_name = get_option( 'blogname' );

		$header = "From: \"{$from_name}\" <{$admin_email}>\n";
		$header.= "MIME-Version: 1.0\r\n"; 
		$header.= "Content-Type: text/plain; charset=\"" . get_option( 'blog_charset' ) . "\"\n";
		$header.= "X-Priority: 1\r\n"; 

		return $header;
	}



	/**
	 * save Instagram username and password function.
	 *
	 * @access public
	 */
	public function wigp_save_user() {
		
		if ( $_POST['wigp_username'] != '' && $_POST['wigp_password'] != '' ) {
			$ig_user = sanitize_user( $_POST['wigp_username'] );

			update_option( 'wigp_username', $ig_user );
			update_option( 'wigp_password', $_POST['wigp_password'] );
			_e( 'User info updated!', 'wpigp');
		}
		else
			_e( 'Empty Username or Password!', 'wpigp');
		
		die(0);
 	}


	/**
	 * load_localisation function.
	 *
	 * @access public
	 * @return void
	 */
	public function load_localisation () {
		$lang_dir = trailingslashit( str_replace( 'classes', 'lang', plugin_basename( dirname(__FILE__) ) ) );
		load_plugin_textdomain( 'wpigp', false, $lang_dir );
	} // End load_localisation()

	/**
	 * activation function.
	 *
	 * @access public
	 * @return void
	 */
	public function activation () {
		$this->register_plugin_version();
	} // End activation()

	/**
	 * register_plugin_version function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_plugin_version () {
		if ( $this->version != '' ) {
			update_option( 'wpigp' . '-version', $this->version );
		}
	} // End register_plugin_version()
} // End Class
?>