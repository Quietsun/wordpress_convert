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

require(dirname(__FILE__)."/ContentManager.php");

/**
 * ローカルディスクでHTMLを取得するための基底クラス
 *
 * @package FtpContentManager
 * @author Naohisa Minagawa
 * @version 1.0
 */
class LocalContentManager extends ContentManager {
	public function __construct($login_id, $password, $basedir){
		parent::__construct($login_id, $password, $basedir);
	}
	
	public function isAccessible(){
		return true;
	}
	
	public function getContentHome(){
		if(substr($this->basedir, 0, 1) != "/"){
			// １文字目がスラッシュで無い場合は相対パスとして認識
			$dir = str_replace("//", "/", ABSPATH."/".$this->basedir."/");
			if(file_exists($dir) && is_dir($dir)){
				return $dir;
			}
			$dir = str_replace("//", "/", realpath($_SERVER["DOCUMENT_ROOT"])."/".$this->basedir."/");
			if(file_exists($dir) && is_dir($dir)){
				return $dir;
			}
		}else{
			// １文字目がスラッシュの場合は絶対パスとして認識
			$dir = str_replace("//", "/", $this->basedir."/");
			if(file_exists($dir) && is_dir($dir)){
				return $dir;
			}
		}
	}
	
	public function getThemeFile($filename){
		$themeBase = get_theme_root()."/".WORDPRESS_CONVERT_THEME_NAME."/";
		$theme = str_replace("//", "/", str_replace($this->getContentHome(), $themeBase, $filename));
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
	
	public function isGlobalUpdate(){
		if($this->isAccessible()){
			if($this->isUpdated($this->getContentHome()."bdflashinfo/info.xml")){
				return true;
			}
			if($this->isUpdated($this->getContentHome()."index.html")){
				return true;
			}
		}
		return false;
	}
	
	public function isUpdated($filename){
		// 日付を比較する。
		$theme = $this->getThemeFile($filename);
		if(file_exists($filename) && (!file_exists($theme) || filemtime($theme) < filemtime($filename))){
			return true;
		}
		return false;
	}
	
	public function getContent($filename){
		return file_get_contents($filename);
	}
}
