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

/**
 * HTMLをWordpressテンプレートに変換するプラグインの設定用クラス
 *
 * @package WordpressConvertSetting
 * @author Naohisa Minagawa
 * @version 1.0
 */
class WordpressConvertSettingMenu {
	/**
	 * 設定を初期化するメソッド
	 * admin_menuにフックさせる。
	 * @return void
	 */
	public static function init(){
		global $menu, $submenu;
		foreach($menu as $index => $item){
			// ダッシュボードは無効にする。
			if($item[2] ==  "index.php"){
				unset($menu[$index]);
			}
		}
		if(get_option(WORDPRESS_CONVERT_PROJECT_CODE."_professional") != "1"){
			foreach($menu as $index => $item){
				// プロモードでない場合は他のメニューも無効にする。
				switch($item[2]){
					case "upload.php":
					case "link-manager.php":
					case "edit.php?post_type=page":
					case "edit-comments.php":
					case "themes.php":
					case "plugins.php":
					case "users.php":
					case "tools.php":
					case "options-general.php":
						unset($menu[$index]);
						break;
				}
			}
		}
		add_menu_page(
			WORDPRESS_CONVERT_PLUGIN_NAME, 
			WORDPRESS_CONVERT_PLUGIN_NAME,
			"administrator", 
			"wordpress_convert_menu", 
			array( "WordpressConvertSettingMenu", 'execute' ), 
			WORDPRESS_CONVERT_BASE_URL."/menu_icon.png", 
			2 
		);
		$submenu["wordpress_convert_menu"] = array();
		add_submenu_page(
			'wordpress_convert_menu',
			__("Dashboard", WORDPRESS_CONVERT_PROJECT_CODE), __("Dashboard", WORDPRESS_CONVERT_PROJECT_CODE),
			'administrator', "wordpress_convert_dashboard", array( "WordpressConvertSettingMenu", 'execute' )
		);
		foreach($submenu["themes.php"] as $index => $sub){
			if($sub[1] != "edit_themes" && $sub[2] != "theme_options"){
				$submenu["wordpress_convert_menu"][$index] = $sub;
			}
		}
		
		// Wordpressダッシュボードはこちらのダッシュボードにリダイレクト
		if(basename($_SERVER["PHP_SELF"]) == "index.php"){
			wp_redirect(get_option('siteurl') . '/wp-admin/admin.php?page=wordpress_convert_dashboard');
			exit;
		}
	}
	
	/**
	 * 設定画面の制御を行うメソッドです。
	 */
	public static function execute(){
		self::displaySetting();
	}

	/**
	 * 設定画面の表示を行う。
	 * @return void
	 */
	public static function displaySetting(){
		// 設定を取得
		$themeCode = get_option("wordpress_convert_theme_code");
		$template = get_option("template");
		$stylesheet = get_option("stylesheet");
		$contentManagerClass = WORDPRESS_CONVERT_CONTENT_MANAGER;
		$contentManager = new $contentManagerClass(get_option(WORDPRESS_CONVERT_PROJECT_CODE."_ftp_login_id"), get_option(WORDPRESS_CONVERT_PROJECT_CODE."_ftp_password"), get_option(WORDPRESS_CONVERT_PROJECT_CODE."_base_dir"));
		
		if(isset($_POST["activate"])){
			// テンプレートをアクティベート
			update_option("template", $themeCode);
			update_option("stylesheet", $themeCode);
			$template = get_option("template");
			$stylesheet = get_option("stylesheet");
		}
		
		// 設定変更ページを登録する。
		echo "<div class=\"wrap\">";
		echo "<h2>".WORDPRESS_CONVERT_PLUGIN_NAME."</h2>";
		if(file_exists($contentManager->getThemeFile($contentManager->getContentHome()))){
			if($themeCode != $template){
				echo "<form method=\"post\" action=\"".$_SERVER["REQUEST_URI"]."\">";
				echo "<p class=\"submit\"><input type=\"submit\" name=\"activate\" value=\"".__("Activate BiND6 Theme", WORDPRESS_CONVERT_PROJECT_CODE)."\" /></p>";
				echo "</form>";
			}else{
				echo "<p class=\"submit\">".__("BiND Theme is Activated", WORDPRESS_CONVERT_PROJECT_CODE)."</p>";
			}
		}else{
			echo "<p class=\"submit\">".__("There is not BiND Theme", WORDPRESS_CONVERT_PROJECT_CODE)."</p>";
		}
		echo "</div>";
	}
}
?>