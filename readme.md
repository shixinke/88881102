#### envtool

一款轻量级环境工具,根据不同环境生成不同的.env环境配置文件

#### 基本用法

php bin/envtool --env dev --config .env --path env --example .env.example start

* --env:表示环境名称:默认为dev/test/beta/prod 4个,可以根据自己的需要要定义

* --config:设置配置文件名称

* --path:设置环境配置目录

* --example:示例配置文件目录

* --envList:环境列表设置

* start:表示执行的动作为start

#### 与composer结合

```
{
    "scripts":{
        "dev":"php bin/devtool --env dev --config .env --path env --example .env.example start",
    }
}
```

