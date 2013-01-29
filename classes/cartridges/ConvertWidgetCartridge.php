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
	
	public function convert($baseFileName, $content){
		// ウィジェットを変換
		$widgets = pq("div.wp_widgets");
		foreach($widgets as $widget){
			// classの値を取得
			$id = pq($widget)->attr("id");
			$title = pq($widget)->attr("title");
			$this->converter->addWidget($id, $title);
			if(!empty($id) && !empty($title)){
				pq($widget)->replaceWith("<div class=\"wp_widgets\"><ul><?php if(function_exists('dynamic_sidebar')) dynamic_sidebar(\"".$id."\"); ?></ul></div>");
			}else{
				pq($widget)->replaceWith("<div class=\"wp_widgets\"><ul><?php if(function_exists('dynamic_sidebar')) dynamic_sidebar(); ?></ul></div>");
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
