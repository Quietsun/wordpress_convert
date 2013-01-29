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

/**
 * HTMLを取得するための基底クラス
 *
 * @package ContentManager
 * @author Naohisa Minagawa
 * @version 1.0
 */
abstract class ContentManager {
	protected $login_id;
	
	protected $password;
	
	protected $basedir;
	
	public function __construct($login_id, $password, $basedir){
		$this->login_id = $login_id;
		$this->password = $password;
		$this->basedir = $basedir;
	}
	
	abstract public function isAccessible();
	
	abstract public function getContentHome();
	
	abstract public function getThemeFile($filename);
	
	abstract public function getList();
	
	abstract public function isGlobalUpdate();
	
	abstract public function isUpdated($filename);
	
	abstract public function getContent($filename);
}
