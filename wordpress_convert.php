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
*/

// メモリ使用制限を調整
ini_set('memory_limit', '128M');

// メインクラス名
define("WORDPRESS_CONVERT_PLUGIN_NAME", __("Wordpress Convert Plugin"));

// メインクラス名
define("WORDPRESS_CONVERT_MAIN_CLASS", "WordpressConvert");

// メインクラス名
define("WORDPRESS_CONVERT_SETTING_CLASS", "WordpressConvertSetting");

// テンプレート取得クラス
define("WORDPRESS_CONVERT_CONTENT_MANAGER", "LocalContentManager");

// テンプレート取得先サーバー
define("WORDPRESS_CONVERT_SERVER", "/tmp");

// 変換後テーマ名
define("WORDPRESS_CONVERT_THEME_NAME", "ConvertedTheme");

// 使用カートリッジ
define("WORDPRESS_CONVERT_CARTRIDGES", "ConvertPath,ConvertArticle");

require_once(dirname(__FILE__)."/classes/".WORDPRESS_CONVERT_MAIN_CLASS.".php");

// 初期化処理用のアクションを登録する。
add_action( 'init', array( WORDPRESS_CONVERT_MAIN_CLASS, "init" ) );

// 初期化処理用のアクションを登録する。
add_action( 'admin_init', array( WORDPRESS_CONVERT_MAIN_CLASS, "execute" ) );

// インストール時の処理を登録
register_activation_hook( __FILE__, array( WORDPRESS_CONVERT_MAIN_CLASS, "install" ) );

// アンインストール時の処理を登録
register_deactivation_hook( __FILE__, array( WORDPRESS_CONVERT_MAIN_CLASS, "uninstall" ) );
?>