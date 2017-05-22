<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
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
 * 如果附件服务器使用和网站不一样的域名，在配置文件中设置附件网址(storage.siteUrl)，如新浪云的Storage存贮
 * @package     wf.storage
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.storage.html
 * @since       0.1.0
 */
abstract class AStorage
{
    /**
     * 
     * @var array
     */
    protected $cfg;
    
    /**
     * 存贮站点根目录
     * 
     * @var string
     */
    protected $storageDir = '';
    
    /**
     * 附件文件夹网址
     * 
     * @var string
     */
    protected $storageUrl = '';

    /**
     * 构造函数设置附件路径及附件站点URL
     * @param array $cfg
     */
    public function __construct(array $cfg)
    {
        $this->cfg = $cfg;
        
        // 从配置注入存贮目录和存贮站点URL
        $storageUrl = $cfg['siteUrl'];
        $storageUrl || $storageUrl = basename($cfg['dir']);
        $storageDir = realpath(getcwd()).'/'.$cfg['dir'];
           
        if(!storageDir) {
            throw new \wf\storage\Exception('附件目录（' . $storageDir . '）不存在！');
        }
        
        $this->storageDir = $storageDir;
        $this->storageUrl = rtrim($storageUrl, '/') . '/';
    }
    
    /**
     * 获取存贮目录
     * @return string
     */
    public function getStorageDir()
    {
        return $this->storageDir;
    }
    
    /**
     * 获取附件存贮站点网址
     */
    public function getStorageUrl()
    {
        return $this->storageUrl;
    }
            
    /**
     * 获取附件的路径
     * 使用两种路径：相对路径和完整路径
     * 当程序设置使用完整路径的时候就全部使用完整路径，否则在PHP脚本中用header跳转的，一律用完整路径，前端页面用相对路径
     * 
     * @param string $path
     * @return string
     */
    public function getUrl($path)
    {
        $path = $this->getPathFromUrl($path);
        $url = $this->storageUrl . $path;
        
        return $url;
    }
    
    /**
     * 从URL获取文件存贮的path
     * @param string $url
     * @return string
     */
    public function getPathFromUrl($url)
    {
        $basename = $this->storageUrl ? basename($this->storageUrl) : basename($this->storageDir);
        $basename && $url = preg_replace("/(.*$basename\/)/", '', $url);
        return $url;
    }
    
    /**
     * 获取附件完整网址
     * 
     * @param string $path
     * @return string
     */
    public function getFullUrl($path)
    {
        $url = $this->getUrl($path);
                        
        if(!preg_match("/^(\\w+)\\:\\/\\//", $url)) {   
            $basePath = substr($_SERVER["REQUEST_URI"], 0, strripos($_SERVER["REQUEST_URI"], basename($this->storageDir)));
            $domain = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
            $domain .= htmlspecialchars($_SERVER["HTTP_HOST"]);            
            $url = "{$domain}{$basePath}{$url}";
        }
        
        return $url;        
    }
    
    /**
     * 获取上传文件（在存贮介质上）的真实路径 {$wrapper}{$path}，实际文件不存在时也有值返回
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
        $path = "{$this->storageDir}{$path}";
        
        return $path;
    }
    
    /**
     * 获取缩略图相对URL，在显示图片时使用
     *
     * @param int $imgId
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getThumbUrl($path, $width = 100, $height = 100)
    {
        if (!$path) {
            $url = $this->cfg['noPicUrl'];
            if (!$url) {
                throw new Exception('请设置noPicUrl配置选项');
            }
        } else {
            $path = $this->getThumbPath($path, $width, $height);
            $url = $this->getFullUrl($path);
        }
        
        return $url;
    }
    
    /**
     * 删除附件
     * @param string $path
     */
    abstract public function remove($path);
    
    /**
     * 删除缩略图
     * 
     * @param string $path 缩略图路径
     */
    abstract public function removeThumb($path);
    
    /**
     * 删除所有缩略图
     *
     */
    abstract public function clearThumb();
    
    /**
     * 获取缩略图路径
     * 
     * @param string $path
     * @param int $width
     * @param int $height
     */
    public function getThumbPath($path, $width, $height)
    {
        $path = $this->getPathFromUrl($path);
        
        $id = base64_encode($path) . '$' . base64_encode("{$width}x{$height}");
        $sub = sprintf("%x", crc32($path));
        $path = "thumb/{$sub[0]}{$sub[1]}/{$sub[2]}{$sub[3]}/{$id}.jpg";
        
        return $path;
    }
    
    /**
     * 文件上传的附件是否是保存为静态文件，
     * 建议可在继承类中覆盖该方法
     * 
     * @return boolean
     */
    public function isStatic()
    {
        $isStatic = false;
        
        // 存储路径不是以xxx://开头或是以(ftp|http|file|https|saestor)://开头则视附件为静态文件
        if(!preg_match("/^(\\w+)\\:\\/\\//", $this->storageDir, $match) || 
          in_array($match[1], array('ftp', 'http', 'file', 'https', 'saestor'))) {
            $isStatic = true;
        }
        
        return $isStatic;
    }
        
    /**
     * 载入文件以显示
     * 
     * @throws \wf\storage\Exception
     * @param string $path
     */
    public function load($path)
    {
        $path = $this->safePath($path);
        
        if(!$this->isExist($path)) {
            throw new Exception('附件不存在。', 404);
        }
        
        if ($this->isStatic()) {
            header('Location: '.$this->getFullUrl($path));
            return true;
        } else {
                
            $pos = strrpos($path, '.');
            
            if(false !== $pos) {
                $mimes = array(
                    'css'  => 'text/css',
                    'js'   => 'text/javascript',
                    'htm'  => 'text/html',
                    'html' => 'text/html',
                    'jpg'  => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'gif'  => 'image/gif',
                    'png'  => 'image/png',
                    'tiff' => 'image/tiff',
                    'tif'  => 'image/tif',
                    'ico'  => 'image/ico',
                    'svg'  => 'image/svg+xml',
                    'pdf'  => 'application/pdf',
                    'doc'  => 'application/msword',
                    'rtf'  => 'application/rtf',
                    'xls'  => 'application/vnd.ms-excel',
                    'ppt'  => 'application/vnd.ms-powerpoint',
                    'rar'  => 'application/x-rar-compressed',
                    'swf'  => 'application/x-shockwave-flash',
                    'zip'  => 'application/zip',
                    'msi'  => 'application/octet-stream',
                    'exe'  => 'application/octet-stream',
                    'mid'  => 'application/midi',
                    'midi' => 'application/midi',
                    'kar'  => 'application/midi',
                    'mp3'  => 'application/mpeg',
                    '3gp'  => 'application/3gpp',
                    '3gpp' => 'application/3gpp',
                    'mpg'  => 'application/mpeg',
                    'mpeg' => 'application/mpeg',
                    'mov'  => 'application/quicktime',
                    'flv'  => 'application/x-flv',
                    'mng'  => 'application/x-mng',
                    'asx'  => 'application/x-ms-asx',
                    'asf'  => 'application/x-ms-asf',
                    'wmv'  => 'application/x-ms-wmv',
                    'avi'  => 'application/x-ms-avi',
                );
            
                $ext = substr($path, $pos+1);
            
                if(isset($mimes[$ext])) {
                    $mime = $mimes[$ext];
                } else {
                    $mime = 'application/octet-stream';
                }
                
                header("Content-Type: {$mime}");
            }
            
            header('Cache-Control: public, max-age=2592000');
            header('Pragma: public');
            header('Expires: '.date('D, d M Y H:i:s', time()+2592000). ' GMT');
            header('Last-Modified: '.date('D, d M Y H:i:s', mktime(0, 0, 1, 1, 1, 2000)). ' GMT');
            
            print $this->getContent($path);
            
            return true;
        }
    }
    
    /**
     * 获取安全文件路径名
     * @param string $path
     * @return string
     */
    public function safePath($path)
    {
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('/(\.+\\/)/', './', $path);
        $path = preg_replace('/(\\/+)/', '/', $path);
        return $path;
    }

    /**
     * 读取内容
     *
     * @param string $path
     * @return string
     */
    abstract public function getContent($path);
    
    /**
     * 存贮附件
     * 
     * @param string $path
     * @param string $content
     * @return bool
     */
    abstract public function save($path, $content);
    
    /**
     * 生成附件路径
     * @param string $suffix 后缀 xxx
     * @throws \wf\storage\Exception
     * @return string
     */
    public function generatePath($suffix)
    {
        if (!$suffix) {
            throw new Exception('请设置后缀名参数');
        }
        
        $path = date($this->cfg['subdirFormat']);
        $path = $path . '/' . sprintf("%08x%x%x", crc32(uniqid()), mt_rand(0x1000, 0xffff), mt_rand(0x1000, 0xffff)) . '.' . ltrim($suffix, '.');
        
        if($this->isExist($path)) {
            $path = $this->generatePath($suffix);
        }
        
        return $path;
    }

    /**
     * 上传文件
     * @param string $tempFile
     * @param string $uploadPath
     */
    abstract public function upload($tempFile, $uploadPath);
    
    /**
     * 复制文件到附件目录
     * @param string $pathFrom 来源文件完整的路径（注意该文件路径的安全）
     * @param string $pathTo
     * @return boolean
     */
    abstract public function copy($pathFrom, $pathTo);
    
    /**
     * 附件是否存在
     * @param string $path
     * @return boolean
     */
    abstract public function isExist($path);
}


