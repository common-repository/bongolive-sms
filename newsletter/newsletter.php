<?php
	include_once("../../../../wp-load.php");

	$name	= trim($_REQUEST['name']);
	$mobile	= trim($_REQUEST['mobile']);
	$type	= $_REQUEST['type'];
	$group = $_REQUEST['group'];
	global $obj;
	
	
	if($name && $mobile) {
		$mobile = $obj->check_mobile($mobile);
		if($mobile) {
			global $wpdb, $table_prefix;

			$check_mobile = $wpdb->query("SELECT mobile FROM {$table_prefix}bongolive_subscribes WHERE mobile='".$mobile."'");
			if(!$check_mobile || $type != 'subscribe') {
				if($type == 'subscribe'){
					$get_current_date = date('Y-m-d H:i:s' ,current_time('timestamp',0));

					if(get_option('bongolive_allow_activation')) {
						$key = rand(1000, 9999);
						update_option('tempo_activation',$key);
                        $sending_method = get_option('choose_service');
						$obj->to = $mobile;
						$obj->msg = __('Your activation code ', 'bongolive-sms') . ': ' . $key ." Send Activation code NOW!";
                        if ($sending_method == 'local'){
                           $obj->send_sms(); 
                        }else{
                           $obj->send_sms_international();
                        }
                        echo '<br /><span id="code_header">Please enter the activation code sent to your mobile:</span>';
						echo '<input type="text" id="get_activation" name="get_activation"/><br/><button id="send_activation" onclick="sendActivation()">Activate</button>';
                        
					}else{
							if (get_option('bongolive_allow_groups')){
							$query = "INSERT INTO {$table_prefix}bongolive_subscribes (date, name, mobile, status, group_ID) VALUES ('".$get_current_date."', '".$name."', '".$mobile."', '1', '".$group."')";
							}else{
							$query = "INSERT INTO {$table_prefix}bongolive_subscribes (date, name, mobile, status, group_ID) VALUES ('".$get_current_date."', '".$name."', '".$mobile."', '1', '".DEFAULTGROUP."')";
							}
							if($wpdb->query($query))
							{
								_e('Subscribe Successfull', 'bongolive-sms'); ?>
								<script type="text/javascript">
									document.getElementById("subscribe_mobile").value = "";
									document.getElementById("subscribe_name").value = "";
								</script>
							<?php }
					}
				} elseif($type == 'unsubscribe') {
					if($check_mobile) {
						$check = $wpdb->query("DELETE FROM {$table_prefix}bongolive_subscribes WHERE mobile='".$mobile."'");
						if($check) {
							echo '<span id="result-register">' . __('Subscribe deleted.', 'bongolive-sms') . '</span>';?>
							<script type="text/javascript">
									document.getElementById("subscribe_mobile").value = "";
									document.getElementById("subscribe_name").value = "";
							</script>
						<?php }
					} else {
						echo '<span id="result-register">' . __('Nothing found!', 'bongolive-sms') . '</span>';
					}
				}
			} else {
				echo '<span id="result-register">' . __('Phone number is repeated', 'bongolive-sms') . '</span>';
			}
		} else {
			echo '<span id="result-register">' . __('Please enter a valid mobile number', 'bongolive-sms') . '</span>';
		}
	} else {
		echo '<span id="result-register">' . __('Please complete all fields', 'bongolive-sms') . '</span>';
	}
?>
