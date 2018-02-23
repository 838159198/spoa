# spoa2
晟平业务流 2.0版.

目标：无纸化办公！


[TOC]

# 一、环境准备


## 1. windows环境

## step 1. 安装nodeJs.

到(https://nodejs.org)或其他网站下载最新的nodeJs安装程序。

## step 2. npm源改为taobao源或安装cnpm.

在用户home目录中的.npmrc中添加

```
registry = https://registry.npm.taobao.org
```

## step 3. 参考[https://angular.io](https://angular.io)安装```angularJs```.

+ 用管理员身份启动cmd或PowerShell.
+ 执行
+ > npm install -g @angular/cli

## step 4. 从git获得代码副本

> git clone *仓库url*

## step 5. 更新工程

在工程目录下执行：

添加项目依赖包
> npm update --save

添加开发依赖包
> npm update --save-dev



<i>不需要用管理员身份</i>.

这个命令会根据[package.json](package.json)中的内容下载所需模块到本目录.

# 二、静态数据模拟器

静态数据模拟器在"httpserver"目录下。
在命令行中,切换当前目录到httpserver目录下，执行:

>npm start

注释：如果npm start命令启动，出现错误，可能是缺少文件导致的，把项目中生成的node_modules删掉，然后重新执行npm update --save 和 npm update --save-dev

命令，既可启动模拟器.
启动后模拟器监听"1840"端口.
向该端口发送的请求得到应答的内容在"httpserver/data"目录下，对应关系是请求URI中第一个
目录之后的路径与"httpserver/data"下的位置一致，如果请求路径没有扩展名，或扩展名无法
识别，则默认扩展名为"json".

例如：

>  http://localhost:1840/app/service/test

  对应的应答响应文件为：
 > "httpserver/data/service/test.json".

具体参见：```httpserver/server.js```.

# 三、css预编译

本项目css采用sass预编译方式生成.转换命令采用gulp工具集成.

首先执行
> npm install -g gulp

将gulp命令添加到全局环境中，否则在命令行中执行gulp会提示没有命令.


工程的gulp脚本是“gulpfile.js”文件.

执行预编译，并且监听scss文件修改需要在项目根目录执行：
> gulp

执行后程序不退出，监听scss文件改变，并且实时编译.

scss文件保存在```src/sass```目录下,编译后的文件覆盖到```src```目录下.
即:

>```src/sass/app/app.component.scss```

编译后覆盖到

>```src/app/app.component.css```

.



# 四、AngularJs自动生成的文档内容

### Development server

Run `ng serve` for a dev server. Navigate to `http://localhost:4200/`. The app will automatically reload if you change any of the source files.

### Code scaffolding

Run `ng generate component component-name` to generate a new component. You can also use `ng generate directive|pipe|service|class|guard|interface|enum|module`.

### Build

Run `ng build` to build the project. The build artifacts will be stored in the `dist/` directory. Use the `-prod` flag for a production build.

### Running unit tests

Run `ng test` to execute the unit tests via [Karma](https://karma-runner.github.io).

### Running end-to-end tests

Run `ng e2e` to execute the end-to-end tests via [Protractor](http://www.protractortest.org/).

### Further help

To get more help on the Angular CLI use `ng help` or go check out the [Angular CLI README](https://github.com/angular/angular-cli/blob/master/README.md).
