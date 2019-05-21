<?php
/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2019-01-04
 * Version      :   1.0
 */

namespace Controllers;


use Render\Abstracts\Controller;
use TestApp\Models\TestLoginModel;

class SiteController extends Controller
{
    /**
     * 定义外部action列表
     * @return array
     */
    public function actions()
    {
        return [
            'captcha' => [
                'class' => '\Captcha',
            ],
            'test' => [
                'class' => '\Captcha',
            ],
        ];
    }

    /**
     * @throws \Exception
     * @throws \ReflectionException
     */
    public function actionIndex()
    {
        $model = new TestLoginModel();
        if (isset($_POST['submit'])) {
            $model->setAttributes($_POST['TestLoginModel']);
            if ($model->validate()) {
                $this->success('验证成功');
            } else {
                $this->failure('验证失败', $model->getErrors());
            }
        }
        $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * test 清理验证session
     * @throws \Exception
     */
    public function actionDestroy()
    {
        \Captcha::getCaptchaAction('site/captcha')->destroy();
    }
}