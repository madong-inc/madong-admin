
# 文件上传插件

## 特性

#### 🍏 本地对象存储
- ✅ 本地多文件上传

#### 🍓 阿里云对象存储
- ✅ 本地多文件上传
- ✅ `Base64`图片文件上传
- ✅ 上传服务端文件

#### 🍋 腾讯云对象存储
- ✅ 本地多文件上传
- ✅ `Base64`图片文件上传
- ✅ 上传服务端文件

#### 🍇 七牛云对象存储
- ✅ 本地多文件上传
- ✅ `Base64`图片文件上传
- ✅ 上传服务端文件

## 安装

```php
composer require madong/storage
```

## 基本用法

```php
madong\storage\Storage::config(); // 初始化。 默认为本地存储：local，阿里云：oss，腾讯云：cos，七牛：qiniu
$res = madong\storage\Storage::uploadFile();
var_dump(json_encode($res));
```

### 上传成功信息

```json
[
    {
        "key": "test",
        "origin_name": "测试上传.xlsx",
        "save_name": "05419c9bdaf7a38148742c87b96b6867.xlsx",
        "save_path": "runtime/storage/05419c9bdaf7a38148742c87b96b6867.xlsx",
        "save_path": "/var/www/madong/server/public/storage/05419c9bdaf7a38148742c87b96b6867.xlsx",
        "url": "/storage/fd2d472da56c71a6da0a5251f5e1b586.png",
        "uniqid ": "05419c9bdaf7a38148742c87b96b6867",
        "size": 15050,
        "mime_type": "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        "extension": "xlsx"
    }
    ...
]
```
> 失败，抛出`StorageAdapterException`异常
### 成功响应字段

| 字段|描述| 示例值                                                                          |
|:---|:---|:-----------------------------------------------------------------------------|
|key | 上传文件key | test                                                                         |
|origin_name |原始文件名 | 测试上传.xlsx                                                                    |
|save_name |保存文件名 | 05419c9bdaf7a38148742c87b96b6867.xlsx                                        |
|save_path|文件保存路径（相对） | /var/www/madong/server/runtime/storage/05419c9bdaf7a38148742c87b96b6867.xlsx |
|url |url访问路径 | /storage/05419c9bdaf7a38148742c87b96b6867.xlsx                               |
|unique_id|uniqid | 05419c9bdaf7a38148742c87b96b6867                                             |
|size |文件大小 | 15050（字节）                                                                    |
|mime_type |文件类型 | application/vnd.openxmlformats-officedocument.spreadsheetml.sheet            |
|extension |文件扩展名 | xlsx                                                                         |
## 上传规则

默认情况下是上传到本地服务器，会在`runtime/storage`目录下面生成以当前日期为子目录，以文件流的sha1编码为文件名的文件，例如上面生成的文件名可能是：
```
runtime/storage/fd2d472da56c71a6da0a5251f5e1b586.png
```
如果你希望上传的文件是可以直接访问或者下载的话，可以使用`public`存储方式。

你可以在`config/plugin/madong/storage/app.php`配置文件中配置上传根目录，例如：

```php
'local' => [
    'adapter' => \madong\storage\adapter\LocalAdapter::class,
    'root' => public_path() . '/storage',
],

```
> 浏览器访问：http://127.0.0.1:8787/storage/fd2d472da56c71a6da0a5251f5e1b586.png

## 上传验证

支持使用验证类对上传文件的验证，包括文件大小、文件类型和后缀

| 字段|描述|示例值|
|:---|:---|:---|
|single_limit | 单个文件的大小限制，默认200M | 1024 * 1024 * 200 |
|total_limit | 所有文件的大小限制，默认200M | 1024 * 1024 * 200 |
|nums | 文件数量限制，默认10 | 10 |
|include | 被允许的文件类型列表 | ['xlsx','pdf'] |
|exclude | 不被允许的文件类型列表 | ['png','jpg'] |

## 支持上传SDK

#### 阿里云对象存储

```php
composer  require aliyuncs/oss-sdk-php
```
#### 腾讯云对象存储

```php
composer require qcloud/cos-sdk-v5
```

#### 七牛云云对象存储

```php
composer require qiniu/php-sdk
```

## 上传Base64图片

>**使用场景：** 前端直接截图（头像、Canvas等）一个Base64数据流的图片直接上传到云端

#### 请求参数

```json
{
    "extension": "png",
    "base64": "data:image/jpeg;base64,/9j/4AAQSkxxxxxxxxxxxxZJRgABvtyQBIr/MPTPTP/2Q=="
}
```
#### 请求案例（阿里云）

```php
public function upload(Request $request)
{
    Storage::config(Storage::MODE_OSS, false); // 第一个参数为存储方式。第二个参数为是否本地文件（默认是）
    $base64 = $request->post('base64');
    $r = Storage::uploadBase64($base64,'png');
    var_dump($r);
}
```

#### 响应参数
```json
{
	"save_path": "storage/2025040221363962546971439e.png",
	"url": "http://webman.oss.madong.tech/storage/2025040221363962546971439e.png",
	"unique_id": "2025040221363962546971439e",
	"size": 11872,
	"extension": "png"
}
```
## 上传服务端文件

>**使用场景：** 服务端导出文件需要上传到云端存储，或者零时下载文件存储。

#### 请求案例（阿里云）

```php
Storage::config(Storage::MODE_OSS,false);
$localFile = runtime_path() . DIRECTORY_SEPARATOR . 'storage/test.png';
$res = Storage::uploadServerFile($localFile);
```

#### 响应参数

```json
{
	"origin_name": "/var/www/madong/server/runtime/storage/test.png",
	"save_path": "storage/6edf04d7c26f020cf5e46e6457620220402213414.png",
	"url": "http://webman.oss.madong.tech/storage/6ed9ffd54d0df57620220402213414.png",
	"unique_id": "6edf04d7c26f020cf5e46e6403213414",
	"size": 3505604,
	"extension": "png"
}
```
