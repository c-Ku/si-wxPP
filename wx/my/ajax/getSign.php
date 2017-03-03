<?php
/**
 * 
 * @authors c-Ku (chyhoosun@gmail.com)
 * @Weibo	http://weibo.com/345950626
 * @FB		http://www.facebook.com/chyhao.sun
 * @date    2017-02-28 22:50:00
 * 
 */

	include_once( '../../config/config.php');
	if (isset($_POST['nonceStr']) && isset($_POST['url']) && isset($_POST['timestamp'])) {
		$nonceStr = $_POST['nonceStr'];
		$timestamp = $_POST['timestamp'];
		$url = $_POST['url'];

		$wechat_RES = mysql_query("SELECT * FROM seeleed_wechat WHERE id = 1 limit 1");
		$wechat_ROW = mysql_fetch_array($wechat_RES);
		$jsapiTicket = $wechat_ROW['jsapiTicket'];

		$data = sha1("jsapi_ticket=" . $jsapiTicket . "&noncestr=" . $nonceStr . "&timestamp=" . $timestamp . "&url=" . $url);
		$arr = json_encode(Array('signature' => $data));
		echo $arr;
	}