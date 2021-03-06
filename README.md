# CSU-Information

中南信息获取爬虫，获取校内信息。爬虫包括某些绕过验证码的黑科技，可利用此库实现校内信息自动获取。

# 功能

现在包括的功能有

* 获取校内通告
* 获取最新影讯
* 获取勤工助学工资信息
* 获取今日、某日期范围内最近几条校园卡消费流水
* 获取图书馆借阅书籍
* 续借图书
* 登陆教务管理系统（教务其他功能正在建设~）
* 从教务获取学生基本信息

# 使用

举几个例子，详细还是看函数说明

## 包含

使用composer构建项目，只需要包含一个文件

```
require "vendor/autoload.php";
use CSUInformation\...
```

## 密码管理器 PasswordManager

修改src/utils/psd.json，填写账户密码。使用方式见下

## 获取当月工资信息

账户密码为校园卡卡号和校园卡查询密码

```
$t = new Salary();
$t->login('124012', '123456');
var_dump($t->getSalary());
```

使用PasswordManager

```
$p = PasswordManager::getInstance();
$t = new Salary($p);
var_dump($t->getSalary());
```

## 查看借阅图书和全部续借

这里账户密码是图书馆的

```
$t = new Library();
$t->login('0909122723', '123456');
var_dump($t->getBookLoan()); // 借阅图书
$t->renewBook(); // 全部续借
```

使用PasswordManager

```
$t = new Library($p);
var_dump($t->getBookLoan());
```

## 查看个人信息

(由于现在迎新，所以信息门户相关功能可能失效)

使用信息门户账户密码获取

```
$t = new EducationalAdmin();
$t->loginFromMyCSU('0909122723', '123456');
print_r($t->getMyInfo());
```

使用PasswordManager获取

```
$t = new EducationalAdmin($p);
print_r($t->getMyInfo());
```

# License

[GPL 2.0](http://opensource.org/licenses/GPL-2.0)