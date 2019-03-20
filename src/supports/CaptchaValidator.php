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
        $captcha = $this->getCaptchaAction();
        if (is_array($value) || !$captcha->validate($value, $this->caseSensitive)) {
            $this->addError($object, $attribute, $this->message);
        }
    }

    /**
     * 返回验证码所在的"action"
     * @return \Abstracts\Action|\Captcha
     * @throws \Exception
     */
    protected function getCaptchaAction()
    {
        list($controller, $actionID) = \PF::app()->createController(trim($this->captchaAction, '/'));
        /* @var \Render\Abstracts\Controller $controller */
        if (null === $action = $controller->createAction($actionID)) {
            throw new Exception(str_cover('\CaptchaSupports\CaptchaValidator.action"{id}"无效，无法找到指定的action', [
                '{id}' => $this->captchaAction
            ]), 102300101);
        }
        return $action;
    }
}