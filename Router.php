<?php
/** ______ _                _               _ ____
 * |  ____| |              | |             | |___ \
 * | |__  | |_   ___      _| |__   ___  ___| | __) |
 * |  __| | | | | \ \ /\ / / '_ \ / _ \/ _ \ ||__ <
 * | |    | | |_| |\ V  V /| | | |  __/  __/ |___) |
 * |_|    |_|\__, | \_/\_/ |_| |_|\___|\___|_|____/
 *            __/ |
 *           |___/
 *
 * Flywheel3: the inertia rad php framework
 *
 * @category	Flywheel3
 * @package		router
 * @author		wakaba <wakabadou@gmail.com>
 * @copyright	2011- Wakabadou honpo (http://www.wakabadou.net/) / Project ICKX (http://www.ickx.jp/)
 * @license		http://opensource.org/licenses/MIT The MIT License MIT
 * @varsion		1.0.0
 * ASCII Art by Text to ASCII Art Generator (TAAG): http://patorjk.com/software/taag/#p=display&f=Big&t=Flywheel3
 */

namespace fw3\router;

/**
 * @var	bool	現在のSAPIがCLIかどうか
 * @static
 * */
const IS_CLI		= \PHP_SAPI === 'cli';

/**
 * URLルーティング処理を行うクラスです。
 *
 * @category	Flywheel3
 * @package		router
 * @author		wakaba <wakabadou@gmail.com>
 * @license		http://opensource.org/licenses/MIT The MIT License MIT
 * @varsion		1.0.0
 */
abstract class Router {
	use	traits\RestAccessibleTrait;

	//==============================================
	// クラス定数
	//==============================================
	// デフォルト値
	//----------------------------------------------
	/**
	 * @var	string	デフォルト時に使う仮埋め文字列
	 * @static
	 * */
	public const DEFAULT		= ':default:';

	//----------------------------------------------
	// コネクタタイプ
	//----------------------------------------------
	/**
	 * @var	string	コネクタタイプ：HTTP GET
	 * @static
	 */
	public const TYPE_GET		= 'GET';

	/**
	 * @var	string	コネクタタイプ：HTTP POST
	 * @static
	 */
	public const TYPE_POST		= 'POST';

	/**
	 * @var	string	コネクタタイプ：HTTP PUT
	 * @static
	 */
	public const TYPE_PUT		= 'PUT';

	/**
	 * @var	string	コネクタタイプ：HTTP PPATCH
	 * @static
	 */
	public const TYPE_PATCH		= 'PATCH';

	/**
	 * @var	string	コネクタタイプ：HTTP DELETE
	 * @static
	 */
	public const TYPE_DELETE	= 'DELETE';

	/**
	 * @var	string	コネクタタイプ：HTTP OPTIONS
	 * @static
	 */
	public const TYPE_OPTIONS	= 'OPTIONS';

	/**
	 * @var	string	コネクタタイプ：HTTP REDIRECT
	 * @static
	 */
	public const TYPE_REDIRECT	= 'REDIRECT';

	/**
	 * @var	string	コネクタタイプ：VIEW
	 * @static
	 */
	public const TYPE_VIEW		= 'VIEW';

	/**
	 * @var	array	コネクタタイプ：グループ CONNECT
	 * @static
	 */
	public const TYPE_GROUP_CONNECT	= [
		self::TYPE_GET	=> self::TYPE_GET,
		self::TYPE_POST	=> self::TYPE_POST,
	];

	/**
	 * @var	array	コネクタタイプ：グループ HTTP ANY
	 * @static
	 */
	public const TYPE_GROUP_ANY	= [
		self::TYPE_GET		=> self::TYPE_GET,
		self::TYPE_POST		=> self::TYPE_POST,
		self::TYPE_PUT		=> self::TYPE_PUT,
		self::TYPE_PATCH	=> self::TYPE_PATCH,
		self::TYPE_DELETE	=> self::TYPE_DELETE,
		self::TYPE_OPTIONS	=> self::TYPE_OPTIONS,
	];

	//----------------------------------------------
	// 設定値
	//----------------------------------------------
	/**
	 * @var	string	エスケープ文字
	 * @static
	 */
	public const ESCAPE_CHAR	= "\\";

	/**
	 * @var	string	セパレータ
	 * @static
	 */
	public const SEPARATOR		= '/';

	/**
	 * @var	string	置換対象として許可する正規表現パターン
	 * @static
	 */
	public const PATH_PATTERN	= "/\{:([A-Za-z0-9_]*)(?::([A-Za-z0-9_\\\|\[\]\+\*\.\^\/\{\}]+))*\}/u";

	/**
	 * @var	string	置換対象変数名がない事を示す
	 * @static
	 */
	public const SKIP_FLAG		= '';

	//==============================================
	// クラス変数
	//==============================================
	// コネクタリスト
	//----------------------------------------------
	/** @staticvar	array	コネクタリスト */
	protected static $connecterList			= [];

	/** @staticvar	array	コネクタリスト：メソッドマップ */
	protected static $methodConnecterMap	= [];

	/** @staticvar	array	コネクタリスト：グループマップ */
	protected static $groupConnecterMap		= [];

	/** @staticvar	array	コネクタリスト：ドメインマップ */
	protected static $domainConnecterMap	= [];

	/** @staticvar	array	コネクタリスト：プロトコルマップ */
	protected static $protocolConnecterMap	= [];

	/** @staticvar	array	コネクタリスト：パスマップ */
	protected static $pathConnecterMap		= [];

	//----------------------------------------------
	// カレントデータ
	//----------------------------------------------
	/** @staticvar	string	カレントデータ：メソッド */
	protected static $currentMethod		= null;

	/** @staticvar	string	カレントデータ：グループ */
	protected static $currentGroup		= null;

	/** @staticvar	string	カレントデータ：ドメイン */
	protected static $currentDomain		= null;

	/** @staticvar	string	カレントデータ：プロトコル */
	protected static $currentProtocol	= null;

	/** @staticvar	string	カレントデータ：ミドルウェア */
	protected static $currentMiddleware	= null;

	//----------------------------------------------
	// on class cache
	//----------------------------------------------
	/** @staticvar	array	on class cache：解析済みリクエスト */
	protected static $parsedRequestInfo	= [];

	/** @staticvar	array	GetUrl用検索結果キャッシュ */
	protected static $reverseUrl	= [];

	/** @staticvar	array	共通オプション */
	protected static $commonOptions = [];

	/** @staticvar	array	ルールベースオプション */
	protected static $ruleBaseOptions = [];

	//----------------------------------------------
	// 設定
	//----------------------------------------------
	/** @staticvar	string	path cache path */
	protected static $connectPathCachePath	= '/tmp/fw3/default/path_chach';

	//==============================================
	// スタティックメソッド
	//==============================================
	// ルーティング設定
	//----------------------------------------------
	/**
	 * 任意のHTTP METHOD時に有効となるルーティングを設定します。
	 *
	 * @param	string	$path		リクエストURI
	 * @param	array	$configs	設定
	 * @return	\fw3\router\Connecter	コネクタ
	 */
	public static function match ($methods, $path, $configs = []) {
		$domain		= $configs['domain'] ?? static::$currentDomain ?? static::DEFAULT;
		$group		= (array) ($configs['group'] ?? static::$currentGroup ?? static::DEFAULT);
		$protocols	= (array) ($configs['protocol'] ?? static::$currentProtocol ?? $_SERVER['HTTP_PROTOCOL'] ?? static::getCurrentProtocol());
		$middleware	= $configs['middleware'] ?? static::$currentMiddleware ?? static::DEFAULT;

		$connecter	= Connecter::init($path, $configs, $methods, $domain, $group, $middleware);
		static::$connecterList[] = $connecter;

		end(static::$connecterList);
		$index = key(static::$connecterList);

		foreach ($methods as $method) {
			static::$methodConnecterMap[$method][$index]	= $index;
		}
		static::$domainConnecterMap[$domain][$index]			= $index;
		foreach ($protocols as $protocol) {
			static::$protocolConnecterMap[$protocol][$index]	= $index;
		}
		static::$groupConnecterMap = static::SetLowest(static::$groupConnecterMap, array_merge($group, [$index]), $index);

		return static::class;
	}

	/**
	 * 何れかのHTTP METHOD時に有効となるルーティングを設定します。
	 *
	 * @param	string	$path		リクエストURI
	 * @param	array	$configs	設定
	 * @return	\fw3\router\Connecter	コネクタ
	 */
	public static function any ($path, $configs = []) {
		$domain		= $configs['domain'] ?? static::$currentDomain ?? static::DEFAULT;
		$group		= (array) ($configs['group'] ?? static::$currentGroup ?? static::DEFAULT);
		$protocols	= (array) ($configs['protocol'] ?? static::$currentProtocol ?? $_SERVER['HTTP_PROTOCOL'] ?? static::getCurrentProtocol());
		$middleware	= $configs['middleware'] ?? static::$currentMiddleware ?? static::DEFAULT;

		$connecter	= Connecter::init($path, $configs, static::TYPE_GROUP_ANY, $domain, $group, $middleware);
		static::$connecterList[] = $connecter;

		end(static::$connecterList);
		$index = key(static::$connecterList);

		foreach (static::TYPE_GROUP_ANY as $method) {
			static::$methodConnecterMap[$method][$index]	= $index;
		}
		static::$domainConnecterMap[$domain][$index]			= $index;
		foreach ($protocols as $protocol) {
			static::$protocolConnecterMap[$protocol][$index]	= $index;
		}
		static::$groupConnecterMap = static::SetLowest(static::$groupConnecterMap, array_merge($group, [$index]), $index);

		return static::class;
	}

	/**
	 * 標準接続設定時に有効となるルーティングを設定します。
	 *
	 * @param	string	$path		リクエストURI
	 * @param	array	$configs	設定
	 * @return	\fw3\router\Connecter	コネクタ
	 */
	public static function connect ($path, $configs = []) {
		$domain		= $configs['domain'] ?? static::$currentDomain ?? static::DEFAULT;
		$group		= (array) ($configs['group'] ?? static::$currentGroup ?? static::DEFAULT);
		$protocols	= (array) ($configs['protocol'] ?? static::$currentProtocol ?? $_SERVER['HTTP_PROTOCOL'] ?? static::getCurrentProtocol());
		$middleware	= $configs['middleware'] ?? static::$currentMiddleware ?? static::DEFAULT;

		$connecter	= Connecter::init($path, $configs, static::TYPE_GROUP_CONNECT, $domain, $group, $middleware);
		static::$connecterList[] = $connecter;

		end(static::$connecterList);
		$index = key(static::$connecterList);

		foreach (static::TYPE_GROUP_CONNECT as $method) {
			static::$methodConnecterMap[$method][$index]	= $index;
		}
		static::$domainConnecterMap[$domain][$index]			= $index;
		foreach ($protocols as $protocol) {
			static::$protocolConnecterMap[$protocol][$index]	= $index;
		}
		static::$groupConnecterMap = static::SetLowest(static::$groupConnecterMap, array_merge($group, [$index]), $index);

		return static::class;
	}

	/**
	 * リダイレクト用のルーティングを設定します。
	 *
	 * @param	string	$path		リクエストURI
	 * @param	array	$configs	設定
	 * @return	\fw3\router\Connecter	コネクタ
	 */
	public static function redirect ($path, $configs = []) {
		$domain		= $configs['domain'] ?? static::$currentDomain ?? static::DEFAULT;
		$group		= (array) ($configs['group'] ?? static::$currentGroup ?? static::DEFAULT);
		$protocols	= (array) ($configs['protocol'] ?? static::$currentProtocol ?? $_SERVER['HTTP_PROTOCOL'] ?? static::getCurrentProtocol());
		$middleware	= $configs['middleware'] ?? static::$currentMiddleware ?? static::DEFAULT;

		$connecter	= Connecter::init($path, $configs, static::TYPE_GROUP_ANY, $domain, $group, $middleware);
		static::$connecterList[] = $connecter;

		end(static::$connecterList);
		$index = key(static::$connecterList);

		foreach (static::TYPE_GROUP_ANY as $method) {
			static::$methodConnecterMap[$method][$index]	= $index;
		}
		static::$domainConnecterMap[$domain][$index]			= $index;
		foreach ($protocols as $protocol) {
			static::$protocolConnecterMap[$protocol][$index]	= $index;
		}
		static::$groupConnecterMap = static::SetLowest(static::$groupConnecterMap, array_merge($group, [$index]), $index);

		return static::class;
	}

	/**
	 * パススルー用のルーティングを設定します。
	 *
	 * @param	string	$path		リクエストURI
	 * @param	array	$configs	設定
	 * @return	\fw3\router\Connecter	コネクタ
	 */
	public static function view ($path, $configs = []) {
		$domain		= $configs['domain'] ?? static::$currentDomain ?? static::DEFAULT;
		$group		= (array) ($configs['group'] ?? static::$currentGroup ?? static::DEFAULT);
		$protocols	= (array) ($configs['protocol'] ?? static::$currentProtocol ?? $_SERVER['HTTP_PROTOCOL'] ?? static::getCurrentProtocol());
		$middleware	= $configs['middleware'] ?? static::$currentMiddleware ?? static::DEFAULT;

		$connecter	= Connecter::init($path, $configs, static::TYPE_GROUP_ANY, $domain, $group, $middleware);
		static::$connecterList[] = $connecter;

		end(static::$connecterList);
		$index = key(static::$connecterList);

		foreach (static::TYPE_GROUP_ANY as $method) {
			static::$methodConnecterMap[$method][$index]	= $index;
		}
		static::$domainConnecterMap[$domain][$index]			= $index;
		foreach ($protocols as $protocol) {
			static::$protocolConnecterMap[$protocol][$index]	= $index;
		}
		static::$groupConnecterMap = static::SetLowest(static::$groupConnecterMap, array_merge($group, [$index]), $index);

		return static::class;
	}

	//----------------------------------------------
	// グループ設定
	//----------------------------------------------
	/**
	 * ルーティンググループを設定・取得します。
	 *
	 * @param	string|array	$group		ルーティンググループ名
	 * @param	callable		$callback	コールバック
	 * @return	array|string	$callbackの指定がない場合は、グループに紐づくコネクタ配列、そうでない場合は現在のクラス名
	 */
	public static function group ($group = self::DEFAULT, $callback = null) {
		if (is_null($callback)) {
			return Group::init([
				'method'		=> static::$currentMethod ?? $_SERVER['REQUEST_METHOD'],
				'group'			=> $group,
				'domain'		=> static::$currentDomain ?? static::DEFAULT,
				'protocol'		=> static::$currentProtocol ?? static::getCurrentProtocol(),
				'middleware'	=> static::$currentMiddleware ?? static::DEFAULT,
			]);
		}

		$base_group	= static::$currentGroup;
		static::$currentGroup = array_merge((array) static::$currentGroup, (array) $group);
		$callback();
		static::$currentGroup = $base_group;

		return static::class;
	}

	/**
	 * ドメインを設定・取得します。
	 *
	 * @param	string			$domain		ドメイン名
	 * @param	callable		$callback	コールバック
	 * @return	array|string	$callbackの指定がない場合は、グループに紐づくコネクタ配列、そうでない場合は現在のクラス名
	 */
	public static function domain ($domain= self::DEFAULT, $callback = null) {
		if (is_null($callback)) {
			return Group::init([
				'method'		=> static::$currentMethod ?? $_SERVER['REQUEST_METHOD'],
				'group'			=> static::$currentGroup ?? static::DEFAULT,
				'domain'		=> $domain,
				'protocol'		=> static::$currentProtocol ?? static::getCurrentProtocol(),
				'middleware'	=> static::$currentMiddleware ?? static::DEFAULT,
			]);
		}

		$base_domain= static::$currentDomain;
		static::$currentDomain = $domain;
		$callback();
		static::$currentDomain = $base_domain;

		return static::class;
	}

	/**
	 * プロトコルを設定・取得します。
	 *
	 * @param	string|array	$protocol	プロトコル
	 * @param	callable		$callback	コールバック
	 * @return	array|string	$callbackの指定がない場合は、グループに紐づくコネクタ配列、そうでない場合は現在のクラス名
	 */
	public static function protocol($protocol= null, $callback = null) {
		if (is_null($callback)) {
			return Group::init([
				'method'		=> static::$currentMethod ?? $_SERVER['REQUEST_METHOD'],
				'group'			=> static::$currentGroup ?? static::DEFAULT,
				'domain'		=> static::$currentDomain ?? static::DEFAULT,
				'protocol'		=> (array) ($protocol ?? static::getCurrentProtocol()),
				'middleware'	=> static::$currentMiddleware ?? static::DEFAULT,
			]);
		}

		$base_protocol= static::$currentProtocol;
		static::$currentProtocol = $protocol;
		$callback();
		static::$currentProtocol = $base_protocol;

		return static::class;
	}

	/**
	 * ミドルウェアを設定・取得します。
	 *
	 * @param	string|array	$middleware	ミドルウェアエイリアス または ミドルウェアクラスパス
	 * @param	callable		$callback	コールバック
	 * @return	array|string	$callbackの指定がない場合は、グループに紐づくコネクタ配列、そうでない場合は現在のクラス名
	 */
	public static function middleware ($middleware= null, $callback = null) {
		if (is_null($callback)) {
			return Group::init([
				'method'		=> static::$currentMethod ?? $_SERVER['REQUEST_METHOD'],
				'group'			=> static::$currentGroup ?? static::DEFAULT,
				'domain'		=> static::$currentDomain ?? static::DEFAULT,
				'protocol'		=> (array) ($protocol ?? static::getCurrentProtocol()),
				'middleware'	=> $middleware ?? static::$currentMiddleware ?? static::DEFAULT,
			]);
		}

		$base_middleware= static::$currentMiddleware;
		static::$currentMiddleware = $middleware;
		$callback();
		static::$currentMiddleware = $base_middleware;

		return static::class;
	}

	//----------------------------------------------
	// コネクタリスト取得
	//----------------------------------------------
	/**
	 * グループにマッチするコネクタリストを取得します。
	 *
	 * @param	string|array	$group	グループ
	 * @return	array			コネクタリスト
	 */
	public function findConnecterByGroup ($group) {
		return array_intersect_key(
			static::$connecterList,
			static::GetLowest(static::$groupConnecterMap, $group)
		);
	}

	/**
	 * ドメインにマッチするコネクタリストを取得します。
	 *
	 * @param	string	$domain	ドメイン
	 * @return	array	コネクタリスト
	 */
	public function findConnecterByDomain ($domain) {
		return array_intersect_key(
			static::$connecterList,
			static::$domainConnecterMap[$domain] ?? []
		);
	}

	/**
	 * メソッドにマッチするコネクタリストを取得します。
	 *
	 * @param	string	$method	メソッド
	 * @return	array	コネクタリスト
	 */
	public function findConnecterByMethod ($method) {
		return array_intersect_key(
			static::$connecterList,
			static::$methodConnecterMap[$method] ?? []
		);
	}

	/**
	 * プロトコルにマッチするコネクタリストを取得します。
	 *
	 * @param	string|array	$protocols	プロトコル
	 * @return	array	コネクタリスト
	 */
	public function findConnecterByProtocol ($protocols) {
		$connecter_list = static::$connecterList;
		foreach ((array) $protocols as $protocol) {
			$connecter_list = array_intersect_key(
				$connecter_list,
				static::$protocolConnecterMap[$protocol] ?? []
			);
		}
		$connecter_list;
	}

	//----------------------------------------------
	// コネクタリスト操作
	//----------------------------------------------
	/**
	 * 現在のルーティング設定を全て消去します。
	 */
	public static function clear () {
		static::$connecterList			= [];
		static::$methodConnecterMap		= [];
		static::$groupConnecterMap		= [];
		static::$domainConnecterMap		= [];
		static::$protocolConnecterMap	= [];
	}

	//----------------------------------------------
	// ユーティリティ
	//----------------------------------------------
	/**
	 * URIを探索します。
	 *
	 * @param	string	リクエストURI
	 * @param	array	$config	設定
	 * @return	array	検索結果
	 */
	public static function find ($request_uri = null, $config = []) {
		// Request URIの確定
		$path_info		= static::fetchCurrentPath($request_uri);
		$request_uri	= ltrim($path_info['path'] ?? '/', '/');

		// 現在のステータスの確定
		$method		= $config['method'] ?? $_SERVER['REQUEST_METHOD'] ?? static::$currentMethod;
		$group		= (array) ($config['group'] ?? static::$currentGroup ?? static::DEFAULT);
		$domain		= $config['domain'] ?? static::$currentDomain ?? static::DEFAULT;
		$protocols	= (array) ($config['protocol'] ?? static::$currentProtocol ?? static::getCurrentProtocol());

		$protocol	= array_shift($protocols);
		$protocol_map = static::$protocolConnecterMap[$protocol] ?? [];
		foreach ((array) $protocols as $protocol) {
			$protocol_map = array_intersect_key(
				$protocol_map,
				static::$protocolConnecterMap[$protocol] ?? []
			);
		}

		// 有効な検索対象の確定
		if (empty($connecter_list = array_intersect_key(
			static::$connecterList,
			static::$methodConnecterMap[$method] ?? [],
			static::GetLowest(static::$groupConnecterMap, $group),
			static::$domainConnecterMap[$domain] ?? [],
			$protocol_map
		))) {
			return false;
		}

		// キャッシュキーの構築
		$cache_key	= sprintf('%s<>%s<>%s<>%s', implode('~', $group), $domain, $protocol, $method);

		// コネクタパスキャッシュ
		$enable_apcu	= function_exists('apc_fetch');

		if ($enable_apcu) {
			static::$reverseUrl[':path:'] = apc_fetch('connecter_path_cache');
		} else {
			if (file_exists(static::$connectPathCachePath)) {
				static::$reverseUrl[':path:'] = include static::$connectPathCachePath;
			}
		}

		// 探索開始
		foreach ($connecter_list as $connecter) {
			$path = ltrim($connecter->path(), '/');

			// パターン解析
			if (!isset(static::$reverseUrl[':path:'][$cache_key][$path])) {
				static::$reverseUrl[':path:'][$cache_key][$path] = static::pursePathRegex($path);
			}
			[$regex_url, $path_parameter_name_list, $pattern_list] = static::$reverseUrl[':path:'][$cache_key][$path];

			// マッチング
			$match_pattern	= sprintf('@\A%s\z@u', str_replace('@', '\@', $regex_url));
			$match_pattern	= str_replace('\ud', '[1-9][0-9]*|0', $match_pattern);
			$match_pattern	= str_replace('\nn', '[1-9][0-9]*', $match_pattern);

			if (preg_match($match_pattern, $request_uri, $matches) !== 1) {
				continue;
			}

			// ルーティング対象の確定
			$result	= $connecter->copy();
			$result->method($method);
			$result->protocol(static::getCurrentProtocol());
			$result->routingUri(array_shift($matches));
			$result->pathInfo($path_info);
			$result->cliOptions(static::fetchCliOptions());
			$result->header(static::fetchHeader());
			$result->post(static::fetchPost());
			$result->parameters(static::fetchParameters());
			$result->commonConfigs(static::$commonOptions ?? []);
			$result->ruleBaseConfigs(static::$ruleBaseOptions ?? []);

			// パスパラメータの確定
			$path_parameter_list	= [];
			foreach ($path_parameter_name_list as $path_parameter_idx => $path_parameter_name) {
				if ($path_parameter_name === '') {
					unset($path_parameter_name_list[$path_parameter_idx]);
				} else {
					$path_parameter_list[$path_parameter_name]	= $matches[$path_parameter_idx];
				}
			}
			$result->pathParameters($path_parameter_list);

			if ($enable_apcu) {
				apc_add('connecter_path_cache', static::$reverseUrl[':path:'], 300);
			} else {
				if (file_exists($connect_path_cache_dir = dirname(static::$connectPathCachePath)) || mkdir($connect_path_cache_dir, 0775, true)) {
					file_put_contents(static::$connectPathCachePath, sprintf('<?php return %s;', var_export(static::$reverseUrl[':path:'], true)));
				}
			}
			return $result;
		}

		if ($enable_apcu) {
			apc_add('connecter_path_cache', static::$reverseUrl[':path:'], 300);
		} else {
			if (file_exists($connect_path_cache_dir = dirname(static::$connectPathCachePath)) || mkdir($connect_path_cache_dir, 0775, true)) {
				file_put_contents(static::$connectPathCachePath, sprintf('<?php return %s;', var_export(static::$reverseUrl[':path:'], true)));
			}
		}
		return false;
	}

	/**
	 * パスから正規表現パートを抽出します。
	 *
	 * @param	string	$path		解析するパス
	 * @param	array	抽出した正規表現パート
	 */
	public static function pursePathRegex ($path) {
		//チャンク
		$chunk = str_split($path);
		$chunk_length = count($chunk);

		//エスケープレベル
		$escape_lv = 0;

		//スタッカ
		$stack = [];

		//パターンスタッカ
		$pattern_list = [];

		//パラメータスタッカ
		$path_parameter_list = [];

		//一括精査
		for ($i = 0;$i < $chunk_length;$i++) {
			$char = $chunk[$i];

			//エスケープ文字の積み上げ
			if ("\\" === $char) {
				$escape_lv++;
				$stack[] = $char;
				continue;
			}

			//エスケープされているかどうか見ながら処理
			if (($escape_lv % 2 === 0) && '{' === $char && ':' === $chunk[$i + 1]) {
				//ブレスレベル
				$breath_lv = 1;

				//エスケープレベルのリセット
				$escape_lv = 0;

				//パラメータ名
				$parameter_name = '';

				//パターン
				$pattern = '';

				//inner stack
				$inner_stack = [];

				//現在のモード
				$mode = 'parameter';

				//処理対象パート
				for ($i += 2;$i < $chunk_length;$i++) {
					$char = $chunk[$i];

					//パラメータモード
					if ($mode === 'parameter') {
						//パラメータ区切り文字が出るまで積み上げ
						if (($escape_lv % 2 === 0) && $char === ':') {
							$mode = 'pattern';

							//パラメータ名の抽出
							$parameter_name = implode('', $inner_stack);
							$inner_stack = [];
							continue;
						}
					}

					//エスケープ文字の積み上げ
					if ("\\" === $char) {
						$escape_lv++;
						$inner_stack[] = $char;
						continue;
					}

					//エスケープされていないbreathがあった場合積み上げ
					if (($escape_lv % 2 === 0) && '{' === $char) {
						$inner_stack[] = $char;
						$breath_lv++;
						continue;
					}

					//エスケープされていないbreathがあった場合積み下げ
					if (($escape_lv % 2 === 0) && '}' === $char) {
						$breath_lv--;
						//breathが0段になった時点で終了
						if ($breath_lv === 0) {
							break;
						}
						$inner_stack[] = $char;
						continue;
					}

					//エスケープは終わりました
					$escape_lv = 0;

					//インナースタックに積み上げ
					$inner_stack[] = $char;
				}

				//パターンの抽出
				$pattern = implode('', $inner_stack);
				$pattern = ($pattern ?: '[^/]*');

				//スタックに詰める
				$stack[] = '('. $pattern .')';

				//parameter名のリスト
				$path_parameter_list[] = $parameter_name;

				//パターンのリスト
				$pattern = str_replace('\ud', '[1-9][0-9]*|0', $pattern);
				$pattern = str_replace('\nn', '[1-9][0-9]*', $pattern);
				$pattern_list[] = '('. $pattern .')';

				//繰り返しもどし
				continue;
			}

			//文字が変わっているのでエスケープレベルを0に
			$escape_lv = 0;

			//スタックの積み上げ
			$stack[] = $char;
		}

		// サブパターンが存在する場合再帰確認を行う
		$path = implode('', $stack);
		if (false !== mb_strpos($path, '{:')) {
			$before_length	= mb_strlen($path);
			do {
				$sub_result		= static::pursePathRegex($path, 1);
				$path			= $sub_result[0];
				$path_parameter_list	= array_merge($path_parameter_list, $sub_result[1]);
				$pattern_list	= array_merge($pattern_list, $sub_result[2]);
			} while ($before_length !== $before_length = $current_length);
		}

		//処理の終了
		return [$path, $path_parameter_list, $pattern_list];
	}

	/**
	 * 現在接続中のプロトコルを返します。
	 *
	 * @return	string	プロトコル
	 */
	public static function getCurrentProtocol () {
		if (namespace\IS_CLI) {
			return 'cli';
		}
		$https_flag = (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
		return $https_flag ? 'https' : 'http';
	}

	/**
	 * 現在のリクエストパス情報を返します。
	 *
	 * このメソッドの実行結果は同一リエスト内においてキャッシュされます。
	 *
	 * @param string $path
	 * @return array
	 */
	public static function fetchCurrentPath ($request_uri = null) {

		$query_string = '';

		if (is_null($request_uri)) {
			if (isset(static::$parsedRequestInfo[__FUNCTION__])) {
				return static::$parsedRequestInfo[__FUNCTION__];
			}

			if (namespace\IS_CLI) {
				$request_uri	= $_SERVER['argv'][1] ?? '/';
				if (substr($request_uri, 0, 1) !== '/') {
					$request_uri = '/';
				}
				$request_uri	= '/' . ltrim($request_uri, '/');
				$question_pos	= strpos($request_uri, '?');
				$query_string	= false === $question_pos ? '' : substr($request_uri, $question_pos + 1);
			} else {
				$request_uri	= $_SERVER['REQUEST_URI'];
				$query_string	= $_SERVER['QUERY_STRING'];
			}
		}

		if (0 < $query_string_length = strlen($query_string)) {
			$request_uri = substr($request_uri, 0, -$query_string_length);
		}

		$result = [
			'path'			=> rtrim($request_uri, '?'),
			'query_string'	=> $query_string,
		];

		if (is_null($request_uri)) {
			static::$parsedRequestInfo[__FUNCTION__] = $result;
		}

		return $result;
	}

	/**
	 * CLIのオプション引数を取得します。
	 *
	 * このメソッドの実行結果は同一リエスト内においてキャッシュされます。
	 *
	 * @return array
	 */
	public static function fetchCliRawOptions () {
		if (isset(static::$parsedRequestInfo[__FUNCTION__])) {
			return static::$parsedRequestInfo[__FUNCTION__];
		}
		$start_idx	= substr($_SERVER['argv'][1] ?? '/', 0, 1) !== '/' ? 1 : 2;
		return static::$parsedRequestInfo[__FUNCTION__] = isset($_SERVER['argv'][$start_idx]) ? array_slice($_SERVER['argv'], $start_idx): [];
	}

	/**
	 * ハッシュマップ化したCLIオプション引数を取得します。
	 *
	 * このメソッドの実行結果は同一リエスト内においてキャッシュされます。
	 *
	 * @return array
	 */
	public static function fetchCliOptions () {
		if (isset(static::$parsedRequestInfo[__FUNCTION__])) {
			return static::$parsedRequestInfo[__FUNCTION__];
		}

		$cli_raw_options	= static::fetchCliRawOptions();
		$target_keys		= array_keys($cli_raw_options);

		$options			= [];

		$name	= null;

		for ($idx = 0;!is_null($key = $target_keys[$idx] ?? null);++$idx) {
			if (substr($cli_raw_options[$key], 0, 1) === '-') {
				if (substr($cli_raw_options[$key], 0, 2) !== '-') {
					if (!is_null($name) && !isset($options[$name])) {
						$options[$name] = true;
					}
					$name = ltrim($cli_raw_options[$key], '-');
					continue;
				} else if (substr($cli_raw_options[$key], 1, 2) === '-' && substr($cli_raw_options[$key], 0, 3) !== '-') {
					if (!is_null($name) && !isset($options[$name])) {
						$options[$name] = true;
					}
					$name = ltrim($cli_raw_options[$key], '-');
					continue;
				}
			}

			$value	= $cli_raw_options[$key];
			if (!is_null($name)) {
				if (isset($options[$name])) {
					if (is_array($options[$name])) {
						$options[$name][] = $value;
					} else {
						$options[$name] = [$options[$name], $value];
					}
				} else {
					$options[$name]	= $value;
				}
				$name = null;
			} else {
				$name = $value;
			}
		}

		return static::$parsedRequestInfo[__FUNCTION__] = $options;
	}

	/**
	 * HTTPリクエストヘッダを取得します。
	 *
	 * このメソッドの実行結果は同一リエスト内においてキャッシュされます。
	 *
	 * @return array
	 */
	public static function fetchHeader () {
		if (isset(static::$parsedRequestInfo[__FUNCTION__])) {
			return static::$parsedRequestInfo[__FUNCTION__];
		}

		if (namespace\IS_CLI) {
			$options = static::fetchCliOptions();
			$header = array_merge((array) ($options['haeder'] ?? []), (array) ($options['h'] ?? []));
			return static::$parsedRequestInfo[__FUNCTION__] = $header;
		}

		$headers	= [];
		foreach ($_SERVER as $key => $value) {
			if (substr($key, 0, 5) === 'HTTP_') {
				$key = substr($key, 5);
				$headers[] = str_replace('_', '-', ucwords(strtolower($key), '_')) . ': '. $value;
			} else if (substr($key, 0, 2) === 'X-') {
				$headers[] = $key. ': '. $value;
			}
		}

		return static::$parsedRequestInfo[__FUNCTION__] = $headers;
	}

	/**
	 * HTTPポストデータを取得します。
	 *
	 * このメソッドの実行結果は同一リエスト内においてキャッシュされます。
	 *
	 * @return array
	 */
	public static function fetchPost () {
		if (isset(static::$parsedRequestInfo[__FUNCTION__])) {
			return static::$parsedRequestInfo[__FUNCTION__];
		}

		if (namespace\IS_CLI) {
			$options = static::fetchCliOptions();
			parse_str(implode('&', array_merge((array) ($options['data'] ?? []), (array) ($options['d'] ?? []))), $post);
			return static::$parsedRequestInfo[__FUNCTION__] = $post;
		}
		return static::$parsedRequestInfo[__FUNCTION__] = $_POST;
	}

	/**
	 * HTTPゲットパラメータを取得します。
	 *
	 * このメソッドの実行結果は同一リエスト内においてキャッシュされます。
	 *
	 * @return array
	 */
	public static function fetchParameters ($path_info = null) {
		if (is_null($path_info) && isset(static::$parsedRequestInfo[__FUNCTION__])) {
			return static::$parsedRequestInfo[__FUNCTION__];
		}

		if (namespace\IS_CLI) {
			$query_string = ($path_info ?? static::fetchCurrentPath())['query_string'] ?? '';
			parse_str($query_string, $get);
			return static::$parsedRequestInfo[__FUNCTION__] = $get;
		}
		return static::$parsedRequestInfo[__FUNCTION__] = $_GET;
	}

	/**
	 * 指定された階層にある値を設定します。
	 *
	 * @param	array	$array	配列
	 * @param	mixed	$keys	階層
	 * @return	array	設定後の配列
	 */
	protected static function SetLowest ($array, $keys, $value) {
		$keys = (array) $keys;
		if (empty($array)) {
			$tmp =& $array;
		} else {
			$tmp =& $array[array_shift($keys)];
		}

		foreach ($keys as $key) {
			$tmp = (array) $tmp;
			if (!isset($tmp[$key])) {
				$tmp[$key] = null;
			}
			$tmp =& $tmp[$key];
		}
		$tmp = $value;
		unset($tmp);
		return $array;
	}

	/**
	 * 指定された階層にある値を取得します。
	 *
	 * @param	array	$array	配列
	 * @param	mixed	$keys	階層
	 * @return	mixed	指定された改造にある値
	 */
	protected static function GetLowest ($array, $keys) {
		foreach ((array) $keys as $key) {
			if (isset($array[$key]) || array_key_exists($key, $array)) {
				$array = $array[$key];
			} else {
				return [];
			}
		}
		return $array;
	}

	/**
	 * 共通で使用するオプションを登録します。
	 *
	 * @param	array	$options	共通設定オプション
	 */
	public static function SetCommonOptions ($options) {
		static::$commonOptions = $options;
	}

	/**
	 * 共通で使用するオプションを登録します。
	 *
	 * @param	array	$options	共通設定オプション
	 */
	public static function SetRuleBaseOption ($rule, $options) {
		static::$ruleBaseOptions[$rule] = $options;
	}

	/**
	 * コントーラ名、アクション名、パラメータからURLを構築します。
	 *
	 * コントローラ名、アクション名、パラメータが完全に一致する接続パスからURLを構築します。
	 * その際、パラメータの値も見ます。
	 *
	 * GetUrl実行時には値を確定出来ないパラメータ名は$var_parametersに指定してください。
	 *
	 * ex)
	 * Router::Connect('/{:controller:index}/{:action:index}/{id:\d+}/}');
	 *
	 * Router::GetUrl('index', 'index');					// => false, パラメータなしのURLが接続パスが設定されていない
	 * Router::GetUrl('index', 'index', ['id' => 'aaa']);	// => false, idパラメータが\d+にマッチしない
	 * Router::GetUrl('index', 'index', ['id' => '123']);	// => /index/index/123/
	 * Router::GetUrl('index', 'index', [], ['id']);		// => /index/index/{:id}/
	 *
	 * @param	string	$controller_name	コントローラ名
	 * @param	string	$action_name		アクション名
	 * @param	array	$parameters			パラメータ
	 * @param	array	$var_parameters		後付けで差し替えたいパラメータ
	 * @return	mixed	接続パスに存在するURLの場合はstring URL、存在しないURLの場合はbool false
	 */
	public static function GetUrl ($controller_name = null, $action_name = null, $parameters = [], $var_parameters = []) {
		//未指定の場合はindexとみなす
		$controller_name	= $controller_name ?: 'index';
		$action_name		= $action_name ?: 'index';

		//コントローラとアクションは検索対象から外す
		$default_omit_parameters = [
			'controller',
			'action',
		];

		//簡易突合用のパラメータ名リストを作る
		$parameters = !is_array($parameters) ? [] : $parameters;
		$parameter_name_list = array_merge(array_keys($parameters), $var_parameters);
		sort($parameter_name_list);

		//検索結果キャッシュ用にserializeする
		$parameter_name_list_hath = hash('sha256', serialize($parameter_name_list));

		//極力キャッシュから引くようにする
		$is_cache = false;
		if (isset(static::$reverseUrl[$controller_name][$action_name][$parameter_name_list_hath])) {
			list($controller_pattern, $action_pattern, $matching_set, $connection) = static::$reverseUrl[$controller_name][$action_name][$parameter_name_list_hath];
			$is_cache = true;
			$connection_list	= [$connection];
		} else {
			if (static::enableCache()) {
				static::$connecterList = static::getConnectCache();
			}
			$connection_list = static::$connecterList;
		}

		//共通設定オプションの有無の取得
		$enable_common_options = !empty(static::$commonOptions);

		//Connection単位で処理
		foreach ($connection_list as $connection) {
			//コネクションパスからデータ抽出
			$path = ltrim($connection['path'], '/');

			$path = str_replace('\ud', '[1-9][0-9]*|0', $path);
			$path = str_replace('\nn', '[1-9][0-9]*', $path);

			if (!isset(static::$reverseUrl[':path:'][$path])) {
	 			static::$reverseUrl[':path:'][$path] = static::pursePathRegex($path);
			}
			list($regex_url, $path_parameter_list, $pattern_list) = static::$reverseUrl[':path:'][$path];

			if (!$is_cache) {
				$matching_set = array_combine($path_parameter_list, $pattern_list);

				//optionsを先に取得しておく
				$options = isset($connection['options']) ? $connection['options'] : null;
				if ($enable_common_options && $options !== null) {
					$options = array_merge(static::$commonOptions, $options);
				}

				//コントローラパターンの抽出
				$controller_pattern = isset($options['controller']) ? $options['controller'] : null;
				$controller_pattern = $controller_pattern ?: (isset($options[0]) ? $options[0] : null);
				$controller_pattern = $controller_pattern ?: (isset($matching_set['controller']) ? $matching_set['controller'] : 'index');
				$controller_pattern = sprintf('@^%s$@u', str_replace('@', '\@', $controller_pattern));

				//アクションパターンの抽出
				$action_pattern = isset($options['action']) ? $options['action'] : null;
				$action_pattern = $action_pattern ?: (isset($options[1]) ? $options[1] : null);
				$action_pattern = $action_pattern ?: (isset($matching_set['action']) ? $matching_set['action'] : 'index');
				$action_pattern = sprintf('@^%s$@u', str_replace('@', '\@', $action_pattern));

				//コントローラ、アクションがマッチするconnectionのみ処理する
				if (preg_match($controller_pattern, $controller_name) === 1 && preg_match($action_pattern, $action_name) === 1) {
					//除外対象のパラメータを除去
					foreach ($default_omit_parameters as $omit_parameter) {
						if (isset($matching_set[$omit_parameter])) {
							unset($matching_set[$omit_parameter]);
						}
					}

					//簡易突合用にキーを取り出してソート
					$matching_parameter_name_list = array_keys($matching_set);
					sort($matching_parameter_name_list);

					//初回検証
					if ($parameter_name_list !== $matching_parameter_name_list) {
						continue;
					}
				} else {
					continue;
				}
			}

			//詳細検証：値は初回検証で必ずあると判断されている
			foreach ($matching_set as $name => $pattern) {
				if (in_array($name, $var_parameters, true)) {
					continue;
				}

				//一つでも不適合があれば終了
				$param_value = $parameters[$name];
				if (is_object($parameters[$name]) && is_callable($parameters[$name])) {
					$param_value = $parameters[$name]();
				}
				if (preg_match(sprintf('@^%s$@u', str_replace('@', '\@', $pattern)), $param_value) !== 1) {
					continue 2;
				}
			}

			//URL構築
			//controllerとactionを付与
			$parameters['controller']	= $controller_name;
			$parameters['action']		= $action_name;

			//オミットセットは交換可能にする
			foreach ($var_parameters as $var_parameter) {
				$parameters[$var_parameter] = sprintf('{:%s}', $var_parameter);
			}

			//改めてマッチングセット構築
			$work_matching_set = array_combine($path_parameter_list, $pattern_list);

			$url = $regex_url;
			foreach ($work_matching_set as $parameter_name => $pattern) {
				$url = preg_replace(sprintf('@%s@u', str_replace('@', '\@', preg_quote($pattern))), $parameters[$parameter_name], $url, 1);
			}

			//キャッシュ追加
			if (!isset(static::$reverseUrl[$controller_name][$action_name][$parameter_name_list_hath])) {
				static::$reverseUrl[$controller_name][$action_name][$parameter_name_list_hath] = [$controller_pattern, $action_pattern, $matching_set, $connection];
			}

			//マッチするURLがあった場合
			return '/' . $url;
		}

		//マッチするURLがない場合
		return false;
	}
}
