<script type="text/javascript">
	function serviceChoice(){
		var choice = document.getElementById("choose_service").value;
	if (choice == "international"){
		//document.getElementById("type").style.display = "";
		//document.getElementById("deliver").style.display = "";
		document.getElementById("apikey").style.display = "none";
		document.getElementById("list_sender_names").style.display = "none";
		//document.getElementById("nb_international").style.display = "";
		document.getElementById("international_sender_names").style.display = "";
		
	}else{
		//document.getElementById("type").style.display = "none";
		//document.getElementById("deliver").style.display = "none";
		document.getElementById("apikey").style.display = "";
		document.getElementById("list_sender_names").style.display = "";
		//document.getElementById("nb_international").style.display = "none";
		document.getElementById("international_sender_names").style.display = "none";
		}
	
	}

	function openwin() {
		var url=document.form.wp_webservice.value;
		if(url==1) {
			document.location.href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=bongolive-sms/about";
		}
	}
</script>

<style>
p.register{
	background: #009900;
	border-radius: 8px;
	padding: 4px;
	color: #FFFFFF;
	font-size: 11px;
	float: <?php echo is_rtl() == true? "right":"left"; ?>
}
p.register a{
	color: #FFFFFF;
	font-weight: bold;
	text-decoration: none;
}
</style>

<?php
	$new_settings = false;
	$check_international_settings = false;
	$check_local_settings = false;
	// Checking international details and local details to provide the right view
	global $obj;
	$typed_sender_name = $obj->prep_sender_name(get_option('typed_sender_names'));
	if (get_option('choose_service') == 'international'){
		if ( (get_option('wp_bongolive_username') != "") && ( $typed_sender_name != "") && (get_option('wp_bongolive_password') !="")){
				$check_international_settings = true;
                update_option('typed_sender_names',$typed_sender_name);
		}
	}else{
		if ( (get_option('wp_bongolive_username') != "") && (get_option('bongolive_sender') != "") && (get_option('wp_bongolive_password') !="") && (get_option('wp_bongolive_apikey')!= "") ){
				$check_local_settings = true;
		} 
	}
		
	
	//if ((get_option('wp_bongolive_password')!= "") && (get_option('wp_bongolive_apikey')!= "") && get_option('wp_bongolive_username')!= "" && !isset($_GET['settings'])){
	if ((($check_local_settings || $check_international_settings ) && (isset($_GET['settings-updated']))) || !isset($_GET['settings'])){
		$new_settings = true;
		
	}
?>

<div class="wrap">
	
	<h2><img src="<?php bloginfo('url'); ?>/wp-content/plugins/bongolive-sms/images/logo.png"/><br/><?php _e('SMS Setting', 'bongolive-sms'); ?></h2>
	<table class="form-table" style="width: 50%">
	  <?php if (!$new_settings)
	  {?>
		<form method="post" action="options.php" name="form">
			<?php wp_nonce_field('update-options');?>
			<tr><th colspan="2"><h3><?php _e('Bongolive Account Credentials', 'bongolive-sms'); ?></h4></th></tr>
			<tr>
				<td><?php _e('Choose your service', 'bongolive-sms');?></td>
				<td>
					<select name="choose_service" id="choose_service" onchange="serviceChoice()">
						<option value="local" <?php if ((get_option('choose_service'))== "local"){ echo 'selected=\"selected\"';}?>><?php _e('Local SMS only', 'bongolive-sms')?></option>
						<option value="international" <?php if ((get_option('choose_service'))== "international"){ echo 'selected=\"selected\"';}?>><?php _e('International SMS', 'bongolive-sms')?></option>
					</select><br/><span style="font-size: 10px">Choose international to send SMS outside of Tanzania</span>
				</td>
			</tr>	
				<tr  id="type" style="<?php echo 'display:none'; //if (get_option('choose_service') == 'international'){ echo "";} else { echo "display:none";} ?>" >
					<td><?php _e('Type of message', 'bongolive-sms');?></td>
					<td>
						<select id="bongolive_message_type" name="bongolive_message_type">
							<option value="0" <?php //if ((get_option('bongolive_message_type')) == "0"){ echo 'selected=\"selected\"';}?> selected="selected" >Plain Text</option>
							<option value="1" <?php //if ((get_option('bongolive_message_type')) == "1"){ echo 'selected=\"selected\"';}?> >Flash Message</option>
						</select>
					</td>
				</tr>
				<tr id="deliver" style="<?php echo "display:none"; //if (get_option('choose_service') == 'international'){ echo "";} else { echo "display:none";}?>" >
					<td><?php _e('Recieve Delivery reports:'); ?></td>
					<td>
						<input type="checkbox" name="wp_bongolive_delivery" id="wp_bongolive_delivery" value=""<?php //echo get_option('wp_bongolive_delivery') ==true? 'checked="checked"':'';?>/>
						<label for="wp_bongolive_delivery"><?php _e('Yes', 'bongolive-sms'); ?></label><br/>
					</td>
				</tr>
			
			<tr id="international_sender_names" style="<?php if (get_option('choose_service') == 'local'){ echo "display:none";} else { echo "";}?>" >
				<td><?php _e('Send from', 'bongolive-sms'); ?>:</td>
				<td>
					<input type="text" dir="ltr" style="width: 200px;" name="typed_sender_names" value="<?php echo get_option('typed_sender_names'); ?>"/>
					<br/><span style="font-size: 10px">Letters (11 char max) or Valid mobile #</span>
				</td>
				
			</tr>
			
			<tr id = "list_sender_names" style="<?php if (get_option('choose_service') == 'international'){ echo "display:none";} else { echo "";}?>" >
						<td><?php _e('Send from', 'bongolive-sms'); ?>:</td>
						<td>
							
							<?php 
							if (get_option('sender_names') != ""){ ?>
							<select name="bongolive_sender" id="bongolive_sender">
							<?php   $senders = get_option('sender_names');
									$senders_array = explode(',', $senders);
									foreach($senders_array as $sender)
								{ ?>
									<option value="<?php echo $sender;?>" <?php if ((get_option('bongolive_sender'))== $sender){ echo 'selected=\"selected\"';}?>><?php echo $sender; ?></option>
								<?php
								} ?>
								</select>
								<span style="font-size: 10px">Choose a Name to appear when subscribers recieve an SMS</span>
							<?php
							}else{ ?>
								<p style="color:#F00"><em>Unable to get Sender names please provide correct account details or check your internet connection</em></p>
							
							<?php }?>	
						</td>
			</tr>
			
			<tr>
				<td><?php _e('Username', 'bongolive-sms'); ?>:</td>
				<td>
					<input type="text" dir="ltr" style="width: 200px;" name="wp_bongolive_username" value="<?php echo get_option('wp_bongolive_username'); ?>"/>
				</td>
			</tr>
			<tr>
				<td><?php _e('Password', 'bongolive-sms'); ?>:</td>
				<td>
					<input type="password" dir="ltr" style="width: 200px;" name="wp_bongolive_password" value="<?php echo get_option('wp_bongolive_password'); ?>"/>
				</td>
			</tr>
			<tr id="apikey" style="<?php if (get_option('choose_service') == 'international'){ echo "display:none";} else { echo "";}?>" >
				<td><?php _e('API-Key', 'bongolive-sms'); ?>:</td>
				<td>
					<input type="text" dir="ltr" style="width: 200px;" name="wp_bongolive_apikey" value="<?php echo get_option('wp_bongolive_apikey'); ?>"/>
				</td>
			</tr>
			<tr>
				<td><?php _e('Country code:', 'bongolive-sms'); ?>:</td>
				<td>
					<input type="text" dir="ltr" style="width: 200px;" name="wp_bongolive_country_code" value="<?php if (get_option('wp_bongolive_country_code')){echo get_option('wp_bongolive_country_code');} else {echo "255";} ?>"/>
				</td>
			</tr>
			<tr>
				<td><?php _e('Send Unique SMS?', 'bongolive-sms'); ?></td>
				<td>
					<input type="checkbox" name="wp_bongolive_unique" id="wp_bongolive_unique" <?php echo get_option('wp_bongolive_unique') ==true? 'checked="checked"':'';?>/>
					<label for="wp_bongolive_unique"><?php _e('Yes', 'bongolive-sms'); ?></label><br/>
					
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><?php _e("Unique SMS will begin with Subscribe's Name",'bongolive-sms');?></td>
			</tr>
			
			<tr><th colspan="2"><h3><?php _e('Subscribers', 'bongolive-sms'); ?></h4></th></tr>
			<tr>
				<td><?php _e('Allow  subscribers to join different groups?', 'bongolive-sms'); ?></td>
				<td>
					<input type="checkbox" name="bongolive_allow_groups" id="bongolive_allow_groups" <?php echo get_option('bongolive_allow_groups') ==true? 'checked="checked"':'';?>/>
					<label for="bongolive_allow_groups"></label>
					<span style="font-size: 10px">Group option will be shown on the widget</span>
				</td>
			</tr>
			<tr>
				<td><?php _e('Send Activation code to Subscribers', 'bongolive-sms'); ?></td>
				<td>
					<input type="checkbox" name="bongolive_allow_activation" id="bongolive_allow_activation" <?php echo get_option('bongolive_allow_activation') ==true? 'checked="checked"':'';?>/>
					<label for="bongolive_allow_activation"><?php _e('Yes', 'bongolive-sms'); ?></label>
					<span style="font-size: 10px">(An SMS with activation code will be sent when subscribing to the blog)</span>
				</td>
			</tr>
			
			<tr>
				<td><?php _e('Automatically Send New posts to Subscribers', 'bongolive-sms'); ?></td>
				<td>
					<input type="checkbox" name="auto_send_posts" id="auto_send_posts" <?php echo get_option('auto_send_posts') ==true? 'checked="checked"':'';?>/>
					<label for="auto_send_posts"><?php _e('Yes', 'bongolive-sms'); ?></label><br/>
					
				</td>
			</tr>

			<tr>
				<td><?php _e('Calling jQuery in Wordpress?', 'bongolive-sms'); ?></td>
				<td>
					<input type="checkbox" name="wp_call_jquery" id="wp_call_jquery" <?php echo get_option('wp_call_jquery') ==true? 'checked="checked"':'';?>/>
					<label for="wp_call_jquery"><?php _e('Yes', 'bongolive-sms'); ?></label>
					<span style="font-size: 10px">(<?php _e('Enable this option with JQuery is called in the theme', 'bongolive-sms'); ?>)</span>
				</td>
			</tr>
			<tr>
				<td>
					<p class="submit">
					<input type="hidden" name="action" value="update" />
					<input type="hidden" name="page_options" value="typed_sender_names,choose_service,wp_bongolive_delivery,bongolive_message_type,wp_bongolive_country_code,wp_bongolive_username,wp_bongolive_password,wp_bongolive_apikey,wp_bongolive_unique,wp_call_jquery,auto_send_posts,bongolive_allow_activation,bongolive_allow_groups,bongolive_sender" />
					<input type="submit" class="button-primary" name="Submit" value="<?php _e('Update', 'wp-sms'); ?>" />
					</p>
				</td>
				<td><p class="register"><?php echo sprintf(__('<a href="%s" target=\"_blank\" >Click to create a Bongolive Account</a>', 'bongolive-sms'),$obj->bongolive_link) ?></td>
			</tr>
		</form>	
		<?php 
		} else {?>
			<tr><th colspan="2"><h3><?php _e('Current SMS settings', 'bongolive-sms'); ?></h4></th></tr>
			<tr>
				<td>
					<?php _e('SMS web Service:','bongolive-sms');?>
				</td>
				<td>
					<?php echo "<a href='http://www.bongolive.co.tz' target=\"_blank\" >"?>Bongolive SMS Services</a><span><?php echo " (".get_option('choose_service').")";?><span>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e('Send From:','bongolive-sms');?>
				</td>
				<td>
					<?php if (get_option('choose_service') == 'international'){ echo get_option('typed_sender_names');} else { echo get_option('bongolive_sender');}?>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e('User Name:','bongolive-sms');?>
				</td>
				<td>
					<?php echo get_option('wp_bongolive_username');?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo "Message Type:"?>
				</td>
				<td>
					<?php if ( get_option('wp_bongolive_unique') == true){
					echo "Unique";
					}else{
						echo "Normal";
					}
					?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo "Posts Sent Automatically?"?>
				</td>
				<td>
					<?php if ( get_option('auto_send_posts') == true){
					echo "Yes";
					}else{
						echo "No";
					}
					?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo "Available Credits:"?>
				</td>
				<td>
					<?php echo get_option('bongolive_sms_credits');?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo "<a href = '".get_bloginfo('url')."/wp-admin/admin.php?page=bongolive-sms/bongolive-sms.php&settings=new'>Edit Settings</a>" ?>
				</td>
			</tr>
		<?php }?>
	</table>
</div>
