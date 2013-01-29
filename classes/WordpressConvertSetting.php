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

/**
 * HTMLをWordPressテンプレートに変換するプラグインの設定用基底クラス
 *
 * @package WordpressConvertSetting
 * @author Naohisa Minagawa
 * @version 1.0
 */
abstract class WordpressConvertSetting {
	private static $dashboardMenu = array();

	private static $originalMenu = null;

	private static $originalSubmenu = null;
	
	/**
	 * ダッシュボードメニューを制御するメソッド
	 */
	public static function controlDashboard(){
		// 元々表示しているメニューを復元する。
		global $menu, $submenu;
		if(self::$originalMenu == null){
			self::$originalMenu = $menu;
			self::$originalSubmenu = $submenu;
		}
		$menu = self::$originalMenu;
		$submenu = self::$originalSubmenu;
		
		// メニューの表示を調整
		foreach($menu as $index => $item){
			// ダッシュボードは無効にする。
			if($item[2] ==  "index.php"){
				unset($menu[$index]);
			}
		}
		
		// 無効にしたダッシュボードの代わりを登録		
		add_menu_page(
			WORDPRESS_CONVERT_PLUGIN_NAME, 
			WORDPRESS_CONVERT_PLUGIN_SHORTNAME,
			"administrator", 
			"wordpress_convert_menu", 
			array( "WordpressConvertSettingMenu", 'execute' ), 
			WORDPRESS_CONVERT_BASE_URL."/images/naviicon.png", 
			2 
		);
		$submenu["wordpress_convert_menu"] = self::$dashboardMenu;
	}
	
	/**
	 * メニューを制御するメソッド
	 * @return void
	 */
	public static function controlMenus(){
		// 元々表示しているメニューを復元する。
		global $menu, $submenu;
		
		self::$dashboardMenu = $submenu["wordpress_convert_menu"];
		
		// プロフェショナルモード出ない場合は、大半のメニューを無効化
		if(get_option(WORDPRESS_CONVERT_PROJECT_CODE."_professional") != "1"){
			foreach($menu as $index => $item){
				// プロモードでない場合は他のメニューも無効にする。
				switch($item[2]){
					case "upload.php":
					case "link-manager.php":
					case "edit.php?post_type=page":
					case "edit-comments.php":
					case "themes.php":
					// case "plugins.php":
					case "users.php":
					// case "tools.php":
					// case "options-general.php":
						unset($menu[$index]);
						break;
				}
			}
			// 無効化したメニューのうち、利用するサブメニューをこちらのメニューの配下に移動する。
			foreach($submenu["themes.php"] as $index => $sub){
				if($sub[1] == "switch_themes"){
					unset($submenu["themes.php"][$index]);
					$sub[3] = $sub[0] = __("Select Themes", WORDPRESS_CONVERT_PROJECT_CODE);
					$submenu["wordpress_convert_menu"][] = $sub;
				}
				if($sub[1] == "edit_theme_options" && $sub[2] == "widgets.php"){
					unset($submenu["themes.php"][$index]);
					$sub[3] = $sub[0] = __("Widget Setting", WORDPRESS_CONVERT_PROJECT_CODE);
					$submenu["wordpress_convert_menu"][] = $sub;
				}
				if($sub[1] == "edit_theme_options" && $sub[2] == "nav-menus.php"){
					unset($submenu["themes.php"][$index]);
					$sub[3] = $sub[0] = __("Menu Setting", WORDPRESS_CONVERT_PROJECT_CODE);
					$submenu["wordpress_convert_menu"][] = $sub;
				}
			}
			foreach($submenu["index.php"] as $index => $sub){
				if($sub[1] == "update_core"){
					unset($submenu["index.php"][$index]);
					$sub[3] = $sub[0] = __("System Update", WORDPRESS_CONVERT_PROJECT_CODE);
					$submenu["wordpress_convert_menu"][] = $sub;
				}
			}
			add_action( 'admin_notices', array( "WordpressConvertSetting", 'recoverMenus' ) );
		}
	}
	
	/**
	 * メニューを制御するメソッド
	 * @return void
	 */
	public static function adminBarMenu(){
		add_action( 'admin_bar_menu', array( "WordpressConvertSetting", 'adminMenu' ), 210 );
	}
	
	public static function adminMenu(){
		global $wp_admin_bar;
		if(get_option(WORDPRESS_CONVERT_PROJECT_CODE."_professional") != "1"){
			$wp_admin_bar->remove_node("new-media");
			$wp_admin_bar->remove_node("new-link");
			$wp_admin_bar->remove_node("new-page");
			$wp_admin_bar->remove_node("new-user");
		}
	}
	
	/**
	 * メニューを制御するメソッド
	 * @return void
	 */
	public static function recoverMenus(){
		// 無効化したメニューのうち、利用するサブメニューをこちらのメニューの配下に移動する。
		global $submenu;
		foreach($submenu["wordpress_convert_menu"] as $index => $sub){
			if($sub[1] == "switch_themes"){
				$sub[3] = $sub[0] = __("Select Themes", WORDPRESS_CONVERT_PROJECT_CODE);
				$submenu["themes.php"][] = $sub;
			}
			if($sub[1] == "edit_theme_options" && $sub[2] == "widgets.php"){
				$sub[3] = $sub[0] = __("Widget Setting", WORDPRESS_CONVERT_PROJECT_CODE);
				$submenu["themes.php"][] = $sub;
			}
			if($sub[1] == "edit_theme_options" && $sub[2] == "nav-menus.php"){
				$sub[3] = $sub[0] = __("Menu Setting", WORDPRESS_CONVERT_PROJECT_CODE);
				$submenu["themes.php"][] = $sub;
			}
		}
	}
}
