<?php
/**
 * WordPress Converter for HTML Plugin
 * 
 * Copyright (c) 2012 NetLife Inc. All Rights Reserved.
 * http://www.netlife-web.com/
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
 * 非表示エリアを変換するためのカートリッジクラス
 *
 * @package ConvertIgnoreCartridge
 * @author Naohisa Minagawa
 * @version 1.0
 */
class ConvertIgnoreCartridge extends ContentConvertCartridge {
	public function __construct(){
		parent::__construct();
	}
	
	public function convert($baseFileName, $content){
		// コメントアウトエリアを変換
		pq(".wp_none")->replaceWith("");
		
		return $content;
	}
}
?>