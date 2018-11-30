![](https://box.kancloud.cn/5a0aaa69a5ff42657b5c4715f3d49221) 

XGservice - 基于TP5的快速api构建
=========================

- [x] 目录结构调整
    - [x] 增加常量定义文件`constant.php`
    - [x] `application`更改为`app`使其命名空间更具语义化
    - [x] 调整自动注册命令目录`command`到根目录，使其`app`下的目录全为模块目录
- [x] 架构层面功能的使用示例增加：服务提供者，门面绑定，钩子回调，中间件，路由
- [x] 自定义命令行
    - [x] 自定义创建`Model`,`Validate`,`Controller`,`Command`
    - [x] 更改默认中间件位置，并自定义创建`middleware`
    - [ ] 自定义命令`build`,一键创建模块
- [x] 定制化redis类
    - [x] 新增`__call`,`redisLock`,`getInstance`
    - [x] 新增`CachekeyMap`静态类，批量管理缓存key
    - [x] 注册到app容器内
- [x] `ResponsData`优化，以及`facade`静态调用与`traits`继承增加
- [x] 全局参数验证器中间件`Validate`增加,自动验证不同接口的请求参数
- [x] 全局签名窜验证器中间件`SignCheck`增加，自动验证接口签名窜
- [x] 基于`symfony/var-dumper`的`dd`,`d`打印调试函数封装
- [ ] 定制化TP日志类