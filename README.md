# 龙德在线



[龙德文档]()

说明
-------------
- 项目基于[Lumen5.0](https://lumen.laravel.com/)框架编写
- 系统运行环境：
  -	PHP >= 7.2
  -	OpenSSL PHP Extension
  - PDO PHP Extension
  - Mbstring PHP Extension
- 协作文档：[Wiki]()

项目目录结构
-------------
注：
1. 其中带有`*`的部分需要重点理解与使用
2. 未来项目稳定后，将补充测试脚本`tests`(含单元测试、功能测试、UI测试)

```php
.
├── assets
├── codeception.yml
├── commands // * cli控制器
├── config // * 配置
├── controllers // * web控制器
├── helpers // 自定义辅助函数
├── mail // 邮件模板
├── messages // i18n多语言配置
├── migrations // * 数据库迁移
├── models // * 数据表模型
├── modules // * 模块
├── runtime // 运行时日志
├── tests // 自动化测试
├── traits$_COOKIE['variable']
├── vendor
├── views // 视图
├── web // web入口
├── widgets
├── wxapp // 小程序
│   └── school
└── yii // cli入口
```









