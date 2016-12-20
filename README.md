Windwork 附件存贮组件
=========================
为兼容本地存贮和第三方云存贮平台或存贮系统，特封装存贮组件

[开发文档](http://www.windwork.org/manual/wf.storage.html)

## 配置参数
```
$cfg = [

	'storage_dir'        => 'storage',       // 附件存贮文件夹，相对于站点根目录
	'storage_site_url'   => 'storage',       // 附件目录url，格式：http://www.windwork.org/（后面带'/'，如果附件访问网址跟附件上传站点不是同一个站时设置）
    'storage_size_limit' => '2048',               // (K)文件上传大小限制
	'storage_adapter'    => 'File',               // 附件处理 adapter


    'storage_adapter'  => 'File',
    'storage_dir'      => 'storage_dir/', // 附件存贮文件夹，相对于站点根目录
    'base_url'         => 'http://stor.demo.com/upload/index.php?',
    'host_info'        => 'http://stor.demo.com/',
    'base_path'        => '/upload/',
    'storage_site_url' => '',//http://stor.demo.com/upload/storage_dir/',
];

```

## 创建实例
```
// 通过工厂方法创建实例
$stor = \wf\storage\StorageFactory::create($cfg);

// 通过函数创建

// 函数定义在 wf/helper/lib/functions.php
//function storage() {
//    return \wf\storage\StorageFactory::create(cfg());
//}

$stor = storage();
```

## thumb 函数
```
/**
 * 获取缩略图的URL，一般在模板中使用
 * @param string|ing $path 图片路径或图片附件id
 * @param int $width = 100 为0时按高比例缩放
 * @param int $height = 0 为0时按宽比例缩放
 * @return string
 */
function thumb($path, $width = 100, $height = 0) {
    return \wf\storage\StorageFactory::create(cfg())->getThumbUrl($path, $width, $height);
}
```
## storageUrl 函数

```
/**
 * 根据上传文件的Path获取完整URL
 * @param string $path
 * @return string
 */
function storageUrl($path) {
    return \wf\storage\StorageFactory::create(cfg())->getFullUrl($path);
}
```