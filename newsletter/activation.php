<?php
	include_once("../../../../wp-load.php");

	$activation = trim($_REQUEST['activation']);
	$mobile = trim($_REQUEST['mobile']);
	$name = trim($_REQUEST['name']);
	$group = trim($_REQUEST['group']);
	
	if($activation)
	{
		global $wpdb, $table_prefix;
		$check_mobile = $wpdb->query("SELECT mobile FROM {$table_prefix}bongolive_subscribes WHERE mobile='".$mobile."'");
	if (!$check_mobile){
		if($activation == get_option('tempo_activation'))
		{
			$get_current_date = date('Y-m-d H:i:s' ,current_time('timestamp',0));
			if (get_option('bongolive_allow_groups')){
				$query = "INSERT INTO {$table_prefix}bongolive_subscribes (date, name, mobile, status, group_ID) VALUES ('".$get_current_date."', '".$name."', '".$mobile."', '1', '".$group."')";
			}else{
				$query = "INSERT INTO {$table_prefix}bongolive_subscribes (date, name, mobile, status, group_ID) VALUES ('".$get_current_date."', '".$name."', '".$mobile."', '1', '".DEFAULTGROUP."')";
			}
			if($wpdb->query($query))
			{
				update_option('bongolive_sms_credits', $obj->get_account(BALANCE));?>
				<script type="text/javascript">$("#code_header").fadeOut();
											   $("#get_activation").fadeOut();
											   $("#activation").fadeOut();
											   $("#show_result").html('Subscribe Successful!');
											   document.getElementById("subscribe_mobile").value = "";
											   document.getElementById("subscribe_name").value = "";
				</script>
			<?php				
			}
		} else {
			_e('Security Code is wrong', 'bongolive-sms');
		}
	}else{
		_e('Mobile Number exists','bongolive-sms');
	}
	} else {
		_e('Please complete all fields', 'bongolive-sms');
	}
?>
