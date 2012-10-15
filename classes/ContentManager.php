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
	
	abstract public function isUpdated($filename);
	
	abstract public function getContent($filename);
}
?>