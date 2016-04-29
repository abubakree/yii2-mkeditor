<?php

namespace jehdu\mkeditor;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\widgets\InputWidget;

class CKEditor extends \dosamigos\ckeditor\CKEditor {

    //use CKEditorTrait;

    /**
     * @inheritdoc
     */
    public $filemanager = false;
    public $uploadDir = '';
    public $uploadURL = '';
    public $onChange = false;

    public function init() {
        parent::init();
        $this->initOptions();

        if (empty($this->uploadDir)) {
            //$this->uploadDir = Yii::$app->project->basePath.DIRECTORY_SEPARATOR.'UserFiles';
            $this->uploadDir = Yii::getAlias('@webroot/UserFiles');
        }
        if (empty($this->uploadURL)) {
            $this->uploadURL = Yii::getAlias('@web/UserFiles');
        }
    }

    /**
     * @inheritdoc
     */
    public function run() {
        if ($this->hasModel()) {
            echo Html::activeTextarea($this->model, $this->attribute, $this->options);
        } else {
            echo Html::textarea($this->name, $this->value, $this->options);
        }
        $this->registerPlugin();
    }

    /**
     * Registers CKEditor plugin
     * @codeCoverageIgnore
     */
    protected function registerPlugin() {
        $js = [];
        $view = $this->getView();
        $ckeditor = CKEditorAsset::register($view);
        $id = $this->options['id'];

        if ($this->filemanager) {
            $kcfinder = KCFinderAsset::register($view);
            $browse = [
                'filebrowserBrowseUrl' => $kcfinder->baseUrl . '/browse.php?opener=ckeditor&type=files',
                'filebrowserImageBrowseUrl' => $kcfinder->baseUrl . '/browse.php?opener=ckeditor&type=images',
                'filebrowserFlashBrowseUrl' => $kcfinder->baseUrl . '/browse.php?opener=ckeditor&type=flash',
                'filebrowserUploadUrl' => $kcfinder->baseUrl . '/upload.php?opener=ckeditor&type=files',
                'filebrowserImageUploadUrl' => $kcfinder->baseUrl . '/upload.php?opener=ckeditor&type=images',
                'filebrowserFlashUploadUrl' => $kcfinder->baseUrl . '/upload.php?opener=ckeditor&type=flash',
            ];
            $this->clientOptions = ArrayHelper::merge($this->clientOptions, $browse);
            $kcfOptions = [
                'disabled' => false,
                'uploadDir' => $this->uploadDir,
                'uploadURL' => $this->uploadURL,
            ];
            //Yii::$app->session->set('KCFINDER', $kcfOptions);

            $config = $kcfinder->basePath . '/conf/config.php';
            $data = file_get_contents($config);
            $data = str_replace('"upload"', "'upload'", $data);
            $data = str_replace('""', "''", $data);
            $data = str_replace("'disabled' => true,", "'disabled' => false,", $data);
            $data = str_replace("'uploadURL' => 'upload',", "'uploadURL' => '{$this->uploadURL}',", $data);
            $data = str_replace("'uploadDir' => '',", "'uploadDir' => '{$this->uploadDir}',", $data);
            file_put_contents($config, $data);
        }

        if ($this->onChange) {
            $plugin = __DIR__ . '/plugins';
            $this->recurse_copy($plugin, $ckeditor->basePath . '/plugins');
            $browseOptions = [
                'config.extraPlugins' => 'onchange'
            ];
            $this->clientOptions = ArrayHelper::merge($browseOptions, $this->clientOptions);
        }


        $options = $this->clientOptions !== false && !empty($this->clientOptions) ? Json::encode($this->clientOptions) : '{}';
        $js[] = "CKEDITOR.replace('$id', $options);";
        $view->registerJs(implode("\n", $js));
    }

    private function recurse_copy($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ( $file = readdir($dir))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurse_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    private function copyr($source, $dest) {
        // recursive function to copy
        // all subdirectories and contents:
        if (is_dir($source)) {
            $dir_handle = opendir($source);
            $sourcefolder = basename($source);
            mkdir($dest . "/" . $sourcefolder);
            while ($file = readdir($dir_handle)) {
                if ($file != "." && $file != "..") {
                    if (is_dir($source . "/" . $file)) {
                        self::copyr($source . "/" . $file, $dest . "/" . $sourcefolder);
                    } else {
                        copy($source . "/" . $file, $dest . "/" . $file);
                    }
                }
            }
            closedir($dir_handle);
        } else {
            // can also handle simple copy commands
            copy($source, $dest);
        }
    }

}
