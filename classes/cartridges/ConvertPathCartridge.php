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
			// メールリンクでも絶対URLでもページ内リンクでも無い場合に変換処理を実行する。
			if(preg_match("/^((mailto:)|(https?:\\/\\/))/", pq($anchor)->attr("href")) == 0 && substr(pq($anchor)->attr("href"), 0, 1) != "#"){
				// 現在のファイルの存在するディレクトリを元にパス使用しているパスを補正
				$basedir = preg_replace("/^\\./", "", dirname($baseFileName));
				if(!empty($basedir)){
					$basedir .= "/";
				}
				
				// サブディレクトリの上位ディレクトリの指定は相殺させる。
				$path = substr(preg_replace("/\\/[^\\/]+\\/\\.\\.\\//", "/", "/".$basedir.pq($anchor)->attr("href")), 1);
				if($path == "single.html"){
					pq($anchor)->attrPHP("href", "the_permalink();");
				}elseif($path == "category.html"){
					pq($anchor)->attrPHP("href", "echo get_category_link(\$wp_category['term_id']);");
				}elseif($path == "index.html"){
					pq($anchor)->attrPHP("href", "echo home_url()");
/* global以外のBiND リンクに対応 2014.03.12 yuge edit */
				}elseif(preg_match("/\\.html?(#.+)?$/", $path) > 0){
					if(strpos($path, "#") > 0){
						list($path, $dummy) = explode("#", $path);
					}
/* 2014.03.12 yuge edit end */
					pq($anchor)->attrPHP("href", "echo get_page_link(".$this->converter->getPageId(str_replace(".html", "", $path)).")");
				}else{
					$path = get_theme_root_uri()."/".WORDPRESS_CONVERT_THEME_NAME."/".$path;
					pq($anchor)->attr("href", $path);
				}
			}
		}
		return $content;
	}
}
