<?php
/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2019-01-07
 * Version      :   1.0
 */

namespace CaptchaSupports;

use Abstracts\Validator;
use Helper\Exception;

class CaptchaValidator extends Validator
{
    /* @var string 验证码错误提示 */
    public $message = '验证码不正确';
    /* @var boolean 验证码对比是否区分大小写 */
    public $caseSensitive = false; // 验证码对比是否区分大小写
    /* @var boolean 验证码的验证码所在的action路由 */
    public $captchaAction;

    /**
     * 安全规则，不需要做任何验证规则
     * @param \Abstracts\Model $object
     * @param string $attribute
     * @throws \Exception
     */
    protected function validateAttribute($object, $attribute)
    {
        $value = $object->{$attribute};
        if ($this->isEmpty($value)) {
            $this->validateEmpty($object, $attribute);
            return;
        }

        $captcha = \Captcha::getCaptchaAction($this->captchaAction);
        if (is_array($value) || !$captcha->validate($value, $this->caseSensitive)) {
            $this->addError($object, $attribute, $this->message);
        }
    }
}