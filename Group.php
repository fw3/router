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
 * URLルーティンググループです。
 *
 * @category	Flywheel3
 * @package		router
 * @author		wakaba <wakabadou@gmail.com>
 * @license		http://opensource.org/licenses/MIT The MIT License MIT
 * @varsion		1.0.0
 */
class Group {
	use	traits\BasePropertyTrait;

	//==============================================
	// マジックメソッド
	//==============================================
	/**
	 * constructor
	 *
	 * @param	array	$current	設定
	 */
	protected function __construct ($current) {
		$this->method	= $current['method'] ?? null;
		$this->group	= $current['group'] ?? null;
		$this->domain	= $current['domain'] ?? null;
		$this->protocol	= $current['protocol'] ?? null;
	}

	//==============================================
	// スタティックメソッド
	//==============================================
	/**
	 * イニシャライザ
	 *
	 * @param	array	$current	設定
	 * @return	\fw3\router\Group	このクラスのインスタンス
	 */
	public static function init ($current) {
		return new static($current);
	}

	//==============================================
	// メソッド
	//==============================================
	/**
	 * 現在のグループ設定を反映した状態でURIを探索します。
	 *
	 * @param	string	$request_uri	リクエストURI
	 * @return	array	検索結果
	 */
	public function find ($request_uri = null) {
		return Router::find(
			$request_uri,
			[
				'method'	=> $this->method,
				'group'		=> $this->group,
				'domain'	=> $this->domain,
				'protocol'	=> $this->protocol,
			]
		);
	}
}
