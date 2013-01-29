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
	public function convert($baseFileName, $content){
		// コンテンツを編集可能に設定
		$this->content = phpQuery::newDocument($content);
		
		foreach($this->cartridges as $cartridge){
			$cartridge->setConverter($this);
			$this->content = $cartridge->convert($baseFileName, $this->content);
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
