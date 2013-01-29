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
 * カテゴリやカレンダーなどのウィジェットパーツを変換するためのカートリッジクラス
 *
 * @package ConvertWidgetPartsCartridge
 * @author Naohisa Minagawa
 * @version 1.0
 */
class ConvertWidgetPartsCartridge extends ContentConvertCartridge {
	public function __construct(){
		parent::__construct();
	}
	
	public function convert($baseFileName, $content){
		// カレンダーを変換
		pq("div.wp_calendar")->replaceWith("<?php get_calendar(); ?>");
		
		foreach(pq("div.wp_categories") as $category){
			// タイトルを変換
			pq($category)->find("span.wp_category_name")->replaceWith("<?php echo \$wp_category[\"name\"] ?>");
			// 投稿日時を変換
			pq($category)->find("span.wp_category_slug")->replaceWith("<?php echo \$wp_category[\"slug\"] ?>");
			
			$class = pq($category)->attr("class");
			$preHtml = "\$wp_categories = get_categories(";
			//$preHtml .= "array(\"parent\" => \"\")" .
			$preHtml .= ");\r\n";
			$preHtml .= "foreach(\$wp_categories as \$wp_category_obj):\r\n";
			$preHtml .= "\$wp_category = (array) \$wp_category_obj;\r\n";
			pq($category)->prepend("<?php ".$preHtml." ?>");
			pq($category)->append("<?php endforeach; ?>");
		}
		return $content;
	}
}
