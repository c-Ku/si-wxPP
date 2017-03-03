/**
 * 
 * @authors c-Ku (chyhoosun@gmail.com)
 * @Weibo	http://weibo.com/345950626
 * @FB		http://www.facebook.com/chyhao.sun
 * @date	2017-02-28 23:30:00
 * 
 */

time = parseInt(new Date().getTime() / 1000);
url = window.document.location.href.toString();
nonce = setNonce(16);

$("#scanQR").click(function() {
	wx.scanQRCode({
		needResult: 1,
		desc: 'scanQRCode desc',
		success: function (res) {
			// alert(JSON.stringify(res));
			var result = res.resultStr;
		}
	});
});

function setNonce(num) {
	var dict = "0123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM", j, str = "";
	for (var i = 0; i < num; i++) {
		j = parseInt(Math.random() * 62);
		str += dict[j];
	}
	return str;
}

function getSignature() {
	$.post("ajax/getSign.php", 
		{
			timestamp: time,
			nonceStr: nonce,
			url: url
		}, 
		function(data) {
			setWeixin(data.signature);
		},"json"
	);
}

function isWechat(){  
    var ua = navigator.userAgent.toLowerCase();  
    if(ua.match(/MicroMessenger/i)=="micromessenger") {  
        return true;  
    } else {  
        return false;  
    }  
} 

function setWeixin(sign) {
	wx.config({
		debug: false, 					// 开启调试模式
		appId: '', 	// 必填，公众号的唯一标识
		timestamp: time,				// 生成签名的时间戳
		nonceStr: nonce,				// 生成签名的随机串
		signature: sign,				// 签名，见附录1
		jsApiList: [
			'checkJsApi',
			'onMenuShareTimeline',
			'onMenuShareAppMessage',
			'onMenuShareQQ',
			'onMenuShareWeibo',
			'onMenuShareQZone',
			'hideMenuItems',
			'showMenuItems',
			'hideAllNonBaseMenuItem',
			'showAllNonBaseMenuItem',
			'translateVoice',
			'startRecord',
			'stopRecord',
			'onVoiceRecordEnd',
			'playVoice',
			'onVoicePlayEnd',
			'pauseVoice',
			'stopVoice',
			'uploadVoice',
			'downloadVoice',
			'chooseImage',
			'previewImage',
			'uploadImage',
			'downloadImage',
			'getNetworkType',
			'openLocation',
			'getLocation',
			'hideOptionMenu',
			'showOptionMenu',
			'closeWindow',
			'scanQRCode',
			'chooseWXPay',
			'openProductSpecificView',
			'addCard',
			'chooseCard',
			'openCard'
		] 								// 需要使用的JS接口列表
	});
}

// 关闭窗口可用 onclick="WeixinJSBridge.call('closeWindow');"
// 该方法无需调用微信官方API