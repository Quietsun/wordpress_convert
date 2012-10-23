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
	
	private $sidebars;
	
	private $navMenus;
	
	private $pageIds;
	
	public function __construct(){
		// カートリッジを設定
		$this->cartridges = array();
		
		// ウィジェットを初期化
		$this->widgets = get_option("wordpress_convert_widgets");
		if(!is_array($this->widgets)){
			$this->widgets = array();
		}
		
		// メニューを初期化
		$this->navMenus = get_option("wordpress_convert_menus");
		if(!is_array($this->navMenus)){
			$this->navMenus = array();
		}
		
		// ページIDを初期化
		$this->pageIds = array();
	}
	
	/**
	 * カートリッジを追加
	 */
	public function addCartridge($cartridge){
		$this->cartridges[] = $cartridge;
		return $this;
	}
	
	/**
	 * ウィジェットを追加
	 */
	public function addWidget($id, $name){
		if(!isset($this->widgets[$id]) || !empty($name)){
			$this->widgets[$id] = $name;
			update_option("wordpress_convert_widgets", $this->widgets);
		}
	}
	
	/**
	 * ウィジェットを取得
	 */
	public function getWidgets(){
		return $this->widgets;
	}
	
	/**
	 * メニューを追加
	 */
	public function addNavMenu($id, $name){
		if(!isset($this->navMenus[$id]) || !empty($name)){
			$this->navMenus[$id] = $name;
			update_option("wordpress_convert_menus", $this->navMenus);
		}
	}
	
	/**
	 * メニューを取得
	 */
	public function getNavMenus(){
		return $this->navMenus;
	}
	
	/**
	 * ページを追加
	 */
	public function addPage($name, $id){
		if(!isset($this->pageIds[$name])){
			$this->pageIds[$name] = $id;
		}
	}
	
	/**
	 * ページのIDを取得
	 */
	public function getPageId($name){
		return $this->pageIds[$name];
	}
	
	/**
	 * 変換を実行
	 */
	public function convert($content){
		// コンテンツを編集可能に設定
		$this->content = phpQuery::newDocument($content);
		
		foreach($this->cartridges as $cartridge){
			$cartridge->setConverter($this);
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