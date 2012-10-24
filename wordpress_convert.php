<?php
/*
 * Copyright (C) 2012 NetLife Inc. All Rights Reserved.
 * http://www.netlife-web.com/
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/*
Plugin Name: Wordpress Converter for HTML Plugin
Description: This plugin is convert helper for HTML to Wordpress Template.
Version: 0.0.1
Author: Naohisa Minagawa
Author URI: http://www.netlife-web.com/
License: Apache License 2.0
Text Domain: wordpress_convert
*/

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

load_plugin_textdomain(WORDPRESS_CONVERT_PROJECT_CODE, false, WORDPRESS_CONVERT_PROJECT_CODE.'/languages');		

// このプラグインのルートディレクトリ
define("WORDPRESS_CONVERT_BASE_DIR", WP_PLUGIN_DIR."/".WORDPRESS_CONVERT_PROJECT_CODE);

// このプラグインのルートURL
define("WORDPRESS_CONVERT_BASE_URL", WP_PLUGIN_URL."/".WORDPRESS_CONVERT_PROJECT_CODE);

// メインクラス名
define("WORDPRESS_CONVERT_PLUGIN_NAME", __("Wordpress Convert Plugin", WORDPRESS_CONVERT_PROJECT_CODE));

// メインクラス名
define("WORDPRESS_CONVERT_SETTING_CLASSES", "Menu,General,Ftp");

// テンプレート取得クラス
define("WORDPRESS_CONVERT_CONTENT_MANAGER", "SecuredLocalContentManager");

// 使用カートリッジ
define("WORDPRESS_CONVERT_CARTRIDGES", "ConvertPath,ConvertArticle,ConvertWidget,ConvertWidgetParts");

require_once(dirname(__FILE__)."/classes/".WORDPRESS_CONVERT_MAIN_CLASS.".php");

// 初期化処理用のアクションを登録する。
add_action( 'init', array( WORDPRESS_CONVERT_MAIN_CLASS, "init" ) );

// 認証用URL
define("WORDPRESS_CONVERT_AUTH_BASEURL", get_option(WORDPRESS_CONVERT_PROJECT_CODE."_auth_baseurl"));

// テンプレート取得ベースディレクトリ
define("WORDPRESS_CONVERT_TEMPLATE_BASEDIR", get_option(WORDPRESS_CONVERT_PROJECT_CODE."_template_basedir"));

// テンプレート取得先サーバー
define("WORDPRESS_CONVERT_SERVER", get_option(WORDPRESS_CONVERT_PROJECT_CODE."_ftp_host"));

// 変換後テーマ名
define("WORDPRESS_CONVERT_THEME_NAME", get_option(WORDPRESS_CONVERT_PROJECT_CODE."_theme_code"));

// 初期化処理用のアクションを登録する。
add_action( 'admin_init', array( WORDPRESS_CONVERT_MAIN_CLASS, "execute" ) );

// インストール時の処理を登録
register_activation_hook( __FILE__, array( WORDPRESS_CONVERT_MAIN_CLASS, "install" ) );

// アンインストール時の処理を登録
register_deactivation_hook( __FILE__, array( WORDPRESS_CONVERT_MAIN_CLASS, "uninstall" ) );
?>