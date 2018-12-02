![](https://box.kancloud.cn/5a0aaa69a5ff42657b5c4715f3d49221) 

XGservice - 基于TP5的快速api构建
=========================

- [x] 目录结构调整
    - [x] 增加常量定义文件`constant.php`
    - [x] `application`更改为`app`使其命名空间更具语义化
    - [x] 调整自动注册命令目录`command`到根目录，使其`app`下的目录全为模块目录
- [x] 架构层面功能的使用示例增加：服务提供者，门面绑定，钩子回调，中间件，路由
- [x] 新增JWT封装类，增加其门面调用
- [x] 自定义命令行
    - [x] 自定义创建`Model`,`Validate`,`Controller`,`Command`
    - [x] 更改默认中间件位置，并自定义创建`middleware`
    - [ ] 自定义命令`build`,一键创建模块
- [x] 定制化redis类
    - [x] 新增`__call`,`redisLock`,`getInstance`
    - [x] 新增`CachekeyMap`静态类，规范化并批量管理缓存key
    - [x] 自动注册到app容器内
- [ ] 中间件增加
    - [x] 全局参数验证器中间件`Validate`增加,自动验证不同接口的请求参数
    - [x] 全局签名窜验证器中间件`SignCheck`增加，自动验证接口签名窜
    - [ ] JWT中间件增加，在路由分组中调用验证会员信息
- [x] `ResponsData`优化，以及`facade`静态调用与`traits`继承增加
- [x] 基于`symfony/var-dumper`的`dd`,`d`打印调试函数封装: 多参数打印，track打印(加`[]`退出)，json打印，
- [ ] 自定义TP日志适配器
    - [x] 可多参数形式传入
    - [x] 增加日志警报器，error级别日志自动发邮件通知
    - [ ] 接入seasLog驱动
    - [ ] 增加ELK架构，实时监控日志
- [ ] GRPC封装
- [ ] rabbitMQ客户端与服务端封装
- [ ] workerman示例封装
- [ ] 文件上传封装（本地上传，OSS上传） **杨立东**
- [ ] 邮件发送封装  **杨立东**
- [ ] excel，csv 导入导出封装  **刘兆伦**
- [ ] SNS第三方登录封装 **刘兆伦**
- [ ] PHPtask任务管理封装 **杨立东**
- [ ] 微信常用功能封装 **刘兆伦**
- [ ] http client和curl的封装 **刘兆伦**
- [ ] 基于elasticsearch的搜索引擎封装
- [ ] PHP多进程爬虫封装
- [ ] swoole示例封装
- [ ] 支付示例封装
- [ ] 远程调试示例
- [ ] 接口文档自动生成