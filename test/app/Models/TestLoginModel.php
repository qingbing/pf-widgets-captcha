<?php
/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2019-01-07
 * Version      :   1.0
 */

namespace TestApp\Models;


use Abstracts\FormModel;

class TestLoginModel extends FormModel
{
    /* 用户名 */
    public $username;
    /* 登录密码 */
    public $password;
    /* 验证码 */
    public $verifyCode;

    public function rules()
    {
        return [
            ['username', 'username', 'allowEmpty' => false],
            ['password', 'password', 'allowEmpty' => false],
            ['verifyCode', \Captcha::VALIDATOR, 'captchaAction' => 'site/captcha', 'allowEmpty' => false],
        ];
    }
}