<?php
	global $wpdb, $table_prefix;
	
	$group_id = $_GET['group_id'];
	if (!isset($group_id)){
		$currently = "Subscribers";
		$hd_1_Name = "Group";
		$hd_2_Number = "Subscribers";
		$hd_3_status = "Status";
		$fot_1_Name = "Total";
		$hd_4_date = "Date Created";
		$get_result = $wpdb->get_results("SELECT * FROM {$table_prefix}bongolive_groups ");
	}else{
		$currently = "GROUP: " . $_GET['group'];
		$hd_1_Name = "Name";
		$hd_2_Number = "Mobile";
		$hd_3_status = "Status";
		$hd_4_date = "Date of Joining";
		$fot_1_Name = "Name";
		$fot_2_Number = "Mobile";
		$fot_3_status = "Status";
		$fot_4_date = "Date of Joining";
		$get_result = $wpdb->get_results("SELECT * FROM {$table_prefix}bongolive_subscribes WHERE group_ID = {$group_id}");
		$go_back = "<a href = '".get_bloginfo('url')."/wp-admin/admin.php?page=bongolive-sms/subscribe'>Back To Groups</a>";
		
	}
?>




<div class="wrap">
		<h2><img src="<?php bloginfo('url'); ?>/wp-content/plugins/bongolive-sms/images/logo.png"/><br/><?php _e($currently, 'bongolive-sms'); ?></h2>
		<form action="" method="post">
			<table  class="widefat fixed" style="width: 80%" cellspacing="0">
				<thead>
					<tr>
						<th id="cb" scope="col" class="manage-column column-cb check-column" style="width:6%"><input type="checkbox" name="checkAll" value=""/></th>
						<th scope="col" class="manage-column column-name" width="20%"><?php _e($hd_1_Name, 'bongolive-sms'); ?></th>
						<th scope="col" class="manage-column column-name" width="20%"><?php _e($hd_2_Number, 'bongolive-sms'); ?></th>
						<th scope="col" class="manage-column column-name" width="20%"><?php _e($hd_3_status, 'bongolive-sms'); ?></th>
						<th scope="col" class="manage-column column-name" width="20%"><?php _e($hd_4_date, 'bongolive-sms'); ?></th>
					</tr>
				</thead>
			

				<tbody>
					<?php
					if(count($get_result ) > 0)
					{
						foreach($get_result as $gets)
						{
							$i++;
							if (isset($group_id)){
							$number_col = $gets->mobile;
							$name_col = $gets->name;
							$status_col = $gets->status;
							$date_col = $gets->date;
							}else{
							$name_col = "<a href = '".get_bloginfo('url')."/wp-admin/admin.php?page=bongolive-sms/subscribe&group=".$gets->group_name."&group_id=".$gets->ID."'>".$gets->group_name."</a>";
							$number_col = count($wpdb->get_results("SELECT s.mobile FROM {$table_prefix}bongolive_subscribes s WHERE s.group_ID = {$gets->ID}"));
							$status_check = count($wpdb->get_results("SELECT s.status FROM {$table_prefix}bongolive_subscribes s WHERE s.status = 1 AND s.group_ID = '{$gets->ID}'"));
							$fot_2_Number += $number_col;
							$fot_3_status += $status_check;
							$date_col = $gets->date;
								if ($status_check > 0){
									$status_col = 1;
									$status_check = "(".$status_check.")";
								}else{
									$status_col = 0;
									$status_check = "";
								}
							$go_back = "";
							}
					?>
					<tr class="<?php echo $i % 2 == 0 ? 'alternate':'author-self'; ?>" valign="middle" id="link-2">
						<th class="check-column" scope="row"><input type="checkbox" name="column_ID[]" value="<?php echo $gets->ID ; ?>" /></th>
						<td class="column-name"><?php echo $name_col; ?></td>
						<td class="column-name"><?php echo $number_col; ?></td>
						<td class="column-name"><img src="<?php echo bloginfo('url') . '/wp-content/plugins/bongolive-sms/images/' . $status_col; ?>.png" align="middle"/><?php echo $status_check;?></td>
						<td scope="col" class="manage-column column-name" width="20%"><?php _e($date_col, 'bongolive-sms'); ?></td>
					</tr>
					<?php
						}
					} else { ?>
						<tr>
							<td colspan="5"><?php _e('No Subscribers!', 'bongolive-sms'); ?></td>
						</tr>
					<?php } ?>
				</tbody>

				<tfoot>
					<tr>
						<th id="cb" scope="col" class="manage-column column-cb check-column"><input type="checkbox" name="checkAll" value=""/></th>
						<th scope="col" class="manage-column column-name" width="20%"><?php _e($fot_1_Name, 'bongolive-sms'); ?></th>
						<th scope="col" class="manage-column column-name" width="20%"><?php _e($fot_2_Number, 'bongolive-sms'); ?></th>
						<th scope="col" class="manage-column column-name" width="20%"><?php _e($fot_3_status, 'bongolive-sms'); ?></th>
						<th scope="col" class="manage-column column-name" width="20%"><?php _e($fot_4_date, 'bongolive-sms'); ?></th>
					</tr>
				</tfoot>
			</table>
			<?php if (!isset($group_id)) 
			{?>
			<div class="tablenav">
				<div class="alignleft actions">
					<select name="action">
						<option selected="selected"><?php _e('Group Actions', 'bongolive-sms'); ?></option>
						<option value="trash"><?php _e('Delete', 'bongolive-sms'); ?></option>
						<option value="active"><?php _e('Activate', 'bongolive-sms'); ?></option>
						<option value="deactive"><?php _e('Deactivate', 'bongolive-sms'); ?></option>
					</select>
					<input value="<?php _e('Apply', 'bongolive-sms'); ?>" name="groupaction" id="groupaction" class="button-secondary action" type="submit"/>
				</div>
				<br class="clear">
			</div>
			<?php 
			} else {
			?>
			<div class="tablenav">
				<div class="alignleft actions">
					<select name="action">
						<option selected="selected"><?php _e('Subscriber Actions', 'bongolive-sms'); ?></option>
						<option value="trash"><?php _e('Delete', 'bongolive-sms'); ?></option>
						<option value="active"><?php _e('Activate', 'bongolive-sms'); ?></option>
						<option value="deactive"><?php _e('Deactivate', 'bongolive-sms'); ?></option>
					</select>
					<input value="<?php _e('Apply', 'bongolive-sms'); ?>" name="subscribeaction" id="subscribeaction" class="button-secondary action" type="submit"/>
				</div>
				<br class="clear">
			</div>
			<?php 
			}?>
		</form>
		<?php if (isset($group_id))
		{?>
		<form action="" method="post">
			<table>
				<tr>
					<td><span class="label_td"><?php _e('Name', 'bongolive-sms'); ?>:</span><input type="text" name="new_bongolive_subscribe_name" style="width:120px" value="<?php echo get_option('bongolive_add_subscribe');?>"/></td>
					<td><span class="label_td"><?php _e('Mobile', 'bongolive-sms'); ?>:</span><input type="text" name="new_bongolive_subscribe_mobile" class="ltr_td" style="width:120px" value="<?php echo get_option('bongolive_add_mobile');?>"/></td>
					<td><input type="submit" class="button-primary" name="new_bongolive_subscribe" value="<?php _e('Add', 'bongolive-sms'); ?>" /><span style="font-size: 10px">Include a country code e,g 255787000000</span></td>
				</tr>
				<tr></tr>
				<tr>
					<td><?php _e($go_back, 'bongolive-sms'); ?></td>
				</tr>
			</table>
		</form>
		<?php
		} else 
		{ ?>
		<form action="" method="post">
				<tr>
					<td><span class="label_td"><?php _e('New Group', 'bongolive-sms'); ?>:</span><input type="text" name="new_bongolive_group_name"  class="ltr_td" style="width:120px" /></td>
					<td><input type="submit" class="button-primary" name="new_bongolive_subscribe_group" value="<?php _e('CREATE', 'bongolive-sms'); ?>" /></td>
				</tr>
		</form>
		<?php  
		} ?>
	</div>
