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

require(dirname(__FILE__)."/ContentManager.php");

/**
 * FTPアカウントの認証を含むローカルディスクでHTMLを取得するための基底クラス
 *
 * @package FtpContentManager
 * @author Naohisa Minagawa
 * @version 1.0
 */
class SecuredLocalContentManager extends ContentManager {
	public function __construct($login_id, $password, $basedir){
		parent::__construct($login_id, $password, $basedir);
	}
	
	public function isAccessible(){
		$data = file_get_contents(WORDPRESS_CONVERT_AUTH_BASEURL."/jsonp.php?m=ftplogin&callback=ftplogin&login=".$this->login_id."&password=".$this->password."&secret=JK19pDr3cM94LkfEsY0FpQ21");
		eval($data);
		if(!empty($ftplogin)){
			return true;
		}
		return false;
	}
	
	public function getContentHome(){
		$dirs = array_reverse(explode(".", $this->login_id));
		$base = WORDPRESS_CONVERT_TEMPLATE_BASEDIR;
		foreach($dirs as $d){
			$base .= "/".$d;
		}
		if(substr($this->basedir, -1) != "/"){
			$this->basedir .= "/";
		}
		$base .= $this->basedir;
		return $base;
	}
	
	public function getThemeFile($filename){
		$themeBase = get_theme_root()."/".WORDPRESS_CONVERT_THEME_NAME."/";
		$theme = str_replace($this->getContentHome(), $themeBase, $filename);
		$theme = preg_replace("/\\.html?$/i", ".php", $theme);
		return $theme;
	}
	
	public function getList(){
		// ベースのディレクトリを構築する。
		$result = $this->getSubList($this->getContentHome());

		return $result;
	}
	
	public function getSubList($base){
		$result = array();
		if(is_dir($base)){
			if ($dir = opendir($base)) {
				while (($file = readdir($dir)) !== false) {
					if ($file != "." && $file != "..") {
						if(is_dir($base.$file)){
							$result = array_merge($result, $this->getSubList($base.$file."/"));
						}else{
							$result[] = $base.$file;
						}
					}
				}
				closedir($dir);
			}
		}
		return $result;
	}
	
	public function isUpdated($filename){
		// 日付を比較する。
		$theme = $this->getThemeFile($filename);
		if(!file_exists($theme) || filemtime($theme) < filemtime($filename)){
			return true;
		}
		return false;
	}
	
	public function getContent($filename){
		return file_get_contents($filename);
	}
}
?>