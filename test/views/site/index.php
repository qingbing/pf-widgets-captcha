<?php
/* @var $this \Render\Controller */
?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="assets/jquery-3.2.1.min.js"></script>
    <script src="assets/h.js"></script>
</head>
<body>
<?php echo \Html::beginForm(); ?>
<dl>
    <dt><?php echo \Html::activeLabel($model, 'username'); ?></dt>
    <dd><?php echo \Html::activeTextField($model, 'username'); ?></dd>
</dl>
<dl>
    <dt><?php echo \Html::activeLabel($model, 'password'); ?></dt>
    <dd><?php echo \Html::activePasswordField($model, 'password'); ?></dd>
</dl>
<dl>
    <dt><?php echo \Html::activeLabel($model, 'verifyCode'); ?></dt>
    <dd>
        <?php $this->widget('\Widgets\Captcha', [
            'action' => '/site/captcha',
            'alt' => '验证码',
            'attributes' => [
                'id' => 'xxxx',
            ],
        ]); ?>
        <?php echo Html::activeTextField($model, 'verifyCode', [
            'class' => 'captcha',
            'id' => 'IMG-CAPTCHA',
        ]); ?>
    </dd>
</dl>
<dl>
    <dd><?php echo \Html::submitButton('Submit', [
            'name' => 'submit',
        ]); ?></dd>
</dl>
<dl>
    <dd><a href="<?php echo $this->createurl('destroy'); ?>" target="_blank">销毁session</a></dd>
</dl>
<?php echo \Html::endForm(); ?>
</body>
</html>