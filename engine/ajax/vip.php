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

if ( ! defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

define( 'DEBUG', false );

require_once ENGINE_DIR . '/data/vip.conf.php';

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

function update_sql( $arr ) {
	global $db;
	foreach ( $arr as $key => $value ) {
		if ( empty( $value ) ) {
			$result .= $key . "='', ";
		} else if ( $value == "0" ) {
			$result .= $key . "=0, ";
		} else if ( $value == "NULL" ) {
			$result .= $key . "=NULL, ";
		} else if ( is_numeric( $value ) ) {
			$result .= $key . "=" . $value . ", ";
		} else {
			$result .= $key . "='" . $db->safesql( $value ) . "', ";
		}
	}
	$result = substr( $result, 0, -2 );
	return $result;
}

header('Content-Type: application/json');

$result = array( 'error' => "no", 'text' => "" );

if ( ! isset( $_POST['type'] ) || ! isset( $_POST['action'] ) ) {
	$result['text'] = $lang['mwsvip_59'];
	$result['error'] = "ok";
	die( json_encode( $result ) );
}

$_type = $db->safesql( $_POST['type'] );
$_action = $db->safesql( $_POST['action'] );

// Plan Ekle
if ( $_type == "plan" && ( $_action == "add" || $_action == "edit" ) ) {

	$data = array();
	if ( isset( $_POST['data'] ) && is_array( $_POST['data'] ) ) {

		foreach( $_POST['data'] as $_tmp ) {
			if ( preg_match( "#plan\\[([a-z\_]+)\\]#is", $_tmp['name'], $_tmp2 ) ) {
				if ( array_key_exists( $_tmp2[1], $data ) ) {
					if ( is_array( $data[ $_tmp2[1] ] ) ) {
						$data[ $_tmp2[1] ][] = $_tmp['value'];
					} else {
						$data[ $_tmp2[1] ] = array( $data[ $_tmp2[1] ], $_tmp['value'] );
					}
				} else {
					$data[ $_tmp2[1] ] = $_tmp['value'];
				}
			}
		}

		if ( is_array( $data['a_group'] ) ) $data['a_group'] = implode( ",", $data['a_group'] );

		$insert = array(
			'title' 	=> $db->safesql( $data['title'] ),
			'time' 		=> intval( $data['time'] ),
			'period' 	=> $db->safesql( $data['period'] ),
			'price'		=> $db->safesql( $data['price'] ),
			'currency'  => $db->safesql( $data['currency'] ),
			'alt_title' => $db->safesql( $data['alt_title'] ),
			'a_group'   => $data['a_group'],
			'u_group'	=> intval( $data['u_group'] ),
			'n_group'   => intval( $data['n_group'] ),
			'paypal' 	=> $db->safesql( $data['paypal'] ),
			'sold'		=> 0,
			'c_time'	=> time(),
		);

		if ( DEBUG ) $result['data'] = $data;
		if ( DEBUG ) $result['insert'] = $insert;

		$db->query( "UPDATE " . PREFIX . "_usergroups SET time_limit = '1' WHERE id = '{$insert['u_group']}'" );

		if ( empty( $data['id'] ) ) {
			$db->query( "INSERT INTO " . PREFIX . "_vip_plans " . insert_sql( $insert ) );
			$result['text'] = $lang['mwsvip_60'];
		} else {
			$db->query( "UPDATE " . PREFIX . "_vip_plans SET " . update_sql( $insert ) . " WHERE id = '{$data['id']}'" );
			$result['text'] = $lang['mwsvip_61'];
		}

	} else {
		$result['text'] = $lang['mwsvip_62'];
		$result['error'] = "ok";
	}

}

// Plan bilgilerini çek
else if ( $_type == "plan" && $_action == "get" ) {

	$_id = intval( $db->safesql( $_POST['id'] ) );

	$result['plan'] = $db->super_query( "SELECT * FROM " . PREFIX . "_vip_plans WHERE id = '{$_id}'");

}

// Plan sil
else if ( $_type == "plan" && $_action == "del" ) {

	$_id = intval( $db->safesql( $_POST['id'] ) );

	$db->query( "DELETE FROM " . PREFIX . "_vip_plans WHERE id = '{$_id}'");

	$result['text'] = $lang['mwsvip_63'];
}

// Ödeme sil
else if ( $_type == "payment" && $_action == "del" ) {

	$_id = intval( $db->safesql( $_POST['id'] ) );

	$payment = $db->super_query( "SELECT * FROM " . PREFIX . "_vip_payments WHERE id = '{$_id}'" );

	$db->query( "DELETE FROM " . PREFIX . "_vip_payments WHERE id = '{$_id}'");
	$result['text'] = $lang['mwsvip_108'];

	if ( $payment['approve'] ) {
		$db->query( "UPDATE " . PREFIX . "_users SET user_group = '{$payment['t_group']}', time_limit = '' WHERE user_id = '{$payment['user_id']}'" );
		$result['text'] = $lang['mwsvip_107'];
	}

}

// Ödeme aktifleştir
else if ( $_type == "payment" && $_action == "activate" ) {

	$_id = intval( $db->safesql( $_POST['id'] ) );

	$data = $db->super_query( "SELECT pay.user_id, pay.n_group, plan.id, plan.price, plan.time, plan.period, plan.discount, plan.currency FROM " . PREFIX . "_vip_payments pay LEFT JOIN " . PREFIX . "_vip_plans plan ON ( pay.plan_id = plan.id ) WHERE pay.id = '{$_id}'" );
	$_POST = array(
		'custom' 		=> json_encode( array( "user_id" => $data['user_id'], "payment_id" => $_id, "plan_id" => $data['id'] ) ),
		'mc_gross' 		=> $data['price'],
		'mc_fee'		=> "0.00",
		'ipn_track_id' 	=> "AJAX",
		'payment_status'=> "Completed",
	);

	$_GET['action'] = $vipset['ipn'];
	$send_notify = true;
	include ENGINE_DIR . "/modules/vip.module.php";

}


// Şablonları kaydet
else if ( $_type == "template" && $_action == "save" ) {

	$data = array();
	if ( isset( $_POST['data'] ) && is_array( $_POST['data'] ) ) {
		foreach( $_POST['data'] as $_tmp ) {
			if ( preg_match( "#([a-z\_]+)\\[([a-z\_]+)\\]#is", $_tmp['name'], $_tmp2 ) ) {
				$data[ $_tmp2[1] ][ 'send' ] = "0";
				if ( $_tmp2[2] == "template" || $_tmp2[2] == "title" ) {
					$_tmp['value'] = $db->safesql( $_tmp['value'] );
				} else if ( $_tmp2[2] == "send" ) {
					$_tmp['value'] = intval( $_tmp['value'] );
				}
				$data[ $_tmp2[1] ][ $_tmp2[2] ] = $_tmp['value'];
			}
		}
	}

	foreach( $data as $name => $update ) {
		$db->query( "UPDATE " . PREFIX . "_vip_templates SET " . update_sql( $update ) . " WHERE name = '{$name}';" );
	}

	$result['text'] = $lang['mwsvip_79'];
}


if ( DEBUG ) $result['_post'] = $_POST;

die( json_encode( $result ) );