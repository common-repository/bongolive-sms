<link rel="stylesheet" type="text/css" href="<?php bloginfo('url'); ?>/wp-content/plugins/bongolive-sms/css/style.css" />
<style type="text/css">
.clean{
padding-top:10px;
align:center;
}
.powered{
width:90px;
height:10px; 
float:left;
font-family:Tahoma; 
font-size:10px;
margin-left:45px; 
margin-top:10px;
}
.lower{
width:90px; 
height:30px;  
background-image: url(http://www.bongolive.co.tz/plugins/blogspot/bongolive_logo.png); 
background-repeat:no-repeat; 
margin-left:100px;
}
</style>
<?php if(get_option('wp_call_jquery')) { ?>
<script src="<?php bloginfo('url'); ?>/wp-content/plugins/bongolive-sms/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<?php } ?>
<script type="text/javascript">
	$(document).ready(function()
	{
		$("#submit_newsletter").click(function()
		{
			var get_subscribe_name = $("#subscribe_name").val();
			var get_subscribe_mobile = $("#subscribe_mobile").val();
			var get_subscribe_group = $("#selected_group").val();
			var get_subscribe_type = $('input[name=subscribe_type]:checked').val();
			$("#show_result_activation").html('');
			$("#show_result").html('<img src="<?php bloginfo('url'); ?>/wp-content/plugins/bongolive-sms/images/loading.gif"/>');
			$("#show_result").load('<?php echo plugin_dir_url(__FILE__); ?>/newsletter.php', {name:get_subscribe_name, mobile:get_subscribe_mobile, type:get_subscribe_type, group:get_subscribe_group});
		});

	  
	});
	function openWin(){
		window.open("http://www.bongolive.co.tz");
    }
    function sendActivation(){
        
            var get_subscribe_mobile = $("#subscribe_mobile").val();
			var get_activation = $("#get_activation").val();
			var get_subscribe_name = $("#subscribe_name").val();
		    var get_subscribe_group = $("#selected_group").val();
		    $("#show_result_activation").html('<img src="<?php bloginfo('url'); ?>/wp-content/plugins/bongolive-sms/images/loading.gif"/>');
			$("#show_result_activation").load('<?php echo plugin_dir_url(__FILE__); ?>activation.php', {mobile:get_subscribe_mobile, activation:get_activation, name:get_subscribe_name, group:get_subscribe_group});
		
    }
</script>
<div style="margin-left:15px;">
<table cellspacing="0" cellpadding="10">

	<tr >
		<td><?php _e('Your name', 'bongolive-sms'); ?>:</td>
		<td><input type="text" id="subscribe_name"/></td>
	</tr>

	<tr >
		<td><?php _e('Your mobile', 'bongolive-sms'); ?>:</td>
		<td><input type="text" id="subscribe_mobile" value="<?php echo get_option('wp_bongolive_country_code');?>"/></td>
	</tr>

	<?php if (get_option('bongolive_allow_groups')){?>
	
	<tr class="register-tr">
	
		<td><?php _e('Group', 'bongolive-sms'); ?>:</td>
		<td>
			<select name="selected_group" id="selected_group">
				<?php 
					global $wpdb, $table_prefix;
					$get_groups = $wpdb->get_results("SELECT group_name, ID FROM {$table_prefix}bongolive_groups ");
					foreach($get_groups as $groups)
					{ ?>
					<option value="<?php echo $groups->ID;?>" ><?php echo $groups->group_name;?></option>
								
				<?php } ?>
			</select>
		</td>
		
	</tr>
	
	<?php }/*else{?>
			<select name="selected_group" id="selected_group" style="display:none;"><option value=<?php echo DEFAULTGROUP_ID; ?>></option>
	<?php }*/?>
	
	<tr class="register-tr">
		<td colspan="2">
			<input type="radio" name="subscribe_type" id="type_subscribe" value="subscribe" checked="checked"/>
			<label for="type_subscribe"><?php _e('Subscribe', 'bongolive-sms'); ?></label>

			<input type="radio" name="subscribe_type" id="type_unsubscribe" value="unsubscribe"/>
			<label for="type_unsubscribe"><?php _e('Unsubscribe', 'bongolive-sms'); ?></label>
            
		</td>
	</tr>

	<tr>
		<td colspan="2">
			<button id="submit_newsletter" style="align:center"><?php _e('Submit', 'bongolive-sms'); ?></button>
			<span id="show_result"></span>
			<span id="show_result_activation"></span>
		</td>
	</tr>
</table>
</div>
<div class="powered">Powered by </div><a href="javascript:openWin()" ><div class="lower"></div></a>
