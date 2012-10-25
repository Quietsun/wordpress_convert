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
 * ウィジェットやメニューを変換するためのカートリッジクラス
 *
 * @package ConvertWidgetCartridge
 * @author Naohisa Minagawa
 * @version 1.0
 */
class ConvertWidgetCartridge extends ContentConvertCartridge {
	public function __construct(){
		parent::__construct();
	}
	
	public function convert($content){
		// ウィジェットを変換
		$widgets = pq("div.wp_widgets");
		foreach($widgets as $widget){
			// classの値を取得
			$id = pq($widget)->attr("id");
			$title = pq($widget)->attr("title");
			$this->converter->addWidget($id, $title);
			if(!empty($id) && !empty($title)){
				pq($widget)->replaceWith("<div class=\"menuv\"><div class=\"menu-a\"><ul><?php if(function_exists('dynamic_sidebar')) dynamic_sidebar(\"".$id."\"); ?></ul></div></div>");
			}else{
				pq($widget)->replaceWith("<div class=\"menuv\"><div class=\"menu-a\"><ul><?php if(function_exists('dynamic_sidebar')) dynamic_sidebar(); ?></ul></div></div>");
			}
		}
		// メニューを変換
		$menus = pq("div.wp_menus");
		foreach($menus as $menu){
			// classの値を取得
			$id = pq($menu)->attr("id");
			$title = pq($menu)->attr("title");
			$this->converter->addNavMenu($id, $title);
			if(!empty($id)){
				pq($menu)->replaceWith("<?php if(function_exists('wp_nav_menu')){ \$data = array(); \$data[\"theme_location\"] = \"".$id."\"; wp_nav_menu(\"".$id."\"); } ?>");
			}
		}
		return $content;
	}
}
?>