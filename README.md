# yii2-mkeditor
CKEditor and KCFinder
Mkeditor
=======
CKEditor and KCFinder

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require jehdu/yii2-mkeditor "*"
```

or add

```
"jehdu/yii2-mkeditor": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by :

Convert textarea to CKEditor
```php
<?php
//CKEditor
echo $form->field($model, 'detail')->widget(
  \jehdu\mkeditor\CKEditor::className(), 
  [
    'uploadDir' => '/var/www/public_html/UserFiles',
    'uploadURL' => '/UserFiles/',
    'filemanager'=>true, //true = enabled kcfinder, false = disabled kcfinder
    'preset'=>'full' //toolbar -> basic, standard, full
  ]
)->label(false); ?>
```
Example
<img src="http://ikhlasservice.com/uploads/capture/upload.png"/>

Usage with On Change
--------------------

```php
<?php $this->registerJs(" 
    var content = '';
    CKEDITOR.on('instanceCreated', function (e) {
    content = e.editor.getData();
      e.editor.on('change', function (ev) {
        content = ev.editor.getData();
      });
    });

"); ?>

<?php
//CKEditor
echo $form->field($model, 'detail')->widget(
  \jehdu\mkeditor\CKEditor::className(), 
  [
    'uploadDir' => '/var/www/public_html/UserFiles',
    'uploadURL' => '/UserFiles/',
    'filemanager'=>true, //true = enabled kcfinder, false = disabled kcfinder
    'preset'=>'full', //toolbar -> basic, standard, full
    'onChange' => true
  ]
)->label(false); ?>
```
Example
<img src="http://ikhlasservice.com/uploads/capture/Update Article.png"/>

