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

require_once(dirname(__FILE__)."/../ContentConvertCartridge.php");

/**
 * CSSや画像・スクリプトのパスを変換するためのカートリッジクラス
 *
 * @package ConvertPathCartridge
 * @author Naohisa Minagawa
 * @version 1.0
 */
class ConvertPathCartridge extends ContentConvertCartridge {
	public function __construct(){
		parent::__construct();
	}
	
	public function convert($baseFileName, $content){
		foreach(pq("img") as $image){
			if(preg_match("/^https?:\\/\\//", pq($image)->attr("src")) == 0){
				$path = preg_replace("/\\/[^\\/]+\\/\\.\\.\\//", "/", get_theme_root_uri()."/".WORDPRESS_CONVERT_THEME_NAME."/".dirname($baseFileName)."/".pq($image)->attr("src"));
				pq($image)->attr("src", $path);
			}
		}
		foreach(pq("script") as $script){
			if(pq($script)->attr("src") != "" && preg_match("/^https?:\\/\\//", pq($script)->attr("src")) == 0){
				$path = preg_replace("/\\/[^\\/]+\\/\\.\\.\\//", "/", get_theme_root_uri()."/".WORDPRESS_CONVERT_THEME_NAME."/".dirname($baseFileName)."/".pq($script)->attr("src"));
				pq($script)->attr("src", $path);
			}
		}
		foreach(pq("link") as $link){
			if(pq($link)->attr("rel") == "stylesheet" && preg_match("/^https?:\\/\\//", pq($link)->attr("href")) == 0){
				$path = preg_replace("/\\/[^\\/]+\\/\\.\\.\\//", "/", get_theme_root_uri()."/".WORDPRESS_CONVERT_THEME_NAME."/".dirname($baseFileName)."/".pq($link)->attr("href"));
				pq($link)->attr("href", $path);
			}
		}
		foreach(pq("iframe") as $iframe){
			if(preg_match("/^https?:\\/\\//", pq($iframe)->attr("src")) == 0){
				$path = preg_replace("/\\/[^\\/]+\\/\\.\\.\\//", "/", get_theme_root_uri()."/".WORDPRESS_CONVERT_THEME_NAME."/".dirname($baseFileName)."/".preg_replace("/\\.html?$/i", ".php", pq($iframe)->attr("src")));
				pq($iframe)->attr("src", $path);
			}
		}
		foreach(pq("a") as $anchor){
			if(preg_match("/^https?:\\/\\//", pq($anchor)->attr("href")) == 0){
				$basedir = preg_replace("/^\\./", "", dirname($baseFileName));
				if(!empty($basedir)){
					$basedir .= "/";
				}
				if(substr(pq($anchor)->attr("href"), 0, 1) != "#"){
					$path = substr(preg_replace("/\\/[^\\/]+\\/\\.\\.\\//", "/", "/".$basedir.pq($anchor)->attr("href")), 1);
				}else{
					$path = pq($anchor)->attr("href");
				}
				if($path == "single.html"){
					pq($anchor)->attrPHP("href", "the_permalink();");
				}elseif($path == "category.html"){
					pq($anchor)->attrPHP("href", "echo get_category_link(\$wp_category['term_id']);");
				}elseif($path == "index.html"){
					pq($anchor)->attrPHP("href", "echo get_option('siteurl')");
				}elseif(preg_match("/^https?:\\/\\//", pq($anchor)->attr("href")) == 0){
					pq($anchor)->attrPHP("href", "echo get_page_link(".$this->converter->getPageId(str_replace(".html", "", $path)).")");
				}
			}
		}
		return $content;
	}
}
?>