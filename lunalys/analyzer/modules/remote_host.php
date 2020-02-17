<?php

class Remote_Host
{
	
	//---------------------------------------------------------
	//  IPアドレス取得
	//---------------------------------------------------------
	
	static function ip_address()
	{
		
		// デフォルトのIPアドレスを取得
		$ip_address = $_SERVER['REMOTE_ADDR'];
		
		// 環境変数名を配列に格納
		$envs = array('HTTP_FROM','HTTP_CLIENT_IP','HTTP_X_FORWARDED_FOR');
		
		// 環境変数をチェックするループ
		while(list(,$env) = each($envs))
		{
			
			// 環境変数にIPアドレスがセットされている時
			if(isset($_SERVER[$env]) and preg_match('/([\d\.]+)/A',$_SERVER[$env],$h))
			{
				
				// 元のIPアドレスを取得
				$ip_address = $h[1];
				
				// ループを抜ける
				break;
				
			}
			
		}
		
		// IPアドレスを返す
		return $ip_address;
		
	}
	
	
	//---------------------------------------------------------
	//  ホスト/ドメイン 解析
	//---------------------------------------------------------
	
	static function host_domain($ip_address = '')
	{
		
		// IPアドレスを取得
		if(!$ip_address){$ip_address = $_SERVER['REMOTE_ADDR'];}
		
		// IPアドレスをリモートホストに変換
		$remote_host = @gethostbyaddr($ip_address);
		
		// 名前解決できない時
		if(!$remote_host or $remote_host === $ip_address){return array($ip_address,'unknown');}
		
		// ローカルホストらのアクセスの時
		elseif($remote_host === 'localhost'){return array($remote_host,$remote_host);}
		
		// リモートホストを分割
		$hosts = explode('.',$remote_host);
		
		// 不正なホスト名の時
		if(count($hosts) < 3){return array($ip_address,$remote_host);}
		
		// 配列を逆順に並べ替え
		$hosts = array_reverse($hosts);
		
		// 2nd LD の長さを取得
		$sld_len = strlen($hosts[1]);
		
		// 2nd LD の長さが3以上の時（属性無し）
		if($sld_len > 2){$host_domain = $hosts[1] . '.' . $hosts[0];}
		
		// 2nd LD の長さが2以下の時（属性有り）
		else{$host_domain = $hosts[2] . '.' . $hosts[1] . '.' . $hosts[0];}
		
		// ホスト,ドメインを返す
		return array($remote_host,$host_domain);
		
	}
	
	
	//---------------------------------------------------------
	//  都道府県 解析
	//---------------------------------------------------------
	
	static function city($remote_host,$work_dir)
	{
		
		// 変数を初期化
		$city = '';
		
		// 都道府県設定ファイルを読み込み
		$citys = parse_ini_file($work_dir . '/templates/ini/city.ini');
		
		// 都道府県取得ループ
		foreach($citys as $key => $val)
		{
			
			// 特定の文字列が含まれる時
			if(preg_match("/$key/",$remote_host))
			{
				
				// 値を取得
				$city = $val;
				
				// ループ終了
				break;
				
			}
			
		}
		
		// 都道府県を返す
		return $city;
		
	}
	
	//---------------------------------------------------------
	//  国/都道府県 解析
	//---------------------------------------------------------
	
	static function country_city($remote_host,$work_dir)
	{
		
		// リモートホストを分割
		$hosts = explode('.', $remote_host);
		
		// 配列を逆順に並べ替え
		$hosts = array_reverse($hosts);
		
		// トップレベルドメインを取得
		$code = $hosts[0];
		
		// IPアドレスのままの時は空欄を返す
		if(preg_match('/\d/',$code)){return array('','');}
		
		// 変数を初期化
		$country = '';
		$city    = '';
		
		// 日本及び汎用ドメイン以外の時
		if(!preg_match('/jp|net|com|org/i',$code))
		{
			
			// 国設定ファイルを読み込み
			$countrys = parse_ini_file($work_dir . '/templates/ini/country.ini');
			
			// 国を取得
			$country = (isset($countrys[$code])) ? $countrys[$code] : $code;
			
		}
		
		// 日本及び汎用ドメインの時
		else
		{
			
			// 日本の時
			if($code == 'jp'){$country = '日本';}
			
			// 都道府県設定ファイルを読み込み
			$citys = parse_ini_file($work_dir . '/templates/ini/city.ini');
			
			// 都道府県取得ループ
			foreach($citys as $key => $val)
			{
				
				// 都道府県を取得
				if(preg_match("/$key/",$host))
				{
					
					// 値を取得
					$city = $val;
					
					// ループ終了
					break;
					
				}
				
			}
			
		}
		
		// 国,都道府県,ISPを返す
		return array($country,$city);
		
	}
	
}

