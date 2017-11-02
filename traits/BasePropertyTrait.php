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

namespace fw3\router\traits;

/**
 * ルーティング所属グループおよびルーティングコネクタ向けの基本プロパティ特性です。
 *
 * @category	Flywheel3
 * @package		router
 * @author		wakaba <wakabadou@gmail.com>
 * @license		http://opensource.org/licenses/MIT The MIT License MIT
 * @varsion		1.0.0
 */
trait BasePropertyTrait{
	//==============================================
	// プロパティ
	//==============================================
	/** @var	string	対象メソッド */
	protected $method	= null;

	/** @var	string	所属グループ */
	protected $group	= null;

	/** @var	string	稼働ドメイン */
	protected $domain	= null;

	/** @var	string	対象プロトコル */
	protected $protocol	= null;

	/** @var	array	適用ミドルウェア */
	protected $middleware	= null;

	//==============================================
	// メソッド
	//==============================================
	/**
	 * 対象メソッドを取得、設定します。
	 *
	 * @param	array	...$args	設定値
	 * @return	string|\fw3\router\Group|\fw3\router\Connecter	引数が無い場合は現在の値、ある場合は設定した後のこのクラスのインスタンス
	 */
	public function method (...$args) {
		if (empty($args)) {
			return $this->method;
		}
		$this->method = $args[0];
		return $this;
	}

	/**
	 * 所属グループを取得、設定します。
	 *
	 * @param	array	...$args	設定値
	 * @return	string|\fw3\router\Group|\fw3\router\Connecter	引数が無い場合は現在の値、ある場合は設定した後のこのクラスのインスタンス
	 */
	public function group (...$args) {
		if (empty($args)) {
			return $this->group;
		}
		$this->group = $args[0];
		return $this;
	}

	/**
	 * 稼働ドメインを取得、設定します。
	 *
	 * @param	array	...$args	設定値
	 * @return	string|\fw3\router\Group|\fw3\router\Connecter	引数が無い場合は現在の値、ある場合は設定した後のこのクラスのインスタンス
	 */
	public function domain (...$args) {
		if (empty($args)) {
			return $this->domain;
		}
		$this->domain = $args[0];
		return $this;
	}

	/**
	 * 対象プロトコルを取得、設定します。
	 *
	 * @param	array	...$args	設定値
	 * @return	string|\fw3\router\Group|\fw3\router\Connecter	引数が無い場合は現在の値、ある場合は設定した後のこのクラスのインスタンス
	 */
	public function protocol (...$args) {
		if (empty($args)) {
			return $this->protocol;
		}
		$this->protocol = $args[0];
		return $this;
	}

	/**
	 * 適用するミドルウェアを取得、設定します。
	 *
	 * @param	array	...$args	設定値
	 * @return	string|\fw3\router\Group|\fw3\router\Connecter	引数が無い場合は現在の値、ある場合は設定した後のこのクラスのインスタンス
	 */
	public function middleware (...$args) {
		if (empty($args)) {
			return $this->middleware;
		}
		$this->middleware = $args[0];
		return $this;
	}
}
