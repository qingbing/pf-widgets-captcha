<?php
/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2019-01-08
 * Version      :   1.0
 */

namespace Widgets;


use Abstracts\OutputProcessor;

class Captcha extends OutputProcessor
{
    /* @var array image-html 标签属性 */
    public $attributes = [];
    /* @var string 验证码的 "controller/action" */
    public $action;
    /* @var string image-alt 的值 */
    public $alt = '验证码，点击刷新';

    /**
     * 初始化，属性处理
     */
    public function init()
    {
        $this->attributes['class'] = 'IMG-CAPTCHA';
        $this->attributes['alt'] = $this->alt;
        if (!isset($this->attributes['id'])) {
            $this->attributes['id'] = md5(__CLASS__ . $this->action . time());
        }
    }

    /**
     * 运行组件
     * @throws \Exception
     */
    public function run()
    {
        echo \Html::image($this->createUrl($this->action), $this->alt, $this->attributes);
        $refreshUrl = $this->createUrl($this->action, ['refresh' => 1]);

        \ClientScript::getInstance()->registerScript($this->attributes['id'], <<<EDO
jQuery(function () {
    // 验证码点击变换
    $('#{$this->attributes['id']}').click(function (e) {
        var \$this = $(this);
        $.post('{$refreshUrl}', function (res) {
            if (0 === res.code) {
                \$this.attr('src', res.data.url);
            } else {
                alert(res.message);
            }
        }, 'json');
    });
});
EDO
        );
    }
}