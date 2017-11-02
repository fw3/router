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
 * URLルーティングコネクタです。
 *
 * @category	Flywheel3
 * @package		router
 * @author		wakaba <wakabadou@gmail.com>
 * @license		http://opensource.org/licenses/MIT The MIT License MIT
 * @varsion		1.0.0
 */
class Connecter {
	use	traits\BasePropertyTrait;

	//==============================================
	// プロパティ
	//==============================================
	/** @var	string	リクエストURI */
	protected $path				= null;

	/** @var	string	タイプ */
	protected $type				= null;

	/** @var	array	コンフィグ */
	protected $config			= null;

	/** @var	string	ルーティングURI */
	protected $routingUri		= null;

	/** @var	array	パスパラメータ */
	protected $pathParameters	= null;

	/** @var	array	パス情報 */
	protected $pathInfo			= null;

	/** @var	array	CLIオプションパラメータ */
	protected $cliOptions		= null;

	/** @var	array	リクエストヘッダ */
	protected $header			= null;

	/** @var	array	ポストデータ */
	protected $post				= null;

	/** @var	array	リクエストパラメータ */
	protected $parameters		= null;

	/** @var	array	共通設定 */
	protected $commonConfigs	= null;

	/** @var	array	ルールベース設定 */
	protected $ruleBaseConfigs	= null;

	//==============================================
	// マジックメソッド
	//==============================================
	/**
	 * constructor
	 *
	 * @param	string	$path	リクエストURI
	 * @param	array	$config	設定
	 * @param	string	$type	コネクタタイプ
	 * @param	string	$domain	稼働ドメイン
	 * @param	string	$group	所属グループ
	 */
	protected function __construct ($path, $config, $type, $domain, $group, $middleware) {
		$this->path			= $path;
		$this->config		= $config;
		$this->type			= $type;
		$this->domain		= $domain;
		$this->group		= $group;
		$this->middleware	= $middleware;
	}

	//==============================================
	// スタティックメソッド
	//==============================================
	/**
	 * イニシャライザ
	 *
	 * @param	string			$path		リクエストURI
	 * @param	array			$config		設定
	 * @param	string			$type		コネクタタイプ
	 * @param	string			$domain		稼働ドメイン
	 * @param	string			$group		所属グループ
	 * @param	string|array	$middleware	適用ミドルウェア
	 * @return	\fw3\router\Connecter	このクラスのインスタンス
	 */
	public static function init ($path, $config, $type, $domain, $group, $middleware) {
		return new static($path, $config, $type, $domain, $group, $middleware);
	}

	//==============================================
	// メソッド
	//==============================================
	/**
	 * 自身のコピーを返します。
	 *
	 * @return	\fw3\router\Connecter	自身のコピー
	 */
	public function copy () {
		$instance = static::init(
			$this->path,
			$this->config,
			$this->type,
			$this->domain,
			$this->group,
			$this->middleware
		);
		$instance->routingUri		= $this->routingUri;
		$instance->pathParameters	= $this->pathParameters;
		$instance->pathInfo			= $this->pathInfo;
		$instance->cliOptions		= $this->cliOptions;
		$instance->header			= $this->header;
		$instance->post				= $this->post;
		$instance->parameters		= $this->parameters;
		$instance->commonConfigs	= $this->commonConfigs;
		$instance->ruleBaseConfigs	= $this->ruleBaseConfigs;

		return $instance;
	}

	/**
	 * リクエストURIを取得、設定します。
	 *
	 * @param	array	...$args	設定値
	 * @return	string|\fw3\router\Connecter	引数が無い場合は現在の値、ある場合は設定した後のこのクラスのインスタンス
	 */
	public function path (...$args) {
		if (empty($args)) {
			return $this->path;
		}
		$this->path = $args[0];
		return $this;
	}

	/**
	 * コンフィグを取得、設定します。
	 *
	 * @param	array	...$args	設定値
	 * @return	array|\fw3\router\Connecter	引数が無い場合は現在の値、ある場合は設定した後のこのクラスのインスタンス
	 */
	public function config (...$args) {
		if (empty($args)) {
			return $this->config;
		}
		$this->config = $args[0];
		return $this;
	}

	/**
	 * コネクタタイプを取得、設定します。
	 *
	 * @param	array	...$args	設定値
	 * @return	string|\fw3\router\Connecter	引数が無い場合は現在の値、ある場合は設定した後のこのクラスのインスタンス
	 */
	public function type (...$args) {
		if (empty($args)) {
			return $this->type;
		}
		$this->type = $args[0];
		return $this;
	}

	/**
	 * ルーティングURIを取得、設定します。
	 *
	 * @param	array	...$args	設定値
	 * @return	string|\fw3\router\Connecter	引数が無い場合は現在の値、ある場合は設定した後のこのクラスのインスタンス
	 */
	public function routingUri (...$args) {
		if (empty($args)) {
			return $this->routingUri;
		}
		$this->routingUri = $args[0];
		return $this;
	}

	/**
	 * パスパラメータを取得、設定します。
	 *
	 * @param	array	...$args	設定値
	 * @return	string|\fw3\router\Connecter	引数が無い場合は現在の値、ある場合は設定した後のこのクラスのインスタンス
	 */
	public function pathParameters (...$args) {
		if (empty($args)) {
			return $this->pathParameters;
		}
		$this->pathParameters = $args[0];
		return $this;
	}

	/**
	 * パス情報を取得、設定します。
	 *
	 * @param	array	...$args	設定値
	 * @return	string|\fw3\router\Connecter	引数が無い場合は現在の値、ある場合は設定した後のこのクラスのインスタンス
	 */
	public function pathInfo (...$args) {
		if (empty($args)) {
			return $this->pathInfo;
		}
		$this->pathInfo = $args[0];
		return $this;
	}

	/**
	 * CLIオプションパラメータを取得、設定します。
	 *
	 * @param	array	...$args	設定値
	 * @return	string|\fw3\router\Connecter	引数が無い場合は現在の値、ある場合は設定した後のこのクラスのインスタンス
	 */
	public function cliOptions (...$args) {
		if (empty($args)) {
			return $this->cliOptions;
		}
		$this->cliOptions = $args[0];
		return $this;
	}

	/**
	 * リクエストヘッダを取得、設定します。
	 *
	 * @param	array	...$args	設定値
	 * @return	string|\fw3\router\Connecter	引数が無い場合は現在の値、ある場合は設定した後のこのクラスのインスタンス
	 */
	public function header (...$args) {
		if (empty($args)) {
			return $this->header;
		}
		$this->header = $args[0];
		return $this;
	}

	/**
	 * ポストデータを取得、設定します。
	 *
	 * @param	array	...$args	設定値
	 * @return	string|\fw3\router\Connecter	引数が無い場合は現在の値、ある場合は設定した後のこのクラスのインスタンス
	 */
	public function post (...$args) {
		if (empty($args)) {
			return $this->post;
		}
		$this->post = $args[0];
		return $this;
	}

	/**
	 * リクエストパラメータを取得、設定します。
	 *
	 * @param	array	...$args	設定値
	 * @return	string|\fw3\router\Connecter	引数が無い場合は現在の値、ある場合は設定した後のこのクラスのインスタンス
	 */
	public function parameters (...$args) {
		if (empty($args)) {
			return $this->parameters;
		}
		$this->parameters = $args[0];
		return $this;
	}

	/**
	 * 共通設定を取得、設定します。
	 *
	 * @param	array	...$args	設定値
	 * @return	string|\fw3\router\Connecter	引数が無い場合は現在の値、ある場合は設定した後のこのクラスのインスタンス
	 */
	public function commonConfigs (...$args) {
		if (empty($args)) {
			return $this->commonConfigs;
		}
		$this->commonConfigs = $args[0];
		return $this;
	}

	/**
	 * ルールベース設定を取得、設定します。
	 *
	 * @param	array	...$args	設定値
	 * @return	string|\fw3\router\Connecter	引数が無い場合は現在の値、ある場合は設定した後のこのクラスのインスタンス
	 */
	public function ruleBaseConfigs (...$args) {
		if (empty($args)) {
			return $this->ruleBaseConfigs;
		}
		$this->ruleBaseConfigs = $args[0];
		return $this;
	}
}
