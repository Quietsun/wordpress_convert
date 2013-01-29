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
class ConvertCommentCartridge extends ContentConvertCartridge {
	public function __construct(){
		parent::__construct();
	}
	
	public function convert($baseFileName, $content){
		// コメントフォームをリプレイス
		pq("div.wp_comment_form")->replaceWith("<?php comment_form(); ?>");
		
		foreach(pq(".wp_comment_list") as $comment){
			// コメント投稿者を変換
			pq($comment)->find("span.wp_comment_name")->replaceWith("<span class=\"wp_comment_name\"><?php echo \$item[\"comment_author\"]; ?></span>");
			// コメント投稿者メールアドレスを変換
			pq($comment)->find("span.wp_comment_email")->replaceWith("<span class=\"wp_comment_email\"><?php echo \$item[\"comment_author_email\"]; ?></span>");
			// コメント投稿IPを変換
			pq($comment)->find("span.wp_comment_address")->replaceWith("<span class=\"wp_comment_address\"><?php echo \$item[\"comment_author_IP\"]; ?></span>");
			// コメント日付を変換
			pq($comment)->find("span.wp_comment_date")->replaceWith("<span class=\"wp_comment_date\"><?php echo date(get_option('date_format'), strtotime(\$item[\"comment_date\"])); ?></span>");
			// コメント本文を変換
			pq($comment)->find("span.wp_comment_body")->replaceWith("<span class=\"wp_comment_body\"><?php echo \$item[\"comment_content\"]; ?></span>");
			
			pq($comment)->before("<?php \$data = get_approved_comments(get_the_ID()); foreach(\$data as \$itemTmp): \$item = (array) \$itemTmp; ?>");
			pq($comment)->after("<?php endforeach; ?>");
		}
		
		return $content;
	}
}
