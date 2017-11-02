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

use fw3\router\Connecter;

/**
 *
 *
 * @category	Flywheel3
 * @package		router
 * @author		wakaba <wakabadou@gmail.com>
 * @license		http://opensource.org/licenses/MIT The MIT License MIT
 * @varsion		1.0.0
 */
trait RestAccessibleTrait {
	/**
	 * HTTP GET METHOD時に有効となるルーティングを設定します。
	 *
	 * @param	string	$path		リクエストURI
	 * @param	array	$configs	設定
	 * @return	\fw3\router\Connecter	コネクタ
	 */
	public static function get ($path, $configs = []) {
		$domain		= $configs['domain'] ?? static::$currentDomain ?? static::DEFAULT;
		$group		= (array) ($configs['group'] ?? static::$currentGroup ?? static::DEFAULT);
		$protocols	= (array) ($configs['protocol'] ?? static::$currentProtocol ?? $_SERVER['HTTP_PROTOCOL'] ?? static::getCurrentProtocol());
		$middleware	= $configs['middleware'] ?? static::$currentMiddleware ?? static::DEFAULT;

		$connecter	= Connecter::init($path, $configs, static::TYPE_GET, $domain, $group, $middleware);
		static::$connecterList[] = $connecter;

		end(static::$connecterList);
		$index = key(static::$connecterList);

		static::$methodConnecterMap[static::TYPE_GET][$index]	= $index;
		static::$domainConnecterMap[$domain][$index]			= $index;
		foreach ($protocols as $protocol) {
			static::$protocolConnecterMap[$protocol][$index]	= $index;
		}
		static::$groupConnecterMap = static::SetLowest(static::$groupConnecterMap, array_merge($group, [$index]), $index);

		return $connecter;
	}

	/**
	 * HTTP POST METHOD時に有効となるルーティングを設定します。
	 *
	 * @param	string	$path		リクエストURI
	 * @param	array	$configs	設定
	 * @return	\fw3\router\Connecter	コネクタ
	 */
	public static function post ($path, $configs = []) {
		$domain		= $configs['domain'] ?? static::$currentDomain ?? static::DEFAULT;
		$group		= (array) ($configs['group'] ?? static::$currentGroup ?? static::DEFAULT);
		$protocols	= (array) ($configs['protocol'] ?? static::$currentProtocol ?? $_SERVER['HTTP_PROTOCOL'] ?? static::getCurrentProtocol());
		$middleware	= $configs['middleware'] ?? static::$currentMiddleware ?? static::DEFAULT;

		$connecter	= Connecter::init($path, $configs, static::TYPE_POST, $domain, $group, $middleware);
		static::$connecterList[] = $connecter;

		end(static::$connecterList);
		$index = key(static::$connecterList);

		static::$methodConnecterMap[static::TYPE_POST][$index]	= $index;
		static::$domainConnecterMap[$domain][$index]			= $index;
		foreach ($protocols as $protocol) {
			static::$protocolConnecterMap[$protocol][$index]	= $index;
		}
		static::$groupConnecterMap = static::SetLowest(static::$groupConnecterMap, array_merge($group, [$index]), $index);

		return static::class;
	}

	/**
	 * HTTP PUT METHOD時に有効となるルーティングを設定します。
	 *
	 * @param	string	$path		リクエストURI
	 * @param	array	$configs	設定
	 * @return	\fw3\router\Connecter	コネクタ
	 */
	public static function put ($path, $configs = []) {
		$domain		= $configs['domain'] ?? static::$currentDomain ?? static::DEFAULT;
		$group		= (array) ($configs['group'] ?? static::$currentGroup ?? static::DEFAULT);
		$protocols	= (array) ($configs['protocol'] ?? static::$currentProtocol ?? $_SERVER['HTTP_PROTOCOL'] ?? static::getCurrentProtocol());
		$middleware	= $configs['middleware'] ?? static::$currentMiddleware ?? static::DEFAULT;

		$connecter	= Connecter::init($path, $configs, static::TYPE_PUT, $domain, $group, $middleware);
		static::$connecterList[] = $connecter;

		end(static::$connecterList);
		$index = key(static::$connecterList);

		static::$methodConnecterMap[static::TYPE_PUT][$index]	= $index;
		static::$domainConnecterMap[$domain][$index]			= $index;
		foreach ($protocols as $protocol) {
			static::$protocolConnecterMap[$protocol][$index]	= $index;
		}
		static::$groupConnecterMap = static::SetLowest(static::$groupConnecterMap, array_merge($group, [$index]), $index);

		return static::class;
	}

	/**
	 * HTTP PATCH METHOD時に有効となるルーティングを設定します。
	 *
	 * @param	string	$path		リクエストURI
	 * @param	array	$configs	設定
	 * @return	\fw3\router\Connecter	コネクタ
	 */
	public static function patch ($path, $configs = []) {
		$domain		= $configs['domain'] ?? static::$currentDomain ?? static::DEFAULT;
		$group		= (array) ($configs['group'] ?? static::$currentGroup ?? static::DEFAULT);
		$protocols	= (array) ($configs['protocol'] ?? static::$currentProtocol ?? $_SERVER['HTTP_PROTOCOL'] ?? static::getCurrentProtocol());
		$middleware	= $configs['middleware'] ?? static::$currentMiddleware ?? static::DEFAULT;

		$connecter	= Connecter::init($path, $configs, static::TYPE_PATCH, $domain, $group, $middleware);
		static::$connecterList[] = $connecter;

		end(static::$connecterList);
		$index = key(static::$connecterList);

		static::$methodConnecterMap[static::TYPE_PATCH][$index]	= $index;
		static::$domainConnecterMap[$domain][$index]			= $index;
		foreach ($protocols as $protocol) {
			static::$protocolConnecterMap[$protocol][$index]	= $index;
		}
		static::$groupConnecterMap = static::SetLowest(static::$groupConnecterMap, array_merge($group, [$index]), $index);

		return static::class;
	}

	/**
	 * HTTP DELETE METHOD時に有効となるルーティングを設定します。
	 *
	 * @param	string	$path		リクエストURI
	 * @param	array	$configs	設定
	 * @return	\fw3\router\Connecter	コネクタ
	 */
	public static function delete ($path, $configs = []) {
		$domain		= $configs['domain'] ?? static::$currentDomain ?? static::DEFAULT;
		$group		= (array) ($configs['group'] ?? static::$currentGroup ?? static::DEFAULT);
		$protocols	= (array) ($configs['protocol'] ?? static::$currentProtocol ?? $_SERVER['HTTP_PROTOCOL'] ?? static::getCurrentProtocol());
		$middleware	= $configs['middleware'] ?? static::$currentMiddleware ?? static::DEFAULT;

		$connecter	= Connecter::init($path, $configs, static::TYPE_DELETE, $domain, $group, $middleware);
		static::$connecterList[] = $connecter;

		end(static::$connecterList);
		$index = key(static::$connecterList);

		static::$methodConnecterMap[static::TYPE_DELETE][$index]	= $index;
		static::$domainConnecterMap[$domain][$index]			= $index;
		foreach ($protocols as $protocol) {
			static::$protocolConnecterMap[$protocol][$index]	= $index;
		}
		static::$groupConnecterMap = static::SetLowest(static::$groupConnecterMap, array_merge($group, [$index]), $index);

		return static::class;
	}

	/**
	 * HTTP OPTIONS METHOD時に有効となるルーティングを設定します。
	 *
	 * @param	string	$path		リクエストURI
	 * @param	array	$configs	設定
	 * @return	\fw3\router\Connecter	コネクタ
	 */
	public static function options ($path, $configs = []) {
		$domain		= $configs['domain'] ?? static::$currentDomain ?? static::DEFAULT;
		$group		= (array) ($configs['group'] ?? static::$currentGroup ?? static::DEFAULT);
		$protocols	= (array) ($configs['protocol'] ?? static::$currentProtocol ?? $_SERVER['HTTP_PROTOCOL'] ?? static::getCurrentProtocol());
		$middleware	= $configs['middleware'] ?? static::$currentMiddleware ?? static::DEFAULT;

		$connecter	= Connecter::init($path, $configs, static::TYPE_OPTIONS, $domain, $group, $middleware);
		static::$connecterList[] = $connecter;

		end(static::$connecterList);
		$index = key(static::$connecterList);

		static::$methodConnecterMap[static::TYPE_OPTIONS][$index]	= $index;
		static::$domainConnecterMap[$domain][$index]			= $index;
		foreach ($protocols as $protocol) {
			static::$protocolConnecterMap[$protocol][$index]	= $index;
		}
		static::$groupConnecterMap = static::SetLowest(static::$groupConnecterMap, array_merge($group, [$index]), $index);

		return static::class;
	}
}
