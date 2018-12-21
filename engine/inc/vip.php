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

if ( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	die( "Hacking attempt!" );
}

if ( $member_id['user_group'] != 1 ) {
	msg( "error", $lang['index_denied'], $lang['index_denied'] );
}

require_once ENGINE_DIR . "/data/vip.conf.php";
require_once ROOT_DIR . "/language/" . $config['langs'] . "/vip.lng";

if ( ! is_writable(ENGINE_DIR . '/data/vip.conf.php' ) ) {
	$lang['stat_system'] = str_replace( "{file}", "engine/data/vip.conf.php", $lang['stat_system'] );
	$fail = "<div class=\"alert alert-error\">{$lang['stat_system']}</div>";
} else $fail = "";

$action = $_REQUEST['action'];

if ( $action == "save" ) {
	if ( $member_id['user_group'] != 1 ) { msg( "error", $lang['opt_denied'], $lang['opt_denied'] ); }
	if ( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) { die( "Hacking attempt! User not found" ); }

	$find = array(); $replace = array();
	$find[] = "'\r'"; $replace[] = "";
	$find[] = "'\n'"; $replace[] = "";

	$save_con = $_POST['save_con'];
	$save_con = $save_con + $vipset;

	$handler = fopen( ENGINE_DIR . '/data/vip.conf.php', "w" );
	fwrite( $handler, "<?PHP \n\n//MWS VIP Module Settings\n\n\$vipset = array (\n" );
	foreach ( $save_con as $name => $value ) {
		$value = ( is_array( $value ) ) ? implode(",", $value ) : $value;
		$value = trim(strip_tags(stripslashes( $value )));
		$value = htmlspecialchars( $value, ENT_QUOTES, $config['charset']);
		$value = preg_replace( $find, $replace, $value );
		$name = trim(strip_tags(stripslashes( $name )));
		$name = htmlspecialchars( $name, ENT_QUOTES, $config['charset'] );
		$name = preg_replace( $find, $replace, $name );
		$value = str_replace( "$", "&#036;", $value );
		$value = str_replace( "{", "&#123;", $value );
		$value = str_replace( "}", "&#125;", $value );
		//$value = str_replace( ".", "", $value );
		//$value = str_replace( '/', "", $value );
		$value = str_replace( chr(92), "", $value );
		$value = str_replace( chr(0), "", $value );
		$value = str_replace( '(', "", $value );
		$value = str_replace( ')', "", $value );
		$value = str_ireplace( "base64_decode", "base64_dec&#111;de", $value );
		$name = str_replace( "$", "&#036;", $name );
		$name = str_replace( "{", "&#123;", $name );
		$name = str_replace( "}", "&#125;", $name );
		$name = str_replace( ".", "", $name );
		$name = str_replace( '/', "", $name );
		$name = str_replace( chr(92), "", $name );
		$name = str_replace( chr(0), "", $name );
		$name = str_replace( '(', "", $name );
		$name = str_replace( ')', "", $name );
		$name = str_ireplace( "base64_decode", "base64_dec&#111;de", $name );
		fwrite( $handler, "'{$name}' => '{$value}',\n" );
	}
	fwrite( $handler, ");\n\n?>" );
	fclose( $handler );

	msg( "info", $lang['opt_sysok'], $lang['opt_sysok_1'], "{$PHP_SELF}?mod=vip" );

}

echoheader( "<i class=\"fa fa-money\"></i> MWS VIP Module", $lang['mwsvip_0'] );
echo <<< HTML
<style>
#plans { display: none; }
.multiselect { width: 240px; padding: 5px; border: 1px solid #CCCCCC; height: 150px !important; }
.multiselect > option { font-size: 13px; }
.accordion-heading { padding: 5px 0px; }
</style>
<script>
/*!
 * clipboard.js v1.7.1
 * https://zenorocha.github.io/clipboard.js
 *
 * Licensed MIT © Zeno Rocha
 */
!function(t){if("object"==typeof exports&&"undefined"!=typeof module)module.exports=t();else if("function"==typeof define&&define.amd)define([],t);else{var e;e="undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self?self:this,e.Clipboard=t()}}(function(){var t,e,n;return function t(e,n,o){function i(a,c){if(!n[a]){if(!e[a]){var l="function"==typeof require&&require;if(!c&&l)return l(a,!0);if(r)return r(a,!0);var s=new Error("Cannot find module '"+a+"'");throw s.code="MODULE_NOT_FOUND",s}var u=n[a]={exports:{}};e[a][0].call(u.exports,function(t){var n=e[a][1][t];return i(n||t)},u,u.exports,t,e,n,o)}return n[a].exports}for(var r="function"==typeof require&&require,a=0;a<o.length;a++)i(o[a]);return i}({1:[function(t,e,n){function o(t,e){for(;t&&t.nodeType!==i;){if("function"==typeof t.matches&&t.matches(e))return t;t=t.parentNode}}var i=9;if("undefined"!=typeof Element&&!Element.prototype.matches){var r=Element.prototype;r.matches=r.matchesSelector||r.mozMatchesSelector||r.msMatchesSelector||r.oMatchesSelector||r.webkitMatchesSelector}e.exports=o},{}],2:[function(t,e,n){function o(t,e,n,o,r){var a=i.apply(this,arguments);return t.addEventListener(n,a,r),{destroy:function(){t.removeEventListener(n,a,r)}}}function i(t,e,n,o){return function(n){n.delegateTarget=r(n.target,e),n.delegateTarget&&o.call(t,n)}}var r=t("./closest");e.exports=o},{"./closest":1}],3:[function(t,e,n){n.node=function(t){return void 0!==t&&t instanceof HTMLElement&&1===t.nodeType},n.nodeList=function(t){var e=Object.prototype.toString.call(t);return void 0!==t&&("[object NodeList]"===e||"[object HTMLCollection]"===e)&&"length"in t&&(0===t.length||n.node(t[0]))},n.string=function(t){return"string"==typeof t||t instanceof String},n.fn=function(t){return"[object Function]"===Object.prototype.toString.call(t)}},{}],4:[function(t,e,n){function o(t,e,n){if(!t&&!e&&!n)throw new Error("Missing required arguments");if(!c.string(e))throw new TypeError("Second argument must be a String");if(!c.fn(n))throw new TypeError("Third argument must be a Function");if(c.node(t))return i(t,e,n);if(c.nodeList(t))return r(t,e,n);if(c.string(t))return a(t,e,n);throw new TypeError("First argument must be a String, HTMLElement, HTMLCollection, or NodeList")}function i(t,e,n){return t.addEventListener(e,n),{destroy:function(){t.removeEventListener(e,n)}}}function r(t,e,n){return Array.prototype.forEach.call(t,function(t){t.addEventListener(e,n)}),{destroy:function(){Array.prototype.forEach.call(t,function(t){t.removeEventListener(e,n)})}}}function a(t,e,n){return l(document.body,t,e,n)}var c=t("./is"),l=t("delegate");e.exports=o},{"./is":3,delegate:2}],5:[function(t,e,n){function o(t){var e;if("SELECT"===t.nodeName)t.focus(),e=t.value;else if("INPUT"===t.nodeName||"TEXTAREA"===t.nodeName){var n=t.hasAttribute("readonly");n||t.setAttribute("readonly",""),t.select(),t.setSelectionRange(0,t.value.length),n||t.removeAttribute("readonly"),e=t.value}else{t.hasAttribute("contenteditable")&&t.focus();var o=window.getSelection(),i=document.createRange();i.selectNodeContents(t),o.removeAllRanges(),o.addRange(i),e=o.toString()}return e}e.exports=o},{}],6:[function(t,e,n){function o(){}o.prototype={on:function(t,e,n){var o=this.e||(this.e={});return(o[t]||(o[t]=[])).push({fn:e,ctx:n}),this},once:function(t,e,n){function o(){i.off(t,o),e.apply(n,arguments)}var i=this;return o._=e,this.on(t,o,n)},emit:function(t){var e=[].slice.call(arguments,1),n=((this.e||(this.e={}))[t]||[]).slice(),o=0,i=n.length;for(o;o<i;o++)n[o].fn.apply(n[o].ctx,e);return this},off:function(t,e){var n=this.e||(this.e={}),o=n[t],i=[];if(o&&e)for(var r=0,a=o.length;r<a;r++)o[r].fn!==e&&o[r].fn._!==e&&i.push(o[r]);return i.length?n[t]=i:delete n[t],this}},e.exports=o},{}],7:[function(e,n,o){!function(i,r){if("function"==typeof t&&t.amd)t(["module","select"],r);else if(void 0!==o)r(n,e("select"));else{var a={exports:{}};r(a,i.select),i.clipboardAction=a.exports}}(this,function(t,e){"use strict";function n(t){return t&&t.__esModule?t:{default:t}}function o(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}var i=n(e),r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},a=function(){function t(t,e){for(var n=0;n<e.length;n++){var o=e[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(t,o.key,o)}}return function(e,n,o){return n&&t(e.prototype,n),o&&t(e,o),e}}(),c=function(){function t(e){o(this,t),this.resolveOptions(e),this.initSelection()}return a(t,[{key:"resolveOptions",value:function t(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};this.action=e.action,this.container=e.container,this.emitter=e.emitter,this.target=e.target,this.text=e.text,this.trigger=e.trigger,this.selectedText=""}},{key:"initSelection",value:function t(){this.text?this.selectFake():this.target&&this.selectTarget()}},{key:"selectFake",value:function t(){var e=this,n="rtl"==document.documentElement.getAttribute("dir");this.removeFake(),this.fakeHandlerCallback=function(){return e.removeFake()},this.fakeHandler=this.container.addEventListener("click",this.fakeHandlerCallback)||!0,this.fakeElem=document.createElement("textarea"),this.fakeElem.style.fontSize="12pt",this.fakeElem.style.border="0",this.fakeElem.style.padding="0",this.fakeElem.style.margin="0",this.fakeElem.style.position="absolute",this.fakeElem.style[n?"right":"left"]="-9999px";var o=window.pageYOffset||document.documentElement.scrollTop;this.fakeElem.style.top=o+"px",this.fakeElem.setAttribute("readonly",""),this.fakeElem.value=this.text,this.container.appendChild(this.fakeElem),this.selectedText=(0,i.default)(this.fakeElem),this.copyText()}},{key:"removeFake",value:function t(){this.fakeHandler&&(this.container.removeEventListener("click",this.fakeHandlerCallback),this.fakeHandler=null,this.fakeHandlerCallback=null),this.fakeElem&&(this.container.removeChild(this.fakeElem),this.fakeElem=null)}},{key:"selectTarget",value:function t(){this.selectedText=(0,i.default)(this.target),this.copyText()}},{key:"copyText",value:function t(){var e=void 0;try{e=document.execCommand(this.action)}catch(t){e=!1}this.handleResult(e)}},{key:"handleResult",value:function t(e){this.emitter.emit(e?"success":"error",{action:this.action,text:this.selectedText,trigger:this.trigger,clearSelection:this.clearSelection.bind(this)})}},{key:"clearSelection",value:function t(){this.trigger&&this.trigger.focus(),window.getSelection().removeAllRanges()}},{key:"destroy",value:function t(){this.removeFake()}},{key:"action",set:function t(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"copy";if(this._action=e,"copy"!==this._action&&"cut"!==this._action)throw new Error('Invalid "action" value, use either "copy" or "cut"')},get:function t(){return this._action}},{key:"target",set:function t(e){if(void 0!==e){if(!e||"object"!==(void 0===e?"undefined":r(e))||1!==e.nodeType)throw new Error('Invalid "target" value, use a valid Element');if("copy"===this.action&&e.hasAttribute("disabled"))throw new Error('Invalid "target" attribute. Please use "readonly" instead of "disabled" attribute');if("cut"===this.action&&(e.hasAttribute("readonly")||e.hasAttribute("disabled")))throw new Error('Invalid "target" attribute. You can\'t cut text from elements with "readonly" or "disabled" attributes');this._target=e}},get:function t(){return this._target}}]),t}();t.exports=c})},{select:5}],8:[function(e,n,o){!function(i,r){if("function"==typeof t&&t.amd)t(["module","./clipboard-action","tiny-emitter","good-listener"],r);else if(void 0!==o)r(n,e("./clipboard-action"),e("tiny-emitter"),e("good-listener"));else{var a={exports:{}};r(a,i.clipboardAction,i.tinyEmitter,i.goodListener),i.clipboard=a.exports}}(this,function(t,e,n,o){"use strict";function i(t){return t&&t.__esModule?t:{default:t}}function r(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function a(t,e){if(!t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return!e||"object"!=typeof e&&"function"!=typeof e?t:e}function c(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function, not "+typeof e);t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,enumerable:!1,writable:!0,configurable:!0}}),e&&(Object.setPrototypeOf?Object.setPrototypeOf(t,e):t.__proto__=e)}function l(t,e){var n="data-clipboard-"+t;if(e.hasAttribute(n))return e.getAttribute(n)}var s=i(e),u=i(n),f=i(o),d="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},h=function(){function t(t,e){for(var n=0;n<e.length;n++){var o=e[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(t,o.key,o)}}return function(e,n,o){return n&&t(e.prototype,n),o&&t(e,o),e}}(),p=function(t){function e(t,n){r(this,e);var o=a(this,(e.__proto__||Object.getPrototypeOf(e)).call(this));return o.resolveOptions(n),o.listenClick(t),o}return c(e,t),h(e,[{key:"resolveOptions",value:function t(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};this.action="function"==typeof e.action?e.action:this.defaultAction,this.target="function"==typeof e.target?e.target:this.defaultTarget,this.text="function"==typeof e.text?e.text:this.defaultText,this.container="object"===d(e.container)?e.container:document.body}},{key:"listenClick",value:function t(e){var n=this;this.listener=(0,f.default)(e,"click",function(t){return n.onClick(t)})}},{key:"onClick",value:function t(e){var n=e.delegateTarget||e.currentTarget;this.clipboardAction&&(this.clipboardAction=null),this.clipboardAction=new s.default({action:this.action(n),target:this.target(n),text:this.text(n),container:this.container,trigger:n,emitter:this})}},{key:"defaultAction",value:function t(e){return l("action",e)}},{key:"defaultTarget",value:function t(e){var n=l("target",e);if(n)return document.querySelector(n)}},{key:"defaultText",value:function t(e){return l("text",e)}},{key:"destroy",value:function t(){this.listener.destroy(),this.clipboardAction&&(this.clipboardAction.destroy(),this.clipboardAction=null)}}],[{key:"isSupported",value:function t(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:["copy","cut"],n="string"==typeof e?[e]:e,o=!!document.queryCommandSupported;return n.forEach(function(t){o=o&&!!document.queryCommandSupported(t)}),o}}]),e}(u.default);t.exports=p})},{"./clipboard-action":7,"good-listener":4,"tiny-emitter":6}]},{},[8])(8)});
</script>
<script type="text/javascript">
var clipboard = new Clipboard('.clipboard');
function ShowAlert( title, text ) {
	Growl.info({
		title: title,
		text: text
	});
	return false;
}

function Toggle( e, reload ) {
	if ( $( "#" + e ).is(':visible') ) {
		ClearForm();
	}
	$( "#" + e ).slideToggle( 500, function() {
		if ( reload ) { window.location.reload(); }
	});
}

function Open( e ) {
	if ( ! $( "#" + e ).is(':visible') ) {
		$( "#" + e ).slideToggle();
	}
}

function Copy( id ) {
	//var URL = "{$config['http_home_url']}index.php?do=vip&action=buy&plan=" + id;
	//clipboard.copy({ 'text/plain': URL, 'text/html': URL });
	ShowAlert( "{$lang['mwsvip_111']}", "{$lang['mwsvip_112']}" );
}

function ClearForm( ) {
	$("input[name*='plan']").val('');
	$.each( $("input[data-default]"), function( i, e ) {
		$(this).val( $(this).attr('data-default') );
	});
	$("select[name*='plan']").val('');
	$.uniform.update();
}

function Delete( id ) {
	$("#dialog-confirm").text("{$lang['mwsvip_58']}").dialog({
		resizable: false,
		title: '{$lang['mwsvip_57']}',
		height: 150,
		modal: true,
		buttons: {
			"{$lang['mwsvip_41']}": function() {
				$(this).dialog("close");
				$.ajax({
					type :'post',
					url  :'engine/ajax/controller.php?mod=vip',
					dataType: 'json',
					data : { user: { hash: '{$dle_login_hash}' }, id: id, action: 'del', type: 'plan' },
					success: function( result ) {
						ShowAlert( "{$lang['mwsvip_55']}", result.text );
						$("tr[data-id='" + id + "']").slideUp( 500, function() { $(this).remove(); });
					}
				});
			},
			"{$lang['mwsvip_3']}": function() {
				$(this).dialog("close");
			}
		}
	});
}

function Edit( id ) {
	$.ajax({
		type :'post',
		url  :'engine/ajax/controller.php?mod=vip',
		dataType: 'json',
		data : { user: { hash: '{$dle_login_hash}' }, id: id, action: 'get', type: 'plan' },
		beforeSend: function( ) {
			ShowLoading();
			Open('add_plan');
		}, complete: function( ) {
			HideLoading();
		}, success: function( result ) {
			for ( x in result.plan ) {
				if ( x == "a_group" ) {
					$.each( result.plan[ x ].split(","), function( i, e ) {
						$("*[name*='plan[" + x + "]'] option[value='" + e + "']").prop( "selected", true );
					});
				} else {
					$("*[name*='plan[" + x + "]']").val( result.plan[ x ] );
				}
			}
			$.uniform.update();
		}
	});
}


function Clone( id ) {
	$.ajax({
		type :'post',
		url  :'engine/ajax/controller.php?mod=vip',
		dataType: 'json',
		data : { user: { hash: '{$dle_login_hash}' }, id: id, action: 'get', type: 'plan' },
		beforeSend: function( ) {
			ShowLoading();
			Open('add_plan');
		}, complete: function( ) {
			HideLoading();
		}, success: function( result ) {
			console.log( result );
			for ( x in result.plan ) {
				if ( x != "id" ) {
					if ( x == "a_group" ) {
						$.each( result.plan[ x ].split(","), function( i, e ) {
							$("*[name*='plan[" + x + "]'] option[value='" + e + "']").prop( "selected", true ).change();
						});
					} else {
						$("*[name*='plan[" + x + "]']").val( result.plan[ x ] ).change();
					}
				}
			}
			$.uniform.update();
		}
	});
}


function DeletePayment( id ) {
	$("#dialog-confirm").text("{$lang['mwsvip_69']}").dialog({
		resizable: false,
		title: '{$lang['mwsvip_57']}',
		height: 150,
		modal: true,
		buttons: {
			"{$lang['mwsvip_41']}": function() {
				$(this).dialog("close");
				$.ajax({
					type :'post',
					url  :'engine/ajax/controller.php?mod=vip',
					dataType: 'json',
					data : { user: { hash: '{$dle_login_hash}' }, id: id, action: 'del', type: 'payment' },
					success: function( result ) {
						ShowAlert( "{$lang['mwsvip_55']}", result.text );
						$("tr[data-id='" + id + "']").slideUp( 500, function() { $(this).remove(); });
					}
				});
			},
			"{$lang['mwsvip_3']}": function() {
				$(this).dialog("close");
			}
		}
	});
}


function ActivatePayment( id ) {
	$.ajax({
		type :'post',
		url  :'engine/ajax/controller.php?mod=vip',
		data : { user: { hash: '{$dle_login_hash}' }, id: id, action: 'activate', type: 'payment' },
		success: function( result ) {
			$("#dialog-confirm").text(result).dialog({
				resizable: false,
				title: '{$lang['mwsvip_96']}',
				height: 150,
				modal: true,
				buttons: {
					"{$lang['mwsvip_97']}": function() {
						$(this).dialog("close");
						window.location.reload();
					},
					"{$lang['mwsvip_98']}": function() {
						$(this).dialog("close");
					}
				}
			});
		}
	});
}

$(document).ready( function() {
	$("#new_plan").on( 'submit', function( e ) {
		e.preventDefault();
		form = $(this).serializeArray();
		//console.log( form );
		var send = true;
		if (
			$("input[name='plan[title]']").val() == '' ||
			$("input[name='plan[alt_title]']").val() == '' ||
			$("input[name='plan[price]']").val() == '' ||
			parseInt( $("input[name='plan[price]']").val() ) == 0 ||
			$("select[name='plan[u_group]']").val() == '' ||
			$("select[name='plan[n_group]']").val() == '' ||
			$("select[name='plan[period]']").val() == '' ||
			$("select[name='plan[a_group]']").val() == null ||
			$("select[name='plan[currency]']").val() == ''
		) { send = false; }

		if ( send == true ) {
			var action = $("#action").val();
			$.ajax({
				type :'post',
				url  :'engine/ajax/controller.php?mod=vip',
				dataType: 'json',
				data : { user: { hash: '{$dle_login_hash}' }, data: form, action: action, type: 'plan' },
				beforeSend: function( ) {
					ShowLoading();
				}, complete: function( ) {
					HideLoading();
				}, success: function( result ) {
					//console.log( result );
					DLEalert( result.text, "{$lang['mwsvip_55']}" );
					Toggle('add_plan', 1 );
				}
			});
		} else {
			DLEalert( "{$lang['mwsvip_110']}", "{$lang['mwsvip_55']}" );
		}
	});

	$("#templates").on( 'submit', function( e ) {
		e.preventDefault();
		form = $(this).serializeArray();
		console.log( form );
		$.ajax({
			type :'post',
			url  :'engine/ajax/controller.php?mod=vip',
			dataType: 'json',
			data : { user: { hash: '{$dle_login_hash}' }, data: form, action: 'save', type: 'template' },
			beforeSend: function( ) {
				ShowLoading();
			}, complete: function( ) {
				HideLoading();
			}, success: function( result ) {
				$("#dialog-confirm").text(result.text).dialog({
					resizable: false,
					title: '{$lang['mwsvip_96']}',
					height: 150,
					modal: true,
					buttons: {
						"{$lang['mwsvip_97']}": function() {
							$(this).dialog("close");
							window.location.reload();
						},
						"{$lang['mwsvip_98']}": function() {
							$(this).dialog("close");
						}
					}
				});
			}
		});
	});

	$("input[name='save_con[ipn]']").on( 'keyup', function() {
		$("span#ipn").text( $(this).val() );
	});

})
</script>
HTML;

function showRow( $title = "", $description = "", $field = "" ) {
	echo "<tr><td class=\"col-xs-6 col-sm-6 col-md-7\"><h6 class=\"media-heading text-semibold\">{$title}</h6><span class=\"text-muted text-size-small hidden-xs\">{$description}</span></td><td class=\"col-xs-6 col-sm-6 col-md-5\">{$field}</td></tr>";
}

function showSep( ) {
	echo "<tr><td class=\"col-xs-12\" colspan=\"2\">&nbsp;</td></tr>";
}

function makeDropDown( $options, $name, $selected ) {
	$output = "<select class=\"uniform\" style=\"min-width:100px;\" name=\"{$name}\">\r\n";
	foreach ( $options as $value => $description ) {
		$output .= "<option value=\"{$value}\"";
		if( $selected == $value ) {
			$output .= " selected ";
		}
		$output .= ">{$description}</option>\n";
	}
	$output .= "</select>";
	return $output;
}

function makeCheckBox( $name, $selected ) {
	$selected = $selected ? "checked" : "";
	return "<div class=\"text-center\"><input class=\"switch\" type=\"checkbox\" name=\"{$name}\" value=\"1\" {$selected}></div>";
}

function getGroups( $none = [], $use_empty = true, $defaults = [] ) {
	global $user_group, $lang;
	$usergroups = "";
	if ( $use_empty ) $usergroups .= "<option value=\"\">-- " . $lang['mwsvip_54'] . " --</option>";
	if ( count( $defaults ) > 0 ) {
		foreach( $defaults as $default ) {
			$usergroups .= "<option value=\"{$default[0]}\">** {$default[1]} **</option>";
		}
	}
	foreach( $user_group as $group ) {
		if ( ! in_array( $group['id'], $none ) ) {
			$usergroups .= "<option value=\"" . $group['id'] . "\">" . stripslashes( $group['group_name'] ) . "</option>";
		}
	}
	return $usergroups;
}

$a_group = getGroups( [1, 5], false );
$u_group = getGroups( [5] );
$n_group = getGroups( [5], true, array( array( "-1", $lang['mwsvip_64'] ), array( "0", $lang['mwsvip_70'] ) ) );

echo '<div id="dialog-confirm" style="display:none" title=""></div>';
echo $fail;

// plans
if ( $action == "plans" ) {
	echo <<< HTML

<div id="add_plan" style="display: none">
	<form action="" method="post" id="new_plan">
		<div class="panel panel-default">
			<div class="panel-heading">
				<b>{$lang['mwsvip_1']}</b>
				<div class="heading-elements">
		            <ul class="icons-list">
						<li>
							<a href="javascript:Toggle('add_plan');"><i class="fa fa-arrow-down"></i> {$lang['mwsvip_3']}</a>
						</li>
					</ul>
				</div>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label class="control-label col-lg-3">{$lang['mwsvip_10']}</label>
					<div class="col-lg-9">
						<input size="40" name="plan[title]" value="" placeholder="{$lang['mwsvip_12']}" type="text" class="form-control" />
					</div>
				</div>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label class="control-label col-lg-3">{$lang['mwsvip_11']}</label>
					<div class="col-lg-5">
						<input size="60" name="plan[alt_title]" value="" placeholder="{$lang['mwsvip_13']}" type="text" class="form-control" />
					</div>
					<div class="col-lg-4">
						<b>{$lang['mwsvip_14']}</b>
						<br />{$lang['mwsvip_15']}
						<br />{$lang['mwsvip_16']}
						<br />{$lang['mwsvip_17']}
						<br />{$lang['mwsvip_26']}
					</div>
				</div>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label class="control-label col-lg-3">{$lang['mwsvip_18']}</label>
					<div class="col-lg-9">
						<input style="width: 60px" name="plan[time]" value="1" data-default="1" type="number" class="form-control" />
						&nbsp;&nbsp;&nbsp;
						<select name="plan[period]" class="uniform">
							<option value="">-- {$lang['mwsvip_54']} --</option>
							<option value="hour">{$lang['mwsvip_19']}</option>
							<option value="day">{$lang['mwsvip_20']}</option>
							<option value="week">{$lang['mwsvip_21']}</option>
							<option value="month">{$lang['mwsvip_22']}</option>
							<option value="year">{$lang['mwsvip_23']}</option>
						</select>
					</div>
				</div>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label class="control-label col-lg-3">{$lang['mwsvip_24']}</label>
					<div class="col-lg-9">
						<input style="width: 100px" name="plan[price]" value="" data-default="0.00" placeholder="{$lang['mwsvip_65']}" type="number" step="0.01" class="form-control" />
						&nbsp;&nbsp;&nbsp;
						<select name="plan[currency]" class="uniform">
							<option value="">-- {$lang['mwsvip_54']} --</option>
							<option value="EUR">Euro</option>
							<option value="USD">U.S. Dollar</option>
							<option value="TRY">Turkish Lira </option>
							<option value="RUB">Russian Ruble</option>
							<option value="GBP">Pound Sterling </option>
						</select>
						&nbsp;&nbsp;&nbsp;
						<input style="width: 120px" name="plan[dicount]" value="" data-default="0.00" placeholder="{$lang['mwsvip_66']}" type="number" step="0.01" class="form-control" />
					</div>
				</div>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label class="control-label col-lg-3">{$lang['mwsvip_25']}</label>
					<div class="col-lg-9">
						<input size="60" name="plan[paypal]" value="" data-default="{$vipset['paypal']}" placeholder="{$lang['mwsvip_30']}" type="text" class="form-control" />
					</div>
				</div>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label class="control-label col-lg-3">{$lang['mwsvip_27']}</label>
					<div class="col-lg-9">
						<select name="plan[a_group]" class="multiselect" multiple>
							{$a_group}
						</select>
					</div>
				</div>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label class="control-label col-lg-3">{$lang['mwsvip_28']}</label>
					<div class="col-lg-9">
						<select name="plan[u_group]" class="uniform">
							{$u_group}
						</select>
					</div>
				</div>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label class="control-label col-lg-3">{$lang['mwsvip_29']}</label>
					<div class="col-lg-9">
						<select name="plan[n_group]" class="uniform">
							{$n_group}
						</select>
					</div>
				</div>
			</div>

			<div class="panel-footer">
				<input type="hidden" name="plan[id]" id="id" value="" />
				<input type="hidden" id="action" value="add" />
				<input type="hidden" name="user_hash" value="{$dle_login_hash}" />
				<button class="btn bg-teal btn-raised" id="save"><i class="fa fa-floppy-o position-left"></i>{$lang['mwsvip_56']}</button>
			</div>
		</div>
	</form>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<b>{$lang['mwsvip_8']}</b>
        <div class="heading-elements">
            <ul class="icons-list">
                <li>
					<a href="javascript:Toggle('add_plan');"><i class="fa fa-plus"></i> {$lang['mwsvip_2']}</a>
                </li>
                <li style="margin-left: 25px">
					<a href="{$PHP_SELF}?mod=vip"><i class="fa fa-arrow-left"></i> {$lang['mwsvip_9']}</a>
                </li>
            </ul>
        </div>
	</div>
	<div class="table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<td>{$lang['mwsvip_31']}</td>
					<td>{$lang['mwsvip_32']}</td>
					<td>{$lang['mwsvip_33']}</td>
					<td>{$lang['mwsvip_34']}</td>
					<td>{$lang['mwsvip_35']}</td>
					<td>{$lang['mwsvip_67']}</td>
					<td>{$lang['mwsvip_36']}</td>
					<td>{$lang['mwsvip_37']}</td>
					<td>{$lang['mwsvip_38']}</td>
				<tr>
			</thead>
			<tbody>
HTML;

$db->query( "SELECT * FROM " . PREFIX . "_vip_plans ORDER BY id ASC" );
while( $plan = $db->get_row() ) {

	$plan['group_name'] = $user_group[ $plan['u_group'] ]['group_name'];
	$plan['discount'] = number_format( floatval( $plan['discount'] ), 2 );
	$plan['price'] = number_format( floatval( $plan['price'] ), 2 );

	echo <<< HTML
	<tr data-id="{$plan['id']}">
		<td style="text-align: center; width: 40px">{$plan['id']}</td>
		<td style="text-align: center; width: 120px">{$plan['title']}</td>
		<td style="text-align: center; width: 120px">{$plan['time']} {$plan['period']}</td>
		<td style="text-align: center; width: 60px">{$plan['sold']}</td>
		<td style="text-align: center; width: 90px"><b>{$plan['price']}</b> {$plan['currency']}</td>
		<td style="text-align: center; width: 90px"><b>{$plan['discount']}</b> {$plan['currency']}</td>
		<td style="text-align: center; width: 120px">{$plan['paypal']}</td>
		<td style="text-align: center; width: 100px">
			<a href="{$PHP_SELF}?mod=usergroup&amp;action=edit&amp;id={$plan['u_group']}" target="_blank">
				{$plan['group_name']}
			</a>
		</td>
		<td style="text-align: center; width: 60px">
			<div class="btn-group">
				<button class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cogs" style="color: #fff"></i> </button>
				<ul class="dropdown-menu text-left" style="min-width: 150px; right: 0; left: auto;">
					<li><a href="{$config['http_home_url']}index.php?do=vip&amp;action=buy&amp;plan={$plan['id']}" target="_blank" data-clipboard-text="{$config['http_home_url']}index.php?do=vip&amp;action=buy&amp;plan={$plan['id']}" class="clipboard" onclick="Copy('{$plan['id']}'); return false"><i class="fa fa-clipboard"></i> {$lang['mwsvip_88']}</a></li>
					<li><a href="javascript:Clone('{$plan['id']}');"><i class="fa fa-clone"></i> {$lang['mwsvip_39']}</a></li>
					<li><a href="javascript:Edit('{$plan['id']}');"><i class="fa fa-pencil"></i> {$lang['mwsvip_40']}</a></li>
					<li><a href="javascript:Delete('{$plan['id']}');"><i class="fa fa-trash"></i> {$lang['mwsvip_41']}</a></li>
				</ul>
			</div>
		</td>
	</tr>
HTML;

}


echo <<< HTML
			</tbody>
		</table>
	</div>
</div>
HTML;
}


// notifications
else if ( $action == "notifications" ) {

	$text = array(); $send = array(); $titles = array();
	$db->query( "SELECT * FROM " . PREFIX . "_vip_templates" );
	while( $row = $db->get_row() ) {
		$send[ $row['name'] ] = intval( $row['send'] );
		$text[ $row['name'] ] = htmlspecialchars( stripslashes( str_replace( "\\r\\n", "\n", $row['template'] ) ), ENT_QUOTES, $config['charset'] );
		$titles[ $row['name'] ] = stripslashes( $row['title'] );
		$html[ $row['name'] ] = intval( $html['use_html'] );
	}

	$templates = array(

		array(
			'title' => "{$lang['mwsvip_77']} - ( {$lang['mwsvip_75']} ) - [ PM ]",
			'name'	=> "pm_ps",
			'check' => "send_pm_ps",
			'html'	=> false,
			'tags'  => array(
				'title' 		=> $lang['mwsvip_10'],
				'alt_title' 	=> $lang['mwsvip_11'],
				'time' 			=> $lang['mwsvip_50'],
				'period' 		=> $lang['mwsvip_51'],
				'activation' 	=> $lang['mwsvip_83'],
				'finish' 		=> $lang['mwsvip_84'],
				'price' 		=> $lang['mwsvip_35'],
				'discount' 		=> $lang['mwsvip_67'],
				'currency' 		=> $lang['mwsvip_52'],
				'site_url' 		=> $lang['mwsvip_53'],
				'site_name' 	=> $lang['mwsvip_86'],
				'user_name'		=> $lang['mwsvip_80'],
			)
		),

		array(
			'title' => "{$lang['mwsvip_77']} - ( {$lang['mwsvip_75']} ) - [ Mail ]",
			'name'	=> "mail_ps",
			'check' => "send_mail_ps",
			'html'	=> true,
			'tags'  => array(
				'title' 		=> $lang['mwsvip_10'],
				'alt_title' 	=> $lang['mwsvip_11'],
				'time' 			=> $lang['mwsvip_50'],
				'period' 		=> $lang['mwsvip_51'],
				'activation' 	=> $lang['mwsvip_83'],
				'finish' 		=> $lang['mwsvip_84'],
				'price' 		=> $lang['mwsvip_35'],
				'discount' 		=> $lang['mwsvip_67'],
				'currency' 		=> $lang['mwsvip_52'],
				'site_url' 		=> $lang['mwsvip_53'],
				'site_name' 	=> $lang['mwsvip_86'],
				'user_name'		=> $lang['mwsvip_80'],
			)
		),

		array(
			'title' => "{$lang['mwsvip_77']} - ( {$lang['mwsvip_76']} ) - [ PM ]",
			'name'	=> "pm_ps_admin",
			'check' => "send_pm_ps_admin",
			'html'	=> false,
			'tags'  => array(
				'title' 		=> $lang['mwsvip_10'],
				'alt_title' 	=> $lang['mwsvip_11'],
				'time' 			=> $lang['mwsvip_50'],
				'period' 		=> $lang['mwsvip_51'],
				'activation' 	=> $lang['mwsvip_83'],
				'finish' 		=> $lang['mwsvip_84'],
				'price' 		=> $lang['mwsvip_35'],
				'discount' 		=> $lang['mwsvip_67'],
				'currency' 		=> $lang['mwsvip_52'],
				'site_url' 		=> $lang['mwsvip_53'],
				'site_name' 	=> $lang['mwsvip_86'],
				'user_name'		=> $lang['mwsvip_80'],
			)
		),

		array(
			'title' => "{$lang['mwsvip_77']} - ( {$lang['mwsvip_76']} ) - [ Mail ]",
			'name'	=> "mail_ps_admin",
			'check' => "send_mail_ps_admin",
			'html'	=> true,
			'tags'  => array(
				'title' 		=> $lang['mwsvip_10'],
				'alt_title' 	=> $lang['mwsvip_11'],
				'time' 			=> $lang['mwsvip_50'],
				'period' 		=> $lang['mwsvip_51'],
				'activation' 	=> $lang['mwsvip_83'],
				'finish' 		=> $lang['mwsvip_84'],
				'price' 		=> $lang['mwsvip_35'],
				'discount' 		=> $lang['mwsvip_67'],
				'currency' 		=> $lang['mwsvip_52'],
				'site_url' 		=> $lang['mwsvip_53'],
				'site_name' 	=> $lang['mwsvip_86'],
				'user_name'		=> $lang['mwsvip_80'],
			)
		),


		array(
			'title' => "{$lang['mwsvip_78']} - ( {$lang['mwsvip_75']} ) - [ PM ]",
			'name'	=> "pm_vf",
			'check' => "send_pm_vf",
			'html'	=> false,
			'tags'  => array(
				'title' 		=> $lang['mwsvip_10'],
				'alt_title' 	=> $lang['mwsvip_11'],
				'time' 			=> $lang['mwsvip_50'],
				'period' 		=> $lang['mwsvip_51'],
				'activation' 	=> $lang['mwsvip_83'],
				'finish' 		=> $lang['mwsvip_84'],
				'site_url' 		=> $lang['mwsvip_53'],
				'site_name' 	=> $lang['mwsvip_86'],
				'user_name'		=> $lang['mwsvip_80'],
			)
		),

		array(
			'title' => "{$lang['mwsvip_78']} - ( {$lang['mwsvip_75']} ) - [ Mail ]",
			'name'	=> "mail_vf",
			'check' => "send_mail_vf",
			'html'	=> true,
			'tags'  => array(
				'title' 		=> $lang['mwsvip_10'],
				'alt_title' 	=> $lang['mwsvip_11'],
				'time' 			=> $lang['mwsvip_50'],
				'period' 		=> $lang['mwsvip_51'],
				'activation' 	=> $lang['mwsvip_83'],
				'finish' 		=> $lang['mwsvip_84'],
				'site_url' 		=> $lang['mwsvip_53'],
				'site_name' 	=> $lang['mwsvip_86'],
				'user_name'		=> $lang['mwsvip_80'],
			)
		),
	);

	echo <<< HTML

<div class="panel panel-default">
    <div class="panel-heading">
        <b>{$lang['mwsvip_72']}</b>
        <div class="heading-elements">
            <ul class="icons-list">
                <li>
					<a href="{$PHP_SELF}?mod=vip"><i class="fa fa-arrow-left"></i> {$lang['mwsvip_9']}</a>
				</li>
			</ul>
		</div>
	</div>
	<form id="templates" action="" method="post">
		<div class="panel-body">
			<div class="accordion" id="accordion">
HTML;

foreach( $templates as $tpl ) {

	$checked = ( $send[ $tpl['name'] ] ) ? "checked" : "";
	$color = ( $send[ $tpl['name'] ] ) ? "#0d0" : "#d00";
	$checked_html = ( $html[ $tpl['name'] ] ) ? "checked" : "";

	echo <<< HTML
	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse{$tpl['name']}">
				<i class="fa fa-circle" style="color: {$color}"></i>&nbsp;&nbsp;{$tpl['title']}
			</a>
		</div>
		<div id="collapse{$tpl['name']}" class="accordion-body collapse">
			<div class="accordion-inner padded">
				{$lang['mwsvip_74']}: <br />
HTML;

				foreach( $tpl['tags'] as $tag => $title ) {
					echo '<b> {' . $tag . '} </b> - ' . $title . '<br />';
				}

				echo <<< HTML
				<br />
				<input name="{$tpl['name']}[title]" value="{$titles[ $tpl['name'] ]}" placeholder="{$lang['mwsvip_89']}" type="text" class="form-control" size="60" max-length="150" />
				<br /><br />
				<textarea rows="8" style="width:100%;" name="{$tpl['name']}[template]">{$text[ $tpl['name'] ]}</textarea>
				<br />
HTML;

				if ( $tpl['html'] ) {
					echo <<< HTML
					<br />
					<input class="icheck" type="checkbox" id="{$tpl['name']}_html" name="{$tpl['name']}[use_html]" value="1" {$checked_html}>
					<label for="{$tpl['name']}_html">{$lang['email_use_html']}</label>
HTML;
				}

				echo <<< HTML
				<br />
				<input class="icheck" type="checkbox" id="{$tpl['name']}_send" name="{$tpl['name']}[send]" value="1" {$checked}>
				<label for="{$tpl['name']}_send">{$lang['mwsvip_73']}</label>

			</div>
		</div>
	</div>
HTML;

}

echo <<< HTML
			</div>
			<button class="btn bg-teal btn-raised"><i class="fa fa-floppy-o position-left"></i>{$lang['mwsvip_56']}</button>
		</div>
	</form>
</div>
HTML;
}


// payments
else if ( $action == "payments" ) {
	echo <<< HTML
<div class="panel panel-default">
	<div class="panel-heading">
		<b>{$lang['mwsvip_49']}</b>
        <div class="heading-elements">
            <ul class="icons-list">
                <li>
					<a href="{$PHP_SELF}?mod=vip"><i class="fa fa-arrow-left"></i> {$lang['mwsvip_9']}</a>
				</li>
			</ul>
		</div>
	</div>
	<div class="table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<td>{$lang['mwsvip_31']}</td>
					<td>{$lang['mwsvip_32']}</td>
					<td>{$lang['mwsvip_33']}</td>
					<td>{$lang['mwsvip_80']}</td>
					<td>{$lang['mwsvip_81']}</td>
					<td>{$lang['mwsvip_82']}</td>
					<td>{$lang['mwsvip_83']}</td>
					<td>{$lang['mwsvip_84']}</td>
					<td>{$lang['mwsvip_85']}</td>
					<td>{$lang['mwsvip_38']}</td>
				<tr>
			</thead>
			<tbody>
HTML;

$db->query( "SELECT pay.*, plan.title, user.name FROM " . PREFIX . "_vip_payments pay LEFT JOIN " . PREFIX . "_vip_plans plan ON ( pay.plan_id = plan.id ) LEFT JOIN " . PREFIX . "_users user ON ( pay.user_id = user.user_id ) ORDER BY pay.id ASC" );
while( $pay = $db->get_row() ) {

	$pay['name'] = stripslashes( $pay['name'] );
	$pay['n_group_name'] = $user_group[ $pay['n_group'] ]['group_name'];
	$pay['t_group_name'] = $user_group[ $pay['t_group'] ]['group_name'];
	$pay['a_date'] = date( "d-m-Y H:i", $pay['a_date'] );
	$pay['f_date'] = date( "d-m-Y H:i", $pay['f_date'] );
	if ( empty( $pay['a_date'] ) ) $pay['a_date'] = "---";
	if ( empty( $pay['f_date'] ) ) $pay['f_date'] = "---";

	$data = json_decode( $pay['data'], true );

	//echo "<pre>"; print_r( $data ); echo "</pre>";

	if ( $pay['approve'] == '1' ) {
		$btn_class = "btn-success";
		$_prices = "<b>" . $data['mc_gross'] . "</b> / <b>" . $data['mc_fee'] . "</b> " . $data['mc_currency'];
		$act_link = "";
	} else if ( $pay['approve'] == '2' ) {
		$btn_class = "btn-info";
		$_prices = " -- {$lang['mwsvip_109']} --";
		$act_link = "";
	} else {
		$btn_class = "btn-danger";
		$_prices = " -- {$lang['mwsvip_87']} --";
		$act_link = "<li><a href=\"javascript:ActivatePayment('{$pay['id']}');\"><i class=\"fa fa-check\"></i> {$lang['mwsvip_68']}</a></li>";
	}

	echo <<< HTML
	<tr data-id="{$pay['id']}">
		<td style="text-align: center;width: 40px">{$pay['id']}</td>
		<td style="text-align: center;width: 120px">{$pay['title']}</td>
		<td style="text-align: center;width: 120px">{$pay['time']} {$pay['period']}</td>
		<td style="text-align: center;width: 120px"><a href="{$config['http_home_url']}?subaction=userinfo&amp;user={$pay['name']}" target="_blank">{$pay['name']}</a></td>
		<td style="text-align: center;width: 120px">
			<a href="{$PHP_SELF}?mod=usergroup&amp;action=edit&amp;id={$pay['n_group']}" target="_blank">
				{$pay['n_group_name']}
			</a>
		</td>
		<td style="text-align: center;width: 120px">
			<a href="{$PHP_SELF}?mod=usergroup&amp;action=edit&amp;id={$pay['t_group']}" target="_blank">
				{$pay['t_group_name']}
			</a>
		</td>
		<td style="text-align: center;width: 160px">{$pay['a_date']}</td>
		<td style="text-align: center;width: 160px">{$pay['f_date']}</td>
		<td style="text-align: center;width: 120px">{$_prices}</td>
		<td style="text-align: center;width: 60px">
			<div class="btn-group">
				<button class="btn btn-sm {$btn_class} btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cogs" style="color: #fff"></i> </button>
				<ul class="dropdown-menu text-left" style="min-width: 150px; right: 0; left: auto;">
					{$act_link}
					<li><a href="javascript:DeletePayment('{$pay['id']}');"><i class="fa fa-trash"></i> {$lang['mwsvip_41']}</a></li>
				</ul>
			</div>
		</td>
	</tr>
HTML;
}

echo <<< HTML
			</tbody>
		</table>
	</div>
</div>
HTML;
}


// settings
else if ( ! $action || $action == "settings" ) {

	$_waiting = $db->super_query( "SELECT COUNT(id) as pay FROM " . PREFIX . "_vip_payments WHERE approve = '0'" );
	if ( $_waiting['pay'] == 0 ) $_waiting['pay'] = "";

	echo <<< HTML
<form action="{$PHP_SELF}?mod=vip&amp;action=save" name="conf" id="conf" method="post" class="systemsettings">
	<div class="panel panel-flat">
		<div class="panel-heading">
			<b>{$lang['mwsvip_5']}</b>
		    <div class="heading-elements">
		        <ul class="icons-list">
					<li>
						<a href="{$PHP_SELF}?mod=vip&amp;action=notifications"><i class="fa fa-bullhorn"></i> {$lang['mwsvip_71']}</a>
					</li>
					<li style="margin-left: 25px">
						<a href="{$PHP_SELF}?mod=vip&amp;action=payments"><i class="fa fa-credit-card"></i> {$lang['mwsvip_48']} <span class="badge badge-danger">{$_waiting['pay']}</span></a>
					</li>
					<li style="margin-left: 25px">
						<a href="{$PHP_SELF}?mod=vip&amp;action=plans"><i class="fa fa-eye"></i> {$lang['mwsvip_4']}</a>
					</li>
				</ul>
		    </div>
		</div>
		<table class="table table-striped">
			<tbody>
HTML;

				showRow( $lang['mwsvip_42'], $lang['mwsvip_43'], "<input type=\"email\" style=\"text-align: center;\" class=\"form-control\" name=\"save_con[paypal]\" value=\"{$vipset['paypal']}\" size=\"40\">" );
				showRow( $lang['mwsvip_44'], $lang['mwsvip_45'], "<input type=\"url\" style=\"text-align: center;\" class=\"form-control\" name=\"save_con[return_url]\" value=\"{$vipset['return_url']}\" size=\"60\">" );
				showRow( $lang['mwsvip_46'], $lang['mwsvip_47'], "<input type=\"url\" style=\"text-align: center;\" class=\"form-control\" name=\"save_con[cancel_url]\" value=\"{$vipset['cancel_url']}\" size=\"60\">" );
				showRow( $lang['mwsvip_90'], $lang['mwsvip_91'], "<input type=\"text\" style=\"text-align: center;\" class=\"form-control\" name=\"save_con[admin]\" value=\"{$vipset['admin']}\" size=\"30\">" );
				showRow( $lang['mwsvip_92'], $lang['mwsvip_93'], "<input type=\"text\" style=\"text-align: center;\" class=\"form-control\" name=\"save_con[admin_ids]\" value=\"{$vipset['admin_ids']}\" size=\"30\">" );
				showRow( $lang['mwsvip_94'], $lang['mwsvip_95'], "<input type=\"text\" style=\"text-align: center;\" class=\"form-control\" name=\"save_con[ipn]\" value=\"{$vipset['ipn']}\" size=\"30\"><br /><br /><b>IPN: </b>{$config['http_home_url']}index.php?do=vip&action=<span id=\"ipn\">{$vipset['ipn']}</span>" );

echo <<< HTML
				<tr>
					<td colspan="5">
						<input type="hidden" name="user_hash" value="{$dle_login_hash}" />
						<button class="btn bg-teal btn-raised"><i class="fa fa-floppy-o position-left"></i>{$lang['user_save']}</button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</form>
HTML;

}

echofooter();
?>