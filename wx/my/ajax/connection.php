<?php
/**
 * 
 * @authors c-Ku (chyhoosun@gmail.com)
 * @Weibo	http://weibo.com/345950626
 * @FB		http://www.facebook.com/chyhao.sun
 * @date    2017-02-28 15:45:45
 * 
 */

	include_once( '../../config/config.php');
	if (isset($_POST['wxid']) && isset($_POST['user']) && isset($_POST['pass'])) {
		$info = array(
			'wxid' => $_POST['wxid'], 
			'user' => $_POST['user'],
			'pass' => $_POST['pass']
		);

		$arr = acCheck($info);
		echo json_encode($arr);
	}

	function acCheck($info) {
		$acSQL = "SELECT * FROM {WeChat User List} WHERE wechat_ID = '$info[wxid]'";
		$acRES = mysql_query($acSQL);
		$acROW = mysql_fetch_array($acRES);
		if ($acROW['user_Code'] == "") {
			$siSQL = "SELECT * FROM {User List} WHERE mail = '$info[user]' OR mobile = '$info[user]'";
			$siRES = mysql_query($siSQL);
			$siNUM = mysql_num_rows($siRES);
			if ($siNUM) {
				// 存在账户
				$siROW = mysql_fetch_array($siRES);
				$ip = get_real_ip();
				if ($siROW['pw'] == $info['pass']) {
					mysql_query("UPDATE {User List} SET wechat_ID = '$info[wxid]', ip_last = '$ip' WHERE code = '$siROW[code]'");
					mysql_query("UPDATE {WeChat User List} SET user_Code = '$siROW[code]' WHERE wechat_ID = '$info[wxid]'");
					$mCode = 601; $user = mb_convert_encoding($siROW['username'], "UTF-8", "GB2312");
				} else {
					// 密码错误
					$mCode = 602; $user = "";
				}
			} else {
				// 账户不存在
				$mCode = 603; $user = "";
			}
		} else {
			// 账户已经绑定
			$mCode = 604; $user = "";
		}

		$arr = Array('mCode' => $mCode, 'user' => $user);

		return $arr;
	}

	function get_real_ip() {
		$ip = false;
		if(!empty($_SERVER["HTTP_CLIENT_IP"]))
			$ip = $_SERVER["HTTP_CLIENT_IP"];

		if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);

			if($ip)
				array_unshift($ips, $ip); $ip = FALSE;

			for($i = 0; $i < count($ips); $i++) {
				if(!preg_match("/^(10|172\.16|192\.168)\./i",$ips[$i])) {
					$ip = $ips[$i];
					break;
				}
			}
		}
		return($ip ? $ip : $_SERVER['REMOTE_ADDR']);
	}