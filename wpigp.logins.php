<?php
global $wpigp, $is_IE;
?>
<!doctype html>
<html>
  <head>
    <title><?php _e( 'WP Instagram Logins', 'wpigp' ) ?></title>
</head>
<body>
<div class="woosocio_wrap">
  <h1><?php _e( 'WP Instagram Logins', 'wpigp' ) ?></h1>
  <p>
  <?php _e( 'Connect your site to Instagram and automatically share posts with your friends.', 'wpigp' ) ?>
  </p>
  <?php 
	if ($is_IE){
	  echo "<p style='font-size:18px; color:#F00;'>" . __( 'Important Notice:', 'wpigp') . "</p>";
	  echo "<p style='font-size:16px; color:#F00;'>" . 
	  		__( 'You are using Internet Explorer. This plugin may not work properly with IE. Please use any other browser.', 'wpigp') . "</p>";
	  echo "<p style='font-size:16px; color:#F00;'>" . __( 'Recommended: Google Chrome.', 'wpigp') . "</p>";
	}
  ?>
  
  <div id="woosocio-services-block">
	<img src="<?php echo $wpigp->assets_url.'/instagram-logo.png' ?>" alt="Instagram Logo">
    <div class="woosocio-service-entry" >
		<div id="twitter" class="woosocio-service-left">
			<a href="https://www.instagram.com" id="service-link-facebook" target="_top">Instagram</a>
		</div>
		<div class="woosocio-service-right">
           	<div id="app-info">
            <table class="form-table">
            <tr valign="top">
	  			<th scope="row"><label><?php _e('Username:', 'wpigp') ?></label></th>
	  			<td>
	  				<input type="text" name="wigp_username" id="wigp-username" placeholder="<?php _e('Instagram Username', 'wpigp') ?>" value="<?php echo get_option( 'wigp_username' ); ?>" size="55" maxlength="128"><br>
                    <p style="font-size:12px"><?php _e("Don't have an Instagram account? You can create from ", 'wpigp') ?>
                    <a href="https://www.instagram.com/" target="_new" style="font-size:12px">https://www.instagram.com</a>
	  			</td>
	  		</tr>
            <tr valign="top">
	  			<th scope="row"><label><?php _e('Password:', 'wpigp') ?></label></th>
	  			<td>
	  				<input type="password" name="wigp_password" id="wigp-password" placeholder="<?php _e('Instagram Password', 'wpigp') ?>" value="<?php echo get_option( 'wigp_password' ); ?>" size="55" maxlength="128">
	  			</td>
	  		</tr>

            <tr valign="top">
     	  		<th scope="row"></th>
	  			<td>
                	<a id="wigp-btn-save" class="button-primary button" href="javascript:"><?php _e('Save', 'wpigp') ?></a>
                  <img id="working" src="<?php echo $wpigp->assets_url.'/spinner.gif' ?>" alt="Wait..." height="22" width="22" style="display: none;">                  
                  <span id="save-user-msg"></span>
	  			</td>
	  		</tr>
            </table>
            
            </div>
		</div>
	</div>

	<!-- Video tutorial   
	<div class="woosocio-service-entry">    

	</div>
    -->
    <div class="woosocio-service-entry" style="font-size:18px; color:#03D">
        <div class="woosocio-service-left">
            <a href="https://wordpress.org/plugins/woosocio/" target="_top">
            <img src="<?php echo $wpigp->assets_url.'/woosocio_icon.jpg' ?>" alt="WooSocio Free" height="128">
            </a>
        </div>
        <div class="woosocio-service-right">
            <div align="left">
            <?php
				echo '<a href="https://wordpress.org/plugins/woosocio/" target="_top">'.__('* WooSocio Free version *', 'wpigp').'</a></br>';
				_e('* Post product to Facebook, pages and groups', 'wpigp'); echo "</br>";
				_e('* post to groups you dont manage', 'wpigp'); echo "</br>";
				_e('* Add widget for Facebook like box', 'wpigp'); echo "</br>";
				_e('* Multi user ready', 'wpigp'); echo "</br>";
				_e('* Post as image or link', 'wpigp'); echo "</br>";
				_e('* And many more...', 'wpigp'); echo "</br>";
            ?>
            </div>
        </div>
    </div>

    <div class="woosocio-service-entry" style="font-size:18px; color:#03C">
        <div class="woosocio-service-left">
            <a href="https://wordpress.org/plugins/wootweet/" target="_top">
            <img src="<?php echo $wpigp->assets_url.'/wootweet_icon.jpg' ?>" alt="WooTweet">
            </a>
        </div>
        <div class="woosocio-service-right">
            <div align="left">
            <?php
                echo '<a href="https://wordpress.org/plugins/wootweet/" target="_top">'.__('* WooTweet Free *', 'wpigp').'</a>';
                echo "</br></br>";
                _e('* Post product to Twitter', 'wpigp'); echo "</br>";
                _e('* Post products multiple times(on every update)', 'wpigp'); echo "</br>";
                _e('* Add Tweet Widget for latest Tweets', 'wpigp'); echo "</br>";
                _e('* And many more to come...', 'wpigp'); echo "</br>";
            ?>
            </div>
        </div>
    </div>

  </div>
    <!-- Right Area Widgets -->  
    <?php 
		include_once 'right_area.php';
	 ?>
    <!-- Right Area Widgets -->  
</div>
  </body>
</html>
<script type="text/javascript"><!--
jQuery(document).ready(function($){
		
	$("#wigp-btn-save").click(function(){
		$("#working").show();
		
		var data = {
			action: 'wigp_save_user',
			wigp_username: $("#wigp-username").val(),
			wigp_password: $("#wigp-password").val(),
		};
		
		$.post(ajaxurl, data, function(response) {
			//console.log('Got this from the server: ' + response);
      $("#save-user-msg").html(response);
      $("#working").hide();
		//location.reload();
		});	
		
	});

});
//-->
</script>