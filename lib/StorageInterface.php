<?php
/**
 * Windwork
 * 
 * 一个用于快速开发高并发Web应用的轻量级PHP框架
 * 
 * @copyright Copyright (c) 2008-2017 Windwork Team. (http://www.windwork.org)
 * @license   http://opensource.org/licenses/MIT
 */
namespace wf\storage;

/**
 * 存贮文件（用户上传的附件）操作
 * 长久保存可以从网络访问的数据 
 * 
 * 附件存贮规范：
 * 如果附件服务器使用和网站不一样的域名，在配置文件中设置附件网址(storage.storageUrl)，如新浪云的Storage存贮
 * @package     wf.storage
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.storage.html
 * @since       0.1.0
 */
interface StorageInterface
{    
    /**
     * 获取附件存贮站点网址
     */
    public function getStorageUrl();
            
    /**
     * 获取附件的链接
     * 使用两种路径：相对路径和完整路径
     * 当程序设置使用完整路径的时候就全部使用完整路径，否则在PHP脚本中用header跳转的，一律用完整路径，前端页面用相对路径
     * 
     * @param string $path
     * @return string
     */
    public function getUrl($path);
    
    /**
     * 从URL获取文件存贮的路径（path）
     * @param string $url
     * @return string
     */
    public function getPathFromUrl($url);
    
    /**
     * 获取附件完整网址
     * 
     * @param string $path
     * @return string
     */
    public function getFullUrl($path);
    
    /**
     * 获取上传文件（在存贮介质上）的真实路径 {$wrapper}{$path}，实际文件不存在时也有值返回
     * 
     * @param string $path
     * @param string $type 
     * @return string
     */
    public function getRealPath($path);
    
    /**
     * 获取缩略图相对URL，在显示图片时使用
     *
     * @param int $imgId
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getThumbUrl($path, $width = 100, $height = 100);
    
    /**
     * 删除附件
     * @param string $path
     */
    public function remove($path);
    
    /**
     * 删除缩略图
     * 
     * @param string $path 缩略图路径
     */
    public function removeThumb($path);
    
    /**
     * 删除所有缩略图
     *
     */
    public function clearThumb();
    
    /**
     * 获取缩略图路径
     * 
     * @param string $path
     * @param int $width
     * @param int $height
     */
    public function getThumbPath($path, $width, $height);
    
    /**
     * 文件上传的附件是否是保存为静态文件，
     * 建议可在继承类中覆盖该方法
     * 
     * @return boolean
     */
    public function isStatic();
        
    /**
     * 载入文件以显示
     * 
     * @throws \wf\storage\Exception
     * @param string $path
     */
    public function load($path);
    
    /**
     * 读取内容
     *
     * @param string $path
     * @return string
     */
    public function getContent($path);
    
    /**
     * 存贮附件
     * 
     * @param string $path
     * @param string $content
     * @return bool
     */
    public function save($path, $content);
    
    /**
     * 生成附件路径
     * @param string $suffix 后缀 xxx
     * @throws \wf\storage\Exception
     * @return string
     */
    public function generatePath($suffix);

    /**
     * 上传文件
     * @param string $tempFile
     * @param string $uploadPath
     */
    public function upload($tempFile, $uploadPath);
    
    /**
     * 复制文件到附件目录
     * @param string $pathFrom 来源文件完整的路径（注意该文件路径的安全）
     * @param string $pathTo
     * @return boolean
     */
    public function copy($pathFrom, $pathTo);
    
    /**
     * 附件是否存在
     * @param string $path
     * @return boolean
     */
    public function isExist($path);
}


