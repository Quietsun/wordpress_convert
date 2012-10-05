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

require(dirname(__FILE__)."/ContentManager.php");

/**
 * FTP経由でHTMLを取得するための基底クラス
 *
 * @package FtpContentManager
 * @author Naohisa Minagawa
 * @version 1.0
 */
class FtpContentManager extends ContentManager {
	public function __construct($login_id, $password, $basedir){
		parent::__construct($login_id, $password, $basedir);
	}
	
	public function getList(){
		//FTP サーバに接続する。
		if( $ftp = ftp_connect( WORDPRESS_CONVERT_SERVER, 21 ) ){
			//接続した FTP サーバにログインする。
			if( ftp_login( $ftp, $this->login_id, $this->password ) ){
				//FTP サーバ上の特定のディレクトリのファイル一覧を取得する。
				$this->getSubList($ftp, $this->basedir);
				//FTP サーバから切断する。
				ftp_close( $ftp );
			}
		}
	}
	
	public function getSubList($ftp, $dir){
		$filelist = ftp_nlist( $ftp, $dir );
		if($filelist !== FALSE){
			foreach( $filelist as $filename ){
				if($this->getSubList($ftp, $filename) === FALSE){
				    print "{$file_name}\n";
				}
			}
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	public function isUpdated($filename){
		// 常に更新対象とする。
		return true;
	}
	
	public function getContent($filename){
		
	}
}
?>