<?php
/**
 * Windwork
 * 
 * 一个用于快速开发高并发Web应用的轻量级PHP框架
 * 
 * @copyright Copyright (c) 2008-2017 Windwork Team. (http://www.windwork.org)
 * @license   http://opensource.org/licenses/MIT
 */
namespace wf\storage\strategy;

/**
 * 存贮文件（用户上传的附件）操作
 * 
 * @package     wf.storage.strategy
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.storage.html
 * @since       0.1.0
 */
class File extends \wf\storage\StorageAbstract
{

    /**
     * 支持通过wrapper访问存贮
     * @param array $cfg
     */
    public function __construct(array $cfg) 
    {
        parent::__construct($cfg);
    }
    
    /**
     * {@inheritDoc}
     * @see \wf\storage\StorageInterface::remove()
     */
    public function remove($path)
    {
        return @unlink($this->getRealPath($path));
    }
    
    /**
     * {@inheritDoc}
     * @see \wf\storage\StorageInterface::removeThumb()
     */
    public function removeThumb($path)
    {        
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
     * {@inheritDoc}
     * @see \wf\storage\StorageInterface::clearThumb()
     */
    public function clearThumb() 
    {
        $this->removeDir($this->getRealPath('thumb'), false);
    }

    /**
     * {@inheritDoc}
     * @see \wf\storage\StorageInterface::getContent()
     */
    public function getContent($path)
    {
        return file_get_contents($this->getRealPath($path));
    }
    
    /**
     * {@inheritDoc}
     * @see \wf\storage\StorageInterface::save()
     */
    public function save($path, $content)
    {
        $path = $this->getRealPath($path);
        if (!is_dir(dirname($path))) {
            @mkdir(dirname($path), 0755, true);
        }
        
        return file_put_contents($path, $content);
    }

    /**
     * {@inheritDoc}
     * @see \wf\storage\StorageInterface::saveUploadFile()
     */
    public function saveUploadFile($tempFile, $uploadPath)
    {
        $uploadPath = $this->getRealPath($uploadPath);
        if (!is_dir(dirname($uploadPath))) {
            @mkdir(dirname($uploadPath), 0755, true);
        }
        return move_uploaded_file($tempFile, $uploadPath);
    }
    
    /**
     * {@inheritDoc}
     * @see \wf\storage\StorageInterface::copy()
     */
    public function copy($pathFrom, $pathTo)
    {
        $pathFrom = $this->getRealPath($pathFrom);
        $pathTo = $this->getRealPath($pathTo);
        
        if (!is_dir(dirname($pathTo))) {
            @mkdir(dirname($pathTo), 0755, true);
        }
    
        return copy($pathFrom, $pathTo);
    }
    
    /**
     * {@inheritDoc}
     * @see \wf\storage\StorageInterface::isExist()
     */
    public function isExist($path)
    {
        return is_file($this->getRealPath($path));
    }
    
    /**
     * 获取上传文件（在存贮介质上）的真实路径 {$wrapper}$path
     * 
     * @param string $path
     * @param string $type 
     * @return string
     */
    public function getRealPath($path)
    {
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
    private function removeDir($dir, $rmSelf = true)
    {
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


