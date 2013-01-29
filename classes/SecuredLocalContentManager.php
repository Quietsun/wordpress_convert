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

require(dirname(__FILE__)."/LocalContentManager.php");

/**
 * FTPアカウントの認証を含むローカルディスクでHTMLを取得するための基底クラス
 *
 * @package FtpContentManager
 * @author Naohisa Minagawa
 * @version 1.0
 */
class SecuredLocalContentManager extends LocalContentManager {
	public function __construct($login_id, $password, $basedir){
		parent::__construct($login_id, $password, $basedir);
	}
	
	public function isAccessible(){
		$data = @file_get_contents(WORDPRESS_CONVERT_AUTH_BASEURL."/jsonp.php?m=ftplogin&callback=ftplogin&login=".$this->login_id."&password=".$this->password."&secret=JK19pDr3cM94LkfEsY0FpQ21");
		eval($data);
		if(!empty($ftplogin)){
			return true;
		}
		return false;
	}
}
