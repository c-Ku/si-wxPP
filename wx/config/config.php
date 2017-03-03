<?                                                             
	@ mysql_connect("{Your server name}", "{your db username}","{your db password}")or die("不能连接数据库");
	@ mysql_select_db( "{your db}") or die("没有选择数据库"); 
	
	mysql_query("set names gb2312");  

	define("TOKEN", "{TOKEN Value}");
	$encodingAesKey = "{Encoding AES Key Value}";
	$appSecret = "{App Secret Value}";
	$appId = "{App ID}"; 
	$urlToken = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appId . "&secret=" . $appSecret;
	$urlTicket = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=";

	// 此处连接实现每超过两个小时更新公众号的AccessToken 以及 JsapiTicket
	$wechat_RES = mysql_query("SELECT * FROM seeleed_wechat WHERE id = 1 limit 1");
	$wechat_ROW = mysql_fetch_array($wechat_RES);

	$timeNow = time();
	$timeWas = $wechat_ROW['addTime'];
	$timeCnt = $timeNow - $timeWas;

	if ($timeCnt >= 7200) {
		$accessToken = jsonGet($urlToken, 'access_token');
		$jsapiTicket = jsonGet($urlTicket . $accessToken, 'ticket');
		mysql_query("UPDATE {WeChat Info List} SET accessToken = '$accessToken', jsapiTicket = '$jsapiTicket', addTime = '$timeNow' WHERE id = '1'");
	} else {
		$accessToken = $wechat_ROW['accessToken'];
		$jsapiTicket = $wechat_ROW['jsapiTicket'];
	}

	function jsonGet($url, $sub) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);   
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$result = curl_exec($ch);
		curl_close($ch);

		$arr = json_decode($result, true);
		return $arr[$sub];
	}                                                            
?>                                                                 
