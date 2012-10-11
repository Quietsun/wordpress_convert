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

$settings = explode(",", WORDPRESS_CONVERT_SETTING_CLASSES);
foreach($settings as $setting){
	require_once(dirname(__FILE__)."/WordpressConvertSetting".$setting.".php");
}
require_once(dirname(__FILE__)."/".WORDPRESS_CONVERT_CONTENT_MANAGER.".php");
require_once(dirname(__FILE__)."/ContentConverter.php");
require_once(dirname(__FILE__)."/cartridges/ConvertPathCartridge.php");
require_once(dirname(__FILE__)."/cartridges/ConvertArticleCartridge.php");

/**
 * HTMLをWordpressテンプレートに変換するプラグインのメインクラス
 *
 * @package WordpressConvert
 * @author Naohisa Minagawa
 * @version 1.0
 */
class WordpressConvert {
	/**
	 * Initial
	 * @return void
	 */
	public static function init(){
		// 環境のバージョンチェック
		if( version_compare( PHP_VERSION, '5.3.0', '<' ) )
			trigger_error( __("PHP 5.3 or later is required for this plugin."), E_USER_ERROR );
		
		// 初期化処理
		$settings = explode(",", WORDPRESS_CONVERT_SETTING_CLASSES);
		foreach($settings as $setting){
			add_action( 'admin_menu', array( "WordpressConvertSetting".$setting, 'init' ) );
		}
		
		// 初期表示のメニューを変更
		//if(empty($_GET["page"]) && preg_match("/\\/wp-admin\\//", $_SERVER["REQUEST_URI"]) > 0){
		//	wp_redirect(get_option('siteurl') . '/wp-admin/admin.php?page=wordpress_convert_menu');
		//}
	}
	
	/**
	 * 変換処理を必要に応じて実行する。
	 */
	public static function execute(){
		$contentManagerClass = WORDPRESS_CONVERT_CONTENT_MANAGER;
		$contentManager = new $contentManagerClass(get_option("wordpress_convert_ftp_login_id"), get_option("wordpress_convert_ftp_password"), get_option("wordpress_convert_base_dir"));
		
		// 共通スタイルの自動生成
		$filename = $contentManager->getContentHome()."/style.css";
		$themeFile = $contentManager->getThemeFile($filename);
		$info = pathinfo($themeFile);
		if(!is_dir($info["dirname"])){
			mkdir($info["dirname"], 0755, true);
		}
		if(($fp = fopen($themeFile, "w+")) !== FALSE){
			fwrite($fp, "/* \r\n");
			fwrite($fp, "Theme Name: ".WORDPRESS_CONVERT_THEME_NAME."\r\n");
			fwrite($fp, "Description: Converted Theme by Wordpress Converter\r\n");
			fwrite($fp, "Author: NetLife Inc.\r\n");
			fwrite($fp, "Author URI: http://www.netlife-web.com/\r\n");
			fwrite($fp, "Version: 1.0\r\n");
			fwrite($fp, "\r\n");
			fwrite($fp, "This themes was generated by Wordpress Converter Plugin.\r\n");
			fwrite($fp, "Theme can not use except for servers which we select.\r\n");
			fwrite($fp, "In case, theme uses others, you may spent extra fee.\r\n");
			fwrite($fp, "\r\n");
			fwrite($fp, "*/\r\n");
			fclose($fp);
		}
		
		// 共通関数プログラムの自動生成
		$filename = $contentManager->getContentHome()."/functions.php";
		$themeFile = $contentManager->getThemeFile($filename);
		$info = pathinfo($themeFile);
		if(!is_dir($info["dirname"])){
			mkdir($info["dirname"], 0755, true);
		}
		if(($fp = fopen($themeFile, "w+")) !== FALSE){
			fwrite($fp, "<?php\r\n");
			fwrite($fp, "function eyecatch_setup() {\r\n");
			fwrite($fp, "add_theme_support( 'post-thumbnails' );\r\n");
			fwrite($fp, "}\r\n");
			fwrite($fp, "add_action( 'after_setup_theme', 'eyecatch_setup' );\r\n");
			fwrite($fp, "?>\r\n");
			fclose($fp);
		}
		
		$files = $contentManager->getList();
		foreach($files as $filename){
			if($contentManager->isUpdated($filename)){
				$themeFile = $contentManager->getThemeFile($filename);
				$info = pathinfo($themeFile);
				if(!is_dir($info["dirname"])){
					mkdir($info["dirname"], 0755, true);
				}
				if(($fp = fopen($themeFile, "w+")) !== FALSE){
					$content = $contentManager->getContent($filename);
					if(preg_match("/\\.html?$/i", $filename) > 0){
						$converter = new ContentConverter($content);
						$cartridgeNames = explode(",", WORDPRESS_CONVERT_CARTRIDGES);
						foreach($cartridgeNames as $cartridgeName){
							if(!empty($cartridgeName) && class_exists($cartridgeName."Cartridge")){
								$className = $cartridgeName."Cartridge";
								$converter->addCartridge(new $className());
							}
						}
						fwrite($fp, $converter->convert()->html());
					}elseif(preg_match("/\\.css$/i", $filename) > 0){
						$content = preg_replace("/url\\(([^\\)]+)\\)/", "url(".get_theme_root_uri()."/".WORDPRESS_CONVERT_THEME_NAME."/"."\$1)", $content);
						fwrite($fp, $content);
					}elseif(preg_match("/script\\.js$/i", $filename) > 0){
						$content = preg_replace("/bindobj\\.siteroot = ''/", "bindobj.siteroot = '".get_theme_root_uri()."/".WORDPRESS_CONVERT_THEME_NAME."/'", $content);
						$content = preg_replace("/bindobj\\.dir = ''/", "bindobj.dir = '".get_theme_root_uri()."/".WORDPRESS_CONVERT_THEME_NAME."/'", $content);
						fwrite($fp, $content);
					}else{
						fwrite($fp, $content);
					}
					fclose($fp);
					if($filename == $contentManager->getContentHome()."bdflashinfo/thumbnail.png" || $filename == $contentManager->getContentHome()."siteinfos/thumbnail.png"){
						$screenshotFile = $contentManager->getThemeFile($contentManager->getContentHome()."screenshot.png");
						copy($themeFile, $screenshotFile);
					}
				}
			}
		}
	}

	function install(){
		// インストール時の処理
	}

	function uninstall(){
		// アンインストール時の処理
	}


}
?>