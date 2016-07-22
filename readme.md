Windwork 附件存贮组件
=========================
为兼容本地存贮和第三方云存贮平台或存贮系统，特封装存贮组件

[开发文档](http://www.windwork.org/manual/wf.storage.html)

# 使用前必须初始化参数
```
$cfg = array(
    'storage_adapter'  => 'File',
    'storage_dir'      => 'storage_dir/', // 附件存贮文件夹，相对于站点根目录
    'base_url'         => 'http://stor.demo.com/upload/index.php?',
    'host_info'        => 'http://stor.demo.com/',
    'base_path'        => '/upload/',
    'storage_site_url' => '',//http://stor.demo.com/upload/storage_dir/',
);
StorageFactory::init($cfg);
```

# thumb 函数
```
/**
 * 获取缩略图的URL，一般在模板中使用
 * @param string|ing $path 图片路径或图片附件id
 * @param int $width = 100 为0时按高比例缩放
 * @param int $height = 0 为0时按宽比例缩放
 * @return string
 */
function thumb($path, $width = 100, $height = 0) {
    return \wf\storage\StorageFactory::create()->getThumbUrl($path, $width, $height);
}
```
# pathUrl 函数

```
/**
 * 根据上传文件的Path获取完整URL
 * @param string $path
 * @return string
 */
function pathUrl($path) {
    return \wf\storage\StorageFactory::create()->getFullUrl($path);
}
```