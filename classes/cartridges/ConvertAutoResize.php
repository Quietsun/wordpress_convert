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
		$script = "<script type=\"text/javascript\">\r\n";
		$script .= "jQuery(function(){var ah = \$(\"#area-side-a\").height();";
		$script .= "var bh = \$(\"#area-side-b\").height();";
		$script .= "var ch = \$(\"#area-main\").height();";
		$script .= "if(ah > bh && ah > ch){";
		$script .= "\$(\"#blank-footer\").css(\"height\", ah + \"px\");";
		$script .= "}else if(bh > ch){";
		$script .= "\$(\"#blank-footer\").css(\"height\", bh + \"px\");";
		$script .= "}else{";
		$script .= "\$(\"#blank-footer\").css(\"height\", ch + \"px\");";
		$script .= "}});\r\n";
		$script .= "</script>\r\n";
		pq("head")->append($script);
		return $content;
	}
}
?>