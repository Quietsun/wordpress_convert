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

require(dirname(__FILE__)."/../phpQuery/phpQuery.php");

/**
 * HTMLを変換するためのクラス
 *
 * @package ContentConverter
 * @author Naohisa Minagawa
 * @version 1.0
 */
class ContentConverter {
	private $content;
	
	private $cartridges;
	
	public function __construct($content){
		// コンテンツを編集可能に設定
		$this->content = phpQuery::newDocument($content);
		
		// カートリッジを設定
		$this->cartridges = array();
	}
	
	/**
	 * カートリッジを追加
	 */
	public function addCartridge($cartridge){
		$this->cartridges[] = $cartridge;
		return $this;
	}
	
	/**
	 * 変換を実行
	 */
	public function convert(){
		foreach($this->cartridges as $cartridge){
			$this->content = $cartridge->convert($this->content);
		}
		return $this;
	}
	
	/**
	 * HTMLテキストとして出力する。
	 */
	public function html(){
		return $this->content->htmlOuter();	
	}

	/**
	 * PHPコードとして出力する。
	 */
	public function php(){
		return $this->content->php();	
	}
}
?>