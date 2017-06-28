Windwork 附件存贮组件
=========================
为兼容本地存贮和第三方云存贮平台或存贮系统，特封装存贮组件

## 使用方法
```
1. 在 config/app.php 中设置 srv.storage 组件参数
2. 调用实例方法 wfStorage()->xxx()
```

## 配置参数
```
$cfg = [
    'class'      => '\\wf\\storage\\strategy\\File',          // 附件处理 strategy
    'dir'        => 'storage',       // 附件存贮文件夹，相对于站点根目录
    'storageUrl' => 'storage',       // 附件目录url，格式：http://www.windwork.org/storage/（后面带'/'，如果附件访问网址跟附件上传站点不是同一个站时设置）
    'sizeLimit'  => '2048',          // (M)文件上传大小限制
];

```

## 创建实例
```
// 通过工厂方法创建实例
$class = {$cfg['class']};
$stor = new $class($cfg);

// 通过函数创建

// 函数定义在 wf/web/lib/helper.php
//function wfStorage() {
//    return wfStorage();
//}

$stor = wfStorage();
```

## thumb 函数
生成存储系统中缩略图的链接
```
/**
 * 获取缩略图的URL，一般在模板中使用
 * @param string|ing $path 图片路径或图片附件id
 * @param int $width = 100 为0时按高比例缩放
 * @param int $height = 0 为0时按宽比例缩放
 * @return string
 */
function thumb($path, $width = 100, $height = 0) {
    return wfStorage()->getThumbUrl($path, $width, $height);
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
    return wfStorage()->getFullUrl($path);
}
```

## storagePath 函数

```
/**
 * 根据上传文件的URL获取Path
 * @param string $url
 * @return string
 */
function storageUrl($url) {
    return wfStorage()->getPathFromUrl($url);
}
```

<br />
<br />
> ### 要了解更多？
> - [官方完整文档首页](http://docs.windwork.org/manual/)
> - [官方源码首页](https://github.com/windwork
