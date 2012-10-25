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
 * 記事用のタグを変換するためのカートリッジクラス
 *
 * @package ConvertArticleCartridge
 * @author Naohisa Minagawa
 * @version 1.0
 */
class ConvertArticleCartridge extends ContentConvertCartridge {
	public function __construct(){
		parent::__construct();
	}
	
	public function convert($content){
		foreach(pq(".wp_articles") as $article){
			// タイトルを変換
			pq($article)->find("span.wp_title")->replaceWith("<?php the_title(); ?>");
			// 投稿日時を変換
			pq($article)->find("span.wp_date")->replaceWith("<?php the_time(get_option('date_format')); ?>");
			// 画像を変換
			$images = pq($article)->find("span.wp_image");
			foreach($images as $image){
				// classの値を取得
				$class = pq($image)->attr("class");
				if(preg_match("/^(.*)wp_image(.*)?$/", $class, $params) > 0){
					if(!empty($params[1]) || !empty($params[2])){
						// クラスの値を分解
						$classes1 = explode(" ", $params[1]);
						$classes2 = explode(" ", $params[2]);
						if(empty($classes1[0])){
							array_shift($classes1);
						}
						if(empty($classes2[0])){
							array_shift($classes2);
						}
						
						// classの値に応じて処理を行う。
						switch($classes2[0]){
							case "thumbnail":
							case "medium":
							case "large":
							case "full":
								$size = "\"".array_shift($classes2)."\"";
								break;
							default:
								if(preg_match("/^([0-9]+)x([0-9]+)$/", $classes2[0], $sizes) > 0){
									$size = "array(".$sizes[1].", ".$sizes[2].")";
								}else{
									$size = "\"medium\"";
								}
								break;
						}
						$classes = array_merge($classes1, $classes2);
					}
					$text = "<?php \$imgClass = array(); ?>";
					$text .= "<?php \$imgClass[\"class\"] = \"".implode(" ", $classes)."\"; ?>";
					
					$text .= "<?php the_post_thumbnail(".$size.", \$imgClass); ?>";
					pq($image)->replaceWith($text);
				}
			}
			// 本文を変換
			$bodys = pq($article)->find("span.wp_content");
			foreach($bodys as $body){
				// classの値を取得
				$title = pq($body)->attr("title");
				if(!empty($title)){
					pq($body)->replaceWith("<?php the_content(\"".$title."\"); ?>");
				}else{
					pq($body)->replaceWith("<?php the_content(); ?>");
				}
			}
			
			// カテゴリの変換
			$categories = pq($article)->find("span.wp_category");
			foreach($categories as $category){
				// classの値を取得
				$title = pq($category)->attr("title");
				if(!empty($title)){
					pq($category)->replaceWith("<?php the_category(\"".$title."\"); ?>");
				}
			}
			
			// タグの変換
			$tags = pq($article)->find("span.wp_tag");
			foreach($tags as $tag){
				// classの値を取得
				$title = pq($tag)->attr("title");
				if(!empty($title)){
					pq($tag)->replaceWith("<?php <?php if (get_the_tags()) the_tags('', \"".$title."\"); ?>");
				}
			}
			
			pq($article)->before("<?php if (have_posts()) : while (have_posts()) : the_post(); ?>");
			pq($article)->after("<?php endwhile; endif; ?>");
		}
		return $content;
	}
}
?>