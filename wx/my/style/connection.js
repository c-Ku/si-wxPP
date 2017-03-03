/**
 * 
 * @authors c-Ku (chyhoosun@gmail.com)
 * @Weibo	http://weibo.com/345950626
 * @FB		http://www.facebook.com/chyhao.sun
 * @date	2017-02-28 12:20:00
 * 
 */

// bg = "";

$(document).ready(function() {
	var wxid = $_GET["wxid"];
	if(!isWechat()) {
		location.href = "{Your Domain}";
	}
	// setBack();
	getSignature();

	var Intv = setInterval(function() {
		wx.hideAllNonBaseMenuItem();
		setTimeout(function() {
			clearInterval(Intv);
		}, 1000);
	}, 1);

	$("#confirm").click(function() {
		$.post("ajax/connection.php", 
			{
				wxid: wxid,
				user: $("#user").val(),
				pass: $.md5($("#pass").val())
			}, 
			function(data) {
				if (data.mCode == 601) {
					$("#form").html('<p>' + data.user + ' 您好！</p><p>您已绑定账户</p><p><button type="button" onclick="wx.closeWindow();">点击关闭</button></p>')
				} else if (data.mCode == 602) {
					alert('密码错误，请检查后重新提交！');
				} else if (data.mCode == 603) {
					alert('账户不存在，将为您跳转到登录页面！');
				} else if (data.mCode == 604) {
					$("#form").html('<p>您好 您的账户已经绑定过了</p><p><button type="button" onclick="wx.closeWindow();">点击关闭</button></p>')
				}
			},"json"
		);
	});
});

var $_GET = (function() {
	var url = window.document.location.href.toString();
	var vGet = url.split("?");
	if(typeof(vGet[1]) == "string") {
		vGet = vGet[1].split("&");
		var value = {};
		for(var i in vGet){
			var j = vGet[i].split("=");
			value[j[0]] = j[1];
		}
		return value;
	} else {
		return {};
	}
})();

function setBack() {		
	$("body").css({
		backgroundImage: 'url(' + bg + ')'
	});
}

