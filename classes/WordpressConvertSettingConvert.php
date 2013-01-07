<?php
/**
 * WordPress Converter for HTML Plugin
 * 
 * Copyright (c) 2012 NetLife Inc. All Rights Reserved.
 * http://www.netlife-web.com/
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
			"professional" => "yesno", 
			"auth_baseurl" => "label", 
			"ftp_host" => "label", 
			"template_basedir" => "label", 
			"theme_code" => "label", 
			"ftp_login_id" => "text", 
			"ftp_password" => "text", 
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
			"base_dir" => ""
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
		
		self::saveSetting($labels);
		
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
			
			$_SESSION["WORDPRESS_CONVERT_MESSAGE"] = __("Saved Changes", WORDPRESS_CONVERT_PROJECT_CODE);
		
			wp_safe_redirect($_SERVER["REQUEST_URI"]);
		}
	}

	/**
	 * 設定画面の表示を行う。
	 * @return void
	 */
	public static function displaySetting($labels, $types, $hints, $options){
		// 設定変更ページを登録する。
		echo "<div id=\"bwp-wrap\">";
		echo "<h2>".WORDPRESS_CONVERT_PLUGIN_NAME." ".__("Convert Setting", WORDPRESS_CONVERT_PROJECT_CODE)."</h2>";
		echo "<form method=\"post\" action=\"".$_SERVER["REQUEST_URI"]."\">";
			$errorMessage = call_user_func(array(WORDPRESS_CONVERT_MAIN_CLASS, "convertError"));
		if(!empty($errorMessage)){
			echo "<p class=\"bwp-error\">".$errorMessage."</p>";
		}
		echo "<table class=\"form-table\"><tbody>";
		foreach($labels as $key => $label){
			if($types[$key] != "hidden"){
				echo "<tr><th>".$labels[$key]."</th><td>";
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
				echo "</td></tr>";
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
?>