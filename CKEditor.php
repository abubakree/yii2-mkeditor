<?php

namespace jehdu\mkeditor;

use yii\helpers\ArrayHelper;

//use iutbay\yii2kcfinder\KCFinderAsset;

class CKEditor extends \dosamigos\ckeditor\CKEditor {

    /**
     * @inheritdoc
     */
    public $filemanager = false;
    public $uploadDir = '';
    public $uploadURL = '';
    public $onChange = false;

    /**
     * Registers CKEditor plugin
     */
    protected function registerPlugin() {
        if ($this->filemanager) {
            $this->registerKCFinder();
        }
        if ($this->onChange) {
            $this->registerOnChange();
        }
        parent::registerPlugin();
    }

    /**
     * Registers KCFinder
     */
    protected function registerKCFinder() {
        $view = $this->getView();
        $kcfinder = KCFinderAsset::register($view);
        $kcfinderUrl = $kcfinder->baseUrl;

        $browseOptions = [           
            'filebrowserBrowseUrl' => $kcfinder->baseUrl . '/browse.php?opener=ckeditor&type=files',
            'filebrowserImageBrowseUrl' => $kcfinder->baseUrl . '/browse.php?opener=ckeditor&type=images',
            'filebrowserFlashBrowseUrl' => $kcfinder->baseUrl . '/browse.php?opener=ckeditor&type=flash',
            'filebrowserUploadUrl' => $kcfinder->baseUrl . '/upload.php?opener=ckeditor&type=files',
            'filebrowserImageUploadUrl' => $kcfinder->baseUrl . '/upload.php?opener=ckeditor&type=images',
            'filebrowserFlashUploadUrl' => $kcfinder->baseUrl . '/upload.php?opener=ckeditor&type=flash',
        ];
        $this->clientOptions = ArrayHelper::merge($browseOptions, $this->clientOptions);


        $config = $kcfinder->basePath . '/conf/config.php';
        $data = file_get_contents($config);
        $data = str_replace('"upload"', "'upload'", $data);
        $data = str_replace('""', "''", $data);
        $data = str_replace("'disabled' => true,", "'disabled' => false,", $data);
        $data = str_replace("'uploadURL' => 'upload',", "'uploadURL' => '{$this->uploadURL}',", $data);
        $data = str_replace("'uploadDir' => '',", "'uploadDir' => '{$this->uploadDir}',", $data);
        file_put_contents($config, $data);
    }

    /**
     * 
     * Method register editor on change
     * 
     */
    protected function registerOnChange() {
        $ckeditor = \dosamigos\ckeditor\CKEditorAsset::register($this->view);
        $plugin = __DIR__ . '/plugins';
        $this->recurse_copy($plugin, $ckeditor->basePath . '/plugins');
        $browseOptions = [
            'config.extraPlugins' => 'onchange'
        ];
        $this->clientOptions = ArrayHelper::merge($browseOptions, $this->clientOptions);
    }

    
    /**
     * 
     * @param type ต้นทาง
     * @param type ปลายทาง
     */
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

}
