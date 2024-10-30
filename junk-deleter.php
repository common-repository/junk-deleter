<?php
/*
Plugin Name: Junk Deleter
Plugin URI: http://visualinternetpromotions.com/
Description: Allows you to remove unnecessary items from your WordPress database, making it lean and fast. Has manual as well as automatic clean-up options.
Version: 1.0
Author: Paul Revene
Author URI: http://visualinternetpromotions.com/
License: GPL2
*/

function wpjd_rollback(){
	wp_deregister_style( 'wpjd-admin-page' );
	wp_deregister_script( 'wpjd-admin-page' );
	delete_option('wpjd_automatic_values');
	wp_clear_scheduled_hook( 'wpjd_event_hook' );
}
register_uninstall_hook(__FILE__, 'wpjd_rollback');

function wpjd_build_and_run_queries($data){
	global $wpdb;
	$postmeta_cleanup_needed 			= false;
	$comments_orphan_cleanup_needed 	= false;
	$comments_trash_cleanup_needed 		= false;
	$commentmeta_cleanup_needed 		= false;
	$queries 							= array();
	if($data['wpjd_post_drafts'] || $data['wpjd_post_revisions'] || $data['wpjd_post_auto_drafts'] || $data['wpjd_trashed_posts']){
		$where 	= array();
		if($data['wpjd_post_drafts']){
			$days = (is_numeric($data['wpjd_post_drafts_days']))?$data['wpjd_post_drafts_days']:0;
			$date = date("Y-m-d H:i:s", strtotime("$days days ago"));
			$where[] = "(post_status = 'draft' AND post_modified < '$date')";
		}
		if($data['wpjd_post_revisions']){
			$where[] = "post_type = 'revision'";
		}
		if($data['wpjd_post_auto_drafts']){
			$where[] = "post_status = 'auto-draft'";
		}
		if($data['wpjd_trashed_posts']){
			$where[] = "post_status = 'trash'";
			$comments_trash_cleanup_needed = true;
		}
		$where = implode(' OR ', $where);
		$queries[] = "DELETE FROM $wpdb->posts WHERE $where";
		$postmeta_cleanup_needed = true;
		$comments_orphan_cleanup_needed = true;
	}
	if($data['wpjd_comment_pending'] || $data['wpjd_comment_spam'] || $data['wpjd_comment_trash'] || $comments_trash_cleanup_needed || $comments_orphan_cleanup_needed){
		$where = array();
		if($data['wpjd_comment_pending']){
			$days = (is_numeric($data['wpjd_comment_pending_days']))?$data['wpjd_comment_pending_days']:0;
			$date = date("Y-m-d H:i:s", strtotime("$days days ago"));
			$where[] = "(comment_approved = '0' AND comment_date < '$date')";
		}
		if($data['wpjd_comment_spam'])
			$where[] = "comment_approved = 'spam'";
		if($data['wpjd_comment_trash'])
			$where[] = "comment_approved = 'trash'";
		if($data['wpjd_comment_pingback_trackback'])
			$where[] = "(comment_type = 'pingback' OR comment_type = 'trackback')";
		if($comments_trash_cleanup_needed){
			$where[] = "comment_approved = 'post-trashed'";
		}
		if($comments_orphan_cleanup_needed){
			$where[] = "(comment_post_ID NOT IN (SELECT ID FROM $wpdb->posts))";
		}
		$where = implode(' OR ', $where);
		$queries[] = "DELETE FROM $wpdb->comments WHERE $where";
		$commentmeta_cleanup_needed = true;
	}
	if($data['wpjd_orphan_postmeta'] || $postmeta_cleanup_needed)
		$queries[] = "DELETE FROM $wpdb->postmeta WHERE post_id NOT IN (SELECT ID FROM $wpdb->posts)";
	if($data['wpjd_orphan_commentmeta'] || $commentmeta_cleanup_needed)
		$queries[] = "DELETE FROM $wpdb->commentmeta WHERE comment_id NOT IN (SELECT comment_ID FROM $wpdb->comments)";

	$rows = 0;
	foreach ($queries as $query)
		$rows += $wpdb->query($query);
	return $rows;
}

function wpjd_optimize_ajax(){
	$rows = wpjd_build_and_run_queries($_POST);
	die("Optimization complete. $rows removed from the database.");
}
add_action( 'wp_ajax_wpjd_optimize_ajax', 'wpjd_optimize_ajax' );

function wpjd_save_settings_ajax(){
	update_option('wpjd_automatic_values', $_POST);
	wp_clear_scheduled_hook( 'wpjd_event_hook' );
	if($_POST['wpjd_optimization_interval'] != 'never')
		wp_schedule_event( time(), $_POST['wpjd_optimization_interval'], 'wpjd_event_hook' );
	die("Settings Saved");
}
add_action( 'wp_ajax_wpjd_save_settings_ajax', 'wpjd_save_settings_ajax' );

function wpjd_scheduled_event() {
	$data = get_option('wpjd_automatic_values');
	wpjd_build_and_run_queries($data);
}
add_action( 'wpjd_event_hook', 'wpjd_scheduled_event' );

function wpjd_add_cron_intervals( $schedules ) {
	$schedules['weekly'] = array(
		'interval' => 604800,
		'display' => 'Once Weekly'
	);
	$schedules['monthly'] = array(
		'interval' => 2592000,
		'display' => 'Once Monthly'
	);
	return $schedules;
}
add_filter( 'cron_schedules', 'wpjd_add_cron_intervals' );

include_once('admin-page.php');

?>