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
class WordpressConvertSettingMenu extends WordpressConvertSetting {
	/**
	 * 設定を初期化するメソッド
	 * admin_menuにフックさせる。
	 * @return void
	 */
	public static function init(){
		// ダッシュボード表示切り替え
		parent::controlDashboard();
		
		add_submenu_page(
			'wordpress_convert_menu',
			__("Dashboard", WORDPRESS_CONVERT_PROJECT_CODE), __("Dashboard", WORDPRESS_CONVERT_PROJECT_CODE),
			'administrator', "wordpress_convert_dashboard", array( "WordpressConvertSettingMenu", 'execute' )
		);
		
		if(isset($_GET["professional"])){
			// モードを変更
			update_option("wordpress_convert_professional", $_GET["professional"]);
		}
		
		// メニュー表示切り替え
		parent::controlMenus();
		
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
		
		$professional = get_option("wordpress_convert_professional");
		
		if(isset($_GET["activate"])){
			// テンプレートをアクティベート
			update_option("template", $themeCode);
			update_option("stylesheet", $themeCode);
		}
		$template = get_option("template");
		$stylesheet = get_option("stylesheet");
		
		// 設定変更ページを登録する
		echo "<div id=\"bwp-wrap\">";
		echo "<h1><img src=\"".WORDPRESS_CONVERT_BASE_URL."/images/maintitle.png\" width=\"244\" height=\"31\" alt=\"".WORDPRESS_CONVERT_PLUGIN_NAME."\"></h1>";

		// 適用ボタン系
		if(!file_exists($contentManager->getThemeFile($contentManager->getContentHome())) || filemtime($contentManager->getThemeFile($contentManager->getContentHome())."index.php") < filemtime($contentManager->getContentHome()."index.html")){
			echo "<p class=\"bwp-alert bwp-information\">".WORDPRESS_CONVERT_PLUGIN_NAME.__("was updated.", WORDPRESS_CONVERT_PROJECT_CODE).__("Please apply from here.", WORDPRESS_CONVERT_PROJECT_CODE)."<span><a href=\"admin.php?page=wordpress_convert_dashboard&reconstruct=1\"><img src=\"".WORDPRESS_CONVERT_BASE_URL."/images/apply.png\" alt=\"".__("Apply", WORDPRESS_CONVERT_PROJECT_CODE)."\" width=\"71\" height=\"24\"></a></span></p>";
		}
		if(file_exists($contentManager->getThemeFile($contentManager->getContentHome()))){
			if($themeCode != $template){
				echo "<p class=\"bwp-alert bwp-update\">".__("New theme was uploaded.", WORDPRESS_CONVERT_PROJECT_CODE).__("Please apply from here.", WORDPRESS_CONVERT_PROJECT_CODE)."<span><a href=\"admin.php?page=wordpress_convert_dashboard&activate=1\"><img src=\"".WORDPRESS_CONVERT_BASE_URL."/images/apply.png\" alt=\"".__("Apply", WORDPRESS_CONVERT_PROJECT_CODE)."\" width=\"71\" height=\"24\"></a></span></p>";
			}
		}
		
		$errorMessage = call_user_func(array(WORDPRESS_CONVERT_MAIN_CLASS, "convertError"));
		if(!empty($errorMessage)){
			echo "<p class=\"bwp-error\">".$errorMessage."</p>";
		}
		
		// 記事投稿系
		echo "<h2><img src=\"".WORDPRESS_CONVERT_BASE_URL."/images/posttitle.png\" alt=\"".__("Contribute articles", WORDPRESS_CONVERT_PROJECT_CODE)."\" width=\"179\" height=\"27\"></h2>";
		echo "<p class=\"bwp-button\"><a href=\"post-new.php\"><img src=\"".WORDPRESS_CONVERT_BASE_URL."/images/newpost.png\" alt=\"".__("Contribute new article", WORDPRESS_CONVERT_PROJECT_CODE)."\" width=\"252\" height=\"52\"></a></p>";
		echo "<p class=\"bwp-button\"><a href=\"edit.php\"><img src=\"".WORDPRESS_CONVERT_BASE_URL."/images/editpost.png\" alt=\"".__("Contribute new article", WORDPRESS_CONVERT_PROJECT_CODE)."\" width=\"252\" height=\"52\"></a></p>";
		
		// 新着記事系
		echo "<div id=\"bwp-newtable\">";
		$args = array( 'numberposts' => 2, 'order'=> 'DESC', 'orderby' => 'post_date' );
		$posts = get_posts( $args );
		$screen = get_current_screen();
		set_current_screen("post");
		$wp_list_table = _get_list_table('WP_Posts_List_Table');
		$wp_list_table->prepare_items();
		echo "<table class=\"wp-list-table ".implode( ' ', $wp_list_table->get_table_classes() )."\" cellspacing=\"0\">";
		echo "<thead><tr>".$wp_list_table->print_column_headers()."</tr></thead>";
		echo "<tbody id=\"the-list\">";
		$wp_list_table->display_rows($posts);
		echo "</tbody></table>";
		set_current_screen($screen);
		echo "</div>";
		
		// コメント管理系
		$comments = wp_count_comments();
		echo "<h2><img src=\"".WORDPRESS_CONVERT_BASE_URL."/images/commenttitle.png\" alt=\"".__("Comment", WORDPRESS_CONVERT_PROJECT_CODE)."\" width=\"117\" height=\"22\"></h2>";
		echo "<p class=\"bwp-button\"><a href=\"edit-comments.php\"><img src=\"".WORDPRESS_CONVERT_BASE_URL."/images/commentapply.png\" alt=\"".__("Accept comment", WORDPRESS_CONVERT_PROJECT_CODE)."\" width=\"252\" height=\"52\"></a></p>";
		echo "<p class=\"bwp-button\"><a href=\"edit-comments.php\"><img src=\"".WORDPRESS_CONVERT_BASE_URL."/images/comment.png\" alt=\"".__("Check comments", WORDPRESS_CONVERT_PROJECT_CODE)."\" width=\"252\" height=\"52\"></a>";
		if($comments->moderated > 0){
			echo "<span>".$comments->moderated."</span>";
		}
		echo "</p>";

		// デザイン編集系
		echo "<h2><img src=\"".WORDPRESS_CONVERT_BASE_URL."/images/designtitle.png\" alt=\"".__("Design", WORDPRESS_CONVERT_PROJECT_CODE)."\" width=\"117\" height=\"26\"></h2>";
		echo "<p class=\"bwp-button\"><a href=\"themes.php\"><img src=\"".WORDPRESS_CONVERT_BASE_URL."/images/selecttheme.png\" alt=\"".__("Select theme", WORDPRESS_CONVERT_PROJECT_CODE)."\" width=\"252\" height=\"52\"></a></p>";
		echo "<p class=\"bwp-button\"><a href=\"widgets.php\"><img src=\"".WORDPRESS_CONVERT_BASE_URL."/images/widget.png\" alt=\"".__("Widgets", WORDPRESS_CONVERT_PROJECT_CODE)."\" width=\"252\" height=\"52\"></a></p>";
		echo "<p class=\"bwp-button\"><a href=\"nav-menus.php\"><img src=\"".WORDPRESS_CONVERT_BASE_URL."/images/sidemenu.png\" alt=\"".__("Side menu", WORDPRESS_CONVERT_PROJECT_CODE)."\" width=\"252\" height=\"52\"></a></p>";
		
		// 各種設定系
		echo "<h2><img src=\"".WORDPRESS_CONVERT_BASE_URL."/images/setting.png\" alt=\"".__("Setting", WORDPRESS_CONVERT_PROJECT_CODE)."\" width=\"76\" height=\"27\"></h2>";
		echo "<p class=\"bwp-button\"><a href=\"edit-tags.php?taxonomy=category\"><img src=\"".WORDPRESS_CONVERT_BASE_URL."/images/category.png\" alt=\"".__("Category", WORDPRESS_CONVERT_PROJECT_CODE)."\" width=\"252\" height=\"52\"></a></p>";
		echo "<p class=\"bwp-button\"><a href=\"edit-tags.php?taxonomy=post_tag\"><img src=\"".WORDPRESS_CONVERT_BASE_URL."/images/tag.png\" alt=\"".__("Tag", WORDPRESS_CONVERT_PROJECT_CODE)."\" width=\"252\" height=\"52\"></a></p>";

		// フッタ
		echo "<ul id=\"bwp-footlink\">";
		echo "<li id=\"bwp-weblife\"><a href=\"https://mypage.weblife.me/\">".__("WebLife Server control panel", WORDPRESS_CONVERT_PROJECT_CODE)."</a></li>";
		echo "<li id=\"bwp-help\"><a href=\"#\">".__("Help", WORDPRESS_CONVERT_PROJECT_CODE)."</a></li>";
		if($professional == "1"){
			echo "<li id=\"bwp-custom\"><a href=\"admin.php?page=wordpress_convert_dashboard&professional=0\" style=\"text-decoration: none;\">".__("Change custom mode", WORDPRESS_CONVERT_PROJECT_CODE)."</a></li>";
		}else{
			echo "<li id=\"bwp-custom-off\"><a href=\"admin.php?page=wordpress_convert_dashboard&professional=1\" style=\"text-decoration: none;\">".__("Change custom mode", WORDPRESS_CONVERT_PROJECT_CODE)."</a></li>";
		}
		echo "</ul></div>";
	}
}
?>