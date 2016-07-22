<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace wf\storage\adapter;

/**
 * 存贮文件（用户上传的附件）操作
 * 
 * @package     wf.storage.adapter
 * @author      erzh <cmpan@qq.com>
 * @link        http://www.windwork.org/manual/wf.storage.html
 * @since       0.1.0
 */
class File extends \wf\storage\AStorage {

	/**
	 * 支持通过wrapper访问存贮
	 * @param array $cfg
	 */
	public function __construct(array $cfg) {
		parent::__construct($cfg);
	}
	
	/**
	 * 删除附件
	 * @param string $path
	 */
	public function remove($path) {
		return @unlink($this->getRealPath($path));
	}
	
	/**
	 * 删除缩略图
	 * 
	 * @param string $path 缩略图路径
	 */
	public function removeThumb($path) {		
		// 缩略图路径
		$thumbDir = dirname($this->getThumbPath($path, 1, 1));
		$thumbDir = $this->getRealPath(trim($thumbDir, '/')) . '/';

		if(!is_dir($thumbDir)) {
			return false;
		}
		
		$baseId = base64_encode($path);
		$d = dir($thumbDir);
		
		while (false !== ($entry = $d->read())) {
			if ($entry[0] == '.') {
				continue;
			}
			
			if(false !== $pos = strpos($entry, $baseId.'$')){
				@unlink($thumbDir.'/'.$entry);
			}
		}
		
		$d->close();
	}
	
	/**
	 * 删除所有缩略图
	 *
	 */
	public function clearThumb() {
		$this->removeDir($this->getRealPath('thumb'), false);
	}

	/**
	 * 读取内容
	 *
	 * @param string $path
	 * @return string
	 */
	public function getContent($path) {
		return file_get_contents($this->getRealPath($path));
	}
	
	/**
	 * 存贮附件
	 * 
	 * @param string $path
	 * @param string $content
	 * @return bool
	 */
	public function save($path, $content) {
		$path = $this->getRealPath($path);
		if (!is_dir(dirname($path))) {
			@mkdir(dirname($path), 0755, true);
		}
		
		return file_put_contents($path, $content);
	}

	/**
	 * 上传文件
	 * @param string $tempFile
	 * @param string $uploadPath
	 */
	public function upload($tempFile, $uploadPath) {
		$uploadPath = $this->getRealPath($uploadPath);
		if (!is_dir(dirname($uploadPath))) {
			@mkdir(dirname($uploadPath), 0755, true);
		}
		return move_uploaded_file($tempFile, $uploadPath);
	}
	
	/**
	 * 复制文件到附件目录
	 * @param string $pathFrom 来源文件完整的路径（注意该文件路径的安全）
	 * @param string $pathTo
	 * @return boolean
	 */
	public function copy($pathFrom, $pathTo) {
		$pathFrom = $this->getRealPath($pathFrom);
		$pathTo = $this->getRealPath($pathTo);
		
		if (!is_dir(dirname($pathTo))) {
			@mkdir(dirname($pathTo), 0755, true);
		}
	
		return copy($pathFrom, $pathTo);
	}
	
	/**
	 * 附件是否存在
	 * @param string $path
	 * @return boolean
	 */
	public function isExist($path) {
		return is_file($this->getRealPath($path));
	}
	
	/**
	 * 获取上传文件（在存贮介质上）的真实路径 {$wrapper}$path
	 * 
	 * @param string $path
	 * @param string $type 
	 * @return string
	 */
	public function getRealPath($path) {
	    // 得到确定的path
		$path = $this->getPathFromUrl($path);
		$path = $this->safePath($path);
		
		// *nux系统上不存在的文件realpath() = false，因此先去存贮目录的真实路径
		$path = realpath($this->storageDir) . '/' . $path;
		
		return $path;
	}
	
	/**
	 * 删除文件夹（包括有子目录或有文件）
	 *
	 * @param string $dir 目录
	 * @param bool $rmSelf = false 是否删除本身
	 * @return bool
	 */
	private function removeDir($dir, $rmSelf = true) {
		$dir = rtrim($dir, '/');
		
		// 不处理非法路径
		$dir = $this->safePath($dir);
	
		if(!$dir || !$d = dir($dir)) {
			return;
		}

		$do = true;
		while (false !== ($entry = @$d->read())) {
			if($entry[0] == '.') {
				continue;
			}
			
			$path = $dir.'/'.$entry;
			if (is_dir($path)) {
				$do && $do = static::removeDirs($path, true);
			} else {
				$do && $do = false !== @unlink($path);
			}
		}
			
		@$d->close();
		
		$rmSelf && @rmdir($dir);
		
		return $do;
	}
}


