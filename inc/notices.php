<?php

add_action( 'admin_notices', 'ilw_admin_notices' );
function ilw_admin_notices(){
	global $current_user;
	$user_id = $current_user->ID;

	$activationDate = get_user_meta( $user_id, 'ilw_plugin_activation', true );
	if( empty( $activationDate ) ){
		update_user_meta( $user_id, 'ilw_plugin_activation', date( 'F j, Y' ) );
		update_user_meta( $user_id, 'ilw_rate_ignore', 'false' );
		update_user_meta( $user_id, 'ilw_donate_ignore', 'false' );
		$activationDate = strtotime( 'now' );
	}
	$aWeekFromActivation = strtotime( $activationDate . '+1 week' );
	$twoWeeksFromActivation = strtotime( $activationDate . '+2 weeks' );
	$currentPluginDate = strtotime( 'now' );

	$rateOutput = '<div id="message" class="updated notice">';
		$rateOutput .= '<p>Please rate <a href="https://wordpress.org/plugins/icon-links-widget/" target="_blank">Icon Links Widget</a>. If you have already, simply <a href="?ilw_rate_ignore=dismiss">dismiss this notice</a>.</p>';
	$rateOutput .= '</div>';

	$donateOutput = '<div id="message" class="updated notice">';
		$donateOutput .= '<p>Looks like you\'re enjoying <a href="https://wordpress.org/plugins/icon-links-widget/" target="_blank">Icon Links Widget</a>. Consider <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=yusrimathews%40gmail%2ecom&lc=ZA&item_name=Yusri%20Mathews&item_number=icon%2dlinks%2dwidget&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted" target="_blank">making a donation</a>, alternatively <a href="?ilw_donate_ignore=dismiss">dismiss this notice</a>.</p>';
	$donateOutput .= '</div>';

	if( current_user_can( 'activate_plugins' ) && get_user_meta( $user_id, 'ilw_rate_ignore' ) != 'true' && $currentPluginDate >= $aWeekFromActivation ){
		echo $rateOutput;
	}

	if( current_user_can( 'activate_plugins' ) && get_user_meta( $user_id, 'ilw_donate_ignore' ) != 'true' && $currentPluginDate >= $twoWeeksFromActivation ){
		echo $donateOutput;
	}
}

add_action( 'admin_init', 'ilw_ignore_notices' );
function ilw_ignore_notices(){
	global $current_user;
	$user_id = $current_user->ID;

	if( isset( $_GET['ilw_rate_ignore'] ) && $_GET['ilw_rate_ignore'] == 'dismiss' ){
		update_user_meta( $user_id, 'ilw_rate_ignore', 'true' );
	}

	if( isset( $_GET['ilw_donate_ignore'] ) && $_GET['ilw_donate_ignore'] == 'dismiss' ){
		update_user_meta( $user_id, 'ilw_donate_ignore', 'true' );
	}
}
