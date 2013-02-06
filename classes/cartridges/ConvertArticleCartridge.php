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
	
	public function convert($baseFileName, $content){
		// ページのタイトルを変換
		pq("title")->replaceWith("<title><?php bloginfo( 'name' ); wp_title(); ?></title>");
		pq("meta[property=og:site_name]")->attr("content", "<?php bloginfo( 'name' ) ?>");
		pq("meta[property=og:title]")->attr("content", "<?php bloginfo( 'name' ); wp_title() ?>");
		pq("meta[property=og:image]")->attr("content", "<?php echo get_theme_root_uri().\"/".WORDPRESS_CONVERT_THEME_NAME."/bdflashinfo/thumbnail.png\" ?>");
		pq("meta[property=og:url]")->attr("content", "<?php if(is_front_page()){ echo home_url(); }else{ the_permalink(); } ?>");
		
		// ページのタイトルを変換
		pq("span.wp_blog_title")->replaceWith("<span id=\"wp_blog_title\"><?php bloginfo( 'name' ); ?></span>");
		// ページのタイトルを変換
		pq("span.wp_page_title")->replaceWith("<span id=\"wp_page_title\"><?php wp_title(''); ?></span>");
		// 一覧画面のページャーを変換
		pq("div.wp_list_pager")->replaceWith("<div class=\"wp_list_pager\"><?php wp_list_paginate(); ?></div>");
		// 一覧画面のページャーを変換
		pq("span.wp_list_count")->replaceWith("<span class=\"wp_list_count\"><?php \$wp_query_data = (array) \$wp_query; echo \$wp_query_data[\"found_posts\"]; ?></span>");
		// アーカイブのタイトルを変換
		foreach(pq("div.wp_archive_title") as $archive){
			$dailys = pq($archive)->find(".wp_archive_title_daily");
			if(count($dailys) > 0){
				$daily = $dailys[0];
				$title = pq($daily)->attr("title");
				if(empty($title)){
					$title = "%";
				}
				pq($daily)->replaceWith("<?php if ( is_day() ) : printf( \"".$title."\", get_the_date() ); else: ?>");
				pq($archive)->append("<?php endif; ?>");
			}
			$monthlys = pq($archive)->find(".wp_archive_title_monthly");
			if(count($monthlys) > 0){
				$monthly = $monthlys[0];
				$title = pq($monthly)->attr("title");
				if(empty($title)){
					$title = "%";
				}
				pq($monthly)->replaceWith("<?php if ( is_month() ) : printf( \"".$title."\", get_the_date('Y年F') ); else: ?>");
				pq($archive)->append("<?php endif; ?>");
			}
			$yearlys = pq($archive)->find(".wp_archive_title_yearly");
			if(count($yearlys) > 0){
				$yearly = $yearlys[0];
				$title = pq($yearlys)->attr("title");
				if(empty($title)){
					$title = "%";
				}
				pq($yearly)->replaceWith("<?php if ( is_year() ) : printf( \"".$title."\", get_the_date('Y年') ); else: ?>");
				pq($archive)->append("<?php endif; ?>");
			}
		}
		// 記事のページャーを変換
		foreach(pq("div.wp_post_pager") as $pager){
			$title = explode(",", pq($pager)->attr("title"));
			$link = explode(",", pq($pager)->attr("link"));
			if(!empty($title)){
				if(!empty($link)){
					pq($pager)->replaceWith("<div class=\"wp_post_pager\"><span class=\"nav-next\"><?php next_post_link(\"".$title[0]."\", \"".$link[0]."\"); ?></span><span class=\"nav-previous\"><?php previous_post_link(\"".$title[1]."\", \"".$link[1]."\"); ?></span></div>");
				}else{
					pq($pager)->replaceWith("<div class=\"wp_post_pager\"><span class=\"nav-next\"><?php next_post_link(\"".$title[0]."\"); ?></span><span class=\"nav-previous\"><?php previous_post_link(\"".$title[1]."\"); ?></span></div>");
				}
			}else{
				pq($pager)->replaceWith("<div class=\"wp_post_pager\"><span class=\"nav-next\"><?php next_post_link(); ?></span><span class=\"nav-previous\"><?php previous_post_link(); ?></span></div>");
			}
		}
		// 記事のページャーを変換
		foreach(pq("span.wp_post_pager_prev") as $prev){
			$title = pq($prev)->attr("title");
			$link = pq($prev)->attr("link");
			if(!empty($title)){
				if(!empty($link)){
					pq($prev)->replaceWith("<span class=\"wp_post_pager_prev\"><?php previous_post_link(\"".$title."\", \"".$link."\"); ?></span>");
				}else{
					pq($prev)->replaceWith("<span class=\"wp_post_pager_prev\"><?php previous_post_link(\"".$title."\"); ?></span>");
				}
			}else{
				pq($prev)->replaceWith("<span class=\"wp_post_pager_prev\"><?php previous_post_link(); ?></span>");
			}
		}
		// 記事のページャーを変換
		foreach(pq("span.wp_post_pager_next") as $next){
			$title = pq($next)->attr("title");
			$link = pq($next)->attr("link");
			if(!empty($title)){
				if(!empty($link)){
					pq($next)->replaceWith("<span class=\"wp_post_pager_next\"><?php next_post_link(\"".$title."\", \"".$link."\"); ?></span>");
				}else{
					pq($next)->replaceWith("<span class=\"wp_post_pager_next\"><?php next_post_link(\"".$title."\"); ?></span>");
				}
			}else{
				pq($next)->replaceWith("<span class=\"wp_post_pager_next\"><?php next_post_link(); ?></span>");
			}
		}
			
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
					pq($tag)->replaceWith("<?php if (get_the_tags()) the_tags('', \"".$title."\"); ?>");
				}
			}
			
			// 
			$class = pq($article)->attr("class");
			if(preg_match("/wp_articles_([0-9]+)/", $class, $params) > 0){
				pq($article)->before("<?php query_posts(\$query_string.'&posts_per_page=".$params[1]."'); if (have_posts()) : while (have_posts()) : the_post(); ?>");
			}else{
				pq($article)->before("<?php if (have_posts()) : while (have_posts()) : the_post(); ?>");
			}
			$title = pq($article)->attr("title");
			if(!empty($title)){
				pq($article)->after("<?php endwhile; else: echo \"".$title."\"; endif; ?>");
			}else{
				pq($article)->after("<?php endwhile; endif; ?>");
			}
		}
		return $content;
	}
}
