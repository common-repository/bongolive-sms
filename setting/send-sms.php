<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js" type="text/javascript"></script>
	<script src="<?php bloginfo('url'); ?>/wp-content/plugins/bongolive-sms/js/functions.js" type="text/javascript"></script>
	<script type="text/javascript">
		var boxId2 = 'wp_get_message';
		var counter = 'wp_counter';
		var part = 'wp_part';
		var max = 'wp_max';
		function charLeft2() {
			checkSMSLength(boxId2, counter, part, max);
		}

		$(document).ready(function(){
			$("select#select_sender").change(function(){
				var get_method = "";
				$("select#select_sender option:selected").each(
					function(){
						get_method += $(this).attr('id');
					}
				);
				if(get_method == 'wp_tellephone'){
					$("#wp_get_numbers").fadeIn();
					$("#wp_get_number").focus();
				} else {
					$("#wp_get_numbers").fadeOut();
				}
			});

			charLeft2();
			$("#" + boxId2).bind('keyup', function() {
				charLeft2();
			});
			$("#" + boxId2).bind('keydown', function() {
				charLeft2();
			});
			$("#" + boxId2).bind('paste', function(e) {
				charLeft2();
			});
		});
	</script>

	<style>
	#wp_get_number:focus{border:1px solid #FF0000;}
	.number{font-weight: bold;}
	</style>
	<div class="wrap">
		<h2><img src="<?php bloginfo('url'); ?>/wp-content/plugins/bongolive-sms/images/logo.png"/><br/><?php _e('Send SMS', 'bongolive-sms'); ?></h2>
		<?php
		global $obj, $wpdb, $table_prefix;
		
				?>
				<form method="post" action="">
				<table class="form-table">
					<tr>
						<td colspan="2">
						<?php
							if(isset($_POST['send_sms'])) {

								if($_POST['wp_get_message']) {
									    $valid_typed_sender_name = $obj->prep_sender_name($_POST['typed_sender_name']);
										if ($valid_typed_sender_name || $_POST['bongolive_sender'] != ""){
											if($_POST['wp_send_to'] == "wp_subscribe_user") {
												$number_to_send = $wpdb->get_col("SELECT mobile FROM {$table_prefix}bongolive_subscribes WHERE status = 1");
												$name_to_send = $wpdb->get_col("SELECT name FROM {$table_prefix}bongolive_subscribes WHERE status = 1");
												// form an array of names corresponding to the array of numbers
												$current_names = array();
												foreach( $name_to_send as $current_name){
												$current_names[] = $current_name;
												}										
											}
											else if($_POST['wp_send_to'] == "wp_tellephone") {
												$number_to_send = explode(",", $_POST['wp_get_number']);
												update_option('numbers',$_POST['wp_get_number']);
											}
											$reciever = array();
											
											if (get_option('choose_service') == 'local'){ $obj->sender = $_POST['bongolive_sender'];} else { $obj->sender = $valid_typed_sender_name ;}
											$obj->msg = $_POST['wp_get_message'];
											update_option('tempo_msg',$obj->msg);
											foreach( $number_to_send as $mobile){
												$reciever[] = $obj->check_mobile("$mobile");
												
											}
											//count for setting the array names
											$count = 0; $errors = 0; $success= 0;
											$msgcontent = $obj->msg;
                                            
                                            // Send the messages in a loop and record the errors
                                            
											foreach ($reciever as $mobile){
												$obj->to = $mobile;
												if ((get_option('wp_bongolive_unique')) && ($_POST['wp_send_to'] == "wp_subscribe_user") ){
													$obj->msg = $current_names[$count]." ".$msgcontent;
												}
												$count++;
												if (get_option('choose_service') == 'local'){
													$response = $obj->send_sms();
												}else{
													$response = $obj->send_sms_international();
													$response = substr($response,0,4);
												}
												if ($response == 1 || $response == '1701'){
													$success++;
												}else{
													$errors++;
												}
											}
											if(($success > 0) && ($errors == 0)) {
												echo "<div class='updated'><p>" . __('SMS was sent with success', 'bongolive-sms') . "</p></div>";
													update_option('bongolive_sms_credits', $obj->get_account(BALANCE));
											}elseif($success == 0 && $errors > 0){
											     // Complete failure of sending messages
                                                 if (($response == 1707) || ($response == -6)){
                                                    echo "<div class='error'><p>" . __('Invalid sender name used', 'bongolive-sms') . "</p></div>";
                                                 }elseif(($response == 1025) || ($response == -2)){
                                                    echo "<div class='error'><p>" . __('Insufficient credits to send messages', 'bongolive-sms') . "</p></div>";
                                                 }elseif(($response == 1703) || ($response == -13)){
                                                    echo "<div class='error'><p>" . __('Invalid username or password', 'bongolive-sms') . "</p></div>";
                                                 }                                                  
											}else{
												echo "<div class='error'><p>" .$success." Messages SENT, ".$errors." FAILED</p></div>";
													
													update_option('bongolive_sms_credits', $obj->get_account(BALANCE));
											}
										}else{
											echo "<div class='error'><p>You have entered an invalid sender name</p></div>";
										}
								} else {
									echo "<div class='error'><p>" . __('All fields must be completed check the message field and sender field', 'bongolive-sms') . "</p></div>";
								}
								
							}
						?>
						</td>
					</tr>
					<?php wp_nonce_field('update-options');?>
					<tr>
					  <?php if(get_option('choose_service') == 'local'){?>
						<td>SMS Credit:</td>
						<td><?php echo get_option('bongolive_sms_credits'); ?></td>
					 <?php } ?>
						
					</tr>
					<tr>
						<td><?php _e('Send from', 'bongolive-sms'); ?>:</td>
						<td>
						    <?php if(get_option('choose_service') == 'local'){?>
								<?php 
								if (get_option('sender_names') > 0){ ?>
								<select name="bongolive_sender" id="bongolive_sender">
								<?php $senders = get_option('sender_names');;
									$senders_array = explode(',', $senders);
									foreach($senders_array as $sender)
									{ ?>
										<option value="<?php echo $sender;?>" ><?php echo $sender; ?></option>
									<?php
									} ?>
									</select>
									<span style="font-size: 10px">Choose a Name to appear when subscribers recieve an SMS</span>
								<?php
								}else{ ?>
									<p style="color:#F00"><em>Unable to get Sender names please provide correct account details or check your internet connection</em></p>
								
								<?php }?>
							<?php }else{?>
								<input type="text" style="direction:ltr;" id="typed_sender_name" name="typed_sender_name" value="<?php if ($_POST['typed_sender_name'] != "") echo $_POST['typed_sender_name']; else echo get_option('typed_sender_names')?>"/>
							<?php }?>
						</td>
			</tr>
					<tr>
						<td><?php _e('Send to', 'bongolive-sms'); ?>:</td>
						<td>
							<select name="wp_send_to" id="select_sender">
								<?php global $wpdb, $table_prefix; ?>
								<option value="wp_subscribe_user" id="wp_subscribe_user">
									<?php
										$user_active = $wpdb->query("SELECT mobile FROM {$table_prefix}bongolive_subscribes WHERE status = '1'");
										echo sprintf(__('Subscribe users (%s) active', 'bongolive-sms'), $user_active);
									?>
								</option>
								<option value="wp_tellephone" id="wp_tellephone"><?php _e('Numbers', 'bongolive-sms'); ?></option>
							</select>

							<span id="wp_get_numbers" style="display:none;">
								<input type="text" style="direction:ltr;" id="wp_get_number" name="wp_get_number" value="<?php echo get_option('numbers')?>"/>
								<span style="font-size: 10px">Include a country code e,g 255787000000,255654898989</span>
							</span>
						</td>
					</tr>
					<tr>
						<td> <?php echo 'Unique message:'?></td>
						<td> <?php if (get_option('wp_bongolive_unique')){	
								echo "Active";
						}else{
							echo "Deactive";
						}?></td>
					</tr>
					<tr>
						<td><?php _e('SMS', 'bongolive-sms'); ?>:</td>
						<td>
							<textarea name="wp_get_message" id="wp_get_message" style="width:350px; height: 200px; direction:ltr;" ><?php echo $_POST['wp_get_message']//get_option('tempo_msg');  ?></textarea><br />
							<?php _e('The remaining words', 'bongolive-sms'); ?>: <span id="wp_counter" class="number"></span>/<span id="wp_max" class="number"></span><br />
							<span id="wp_part" class="number"></span> <?php _e('SMS', 'bongolive-sms'); ?><br />

						</td>
					</tr>
					<tr><td>
							&nbsp;
						</td>
						<td>
							<p class="submit">
							<input type="submit" class="button-primary" name="send_sms" value="<?php _e('Send SMS', 'bongolive-sms'); ?>" />
							</p>
						</td>
					</tr>
				</form>
			</table>
	</div>
