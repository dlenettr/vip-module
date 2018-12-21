<?php
/*
=============================================
Name      : MWS VIP Module v1.2
Author    : Mehmet Hanoğlu ( MaRZoCHi )
Site      : https://dle.net.tr/
License   : MIT License
Date      : 28.10.2018
=============================================
*/

if ( ! defined( 'DATALIFEENGINE' ) ) {
die( "Hacking attempt!" );
}

define( 'DEBUG', false );

require_once ENGINE_DIR . "/data/vip.conf.php";
require_once ROOT_DIR   . '/language/' . $config['langs'] . '/vip.lng';
include_once ENGINE_DIR . '/classes/mail.class.php';


$_action = $db->safesql( $_GET['action'] );
$_plan = intval( $_GET['plan'] );

if ( ! function_exists( 'insert_sql' ) ) {
	function insert_sql( $arr ) {
		global $db;
		$result = " (";
		$cols = implode( ",", array_keys( $arr ) );
		$result .= $cols . ") VALUES (";
		foreach ( $arr as $key => $value ) {
			if ( empty( $value ) ) {
				$result .= "'', ";
			} else if ( $value == "0" ) {
				$result .= "0, ";
			} else if ( $value == "NULL" ) {
				$result .= "NULL, ";
			} else if ( is_numeric( $value ) ) {
				$result .= $value . ", ";
			} else {
				$result .= "'" . $db->safesql( $value ) . "', ";
			}
		}
		$result = substr( $result, 0, -2 );
		$result .= ")";
		return $result;
	}
}

// Kullanıcı satın alma
if ( $_action == "buy" && $_plan != 0 ) {

	$plan = $db->super_query( "SELECT * FROM " . PREFIX . "_vip_plans WHERE id = '{$_plan}'" );

	if ( strpos( $plan['a_group'], ",") !== false ) {
		$allowed = explode( ",", $plan['a_group'] );
	} else {
		$allowed = array( $plan['a_group'] );
	}
	if ( DEBUG === true ) $allowed[] = "1";

	if ( in_array( $member_id['user_group'], $allowed ) ) {

		if ( count( $plan ) > 0 ) {

			$_custom = array();
			$_custom['user_id'] = $member_id['user_id'];
			$_custom['date'] = date( "d-m-Y H:i:s", $_TIME );
			$_custom['plan_id'] = $plan['id'];

			$_hash = md5( implode( json_encode( $_custom ) ) );

			$_custom['hash'] = $_hash;

			$data = array(
				"custom"			=> json_encode( $_custom ),
				"amount"			=> number_format( floatval( $plan['price'] ), 2 ),
				"discount_amount"	=> number_format( floatval( $plan['discount'] ), 2 ),
				"no_shipping"		=> "1",
				"charset"			=> "UTF-8",
				"return_url"		=> $vipset['return_url'],
				"cancel_return"		=> $vipset['cancel_url'],
				"business"			=> ( empty( $plan['paypal'] ) ? $vipset['paypal'] : $plan['paypal'] ),
				"cmd"				=> "_xclick",
				"no_note"			=> "1",
				"currency_code"	 	=> $plan['currency']
			);

			$data["email"] 		 = $member_id['email'];
			$data["item_number"] = "VIP_PLAN" . $plan['id'];
			$data["item_name"]   = str_replace(
				array( '{time}', '{period}', '{username}', '{title}' ),
				array( $plan['time'], $plan['period'], $member_id['name'], $plan['title'] ),
				$plan['alt_title']
			);

			if ( ! empty( $member_id['fullname'] ) ) {
				$_tmp = explode( " ", $member_id['fullname'] );
				$data["first_name"] = $_tmp[0];
				$data["last_name"] 	= $_tmp[1];
			} else {
				$data["first_name"] = $member_id['name'];
			}

			$insert = array(
				'user_id' => $member_id['user_id'],
				'plan_id' => $plan['id'],
				'time'    => $plan['time'],
				'period'  => $plan['period'],
				'b_date'  => time(),
				'u_group' => $member_id['user_group'],
				'n_group' => $plan['u_group'],
				'hash'    => $_hash
			);

			if ( $plan['n_group'] == "-1" ) {
				$insert['t_group'] = $user_group[ $plan['u_group'] ]['rid'];
			} else if ( $plan['n_group'] == "0" ) {
				$insert['t_group'] = $member_id['user_group'];
			} else {
				$insert['t_group'] = $plan['n_group'];
			}

			$db->query( "INSERT INTO " . PREFIX . "_vip_payments " . insert_sql( $insert ) );

			$_custom['payment_id'] = $db->insert_id();
			$data['custom'] = json_encode( $_custom );

			$querystring = "";
			foreach( $data as $key => $value ) {
				$value = urlencode( stripslashes( $value ) );
				$querystring .= "{$key}={$value}&";
			}
			$querystring = substr( $querystring, 0, -1 );

			if ( DEBUG !== true ) header( "location: http://www.paypal.com/cgi-bin/webscr?" . $querystring );
			if ( DEBUG !== true ) die();

		} else {
			msgbox( $lang['all_err_1'], "<ul><li>{$lang['mwsvip_99']}</li></ul><a href=\"javascript:history.go(-1)\">$lang[all_prev]</a>" );
		}

	} else {
		msgbox( $lang['all_err_1'], "<ul><li>{$lang['mwsvip_104']}</li></ul><a href=\"javascript:history.go(-1)\">$lang[all_prev]</a>" );
	}

	$result  = "<pre><code>" . print_r( $data, 1 ) . "</code></pre>";
	$result .= "<pre><code>" . print_r( $plan, 1 ) . "</code></pre>";

	if ( DEBUG === true ) echo $result;

// Paypal IPN
} else if ( $_action == $vipset['ipn'] ) {
	// Paypal notification

	if ( ! isset( $send_notify ) || $send_notify == true ) {

		$data = array();
		$_custom = "";
		foreach( $_POST as $key => $value ) {
			$key = $db->safesql( $key );
			$value = $db->safesql( $value );
			if ( $key == "custom" ) {
				$_custom = str_replace( "\\", "", $value );
			} else {
				$data[ $key ] = $value;
			}
		}

		if ( array_key_exists( "test_ipn", $data ) && $data['test_ipn'] == "1" ) {

			die( "Debugging ..." );

		} else if ( array_key_exists( "mc_gross", $data ) && array_key_exists( "ipn_track_id", $data ) && ! empty( $_custom ) ) {

			$_custom = json_decode( $_custom, true );

			$payment = $db->super_query( "SELECT * FROM " . PREFIX . "_vip_payments WHERE id = '{$_custom['payment_id']}'" );

			if ( ! $payment['approve'] ) {

				if ( $_custom['user_id'] == $payment['user_id'] && $data['payment_status'] == "Completed" ) {

					$periods = array(
						"hour"  => 60*60,
						"day"   => 60*60*24,
						"week"  => 60*60*24*7,
						"month" => 60*60*24*30,
						"year"  => 60*60*24*362,
					);

					$_data = $db->safesql( json_encode( $data ) );

					$a_date = time();
					$f_date = $a_date + ( intval( $payment['time'] ) * $periods[ $payment['period'] ]);

					$db->query( "UPDATE " . PREFIX . "_vip_payments SET a_date = '{$a_date}', f_date = '{$f_date}', data = '{$_data}', approve = '1' WHERE id = '{$_custom['payment_id']}'");

					$db->query( "UPDATE " . PREFIX . "_vip_plans SET sold=sold+1 WHERE id = '{$_custom['plan_id']}'");

					$db->query( "UPDATE " . PREFIX . "_users SET user_group = '{$payment['n_group']}', time_limit = '{$f_date}' WHERE user_id = '{$_custom['user_id']}'");

					$send_notify = true;

				} else {
					die( $lang['mwsvip_100'] );
				}
			} else {
				die( $lang['mwsvip_106'] );
			}

			if ( DEBUG === true ) print_r( $data );

		} else {
			die( $lang['mwsvip_101'] );
		}

	}

	// Bildirimler
	if ( isset( $send_notify ) && $send_notify == true ) {

		$user = $db->super_query( "SELECT name, email, user_id, user_group FROM " . PREFIX . "_users WHERE user_id = '{$_custom['user_id']}'" );
		$plan = $db->super_query( "SELECT title, alt_title FROM " . PREFIX . "_vip_plans WHERE id = '{$_custom['plan_id']}'" );

		$vars = array(
			'title' 		=> stripslashes( $plan['title'] ),
			'alt_title' 	=> stripslashes( $data['item_name'] ),
			'time' 			=> $payment['time'],
			'period' 		=> $payment['period'],
			'activation' 	=> date( "d.m.Y H:i", $a_date ),
			'finish' 		=> date( "d.m.Y H:i", $f_date ),
			'price' 		=> number_format( floatval( $data['mc_gross'] ), 2 ),
			'discount' 		=> number_format( floatval( $data['discount'] ), 2 ),
			'currency' 		=> $data['mc_currency'],
			'site_url' 		=> $config['http_home_url'],
			'site_name' 	=> $config['home_title'],
			'user_name'		=> stripslashes( $user['name'] ),
		);

		if ( DEBUG === true ) print_r( $vars );

		$admin_ids = explode( ",", str_replace( " ", "", $vipset['admin_ids'] ) );

		$admin_mails = array();
		$db->query( "SELECT user_id, email FROM " . PREFIX . "_users WHERE user_id IN ( " . implode( ",", $admin_ids ) . " )" );
		while( $row = $db->get_row() ) { $admin_mails[ $row['user_id'] ] = $row['email']; }
		$db->free();

		$sel_tpl = $db->query( "SELECT * FROM " . PREFIX . "_vip_templates WHERE send = '1' AND name IN ( 'pm_ps', 'mail_ps', 'pm_ps_admin', 'mail_ps_admin' )" );
		while( $row = $db->get_row( $sel_tpl ) ) {

			$text = $row['template'];
			$title = $row['title'];

			if ( empty( $title ) || empty( $text ) ) continue;

			foreach( $vars as $key => $val ) {
				$text = str_replace( '{' . $key . '}', $val, $text );
				$title = str_replace( '{' . $key . '}', $val, $title );
			}

			$text = str_replace( "\\r\\n", "<br />", $text );

			if ( $row['name'] == "pm_ps" ) {
				$db->query( "INSERT INTO " . PREFIX . "_pm (subj, text, user, user_from, date, pm_read, folder) VALUES ('{$title}', '{$text}', '{$_custom['user_id']}', '{$vipset['admin']}', '{$_TIME}', '0', 'inbox')" );
				$db->query( "UPDATE " . PREFIX . "_users SET pm_unread = pm_unread + 1, pm_all = pm_all+1 WHERE user_id = '{$_custom['user_id']}'" );
			} else if ( $row['name'] == "pm_ps_admin" ) {
				foreach( $admin_ids as $admin_id ) {
					$db->query( "INSERT INTO " . PREFIX . "_pm (subj, text, user, user_from, date, pm_read, folder) VALUES ('{$title}', '{$text}', '{$admin_id}', 'System', '{$_TIME}', '0', 'inbox')" );
					$db->query( "UPDATE " . PREFIX . "_users SET pm_unread = pm_unread + 1, pm_all = pm_all+1 WHERE user_id = '{$admin_id}'" );
				}
			} else if ( $row['name'] == "mail_ps" ) {
				$mail = new dle_mail( $config, $row['html'] );
				$mail->keepalive = true;
				$mail->send( $user['email'], $title, $text );
			} else if ( $row['name'] == "mail_ps_admin" ) {
				$mail = new dle_mail( $config, $row['html'] );
				$mail->keepalive = true;
				foreach( $admin_ids as $admin_id ) {
					$mail->send( $admin_mails[ $admin_id ], $title, $text );
				}
			}
		}
		die( $lang['mwsvip_102'] );
	}

// VIP bitiş
} else if ( $_action == "finish" ) {

	$payment = $db->super_query( "SELECT * FROM " . PREFIX . "_vip_payments WHERE user_id = '{$member_id['user_id']}' AND approve = '1'" );
	$plan = $db->super_query( "SELECT * FROM " . PREFIX . "_vip_plans WHERE id = '{$payment['plan_id']}'" );

	if ( count( $payment ) > 0 ) {
		$db->query( "UPDATE " . PREFIX . "_users SET user_group = '{$payment['t_group']}', time_limit = '' WHERE user_id = '{$member_id['user_id']}'" );
		$db->query( "UPDATE " . PREFIX . "_vip_payments SET approve = '2' WHERE id = '{$payment['id']}'" );

		$data = json_decode( $payment['data'], true );

		$vars = array(
			'title' 		=> stripslashes( $plan['title'] ),
			'alt_title' 	=> stripslashes( $plan['alt_title'] ),
			'time' 			=> $payment['time'],
			'period' 		=> $payment['period'],
			'activation' 	=> date( "d.m.Y H:i", $payment['a_date'] ),
			'finish' 		=> date( "d.m.Y H:i", $payment['f_date'] ),
			'price' 		=> number_format( floatval( $data['mc_gross'] ), 2 ),
			'discount' 		=> number_format( floatval( $data['discount'] ), 2 ),
			'currency' 		=> $data['mc_currency'],
			'site_url' 		=> $config['http_home_url'],
			'site_name' 	=> $config['home_title'],
			'user_name'		=> stripslashes( $member_id['name'] ),
		);

		$sel_tpl = $db->query( "SELECT * FROM " . PREFIX . "_vip_templates WHERE send = '1' AND name IN ( 'pm_vf', 'mail_vf' )" );
		while( $row = $db->get_row( $sel_tpl ) ) {

			$text = $row['template'];
			$title = $row['title'];

			if ( empty( $title ) || empty( $text ) ) continue;

			foreach( $vars as $key => $val ) {
				$text = str_replace( '{' . $key . '}', $val, $text );
				$title = str_replace( '{' . $key . '}', $val, $title );
			}

			$text = str_replace( "\\r\\n", "<br />", $text );

			if ( $row['name'] == "pm_vf" ) {
				$db->query( "INSERT INTO " . PREFIX . "_pm (subj, text, user, user_from, date, pm_read, folder) VALUES ('{$title}', '{$text}', '{$member_id['user_id']}', '{$vipset['admin']}', '{$_TIME}', '0', 'inbox')" );
				$db->query( "UPDATE " . PREFIX . "_users SET pm_unread = pm_unread + 1, pm_all = pm_all+1 WHERE user_id = '{$member_id['user_id']}'" );
			} else if ( $row['name'] == "mail_vf" ) {
				$mail = new dle_mail( $config, $row['html'] );
				$mail->keepalive = true;
				$mail->send( $member_id['email'], $title, $text );
			}
		}
	}

} else {
	msgbox( $lang['all_err_1'], "<ul><li>{$lang['mwsvip_103']}</li></ul><a href=\"javascript:history.go(-1)\">$lang[all_prev]</a>" );
}
