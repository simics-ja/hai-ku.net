<?php

class User_Agent
{
	
	//---------------------------------------------------------
	//  使用OS・ブラウザ解析 関数
	//---------------------------------------------------------
	
	static function ua_os($ua_full)
	{
		
		// UA情報が存在しない時は空白を返す
		if(!$ua_full){return array('','','Robot');}
		
		// 変数を初期化
		$ua = '';
		$os = '';
		$ua_type = 'PC';
		$device  = '';
		$carrier = '';
		$smart   = false;
		
		////////////////////////////////////////////////////////////////////////////////
		// [Robot] //
		
		// Googlebotの時
		if(preg_match('/Googlebot/',$ua_full,$h)){$ua = 'Googlebot';$ua_type = 'Robot';}
		
		// Google系の時
		elseif(preg_match('/([\w]+-Google)/',$ua_full,$h)){$ua = $h[1];$ua_type = 'Robot';}
		
		// Yahoo! Slurpの時
		elseif(preg_match('/Yahoo! Slurp/',$ua_full,$h)){$ua = 'Yahoo! Slurp';$ua_type = 'Robot';}
		
		// Y!J-AGENTの時
		elseif(preg_match('/Y!J-AGENT/',$ua_full,$h)){$ua = 'Y!J-AGENT';$ua_type = 'Robot';}
		
		// Bingbotの時
		elseif(preg_match('/bingbot/',$ua_full,$h)){$ua = 'Bingbot';$ua_type = 'Robot';}
		
		// MSNBotの時
		elseif(preg_match('/msnbot/',$ua_full,$h)){$ua = 'MSNBot';$ua_type = 'Robot';}
		
		// 百度の時
		elseif(preg_match('/Baiduspider/',$ua_full,$h)){$ua = 'Baiduspider';$ua_type = 'Robot';}
		
		// Ask Jeevesの時
		elseif(preg_match('/Ask Jeeves/',$ua_full,$h)){$ua = 'Ask Jeeves';$ua_type = 'Robot';}
		
		// CFNetwork の時
		elseif(preg_match('/CFNetwork/',$ua_full,$h)){$ua = 'CFNetwork';$ua_type = 'Robot';}
		
		// WinHttpRequest の時
		elseif(preg_match('/WinHttpRequest/',$ua_full,$h)){$ua = 'WinHttpRequest';$ua_type = 'Robot';}
		
		// libwww-perlの時
		elseif(preg_match('/libwww-perl/',$ua_full,$h)){$ua = 'libwww-perl';$ua_type = 'Robot';}
		
		// DiffBrowser の時
		elseif(preg_match('/DiffBrowser/',$ua_full,$h)){$ua = 'DiffBrowser';$ua_type = 'Robot';}
		
		// 特定の用語が含まれる時
		elseif(preg_match('/http:|bot|spider|crawler|checker|reader|antenna|capture|screenshot/i',$ua_full)){$ua = strip_tags($ua_full);$ua_type = 'Robot';}
		
		////////////////////////////////////////////////////////////////////////////////
		// [フィーチャーフォン] //
		
		// jig browserの時
		elseif(preg_match('/jig browser[^\)]*\d; (\w*)/',$ua_full,$h))
		{
			
			// OSとUA種別を設定
			$os = 'Symbian OS';
			$ua = 'jig browser';
			$ua_type = 'Mobile';
			$device = $h[1];
			
			// キャリアを判定
			    if(preg_match('/([A-Z]{1,2})(\d{2}[A-Z])/',$device,$h)){$carrier = 'docomo';$device = $h[1] . '-' . $h[2];}
			elseif(preg_match('/[A-Z]{1,2}\d{3}i/',$device)){$carrier = 'docomo';}
			elseif(preg_match('/[A-Z]{2}\d{1}[A-Z]/',$device)){$carrier = 'au';}
			elseif(preg_match('/[A-Z]{2}\d{2}$/',$device)){$carrier = 'au';}
			
			// UA,OS,UA種別,端末種別,キャリア を返す
			return array($ua,$os,$ua_type,$device,$carrier);
			
		}
		
		// Google Wireless Transcoder の時
		elseif(preg_match('/Google Wireless Transcoder/',$ua_full,$h)){$ua = 'Google Transcoder';$ua_type = 'Mobile';}
		
		// ドコモの時
		elseif(preg_match('/DoCoMo\/\d\.\d ([A-Z]{1,2})(\d{2}[A-Z])/',$ua_full,$h)){$carrier = 'docomo';$device = $h[1] . '-' . $h[2];}
		
		// ドコモの時2
		elseif(preg_match('/DoCoMo\/\d\.\d[\/ ]([A-Z]{1,2}\d{3}\w*)/',$ua_full,$h)){$carrier = 'docomo';$device = $h[1];}
		
		// ドコモの時3
		elseif(preg_match('/\(([A-Z]{1,2})(\d{2}[A-Z]);FOMA/',$ua_full,$h)){$carrier = 'docomo';$device = $h[1] . '-' . $h[2];}
		
		// ドコモの時4
		elseif(preg_match('/BlackBerry/',$ua_full,$h)){$carrier = 'docomo';$device = 'BlackBerry';}
		
		// ソフトバンクの時
		elseif(preg_match('/(SoftBank|Vodafone|J-PHONE)\/\d\.\d\/([^\/_]*)/',$ua_full,$h)){$carrier = 'SoftBank';$device = $h[2];}
		
		// ソフトバンクの時2
		elseif(preg_match('/\((.*);SoftBank/',$ua_full,$h)){$carrier = 'SoftBank';$device = $h[1];}
		
		// ソフトバンクの時3
		elseif(preg_match('/SoftBank ([^\s]*)/',$ua_full,$h)){$carrier = 'SoftBank';$device = $h[1];}
		
		// au の時
		elseif(preg_match('/KDDI-(\w*)/',$ua_full,$h)){$carrier = 'au';$device = $h[1];}
		
		////////////////////////////////////////////////////////////////////////////////
		// [Game] //
		
		// PS系の時
		elseif(preg_match('/PlayStation/i',$ua_full,$h))
		{
			
			// OSとUA種別を設定（統一）
			$os = 'PS OS';
			$ua = 'PS Browser';
			$ua_type = 'Game';
			
			// PSPの時
			if(preg_match('/PlayStation Portable/',$ua_full,$h)){$device = 'PSP';}
			
			// PS Vitaの時
			elseif(preg_match('/PlayStation Vita/',$ua_full,$h)){$device = 'PS Vita';}
			
			// PS3,PS4～の時
			elseif(preg_match('/PlayStation (\d{1,})/i',$ua_full,$h)){$device = 'PS' . $h[1];}
			
			// UA,OS,UA種別,端末種別,キャリア を返す
			return array($ua,$os,$ua_type,$device,$carrier);
			
		}
		
		// 任天堂系の時
		elseif(preg_match('/Nintendo/',$ua_full,$h))
		{
			
			// OSとUA種別を設定（統一）
			$os = 'Nintendo OS';
			$ua = 'Nintendo Browser';
			$ua_type = 'Game';
			
			// New Nintendo 3DSの時
			if(preg_match('/New Nintendo 3DS/',$ua_full,$h)){$device = 'New Nintendo 3DS';}
			
			// その他ハードの時
			elseif(preg_match('/(Nintendo [^;\)]+)[;\)]/',$ua_full,$h)){$device = $h[1];}
			
			// UA,OS,UA種別,端末種別,キャリア を返す
			return array($ua,$os,$ua_type,$device,$carrier);
			
		}
		
		////////////////////////////////////////////////////////////////////////////////
		// [PC] //
		
		// Vivaldiの時
		elseif(preg_match('/Vivaldi\/(\d{1,}\.\d)/',$ua_full,$h)){$ua = 'Vivaldi ' . $h[1];}
		
		// Operaの時
		elseif(preg_match('/OPR\/(\d{1,}\.\d)/',$ua_full,$h)){$ua = 'Opera ' . $h[1];}
		
		// Opera10以上の時
		elseif(preg_match('/Opera.*Version\/(\d{1,}\.\d)/',$ua_full,$h)){$ua = 'Opera ' . $h[1];}
		
		// Opera10未満の時
		elseif(preg_match('/Opera[ \/](\d{1,}\.\d)/',$ua_full,$h)){$ua = 'Opera ' . $h[1];}
		
		// Lunascape の時
		elseif(preg_match('/Lunascape[ \/](\d{1,}\.\d)/',$ua_full,$h)){$ua = 'Lunascape ' . $h[1];}
		
		// Sleipnir の時
		elseif(preg_match('/Sleipnir( Version |\/)(\d{1,}\.\d)/',$ua_full,$h)){$ua = 'Sleipnir ' . $h[2];}
		
		// Edgeの時
		elseif(preg_match('/Edge\/(\d{1,}\.\d)/',$ua_full,$h)){$ua = 'Edge ' . $h[1];}
		
		// IE11以上の時
		elseif(preg_match('/Trident.+ rv:(\d{1,})\.\d/',$ua_full,$h)){$ua = 'IE ' . $h[1];}
		
		// IE10以下の時
		elseif(preg_match('/MSIE (\d{1,}\.\d)/',$ua_full,$h)){$ua = 'IE ' . $h[1];}
		
		// Netscape6以上の時
		elseif(preg_match('/(Netscape|Navigator)[^\/]*\/(\d{1,}\.\d)/',$ua_full,$h)){$ua = 'Netscape ' . $h[2];}
		
		// Chromeの時
		elseif(preg_match('/Chrome\/(\d{1,}\.\d)/',$ua_full,$h)){$ua = 'Chrome ' . $h[1];}
		
		// Safari3以上の時
		elseif(preg_match('/Version\/(\d{1,}\.\d).* Safari/',$ua_full,$h)){$ua = 'Safari ' . $h[1];}
		
		// Safari3未満/WebKit系の時
		elseif(preg_match('/AppleWebKit\/(\d*)/',$ua_full,$h))
		{
			
			// ビルドNoによってバージョン判定
			    if($h[1] >= 533){$ua = 'Safari 5.0';}
			elseif($h[1] >= 528){$ua = 'Safari 4.0';}
			elseif($h[1] >= 525){$ua = 'Safari 3.1';}
			elseif($h[1] >= 500){$ua = 'Safari 3.0';}
			elseif($h[1] >= 400){$ua = 'Safari 2.0';}
			elseif($h[1] >= 300){$ua = 'Safari 1.3';}
			elseif($h[1] >= 120){$ua = 'Safari 1.2';}
			elseif($h[1] >= 100){$ua = 'Safari 1.1';}
			else{$ua = 'Safari 1.0';}
			
		}
		
		// Mozilla Firefoxの時
		elseif(preg_match('/Firefox\/(\d{1,}\.\d)/',$ua_full,$h)){$ua = 'Firefox ' . $h[1];}
		
		// Mozillaの時
		elseif(preg_match('/rv:(\d{1,}\.\d)[^\)]*\) Gecko/',$ua_full,$h)){$ua = 'Mozilla ' . $h[1];}
		
		// Classillaの時
		elseif(preg_match('/rv:(\d{1,}\.\d).*Classilla/',$ua_full,$h)){$ua = 'Classilla ' . $h[1];}
		
		// Netscape4以下の時
		elseif(preg_match('/Mozilla\/(\d\.\d)(\d*) \[/',$ua_full,$h)){$ua = 'Netscape ' . $h[1];}
		
		// Lynxの時
		elseif(preg_match('/Lynx\/(\d{1,}\.\d)/',$ua_full,$h)){$ua = 'Lynx ' . $h[1];}
		
		////////////////////////////////////////////////////////////////////////////////
		// [etc] //
		
		// Blue Coat Proxyの時
		elseif($ua_full === 'Mozilla/4.0 (compatible;)'){$ua = 'Blue Coat Proxy';}
		
		// 不明の時
		else
		{
			
			// フルUAをそのままUAにセット
			$ua = $ua_full;
			
			// 言語設定が無い時はロボットとみなす
			if(!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){$ua_type = 'Robot';}
			
			// その他特定の書式の場合もロボットとみなす
			elseif(preg_match('/compatible; ([^;]+)\/(\d\.\d)/',$ua_full,$h)){$ua = $h[1] . ' ' . $h[2];$ua_type = 'Robot';}
			
		}
		
		// ロボットの時は終了
		if($ua_type === 'Robot'){return array($ua,$os,$ua_type,$device,$carrier);}
		
		////////////////////////////////////////////////////////////////////////////////
		
		// [OSの判定] //
		
		// Windowsの時
		if(preg_match('/Windows/',$ua_full,$h))
		{
			
			// Windows Phone の時
			if(preg_match('/; ([^;]*); Windows Phone (\d\.\d)/',$ua_full,$h)){$os = 'Windows Phone ' . $h[2];$ua_type = 'Mobile';}
			
			// Windows 10 Mobile の時
			elseif(preg_match('/Windows Phone 10.0/',$ua_full,$h)){$os = 'Windows 10 Mobile';$ua_type = 'Mobile';}
			
			// Windows Mobile の時
			elseif(preg_match('/Windows CE.*\) (.*)/',$ua_full,$h)){$os = 'Windows Mobile';$ua_type = 'Mobile';}
			
			// Windows Mobile の時2
			elseif(preg_match('/Windows CE/',$ua_full,$h)){$os = 'Windows Mobile';$ua_type = 'Mobile';}
			
			// その他 Windows の時
			elseif(preg_match('/Windows NT 10.0/'      ,$ua_full,$h)){$os = 'Windows 10';}
			elseif(preg_match('/Windows NT 6.3/'       ,$ua_full,$h)){$os = 'Windows 8.1';}
			elseif(preg_match('/Windows NT 6.2/'       ,$ua_full,$h)){$os = 'Windows 8';}
			elseif(preg_match('/Windows NT 6.1/'       ,$ua_full,$h)){$os = 'Windows 7';}
			elseif(preg_match('/Windows NT 6.0/'       ,$ua_full,$h)){$os = 'Windows Vista';}
			elseif(preg_match('/Windows (NT 5.1|XP)/'  ,$ua_full,$h)){$os = 'Windows XP';}
			elseif(preg_match('/Windows (NT 5.0|2000)/',$ua_full,$h)){$os = 'Windows 2000';}
			elseif(preg_match('/Windows ME|Win 9x/'    ,$ua_full,$h)){$os = 'Windows Me';}
			elseif(preg_match('/Windows 98|Win98/'     ,$ua_full,$h)){$os = 'Windows 98';}
			elseif(preg_match('/Windows 95|Win95/'     ,$ua_full,$h)){$os = 'Windows 95';}
			
			elseif(preg_match('/NT 5.2/'    ,$ua_full,$h)){$os = 'Windows Server 2003';}
			elseif(preg_match('/NT(\d\.\d)/',$ua_full,$h)){$os = 'Windows NT' . $h[1];}
			
		}
		
		// iOS の時
		elseif(preg_match('/(iPhone|iPad|iPod touch).* OS (\d{1,})_(\d)/' ,$ua_full,$h))
		{
			
			// OSとUA種別を設定（統一）
			$os = 'iOS ' . $h[2] . '.' . $h[3];
			$device = $h[1];
			$smart = true;
			
			// UAを再判定
			
			// Opera Mini の時
			if(preg_match('/OPiOS\/(\d{1,}\.\d)/',$ua_full,$h)){$ua = 'Opera Mini ' . $h[1];}
			
			// Opera Coast の時
			elseif(preg_match('/Coast\/(\d{1,}\.\d)/',$ua_full,$h)){$ua = 'Opera Coast ' . $h[1];}
			
			// Firefox Mobile の時
			elseif(preg_match('/FxiOS\/(\d{1,}\.\d)/',$ua_full,$h)){$ua = 'Firefox Mobile ' . $h[1];}
			
			// Chrome Mobile の時
			elseif(preg_match('/CriOS\/(\d{1,}\.\d)/',$ua_full,$h)){$ua = 'Chrome Mobile ' . $h[1];}
			
			// iLunascape の時
			elseif(preg_match('/iLunascape/',$ua_full,$h)){$ua = 'iLunascape';}
			
			// Mobile Safari の時
			elseif(preg_match('/Version\/(\d{1,}\.\d).* Mobile.* Safari/',$ua_full,$h)){$ua = 'Safari Mobile ' . $h[1];}
			
			// その他は標準ブラウザと判定
			else{$ua = 'Safari Mobile';}
			
		}
		
		// Androidの時
		elseif(preg_match('/Android/',$ua_full,$h))
		{
			
			// OSを仮設定
			$os = 'Android';
			$smart = true;
			
			// 端末機種が取れる時
			if(preg_match('/Android (\d{1,}\.\d).*; (.+) Build/',$ua_full,$h)){$os = 'Android ' . $h[1];$device = $h[2];}
			
			// 端末機種が取れない時
			elseif(preg_match('/Android (\d{1,}\.\d)[^;]*;/',$ua_full,$h)){$os = 'Android ' . $h[1];}
			
			// UAを再判定
			
			// Opera Mini の時
			if(preg_match('/Opera Mini\/(\d{1,}\.\d)/',$ua_full,$h)){$ua = 'Opera Mini ' . $h[1];}
			
			// Opera Mobile の時
			elseif(preg_match('/OPR\/(\d{1,}\.\d)/',$ua_full,$h)){$ua = 'Opera Mobile ' . $h[1];}
			
			// iLunascape の時
			elseif(preg_match('/iLunascape\/(\d{1,}\.\d)/',$ua_full,$h)){$ua = 'iLunascape ' . $h[1];;}
			
			// Sleipnir Mobile の時
			elseif(preg_match('/Sleipnir\/(\d{1,}\.\d)/',$ua_full,$h)){$ua = 'Sleipnir Mobile ' . $h[1];}
			
			// Firefox Mobile の時
			elseif(preg_match('/Firefox\/(\d{1,}\.\d)/',$ua_full,$h)){$ua = 'Firefox Mobile ' . $h[1];}
			
			// Chrome Mobile の時
			elseif(preg_match('/Chrome\/(\d{1,}\.\d)/',$ua_full,$h)){$ua = 'Chrome Mobile ' . $h[1];}
			
			// その他は標準ブラウザと判定
			else{$ua = 'Chrome Mobile';}
			
		}
		
		// Mac OS / OS X / macOS の時
		elseif(preg_match('/Mac OS X (\d{2})[_\.](\d{1,})/',$ua_full,$h)){$os =  'Mac OS X ' . $h[1] . '.' . $h[2];}
		elseif(preg_match('/Mac OS X/'  ,$ua_full,$h)){$os = 'Mac OS X';}
		elseif(preg_match('/Mac/'       ,$ua_full,$h)){$os = 'Mac OS';}
		
		// Linux の時
		elseif(preg_match('/TurboLinux/',$ua_full,$h)){$os = 'Turbo Linux';}
		elseif(preg_match('/VineLinux/' ,$ua_full,$h)){$os = 'Vine Linux';}
		elseif(preg_match('/Red Hat/'   ,$ua_full,$h)){$os = 'RedHat Linux';}
		elseif(preg_match('/Debian/'    ,$ua_full,$h)){$os = 'Debian Linux';}
		elseif(preg_match('/Fedora/'    ,$ua_full,$h)){$os = 'Fedora';}
		elseif(preg_match('/Ubuntu/'    ,$ua_full,$h)){$os = 'Ubuntu';}
		elseif(preg_match('/CentOS/'    ,$ua_full,$h)){$os = 'CentOS';}
		elseif(preg_match('/Linux/'     ,$ua_full,$h)){$os = 'Linux';}
		
		// Solaris の時
		elseif(preg_match('/SunOS/',$ua_full,$h)){$os = 'Solaris';}
		
		////////////////////////////////////////////////////////////////////////////////
		
		// [スマートフォンの判定] //
		
		// フィーチャーフォンの時
		if($carrier != '')
		{
			
			// OSとUA種別を設定（統一）
			$os = 'Symbian OS';
			$ua_type = 'Mobile';
			
			// docomoの時
			if($carrier == 'docomo'){$ua = 'i-mode Browser';}
			
			// SoftBankの時
			elseif($carrier == 'SoftBank'){$ua = 'NetFront Browser';}
			
			// auの時
			elseif($carrier == 'au'){$ua = 'UP.Browser';}
			
		}
		
		// スマートフォンの時
		elseif($smart)
		{
			
			// UA種別を設定
			$ua_type = 'Mobile';
			
			// IPアドレスをリモートホストに変換
			$remote_host = @gethostbyaddr($_SERVER['REMOTE_ADDR']);
			
			// 特定の文字列を削除
			$device = preg_replace('/HTC|SonyEricsson/','',$device);
			$device = preg_replace('/SonySO/','SO',$device);
			
			// キャリアを判定（リモートホストから）
			    if(preg_match('/spmode|mopera/',$remote_host)){$carrier = 'docomo';}
			elseif(preg_match('/au-net|ezweb/',$remote_host)){$carrier = 'au';}
			elseif(preg_match('/openmobile|pcsitebrowser|panda-world|bbtec/',$remote_host)){$carrier = 'SoftBank';}
			
			// キャリアを判定（端末機種から）
			elseif(preg_match('/[A-Z]{1,2}-\d{2}[A-Z]|FOMA|Docomo/',$device)){$carrier = 'docomo';}
			elseif(preg_match('/IS/A',$device)){$carrier = 'au';}
			elseif(preg_match('/SBM|DM|[0-9]{3}/A',$device)){$carrier = 'SoftBank';}
			elseif(preg_match('/[A-Z]{3}\d{2}/A',$device)){$carrier = 'au';}
			
			// 判別出来ない時はWi-Fiと判定
			else{$carrier = 'Wi-Fi';}
			
			// 特定の文字列を削除
			$device = preg_replace('/_| |FOMA |Docomo |SBM/A','',$device);
			//$device = preg_replace('/_.+$/','',$device);
			
		}
		
		// UA,OS,UA種別,端末種別,キャリア を返す
		return array($ua,$os,$ua_type,$device,$carrier);
		
	}
	
}

