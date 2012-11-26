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
?>