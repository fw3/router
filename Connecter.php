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
	protected $path		= null;

	/** @var	string	タイプ */
	protected $type		= null;

	/** @var	array	コンフィグ */
	protected $config	= null;

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
	protected function __construct ($path, $config, $type, $domain, $group) {
		$this->path		= $path;
		$this->config	= $config;
		$this->type		= $type;
		$this->domain	= $domain;
		$this->group	= $group;
	}

	//==============================================
	// スタティックメソッド
	//==============================================
	/**
	 * イニシャライザ
	 *
	 * @param	string	$path	リクエストURI
	 * @param	array	$config	設定
	 * @param	string	$type	コネクタタイプ
	 * @param	string	$domain	稼働ドメイン
	 * @param	string	$group	所属グループ
	 * @return	\fw3\router\Connecter	このクラスのインスタンス
	 */
	public static function init ($path, $config, $type, $domain, $group) {
		return new static($path, $config, $type, $domain, $group);
	}

	//==============================================
	// メソッド
	//==============================================
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
}
