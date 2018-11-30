![](https://box.kancloud.cn/5a0aaa69a5ff42657b5c4715f3d49221) 

XGservice - 基于TP5的快速api构建
=========================

- [x] 自定义命令行
    - [x] 自定义创建Model,Validate,Controller,Command
    - [x] 更改默认中间件位置，并自定义创建middleware
- [x] 定制化redis类
    - [x] 新增`__call`,`redisLock`,`getInstance`
    - [x] 新增`CachekeyMap`静态类，批量管理缓存key
- [x] 架构层面功能的使用示例增加：服务提供者，门面绑定，钩子回调，中间件，路由
- [x] `ResponsData`优化，以及`facade`静态调用与`traits`继承增加
- [x] 全局验证器中间件`Validate`增加