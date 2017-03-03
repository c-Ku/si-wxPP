<?php
	include('config/config.php');

	if(isset($GLOBALS["HTTP_RAW_POST_DATA"])) {

		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

		$fromUsername = $postObj->FromUserName;
		$toUsername = $postObj->ToUserName;
		$content = trim($postObj->Content);
		$PicUrl = $postObj->PicUrl;
		$MsgType = $postObj->MsgType;
		$MsgId = $postObj->MsgId;
		$event = $postObj->Event;
		$EventKey = $postObj->EventKey;

		$time = time();
		$sTime = date("Y-m-d H:i:s");
		$sDate = date("Y-m-d");

	} else {
		echo "<script>location.href='http://{Your domain}';</script>";
	}

	if ($event == "subscribe" || $content == "绑定" || $content == "账户" || strstr($content, "绑定")) {
		$resArr = json_decode(acCheckRes($event, acCheck()), true);
		resPack($resArr);
		return 1;
	} elseif (preg_match("/^\d{6}$/", $content)) {
		$acCheck = acCheck();
		if(!$acCheck) {
			$resArr = acCheckRes(0, 0);
		} else {
			$resMes = noSpace(file_get_contents("http://{Your domain}/func/wxLogin.php?cd=" . $content . "&wxid=" . $fromUsername . "&uid=" . $acCheck));
			$resArr = '[
							{
								"Tit": "【{Your product name}通行证】微信快捷登录", 
								"Des": "' . $resMes . '\n",
								"Pic": "",
								"Url": ""
							}
						]';
		}
		$resArr = json_decode($resArr, true);
		resPack($resArr);
		return 1;
	} elseif ($content == "你好") {
		resInfo("你好");
		return 1;
	} else {
		resInfo("系统调试中...");
		return 1;
	}

	// 以下为功能函数
	function noSpace($str) {
		$qian = array(" ","　","\t","\n","\r"); $hou = array("","","","","");
    	@ $result = str_replace($qian, $hou, $str);
    	return $result;
    }

	// 以下为微信回复信息封装函数
	function resInfo($resMes) {
		global $fromUsername;
		global $toUsername;
		global $time;

		$resText = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					<FuncFlag>0</FuncFlag>
					</xml>";
					
		$msgType = "text";

		$resText = sprintf($resText, $fromUsername, $toUsername, $time, $msgType, $resMes);
		echo $resText;
	}
	function resPack($resArr) {
		global $fromUsername;
		global $toUsername;
		global $time;

		$resNum = sizeof($resArr);
		$resPac = "<xml>
					<ToUserName><![CDATA[$fromUsername]]></ToUserName>
					<FromUserName><![CDATA[$toUsername]]></FromUserName>
					<CreateTime>$time</CreateTime>
					<MsgType><![CDATA[news]]></MsgType>
					<ArticleCount>$resNum</ArticleCount>
					<Articles>";

		for ($i = 0; $i < $resNum; $i++) { 
			$Tit = $resArr[$i]['Tit'];
			$Des = $resArr[$i]['Des'];
			$Pic = $resArr[$i]['Pic'];
			$Url = $resArr[$i]['Url'];
			$resPac .= "<item>
						<Title><![CDATA[$Tit]]></Title>
						<Description><![CDATA[$Des]]></Description>
						<PicUrl><![CDATA[$Pic]]></PicUrl>
						<Url><![CDATA[$Url]]></Url>
						</item>";
		}

		$resPac .= "</Articles>
					<FuncFlag>0</FuncFlag>
					</xml>";
					
		echo $resPac;
	}

	// 以下部位验证账户信息
	function acCheck() {
		global $fromUsername;
		global $toUsername;
		global $time;

		$acSQL = "SELECT * FROM {Your wechat user list} WHERE wechat_ID = '$fromUsername'";
		$acRES = mysql_query($acSQL);
		$acNUM = mysql_num_rows($acRES);
		if(!$acNUM) {
			$ctrSQL = "INSERT INTO {Your wechat user list} (wechat_ID, user_inTime) VALUE ('$fromUsername','$time')";
			mysql_query($ctrSQL);
			return 0;
		} else {
			$acROW = mysql_fetch_array($acRES);
			if($acROW['user_Code'] == "")
				return 0;
			else
				return $acROW['user_Code'];
		}
	}
    // 以下部位验证账户绑定信息
	function acCheckRes($event, $acCheck) {
		global $fromUsername;
		global $toUsername;
		global $time;
		
		if(!$acCheck || (!$event && !$acCheck)) {
			$resArr = '[
							{
								"Tit": "感谢您的关注", 
								"Des": "请点击此处绑定通行证",
								"Pic": "",
								"Url": "http://{Your domain}/my/connection.html?wxid=' . $fromUsername . '"
							}
						]';
		} else {
			$userInfo = siCheck();
			$userMes = $event == "subscribe" ? "感谢您重新关注" : "您已绑定关注";
			$resArr = '[
							{
								"Tit": "尊敬的 ' . $userInfo["user"] . '", 
								"Des": "' . $userMes . '\n当前手机：' . $userInfo["mobi"] . '\n当前邮箱：' . $userInfo["mail"] . '\n最后登录：' . $userInfo["laip"] . ' (' . $userInfo["lada"] . ')\n",
								"Pic": "",
								"Url": ""
							}
						]';
		}
		return $resArr;
	}
	
	function siCheck() {
		global $fromUsername;
		global $toUsername;
		global $time;

		$acSQL = "SELECT * FROM {User List} WHERE wechat_ID = '$fromUsername'";
		$acRES = mysql_query($acSQL);
		$acROW = mysql_fetch_array($acRES);
		$info = array(
			'user' => mb_convert_encoding($acROW['username'], "UTF-8", "GB2312"), 
			'mail' => $acROW['mail'], 
			'mobi' => $acROW['mobile'], 
			'mode' => $acROW['mode'], 
			'laip' => $acROW['ip_last'], 
			'lada' => $acROW['lastDate']
		);
		return $info;
	}


	// 绑定验证时将下方函数注释释放
	// valid();
	function valid() {
		if(checkSignature()) {
			$echoStr = $_GET["echostr"];
			echo $echoStr;
		}
	}
	function checkSignature() {
		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce = $_GET["nonce"];	
				
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature )
			return true;
		else
			return false;
	}
?>