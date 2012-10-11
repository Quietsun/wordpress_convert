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
	
	public function convert($content){
		foreach(pq("img") as $image){
			pq($image)->attr("src", get_theme_root_uri()."/".WORDPRESS_CONVERT_THEME_NAME."/".pq($image)->attr("src"));
		}
		foreach(pq("script") as $script){
			if(pq($script)->attr("src") != ""){
				pq($script)->attr("src", get_theme_root_uri()."/".WORDPRESS_CONVERT_THEME_NAME."/".pq($script)->attr("src"));
			}
		}
		foreach(pq("link") as $link){
			if(pq($link)->attr("rel") == "stylesheet"){
				pq($link)->attr("href", get_theme_root_uri()."/".WORDPRESS_CONVERT_THEME_NAME."/".pq($link)->attr("href"));
			}
		}
		foreach(pq("a") as $anchor){
			if(pq($anchor)->attr("href") == "single.html"){
				pq($anchor)->attr("href", "<?php the_permalink(); ?>");
			}elseif(pq($anchor)->attr("href") == "index.html"){
				pq($anchor)->attr("href", get_option('siteurl'));
			}else{
				pq($anchor)->attr("href", get_theme_root_uri()."/".WORDPRESS_CONVERT_THEME_NAME."/".str_replace(".html", ".php", pq($anchor)->attr("href")));
			}
		}
		return $content;
	}
}
?>