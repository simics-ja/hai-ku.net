<?php

class Param_Daily
{
	
	//---------------------------------------------------------
	//  コントロール設定
	//---------------------------------------------------------
	
	static function control()
	{
		
		// コントロール設定を返す
		return array(true,true,'header','footer');
		
	}
	
	
	//---------------------------------------------------------
	//  日別統計表示
	//---------------------------------------------------------
	
	static function execute()
	{
		
		// グローバル変数を定義
		global $obj,$args,$path,$conf;
		
		// 汎用クラスインスタンスを取得
		$db   = $obj['db'];
		$tmpl = $obj['tmpl'];
		
		// スクリプト名を取得
		$scr_php = $path['scr_php'];
		
		// テーブル名プレフィックスを取得
		$prefix = $path['prefix'];
		
		// 年月日を取得
		$y = $args['y'];
		$m = $args['m'];
		$d = $args['d'];
		
		// DB名を定義
		$t_db = $prefix . '_' . $y . '_' . $m . '.db';
		
		// DBが存在しない時は終了
		if(!$db->exists($t_db)){return;}
		
		////////////////////////////////////////////////////////////
		
		// 詳細ログテーブル名を定義
		$d_table = $prefix . '_d_' . $y . '_' . $m;
		
		// 対象日付を定義
		$t_date = $y . '-' . $m . '-' . $d;
		
		// リンク用年月日を定義
		$ymd = $y . '/' . $m . '/' . $d;
		
		// グローバル変数に保持
		$path['d_table'] = $d_table;
		$path['t_date']  = $t_date;
		$path['ymd']     = $ymd;
		
		// 最大文字数の定数を設定
		define('max_len' ,80);
		
		////////////////////////////////////////////////////////////
		
		// DBに接続
		$db->attach($t_db,'t');
		
		// 対象パラメータを取得
		$param = (isset($args[1])) ? $args[1] : 'referrer';
		
		// 配列にセット
		$params = array($param);
		
		// パラメータを表示
		self::view($db,$tmpl,$params,'param_daily.htm','desc','',false);
		
		// DBと切断
		$db->close();
		
	}
	
	
	//---------------------------------------------------------
	//  View表示
	//---------------------------------------------------------
	
	static function view($db,$tmpl,$params,$htm,$desc,$limit,$index)
	{
		
		// グローバル変数を定義
		global $path,$title_ini;
		
		// ワークディレクトリを取得
		$work_dir = $path['work_dir'];
		
		// スクリプト名を取得
		$scr_php = $path['scr_php'];
		
		// テーブル名を取得
		$d_table = $path['d_table'];
		
		// 日付を取得
		$t_date  = $path['t_date'];
		$ymd     = $path['ymd'];
		
		// ジャンプ先定数を定義
		define('jump_php',$scr_php . '/jump/');
		
		// テンプレートを分割
		list($head,$main,$foot) = $tmpl->read($htm);
		
		// ループカウンタ
		$p = 1;
		
		// パラメータ表示ループ
		foreach($params as $param)
		{
			
			// 未定義のパラメータは何もしない
			if(!isset($title_ini[$param])){continue;}
			
			// 変数を初期化
			$replace = '';
			$column  = '';
			$where   = '';
			$from    = '';
			$slash   = '/';
			
			// テンプレート変数を初期化
			$v = array();
			
			// テンプレート変数をセット
			$v['param'] = $param;
			$v['title'] = $title_ini[$param];
			$v['table'] = ($p % 2 === 0) ? 'right_table' : 'left_table';
			
			// ヘッダーテンプレートを出力
			$tmpl->view($head,$v);
			
			// 置換関数があるときはセット
			if(method_exists('Param_Daily','replace_' . $param)){$replace = 'replace_' . $param;}
			
			////////////////////////////////////////////////////////////
			
			// リンク元統計の時
			if($param === 'referrer'){list($column,$where,$from) = self::referrer();}
			
			// UAの時
			elseif($param === 'ua'){$where = "and ua_type != 'Robot'";}
			
			// OS or 端末機種の時
			elseif($param === 'os' or $param === 'device'){self::$param();}
			
			// ページ統計の時
			elseif($param === 'page' or $param === 'click')
			{
				
				// 特殊表示
				self::page_view($db,$tmpl,$param,$main,$foot,$index);
				
				// カウンタをイクリメント
				$p++;
				
				// 次へ
				continue;
				
			}
			
			// 全項目の時
			elseif($param === 'all')
			{
				
				// 項目一覧を表示
				self::all_view($tmpl,$main,$foot);
				
				// 終了
				return;
				
			}
			
			////////////////////////////////////////////////////////////
			
			// SQLを定義
			$q = "select $param as name,count($param) as cnt $column from $d_table $from where a_date = '$t_date' and $param != '' $where group by $param order by cnt $desc $limit;";
			
			// SQLを実行
			$r = $db->query($q);
			
			// 簡易検索用リンクを定義
			$link = "$scr_php/detail_search/$param/$ymd";
			
			// ループカウンタ
			$i = 1;
			
			// 詳細ログを表示するループ
			while($v = $db->fetch($r))
			{
				
				// 簡易検索リンクを定義
				$v['cnt'] = '<a href="' . $link . '/' . $v['name'] . $slash . '">' . $v['cnt'] . '</a>';
				
				// 表示文字列を置換
				if($replace){$v['name'] = self::$replace($v);}
				
				// 色分け用class属性を定義
				$v['tr'] = ($i % 2 === 0) ? 2 : 1;
				
				// 順位を取得
				$v['i'] = $i;
				
				// メインテンプレートを出力
				$tmpl->view($main,$v);
				
				// カウンタを1増加
				$i++;
				
			}
			
			// フッターテンプレートを出力
			$tmpl->view($foot);
			
			// カウンタをイクリメント
			$p++;
			
		}
		
	}
	
	
	//---------------------------------------------------------
	//  リンク元
	//---------------------------------------------------------
	
	static function referrer()
	{
		
		// グローバル変数を定義
		global $path;
		
		// カラム名を定義
		$column = ",title,url";
		
		// where句を定義
		$where = " and referrer = no";
		
		// リンク元テーブル名を定義
		$from = ',' . $path['prefix'] . '_referrer';
		
		// リンク設定、SQL文を返す
		return array($column,$where,$from);
		
	}
	
	
	//---------------------------------------------------------
	//  OS
	//---------------------------------------------------------
	
	static function os()
	{
		
		// グローバル変数を定義
		global $path,$ini;
		
		// iniファイルをパース
		$ini = parse_ini_file($path['work_dir'] . '/templates/ini/os.ini');
		
	}
	
	
	//---------------------------------------------------------
	//  端末機種
	//---------------------------------------------------------
	
	static function device()
	{
		
		// グローバル変数を定義
		global $path,$ini;
		
		// iniファイルをパース
		$ini = parse_ini_file($path['work_dir'] . '/templates/ini/device.ini');
		
	}
	
	
	//---------------------------------------------------------
	//  表示名の置換：リンク元
	//---------------------------------------------------------
	
	static function replace_referrer($v)
	{
		
		// リンクを整形
		return '<a href="' . jump_php . $v['url'] . '" class="out">' . mb_strimwidth($v['title'],0,max_len,'...','UTF-8') . '</a>';
		
	}
	
	
	//---------------------------------------------------------
	//  表示名の置換：OS
	//---------------------------------------------------------
	
	static function replace_os($v)
	{
		
		// グローバル変数を定義
		global $ini;
		
		// 値を別名で保持
		$os = $v['name'];
		
		// 愛称が存在するときはUAに追記
		if(isset($ini[$os])){$os = $ini[$os];}
		
		// UAを返す
		return $os;
		
	}
	
	
	//---------------------------------------------------------
	//  表示名の置換：UA
	//---------------------------------------------------------
	
	static function replace_ua($v)
	{
		
		// 文字数を制限
		return mb_strimwidth($v['name'],0,max_len,'...','UTF-8');
		
	}
	
	
	//---------------------------------------------------------
	//  表示名の置換
	//---------------------------------------------------------
	
	static function replace_device($v)
	{
		
		// グローバル変数を定義
		global $ini;
		
		// 値を別名で保持
		$device = $v['name'];
		
		// 愛称が存在するときはUAに追記
		if(isset($ini[$device])){$device = $ini[$device] . ' (' . $device . ')';}
		
		// 端末機種を返す
		return $device;
		
	}
	
	//---------------------------------------------------------
	//  表示名の置換：訪問回数
	//---------------------------------------------------------
	
	static function replace_visit($v)
	{
		
		// 文字を整形
		return $v['name'] . '回目';
		
	}
	
	
	//---------------------------------------------------------
	//  表示名の置換：検索語
	//---------------------------------------------------------
	
	static function replace_search_words($v)
	{
		
		// リンクを整形
		return '<a href="' . jump_php . 'https://www.google.co.jp/' . '::' . preg_replace('/"/',"'",$v['name']) . '" class="out">' . mb_strimwidth($v['name'],0,max_len,'...','UTF-8') . '</a>';
		
	}
	
	
	//---------------------------------------------------------
	//  表示名の置換：ドメイン
	//---------------------------------------------------------
	
	static function replace_host_domain($v)
	{
		
		// unknownの時はそのまま返す
		if($v['name'] === 'unknown'){return $v['name'];}
		
		// リンクを整形
		return '<a href="' . jump_php . 'http://www.' . $v['name'] . '/" class="out">' . $v['name'] . '</a>';
		
	}
	
	
	//---------------------------------------------------------
	//  ページ統計
	//---------------------------------------------------------
	
	static function page_view($db,$tmpl,$param,$main,$foot,$index)
	{
		
		// グローバル変数を定義
		global $path,$conf;
		
		// スクリプト名を取得
		$scr_php = $path['scr_php'];
		
		// テーブル名プレフィックスを取得
		$prefix = $path['prefix'];
		
		// テーブル名を取得
		$d_table = $path['d_table'];
		
		// 日付を取得
		$t_date  = $path['t_date'];
		$ymd     = $path['ymd'];
		
		// indexページの時
		if($index)
		{
			
			// 表示上限を取得
			$limit = (int) $conf['index_param_limit'];
			
			// 並べ替えを取得
			$sort = (int) $conf['index__param_sort'];
			
		}
		
		// indexページではない時
		else
		{
			
			// 表示上限を設定
			$limit = 100;
			
			// 並べ替えを降順に設定
			$sort = 1;
			
		}
		
		// 変数を初期化
		$where  = '';
		$pns    = array();
		$urls   = array();
		$titles = array();
		
		// テーブル名を定義
		$n_table = $prefix . '_' . $param;
		
		// 簡易検索用リンクを定義
		$link = "$scr_php/detail_search/$param/$ymd";
		
		// 対象カラム名を定義
		$param .= '_route';
		
		// SQLを定義
		$q = "select no,url,title from $n_table;";
		
		// SQLを実行
		$r = $db->query($q);
		
		// SQLを定義
		$q = "select count(no) as cnt from $n_table;";
		
		// レコード数を取得
		$rows = $db->query_fetch($q,'cnt');
		
		// データが存在しない時
		if(!$rows){$tmpl->view($foot);return false;}
		
		// データレコード取得ループ
		while($a = $db->fetch($r))
		{
			
			// Noを取得
			$no = $a['no'];
			
			// リンクタイトルを配列にセット
			$titles[$no] = '<a href="' . jump_php . $a['url'] . '"  class="out">' . mb_strimwidth($a['title'],0,max_len,'...','UTF-8') . '</a>';
			
		}
		
		// SQLを定義
		$q = "select $param as param,count($param) as cnt from $d_table where a_date = '$t_date' and $param != '' group by $param;";
		
		// SQLを実行
		$r = $db->query($q);
		
		// 詳細ログを表示するループ
		while($a = $db->fetch($r))
		{
			
			// 別名で変数を保持
			$ns = explode('-',$a['param']);
			
			// カウント数を連想配列にセット
			foreach($ns as $n){$pns[$n] = (isset($pns[$n])) ? $pns[$n] + $a['cnt'] : $a['cnt'];}
			
		}
		
		// ループカウンタ
		$i = 1;
		
		// データが存在する時
		if($pns)
		{
			
			// 降順でソート
			if($sort === 1){arsort($pns);}
			
			// 昇順でソート
			else{asort($pns);}
			
		}
		
		// 表示ループ
		foreach($pns as $key => $val)
		{
			
			// タイトルが存在しない時は次へ
			if(!isset($titles[$key])){continue;}
			
			// 色分け用class属性を定義
			$v['tr'] = ($i % 2 === 0) ? 2 : 1;
			
			// 順位を取得
			$v['i'] = $i;
			
			// 項目名を定義
			$v['name'] = $titles[$key];
			
			// 簡易検索リンクを定義
			$v['cnt'] = '<a href="' . $link . '/' . $key . '/' . '">' . $val . '</a>';
			
			// メインテンプレートを出力
			$tmpl->view($main,$v);
			
			// 上限に達した時はループ終了
			if($i === $limit){break;}
			
			// カウンタを1増加
			$i++;
			
		}
		
		// フッターテンプレートを出力
		$tmpl->view($foot);
		
	}
	
	
	//---------------------------------------------------------
	//  その他統計項目一覧表示
	//---------------------------------------------------------
	
	static function all_view($tmpl,$main,$foot)
	{
		
		// グローバル変数を定義
		global $path,$title_ini;
		
		// スクリプト名を取得
		$scr_php = $path['scr_php'];
		
		// 日付リンクパスを取得
		$ymd = $path['ymd'];
		
		// 詳細ログパラメータ配列を定義
		$params = array
		(
			
			'referrer',
			'click',
			'page',
			'search_words',
			'visit',
			'os',
			'ua',
			'device',
			'client_size',
			'display_size',
			'host_domain',
			'carrier',
			'city',
			'ip_address',
			'remote_host'
			
		);
		
		// テンプレート変数を初期化
		$v['cnt'] = '';
		
		// ループカウンタ
		$i = 1;
		
		// 詳細ログを表示するループ
		foreach($params as $key)
		{
			
			// 表示項目名を取得
			$val = $title_ini[$key];
			
			// シーケンス番号を設定
			$v['i'] = $i;
			
			// 色分け用class属性を定義
			$v['tr'] = ($i % 2 === 0) ? 2 : 1;
			
			// リンクを定義
			$link = "$scr_php/param_daily/$key/$ymd/";
			
			// リンクを整形
			$v['name'] = '<a href="' . $link . '">' . $val . '</a>';
			
			// メインテンプレートを出力
			$tmpl->view($main,$v);
			
			// カウンタを1増加
			$i++;
			
		}
		
		// フッターテンプレートを出力
		$tmpl->view($foot);
		
	}
	
}

