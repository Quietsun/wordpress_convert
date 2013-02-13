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
 * HTMLをWordPressテンプレートに変換するプラグインの設定用クラス
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
		if(isset($_GET["site_closed"])){
			// モードを変更
			update_option("wordpress_convert_site_closed", $_GET["site_closed"]);
		}
		
		// メニュー表示切り替え
		parent::controlMenus();
		
		// WordPressダッシュボードはこちらのダッシュボードにリダイレクト
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
		$site_closed = get_option("wordpress_convert_site_closed");
		
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
		echo "<div class=\"bwp-alert bwp-information\">".__("There is possibility of inactivation functions of WordPress Visual Editor in BiND template.", WORDPRESS_CONVERT_PROJECT_CODE) ."</div>";

		// 適用ボタン系
		if(!file_exists($contentManager->getContentHome()."bdflashinfo/info.xml") && !file_exists($contentManager->getContentHome()."index.html")){
			echo "<p class=\"bwp-alert bwp-information\">".__("Target HTML was not found.", WORDPRESS_CONVERT_PROJECT_CODE)."</p>";
		}elseif($contentManager->isGlobalUpdate()){
			echo "<p class=\"bwp-alert bwp-information\">".WORDPRESS_CONVERT_PLUGIN_NAME.__("was updated.", WORDPRESS_CONVERT_PROJECT_CODE).__("Please apply from here.", WORDPRESS_CONVERT_PROJECT_CODE)."<span><a href=\"admin.php?page=wordpress_convert_dashboard&reconstruct=1\"><img src=\"".WORDPRESS_CONVERT_BASE_URL."/images/apply.png\" alt=\"".__("Apply", WORDPRESS_CONVERT_PROJECT_CODE)."\" width=\"71\" height=\"24\"></a></span></p>";
		}else{
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
		echo "<p class=\"bwp-button\"><a href=\"edit-comments.php\"><img src=\"".WORDPRESS_CONVERT_BASE_URL."/images/comment.png\" alt=\"".__("Check comments", WORDPRESS_CONVERT_PROJECT_CODE)."\" width=\"252\" height=\"52\"></a>";
		echo "</p>";
		echo "<p class=\"bwp-button\"><a href=\"edit-comments.php?comment_status=moderated\"><img src=\"".WORDPRESS_CONVERT_BASE_URL."/images/commentapply.png\" alt=\"".__("Accept comment", WORDPRESS_CONVERT_PROJECT_CODE)."\" width=\"252\" height=\"52\"></a>";
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
		// echo "<li id=\"bwp-weblife\"><a href=\"https://mypage.weblife.me/\">".__("WebLife Server control panel", WORDPRESS_CONVERT_PROJECT_CODE)."</a></li>";
		// echo "<li id=\"bwp-help\"><a href=\"#\">".__("Help", WORDPRESS_CONVERT_PROJECT_CODE)."</a></li>";
		if($professional == "1"){
			echo "<a href=\"admin.php?page=wordpress_convert_dashboard&professional=0\" style=\"text-decoration: none;\"><li class=\"bwp-custom\">".__("Change easy mode", WORDPRESS_CONVERT_PROJECT_CODE)."</li></a>";
		}else{
			echo "<a href=\"admin.php?page=wordpress_convert_dashboard&professional=1\" style=\"text-decoration: none;\"><li class=\"bwp-custom-off\">".__("Change custom mode", WORDPRESS_CONVERT_PROJECT_CODE)."</li></a>";
		}
		if($site_closed == "1"){
			echo "<a href=\"admin.php?page=wordpress_convert_dashboard&site_closed=0\" style=\"text-decoration: none;\"><li class=\"bwp-private\">".__("Open this site", WORDPRESS_CONVERT_PROJECT_CODE)."</li></a>";
		}else{
			echo "<a href=\"admin.php?page=wordpress_convert_dashboard&site_closed=1\" style=\"text-decoration: none;\"><li class=\"bwp-public\">".__("Close this site", WORDPRESS_CONVERT_PROJECT_CODE)."</li></a>";
		}
		echo "</ul></div>";
	}
}
