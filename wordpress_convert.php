<?php
/**
 * BiND for WordPress theme converter
 * 
 * Copyright (c) 2012 digitalstage inc. All Rights Reserved.
 * http://www.digitalstage.jp/
 * 
 * This work complements FLARToolkit, developed by Saqoosha as part of the Libspark project.
 *     http://www.libspark.org/wiki/saqoosha/FLARToolKit
 * FLARToolKit is Copyright (C)2008 Saqoosha,
 * and is ported from NyARToolKit, which is ported from ARToolKit.
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/*
Plugin Name: BiND for WordPress theme converter
Description: "BiND for WordPress theme converter" plug-in is template converter. It converts the WordPress template created by "BiND for WebLiFE 6" into WordPress Theme files.
Version: 1.0.2
Author: digitalstage inc.
Author URI: http://www.digitalstage.jp/
License: GPLv2
Text Domain: wordpress_convert
*/

define("WORDPRESS_CONVERT_VERSION", "1.0.2");

// メモリ使用制限を調整
ini_set('memory_limit', '128M');

class WordpressConvertPluginInfo{
	public static function getBaseDir(){
		return plugin_dir_path( __FILE__ );
	}

	public static function getBaseUrl(){
		return plugin_dir_url( __FILE__ );
	}
}

// プロジェクトコード
define("WORDPRESS_CONVERT_PROJECT_CODE", "wordpress_convert");

// メインクラス名
define("WORDPRESS_CONVERT_MAIN_CLASS", "WordpressConvert");

// デフォルト変換テーマ名
define("WORDPRESS_CONVERT_DEFAULT_NAME", "BiND6Theme");

// このプラグインのルートディレクトリ
define("WORDPRESS_CONVERT_BASE_DIR", realpath(dirname(__FILE__)));

// このプラグインのルートURL
define("WORDPRESS_CONVERT_BASE_URL", str_replace(WP_PLUGIN_DIR, WP_PLUGIN_URL, WORDPRESS_CONVERT_BASE_DIR));

// 言語設定を読み込み
load_plugin_textdomain(WORDPRESS_CONVERT_PROJECT_CODE, false, str_replace(WP_PLUGIN_DIR."/", "", WORDPRESS_CONVERT_BASE_DIR).'/languages');		

// メインクラス名
define("WORDPRESS_CONVERT_PLUGIN_SHORTNAME", __("WP Convert Plugin", WORDPRESS_CONVERT_PROJECT_CODE));

// メインクラス名
define("WORDPRESS_CONVERT_PLUGIN_NAME", __("Wordpress Convert Plugin", WORDPRESS_CONVERT_PROJECT_CODE));

// メインクラス名
define("WORDPRESS_CONVERT_SETTING_CLASSES", "Menu,Convert");
// define("WORDPRESS_CONVERT_SETTING_CLASSES", "Menu");

// テンプレート取得クラス
// define("WORDPRESS_CONVERT_CONTENT_MANAGER", "SecuredLocalContentManager");
define("WORDPRESS_CONVERT_CONTENT_MANAGER", "LocalContentManager");

// 使用カートリッジ
define("WORDPRESS_CONVERT_CARTRIDGES", "ConvertIgnore,ConvertPath,ConvertArticle,ConvertComment,ConvertWidget,ConvertWidgetParts");

require_once(dirname(__FILE__)."/classes/".WORDPRESS_CONVERT_MAIN_CLASS.".php");

// 初期化処理用のアクションを登録する。
add_action( 'init', array( WORDPRESS_CONVERT_MAIN_CLASS, "init" ) );

// 認証用URL
define("WORDPRESS_CONVERT_AUTH_BASEURL", get_option(WORDPRESS_CONVERT_PROJECT_CODE."_auth_baseurl"));

// テンプレート取得ベースディレクトリ
define("WORDPRESS_CONVERT_TEMPLATE_BASEDIR", get_option(WORDPRESS_CONVERT_PROJECT_CODE."_template_basedir", "sitedata"));

// テンプレート取得先サーバー
define("WORDPRESS_CONVERT_SERVER", get_option(WORDPRESS_CONVERT_PROJECT_CODE."_ftp_host"));

// 変換後テーマ名
define("WORDPRESS_CONVERT_THEME_NAME", get_option(WORDPRESS_CONVERT_PROJECT_CODE."_theme_code", "BiND for WordPress"));

// アーカイブタイトルのフィルタ用メソッド
add_filter( 'wp_title', array( WORDPRESS_CONVERT_MAIN_CLASS, 'wp_title' ), 1, 2);

// 表画面表示時の処理
add_action('template_redirect', array( WORDPRESS_CONVERT_MAIN_CLASS, "display" ));

// 初期化処理用のアクションを登録する。
add_action( 'admin_init', array( WORDPRESS_CONVERT_MAIN_CLASS, "execute" ) );

// 初期化処理用のアクションを登録する。
add_action( 'admin_head', array( WORDPRESS_CONVERT_MAIN_CLASS, "header" ) );

// 管理バーメニィーのアクションを登録する。
add_action( 'add_admin_bar_menus', array( "WordpressConvertSetting", 'adminBarMenu' ) );

// メール送信処理の初期化後のアクションを登録する。
add_action( 'phpmailer_init', array( WORDPRESS_CONVERT_MAIN_CLASS, "mailer_init" ) );

// インストール時の処理を登録
register_activation_hook( __FILE__, array( WORDPRESS_CONVERT_MAIN_CLASS, "install" ) );

// アンインストール時の処理を登録
register_deactivation_hook( __FILE__, array( WORDPRESS_CONVERT_MAIN_CLASS, "uninstall" ) );
