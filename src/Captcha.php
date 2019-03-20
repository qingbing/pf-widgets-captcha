<?php

use Abstracts\Action;
use Helper\JsonOutput;

/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2019-01-02
 * Version      :   1.0
 */
class Captcha extends Action
{
    /* @var string 验证码模型验证类 */
    const VALIDATOR = '\CaptchaSupports\CaptchaValidator';
    /* @var string GET参数，当设置时指示是否重新生成 captcha 图像 */
    public $keyVar = 'refresh';
    /* @var string 标记前缀 */
    public $keyPrefix = 'PF_CAPTCHA';
    /* @var int 宽度（px） */
    public $width = 120;
    /* @var int 高度（px） */
    public $height = 50;
    /* @var int 文字内边距 */
    public $padding = 2;
    /* @var string 背景颜色 */
    public $backColor = 0xFFFFFF;
    /* @var string 文字颜色 */
    public $foreColor = 0x2040A0;
    /* @var boolean 是否采用透明背景 */
    public $transparent = false;
    /* @var int 文字最小长度 */
    public $minLength = 4;
    /* @var int 文字最大长度 */
    public $maxLength = 6;
    /* @var int 文字字符间距 */
    public $offset = -2;
    /* @var string 文字字体文件 */
    public $fontFile;
    /* @var string 当设置时，验证码将以设定的值返回，主要用于测试结算 */
    public $fixedVerifyCode;
    /* @var int 验证码有效的验证次数 */
    public $testLimit = 3;

    /**
     * 是否刷新
     * @return bool
     * @throws \Exception
     */
    protected function isRefresh()
    {
        $app = $this->getController()->getApp();
        /* @var $app \Web\Application */
        $refresh = $app->getRequest()->getParam($this->keyVar);
        return !!$refresh;
    }

    /**
     * 运行action主体
     * @throws \Exception
     */
    public function run()
    {
        if ($this->isRefresh()) // AJAX request for regenerating code
        {
            $code = $this->getVerifyCode(true);
            $controller = $this->getController();
            /* @var \Render\Abstracts\Controller $controller */
            JsonOutput::success([
                'hash1' => $this->generateValidationHash($code),
                'hash2' => $this->generateValidationHash(strtolower($code)),
                // we add a random 'v' parameter so that FireFox can refresh the image
                // when src attribute of image tag is changed
                'url' => $controller->createUrl($this->getId(), ['v' => uniqid()]),
            ], '验证码刷新');
        } else {
            $this->renderImage($this->getVerifyCode($this->isRefresh()));
        }
    }

    /**
     * 获取或创建验证码字符
     * @param bool $regenerate
     * @return string
     * @throws \Exception
     */
    public function getVerifyCode($regenerate = false)
    {
        if (null !== $this->fixedVerifyCode) {
            return $this->fixedVerifyCode;
        }
        $session = $this->getController()->getApp()->getSession();
        /* @var $session \Components\Session */
        $session->open();
        $name = $this->getSessionKey();
        if (null === $session->get($name) || $regenerate) {
            $session->set($name, $this->generateVerifyCode());
            $session->set($name . '.count', 1);
        }
        return $session->get($name);
    }

    /**
     * 获取当前captcha的值
     * @return string
     */
    protected function getSessionKey()
    {
        return implode('.', [$this->keyPrefix, $this->getController()->getId(), $this->getId()]);
    }

    /**
     * 创建验证码
     * @return string
     */
    protected function generateVerifyCode()
    {
        if ($this->minLength > $this->maxLength) {
            $this->maxLength = $this->minLength;
        }
        if ($this->minLength < 3) {
            $this->minLength = 3;
        }
        if ($this->maxLength > 20) {
            $this->maxLength = 20;
        }
        $length = mt_rand($this->minLength, $this->maxLength);

        $letters = 'bcdfghjklmnpqrstvwxyz';
        $vowels = 'aeiou';
        $code = '';
        for ($i = 0; $i < $length; ++$i) {
            if ($i % 2 && mt_rand(0, 10) > 2 || !($i % 2) && mt_rand(0, 10) > 9)
                $code .= $vowels[mt_rand(0, 4)];
            else
                $code .= $letters[mt_rand(0, 20)];
        }
        return $code;
    }

    /**
     * 绘制验证码图片
     * @param string $code
     */
    protected function renderImage($code)
    {
        $img = imagecreatetruecolor($this->width, $this->height); // 创建真彩图片画布

        $backColor = imagecolorallocate($img,
            (int)($this->backColor % 0x1000000 / 0x10000),
            (int)($this->backColor % 0x10000 / 0x100),
            $this->backColor % 0x100); // 获取颜色（背景）
        imagefilledrectangle($img, 0, 0, $this->width, $this->height, $backColor); // 画长方形
        imagecolordeallocate($img, $backColor); // 填充

        if ($this->transparent) {
            imagecolortransparent($img, $backColor); // 将画布设置为透明
        }

        $foreColor = imagecolorallocate($img,
            (int)($this->foreColor % 0x1000000 / 0x10000),
            (int)($this->foreColor % 0x10000 / 0x100),
            $this->foreColor % 0x100); // 获取颜色（前景）

        if ($this->fontFile === null) {
            $this->fontFile = dirname(__FILE__) . '/resource/SpicyRice.ttf';
        }

        $length = strlen($code);
        $box = imagettfbbox(30, 0, $this->fontFile, $code);
        $w = $box[4] - $box[0] + $this->offset * ($length - 1);
        $h = $box[1] - $box[5];
        $scale = min(($this->width - $this->padding * 2) / $w, ($this->height - $this->padding * 2) / $h);
        $x = 10;
        $y = round($this->height * 27 / 40);
        for ($i = 0; $i < $length; ++$i) {
            $fontSize = (int)(rand(26, 32) * $scale * 0.8);
            $angle = rand(-10, 10);
            $letter = $code[$i];
            $box = imagettftext($img, $fontSize, $angle, $x, $y, $foreColor, $this->fontFile, $letter);
            $x = $box[2] + $this->offset;
        }

        imagecolordeallocate($img, $foreColor); // 填充

        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Transfer-Encoding: binary');
        header("Content-Type: image/png");
        imagepng($img);
        imagedestroy($img);
    }

    /**
     * 为验证字符串生成HASH值
     * @param string $code
     * @return string
     */
    protected function generateValidationHash($code)
    {
        for ($h = 0, $i = strlen($code) - 1; $i >= 0; --$i)
            $h += ord($code[$i]);
        return $h;
    }

    /**
     * 验证码的model对比验证
     * @param string $input user input
     * @param bool $caseSensitive
     * @return bool
     * @throws \Exception
     */
    public function validate($input, $caseSensitive)
    {
        $code = $this->getVerifyCode();
        $valid = $caseSensitive ? ($input === $code) : strcasecmp($input, $code) === 0;
        $session = $this->getController()->getApp()->getSession();
        /* @var $session \Components\Session */
        $session->open();
        $name = $this->getSessionKey();
        $countKey = $name . '.count';
        $session->set($countKey, $session->get($countKey) + 1);
        if ($session->get($countKey) > $this->testLimit && $this->testLimit > 0) {
            $session->set($name, null);
        }
        return $valid;
    }
}