<?php

function wpjd_scripts_styles($hook) {
	global $wpjd_page_hook;
	if( $wpjd_page_hook != $hook )
		return;
	wp_enqueue_style("wpjd-admin-page", plugins_url( "css/admin.css" , __FILE__ ), false, "1.0", "all");
	wp_enqueue_script("wpjd-admin-page", plugins_url( "js/admin.js" , __FILE__ ), false, "1.0");
}
add_action( 'admin_enqueue_scripts', 'wpjd_scripts_styles' );

function wpjd_menu_item() {
	global $wpjd_page_hook;
    $wpjd_page_hook = add_menu_page( 'Junk Deleter', 'Junk Deleter', 'administrator', 'wpjd_settings', 'wpjd_render_settings_page' );
}
add_action( 'admin_menu', 'wpjd_menu_item' );

function wpjd_render_settings_page() {
?>
	<div class="wrap">
		<div name="icon-options-general" class="icon32"></div>
		<h2>Junk Deleter</h2>
		<?php settings_errors(); ?>
		<?php
			$manual = (!isset($_GET['tab']) || $_GET['tab'] == 'manual');
			$automatic = (isset($_GET['tab']) && $_GET['tab'] == 'automatic');
			if($automatic) $values = get_option('wpjd_automatic_values');
		?>
		<p class="warning">Please make sure you have a backup of your database before proceeding. The operations listed here can not be undone.</p>
		<ul id="wpjd-tabs">
			<li><a class="<?=(($manual)?'active':'')?>" href="?page=wpjd_settings&tab=manual">Manual</a></li>
			<li><a class="<?=(($automatic)?'active':'')?>" href="?page=wpjd_settings&tab=automatic">Automatic</a></li>
		</ul>
		<form id="wpjd-form">
			<p>
			<?php if($manual): ?>
				The selected operations will be performed as soon as you hit the 'Go' button
			<?php else: ?>
				The optimizations that you select and save here will be performed automatically after regular intervals
			<?php endif; ?>
			</p>
			<ul id="wpjd-form-ul">
				<li>
					<input type="hidden" value="0" name="wpjd_post_drafts" />
					<input type="checkbox" value="1" id="wpjd_post_drafts" name="wpjd_post_drafts" <?php if(isset($values['wpjd_post_drafts'])) checked($values['wpjd_post_drafts'], "1"); ?> /> <label for="wpjd_post_drafts">Delete post drafts that have not been modified in the last</label> <input type="text" name="wpjd_post_drafts_days" value="<?php if(isset($values['wpjd_post_drafts_days'])) echo $values['wpjd_post_drafts_days']; else echo "100"; ?>" /> days
				</li>
				<li>
					<input type="hidden" value="0" name="wpjd_post_revisions" />
					<input type="checkbox" value="1" id="wpjd_post_revisions" name="wpjd_post_revisions" <?php if(isset($values['wpjd_post_revisions'])) checked($values['wpjd_post_revisions'], "1"); ?> /> <label for="wpjd_post_revisions">Delete post revisions</label>
				</li>
				<li>
					<input type="hidden" value="0" name="wpjd_post_auto_drafts" />
					<input type="checkbox" value="1" id="wpjd_post_auto_drafts" name="wpjd_post_auto_drafts" <?php if(isset($values['wpjd_post_auto_drafts'])) checked($values['wpjd_post_auto_drafts'], "1"); ?> /> <label for="wpjd_post_auto_drafts">Delete post auto-drafts</label>
				</li>
				<li>
					<input type="hidden" value="0" name="wpjd_trashed_posts" />
					<input type="checkbox" value="1" id="wpjd_trashed_posts" name="wpjd_trashed_posts" <?php if(isset($values['wpjd_trashed_posts'])) checked($values['wpjd_trashed_posts'], "1"); ?> /> <label for="wpjd_trashed_posts">Delete trashed posts</label>
				</li>
				<li>
					<input type="hidden" value="0" name="wpjd_orphan_postmeta" />
					<input type="checkbox" value="1" id="wpjd_orphan_postmeta" name="wpjd_orphan_postmeta" <?php if(isset($values['wpjd_orphan_postmeta'])) checked($values['wpjd_orphan_postmeta'], "1"); ?> /> <label for="wpjd_orphan_postmeta">Delete orphan post meta</label>
				</li>
				<li>
					<input type="hidden" value="0" name="wpjd_comment_pending" />
					<input type="checkbox" value="1" id="wpjd_comment_pending" name="wpjd_comment_pending" <?php if(isset($values['wpjd_comment_pending'])) checked($values['wpjd_comment_pending'], "1"); ?> /> <label for="wpjd_comment_pending">Delete pending comments older than</label> <input type="text" name="wpjd_comment_pending_days" value="<?php if(isset($values['wpjd_comment_pending_days'])) echo $values['wpjd_comment_pending_days']; else echo "100"; ?>" /> days
				</li>
				<li>
					<input type="hidden" value="0" name="wpjd_comment_spam" />
					<input type="checkbox" value="1" id="wpjd_comment_spam" name="wpjd_comment_spam" <?php if(isset($values['wpjd_comment_spam'])) checked($values['wpjd_comment_spam'], "1"); ?> /> <label for="wpjd_comment_spam">Delete spam comments</label>
				</li>
				<li>
					<input type="hidden" value="0" name="wpjd_comment_trash" />
					<input type="checkbox" value="1" id="wpjd_comment_trash" name="wpjd_comment_trash" <?php if(isset($values['wpjd_comment_trash'])) checked($values['wpjd_comment_trash'], "1"); ?> /> <label for="wpjd_comment_trash">Delete trash comments</label>
				</li>
				<li>
					<input type="hidden" value="0" name="wpjd_comment_pingback_trackback" />
					<input type="checkbox" value="1" id="wpjd_comment_pingback_trackback" name="wpjd_comment_pingback_trackback" <?php if(isset($values['wpjd_comment_pingback_trackback'])) checked($values['wpjd_comment_pingback_trackback'], "1"); ?> /> <label for="wpjd_comment_pingback_trackback">Delete pingbacks and trackbacks</label>
				</li>
				<li>
					<input type="hidden" value="0" name="wpjd_orphan_commentmeta" />
					<input type="checkbox" value="1" id="wpjd_orphan_commentmeta" name="wpjd_orphan_commentmeta" <?php if(isset($values['wpjd_orphan_commentmeta'])) checked($values['wpjd_orphan_commentmeta'], "1"); ?> /> <label for="wpjd_orphan_commentmeta">Delete orphan comment meta</label>
				</li>
				<?php if($automatic): ?>
					<li>
						Perform these optimizations:
						<select name="wpjd_optimization_interval">
							<option value="never" <?php if(isset($values['wpjd_optimization_interval'])) selected($values['wpjd_optimization_interval'], 'never'); ?>>never</option>
							<option value="weekly" <?php if(isset($values['wpjd_optimization_interval'])) selected($values['wpjd_optimization_interval'], 'weekly'); ?>>every week</option>
							<option value="monthly" <?php if(isset($values['wpjd_optimization_interval'])) selected($values['wpjd_optimization_interval'], 'monthly'); ?>>every month</option>
						</select>
					</li>
				<?php endif; ?>
				<input type="hidden" name="action" value="<?=($manual)?'wpjd_optimize_ajax':'wpjd_save_settings_ajax'?>"/>
			</ul>
			<button type="button" class="button"><?=($manual)?'Go':'Save Schedule Changes'?></button><img class="wpjd-loading" src="<?=plugins_url('img/loading.gif', __FILE__)?>">
		</form>
	</div>
<?php }

?>