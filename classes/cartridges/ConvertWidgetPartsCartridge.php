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
?>