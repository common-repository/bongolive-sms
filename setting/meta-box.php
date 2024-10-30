<p>
	<label for="subscribe_post"><?php _e('Send this post to subscribers?', 'bongolive-sms'); ?></label>
	<select name="subscribe_post" id="subscribe_post">
		<option value="yes" <?php if ( get_option('auto_send_posts') == true){echo 'selected=\"selected\"';}?>><?php _e('Yes');?></option>
		<option value="no" <?php if ( !get_option('auto_send_posts') == true){echo 'selected=\"selected\"';}?>><?php _e('No');?></option>
	</select>
	<?php if ( get_option('auto_send_posts') == true){?>
		<script type="text/javascript">alert("An SMS will be sent for this post.To STOP change settings below");</script>
	<?php } else{?>
		<script type="text/javascript">alert("An SMS will NOT be sent for this post.To SEND change settings below");</script>
	<?php }?>
</p>

