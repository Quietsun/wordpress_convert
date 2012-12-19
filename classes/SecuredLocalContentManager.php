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
		$data = @file_get_contents(WORDPRESS_CONVERT_AUTH_BASEURL."/jsonp.php?m=ftplogin&callback=ftplogin&login=".$this->login_id."&password=".$this->password."&secret=JK19pDr3cM94LkfEsY0FpQ21");
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
		if(isset($_POST["reconstruct"])){
			return true;
		}
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