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
class WordpressConvertSettingConvert extends WordpressConvertSetting {
	/**
	 * 設定を初期化するメソッド
	 * admin_menuにフックさせる。
	 * @return void
	 */
	public static function init(){
		// プロフェショナルモードの設定のみ反映させる。
		if( isset( $_POST['wp_convert_submit'] ) && isset( $_POST['professional'] ) ){
			update_option("wordpress_convert_professional", $_POST['professional']);
		}

		$labels = array(
			"professional" => __("Professional Mode", WORDPRESS_CONVERT_PROJECT_CODE), 
			"auth_baseurl" => __("Authenticate BaseURL", WORDPRESS_CONVERT_PROJECT_CODE), 
			"ftp_host" => __("FTP Host", WORDPRESS_CONVERT_PROJECT_CODE), 
			"template_basedir" => __("Template Basedir", WORDPRESS_CONVERT_PROJECT_CODE), 
			"theme_code" => __("Theme Code", WORDPRESS_CONVERT_PROJECT_CODE), 
			"ftp_login_id" => __("FTP Login ID", WORDPRESS_CONVERT_PROJECT_CODE), 
			"ftp_password" => __("FTP Password", WORDPRESS_CONVERT_PROJECT_CODE), 
			"base_dir" => __("Base Directory", WORDPRESS_CONVERT_PROJECT_CODE)
		);
		self::saveSetting($labels);
		
		// ダッシュボード表示切り替え
		parent::controlDashboard();
		
		add_submenu_page(
			'wordpress_convert_menu',
			__("Convert Setting", WORDPRESS_CONVERT_PROJECT_CODE), __("Convert Setting", WORDPRESS_CONVERT_PROJECT_CODE),
			'administrator', "wordpress_convert_convert_setting", array( "WordpressConvertSettingConvert", 'execute' )
		);
		
		// メニュー表示切り替え
		parent::controlMenus();
	}
	
	/**
	 * 設定画面の制御を行うメソッドです。
	 */
	public static function execute(){
		$labels = array(
			"professional" => __("Professional Mode", WORDPRESS_CONVERT_PROJECT_CODE), 
			"auth_baseurl" => __("Authenticate BaseURL", WORDPRESS_CONVERT_PROJECT_CODE), 
			"ftp_host" => __("FTP Host", WORDPRESS_CONVERT_PROJECT_CODE), 
			"template_basedir" => __("Template Basedir", WORDPRESS_CONVERT_PROJECT_CODE), 
			"theme_code" => __("Theme Code", WORDPRESS_CONVERT_PROJECT_CODE), 
			"ftp_login_id" => __("FTP Login ID", WORDPRESS_CONVERT_PROJECT_CODE), 
			"ftp_password" => __("FTP Password", WORDPRESS_CONVERT_PROJECT_CODE), 
			"base_dir" => __("Base Directory", WORDPRESS_CONVERT_PROJECT_CODE)
		);
		$types = array(
			"professional" => "hidden", 
			"auth_baseurl" => "hidden", 
			"ftp_host" => "hidden", 
			"template_basedir" => "hidden", 
			"theme_code" => "hidden", 
			"ftp_login_id" => "hidden", 
			"ftp_password" => "hidden", 
			"base_dir" => "text"
		);
		$values = array(
			"professional" => "0", 
			"auth_baseurl" => "https://mypage.weblife.me", 
			"ftp_host" => "", 
			"template_basedir" => "/d/premium", 
			"theme_code" => WORDPRESS_CONVERT_DEFAULT_NAME, 
			"ftp_login_id" => "", 
			"ftp_password" => "", 
			"base_dir" => "sitedata"
		);
		$hints = array(
			"professional" => __("Please select Wordpress menus to be professional or not.", WORDPRESS_CONVERT_PROJECT_CODE), 
			"auth_baseurl" => __("Please input Authenticate BaseURL", WORDPRESS_CONVERT_PROJECT_CODE), 
			"ftp_host" => __("Please input your FTP Hostname or IP Address", WORDPRESS_CONVERT_PROJECT_CODE), 
			"template_basedir" => __("Please input template basedir", WORDPRESS_CONVERT_PROJECT_CODE), 
			"theme_code" => __("Theme code which this plugin convert to", WORDPRESS_CONVERT_PROJECT_CODE),
			"ftp_login_id" => __("Please input your FTP login ID", WORDPRESS_CONVERT_PROJECT_CODE), 
			"ftp_password" => __("Please input your FTP password", WORDPRESS_CONVERT_PROJECT_CODE), 
			"base_dir" => __("Please input template base directory by ftp root directory", WORDPRESS_CONVERT_PROJECT_CODE)
		);
		
		$options = array();
		foreach($labels as $key => $label){
			$options[$key] = get_option("wordpress_convert_".$key, $values[$key]);
		}
		
		self::displaySetting($labels, $types, $hints, $options);
	}

	/**
	 * エラーチェックを行う。
	 */
	protected static function is_valid($values){
		$errors = array();
		/*
		if(empty($values["ftp_host"]) && empty($values["template_basedir"])){
			$errors["ftp_host"] = __("Empty FTP Host and Template Basedir", WORDPRESS_CONVERT_PROJECT_CODE);
			$errors["template_basedir"] = __("Empty FTP Host and Template Basedir", WORDPRESS_CONVERT_PROJECT_CODE);
		}
		if(empty($values["theme_code"])){
			$errors["theme_code"] = __("Empty Theme Code", WORDPRESS_CONVERT_PROJECT_CODE);
		}
		if(empty($values["ftp_login_id"])){
			$errors["ftp_login_id"] = __("Empty FTP login ID", WORDPRESS_CONVERT_PROJECT_CODE);
		}
		if(empty($values["ftp_password"])){
			$errors["ftp_password"] = __("Empty FTP password", WORDPRESS_CONVERT_PROJECT_CODE);
		}
		*/
		if(empty($values["base_dir"])){
			$errors["base_dir"] = __("Empty Base Directory", WORDPRESS_CONVERT_PROJECT_CODE);
		}
		if(!empty($errors)){
			return $errors;
		}
		return true;
	}
	
	/**
	 * 設定を保存する。
	 */
	protected static function saveSetting($labels){
		if( isset( $_POST['wp_convert_submit'] ) && ( $errors = self::is_valid( $_POST ) ) === true ){
			unset($_POST["wp_convert_submit"]);
			foreach( $labels as $key => $label ){
				update_option("wordpress_convert_".$key, $_POST[$key]);
				$options[$key] = $_POST[$key];
			}
			update_option("wordpress_convert_template_files", json_encode(array()));
			
			WordpressConvert::$convertError = __("Saved Changes", WORDPRESS_CONVERT_PROJECT_CODE);
		
			wp_safe_redirect($_SERVER["REQUEST_URI"]);
		}elseif(isset( $_POST['wp_convert_submit'] )){
			foreach($errors as $error){
				WordpressConvert::$convertError = $error;
				break;
			}
		}
	}

	/**
	 * 設定画面の表示を行う。
	 * @return void
	 */
	public static function displaySetting($labels, $types, $hints, $options){
		// コンテンツマネージャを生成
		$contentManagerClass = WORDPRESS_CONVERT_CONTENT_MANAGER;
		$contentManager = new $contentManagerClass(get_option(WORDPRESS_CONVERT_PROJECT_CODE."_ftp_login_id"), get_option(WORDPRESS_CONVERT_PROJECT_CODE."_ftp_password"), get_option(WORDPRESS_CONVERT_PROJECT_CODE."_base_dir"));
		
		// 設定変更ページを登録する
		echo "<style type=\"text/css\">\r\n";
		echo ".form-table { font-size: 12px; width: 100%;border-top: 1px solid #cccccc; }\r\n";
		echo ".form-table td { padding: 10px; background-color: #ffffff; border-bottom: 1px solid #cccccc; }\r\n";
		echo ".form-table th { padding: 10px; background-color: #f8f8f8; border-bottom: 1px solid #cccccc; }\r\n";
		echo ".form-table .bwp-title01 { font-weight: bolder; text-align: center; width: 300px; }\r\n";
		echo ".bwp-txt { font-size: 12px; line-height: 20px; padding: 18px 0px; }\r\n";
		echo "</style>\r\n";
		
		echo "<div id=\"bwp-wrap\">";
		echo "<h1><img src=\"".WORDPRESS_CONVERT_BASE_URL."/images/maintitle.png\" width=\"244\" height=\"31\" alt=\"".WORDPRESS_CONVERT_PLUGIN_NAME."\"></h1>";

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
		
		echo "<h2><img src=\"".WORDPRESS_CONVERT_BASE_URL."/images/conversion.png\" alt=\"".WORDPRESS_CONVERT_PLUGIN_NAME."\"></h2>";
		echo "<p class=\"bwp-txt\">".__("Please point directory which contents uploaded with BiND.", WORDPRESS_CONVERT_PROJECT_CODE)."<br>";
		echo __("After finished settings, starting to convert BiND WordPress template to WordPress theme.", WORDPRESS_CONVERT_PROJECT_CODE)."<br>";
		echo __("Please create directory and upload WordPress template generated by BiND to it because of directory installed WordPress as document root in the local directory.", WORDPRESS_CONVERT_PROJECT_CODE)."<br>";
		echo __("You can set path of directory for template remove first of `slashes` in it.", WORDPRESS_CONVERT_PROJECT_CODE)."</p>";
		
		echo "<form method=\"post\" action=\"".$_SERVER["REQUEST_URI"]."\">";
		echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"form-table\"><tbody>";
		echo "<tr>";
		// echo "<th>".__("setting name", WORDPRESS_CONVERT_PROJECT_CODE) ."</th>";
		echo "<th>".__("current setting", WORDPRESS_CONVERT_PROJECT_CODE) ."</th>";
		echo "<th align=\"left\">".__("setting value", WORDPRESS_CONVERT_PROJECT_CODE) ."</th>";
		echo "<tr>";
		foreach($labels as $key => $label){
			if($types[$key] != "hidden"){
				echo "<tr>";
				// echo "<td>".$labels[$key]."</td>";
				echo "<td class=\"bwp-title01\">".get_option("wordpress_convert_".$key, __("no setting", WORDPRESS_CONVERT_PROJECT_CODE))."</td>";
				echo "<td>";
				if(!empty($errors[$key])){
					$class = $key." error";
				}else{
					$class = $key;
				}
				if($types[$key] == "yesno"){
					echo "<input type=\"radio\" class=\"".$class."\" name=\"".$key."\" value=\"1\"".(($options[$key] == "1")?" checked":"")." />".__("YES");
					echo "&nbsp;<input type=\"radio\" class=\"".$class."\" name=\"".$key."\" value=\"0\"".(($options[$key] != "1")?" checked":"")." />".__("NO");
				}elseif($types[$key] == "label"){
					echo nl2br(htmlspecialchars($options[$key]));
					echo "<input type=\"hidden\" name=\"".$key."\" value=\"".$options[$key]."\" />";
				}else{
					echo "<input type=\"text\" class=\"".$class."\" name=\"".$key."\" value=\"".$options[$key]."\" size=\"44\" />";
				}
				if(!empty($errors[$key])){
					echo "<p class=\"error\">".$errors[$key]."</p>";
				}
				if(!empty($hints[$key])){
					echo "<p class=\"hint\">".$hints[$key]."</p>";
				}
				echo "</td>";
				echo "</tr>";
			}else{
				echo "<input type=\"hidden\" name=\"".$key."\" value=\"".$options[$key]."\" />";
			}
		}
		echo "</tbody></table>";
		if(!empty($_SESSION["WORDPRESS_CONVERT_MESSAGE"])){
			echo "<p class=\"caution\">".$_SESSION["WORDPRESS_CONVERT_MESSAGE"]."</p>";
			unset($_SESSION["WORDPRESS_CONVERT_MESSAGE"]);
		}
		echo "<p class=\"submit\"><input type=\"submit\" name=\"wp_convert_submit\" value=\"".__("Save Changes", WORDPRESS_CONVERT_PROJECT_CODE)."\" /></p>";
		echo "</form></div>";
	}
}
