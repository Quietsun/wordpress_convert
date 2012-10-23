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
class WordpressConvertSettingFtp {
	/**
	 * 設定を初期化するメソッド
	 * admin_menuにフックさせる。
	 * @return void
	 */
	public static function init(){
		add_submenu_page(
			'wordpress_convert_menu',
			__("FTP Account Setting", WORDPRESS_CONVERT_PROJECT_CODE), __("FTP Account Setting", WORDPRESS_CONVERT_PROJECT_CODE),
			'administrator', "wordpress_convert_ftp_account", array( "WordpressConvertSettingFtp", 'execute' )
		);
	}
	
	/**
	 * 設定画面の制御を行うメソッドです。
	 */
	public static function execute(){
		$labels = array(
			"ftp_login_id" => __("FTP Login ID", WORDPRESS_CONVERT_PROJECT_CODE), 
			"ftp_password" => __("FTP Password", WORDPRESS_CONVERT_PROJECT_CODE), 
			"base_dir" => __("Base Directory", WORDPRESS_CONVERT_PROJECT_CODE)
		);
		$hints = array(
			"ftp_login_id" => __("Please input your FTP login ID", WORDPRESS_CONVERT_PROJECT_CODE), 
			"ftp_password" => __("Please input your FTP password", WORDPRESS_CONVERT_PROJECT_CODE), 
			"base_dir" => __("Please input template base directory by ftp root directory", WORDPRESS_CONVERT_PROJECT_CODE)
		);
		
		$caution = self::saveSetting($labels);
		
		$options = array();
		foreach($labels as $key => $label){
			$options[$key] = get_option("wordpress_convert_".$key);
		}
		
		self::displaySetting($labels, $options, $hints, $caution);
	}

	/**
	 * エラーチェックを行う。
	 */
	protected static function is_valid($values){
		$errors = array();
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
		if( isset( $_POST['submit'] ) && ( $errors = self::is_valid( $_POST ) ) === true ){
			foreach( $labels as $key => $label ){
				update_option("wordpress_convert_".$key, $_POST[$key]);
				$options[$key] = $_POST[$key];
			}
			update_option("wordpress_convert_template_files", json_encode(array()));
			
			return __("Saved Changes");
		}
	}

	/**
	 * 設定画面の表示を行う。
	 * @return void
	 */
	public static function displaySetting($labels, $options, $hints, $caution){
		// 設定変更ページを登録する。
		echo "<div class=\"wrap\">";
		echo "<h2>".WORDPRESS_CONVERT_PLUGIN_NAME." ".__("FTP Account Setting", WORDPRESS_CONVERT_PROJECT_CODE)."</h2>";
		echo "<form method=\"post\" action=\"".$_SERVER["REQUEST_URI"]."\">";
		echo "<table class=\"form-table\"><tbody>";
		foreach($labels as $key => $label){
			echo "<tr><th>".$labels[$key]."</th><td>";
			if(!empty($errors[$key])){
				$class = $key." error";
			}else{
				$class = $key;
			}
			echo "<input type=\"text\" class=\"".$class."\" name=\"".$key."\" value=\"".$options[$key]."\" size=\"44\" />";
			if(!empty($errors[$key])){
				echo "<p class=\"error\">".$errors[$key]."</p>";
			}
			if(!empty($hints[$key])){
				echo "<p class=\"hint\">".$hints[$key]."</p>";
			}
			echo "</td></tr>";
		}
		echo "</tbody></table>";
		if(!empty($caution)){
			echo "<p class=\"caution\">".$caution."</p>";
		}
		echo "<p class=\"submit\"><input type=\"submit\" name=\"submit\" value=\"".__("Save Changes", WORDPRESS_CONVERT_PROJECT_CODE)."\" /></p>";
		echo "</form></div>";
	}
}
?>