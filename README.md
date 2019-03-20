# pf-widgets-captcha
## 描述
渲染部件——图片验证码，验证码模型验证

## 注意事项
- 该组件基于"qingbing/php-render"上开发运行
- "\Captcha" 必须配置在继承了"\Render\Controller"的控制器上
- 默认 ?refresh=1表示获取一个新的验证码的地址的接收回调
- 支持验证码的模型验证，验证类型为验证类名"\CaptchaSupports\CaptchaValidator"，简化为："\Captcha::VALIDATOR"
- 如果使用视图小部件"\Widgets\Captcha"，务必注意一下几点
  - 在页面头引入jquery组件

## 使用方法
### 1. 验证码控制器声明
```php
    /**
     * 定义外部action列表
     * @return array
     */
    public function actions()
    {
        return [
            'captcha' => [
                'class' => '\Captcha',
                'attribute1' => 'value1',
                'attribute...' => 'value...',
            ],
        ];
    }
```

### 2. 验证组件视图使用方法
```php
$this->widget('\Widgets\Captcha', [
    'action' => '/site/captcha',
    'alt' => '验证码',
    'attributes' => [
        'id' => 'xxxx',
        'otherAttributes' => 'otherAttributes',
    ],
]);
```

### 3. 模型验证配置
```php

class TestLoginModel extends FormModel
{
    /* 验证码 */
    public $verifyCode;

    public function rules()
    {
        return [
            ['verifyCode', \Captcha::VALIDATOR, 'captchaAction' => 'site/captcha', 'allowEmpty' => false],
        ];
    }
}
```

## ====== 异常代码集合 ======

异常代码格式：1023 - XXX - XX （组件编号 - 文件编号 - 代码内异常）
```
 - 102300101 : \CaptchaSupports\CaptchaValidator.action"{id}"无效，无法找到指定的action
```