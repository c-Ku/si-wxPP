<?php
/**
 * 
 * @authors c-Ku (chyhoosun@gmail.com)
 * @Weibo	http://weibo.com/345950626
 * @FB		http://www.facebook.com/chyhao.sun
 * @date    2017-03-01 19:30:00
 * 
 */

	include_once( '../config/config.php');
	if (isset($_GET['cd']) && isset($_GET['wxid']) && isset($_GET['uid'])) {
		$codeValu = $_GET['cd']; $wxid = $_GET['wxid']; $uid = $_GET['uid']; $timeST = time();
		$RES = mysql_query("SELECT * FROM seeleed_wechat_login WHERE codeValu = '$codeValu'");
		$NUM = mysql_num_rows($RES);
		if ($NUM) {
			$ROW = mysql_fetch_array($RES);
			if(!$ROW['codeMode'] && ($timeST - $ROW['codeTime']) < 90) {
				// 说明该码合乎规范
				$wxRES = mysql_query("SELECT * FROM seeleed_user_wx WHERE wechat_ID = '$wxid'");
				$wxNUM = mysql_num_rows($wxRES);
				if ($wxNUM) {
					// 说明存在该微信账户
					$wxROW = mysql_fetch_array($wxRES);
					if ($wxROW['user_Code'] == $uid) {
						// 说明该微信绑定了账户 操作正确 允许登录
						mysql_query("UPDATE seeleed_wechat_login SET codeMode = '1', userCode = '$uid' WHERE codeValu = '$codeValu'");
						$mMes = "#600-登录成功！";
					} else {
						// 用户码与传入值不匹配 欺诈？
						$mMes = "#601-用户不存在...";
					}
				} else {
					// 说明不存在该微信账户
					$mMes = "#602-用户不存在...";
				}
			} else {
				// 说明该码已被验证
				$mMes = "#603-已登录";
			}
		} else {
			// 说明不存在该码
			$mMes = "#604-该登录码不存在或已失效...";
		}
		
		echo $mMes;
	}