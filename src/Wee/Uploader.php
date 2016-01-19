<?php
/**
 * @author miaokuan
 */

namespace Wee;

use Wee\Exception;
use Wee\Str;

class Uploader
{
    /**
     * 只允许上传图片文件
     * @var boolean
     */
    protected $onlyAllowImage = true;

    /**
     * 允许的文件后缀
     * @var array
     */
    protected $allowFileExtArr = array('gif', 'jpg', 'jpeg', 'png', 'bmp', 'rar', 'zip', 'txt');

    /**
     * 允许的图片后缀
     * @var array
     */
    protected $allowImageExtArr = array('gif', 'jpg', 'jpeg', 'png', 'bmp');

    /**
     * 最大文件
     * @var integer
     */
    protected $maxFileSize = 1073741824;

    /**
     * 待上传的文件
     * @var array
     */
    protected $fileArr = array();

    /**
     * 已上传的文件
     * @var array
     */
    protected $uploadedFileArr = array();

    /**
     * 上传结果
     * @var array
     */
    protected $resultArr = array();

    protected $savePath;

    /**
     * 文件上传目录
     * @var string
     */
    protected $fileDir;

    public function __construct($name, array $descriptionArr = array())
    {
        $this->fileDir = VAR_DIR . '/upload';

        $noFileArr = array();
        if (is_array($_FILES[$name])) {
            foreach ($_FILES[$name] as $key => $val) {
                foreach ($val as $k => $v) {
                    if ($v == UPLOAD_ERR_NO_FILE) {
                        $noFileArr[] = $k;
                    }
                    $this->fileArr[$k][$key] = $v;
                    $this->fileArr[$k]['description'] = empty($descriptionArr[$k]) ?
                    '' : $descriptionArr[$k];
                }
            }
        }

        foreach ($noFileArr as $k) {
            unset($this->fileArr[$k]);
        }
    }

    public function setFileDir($dir)
    {
        $this->fileDir = $dir;
        return $this;
    }

    public function upload()
    {
        $this->savePath = '/' . date('Ym');

        if (!is_dir($this->fileDir . $this->savePath) &&
            !mkdir($this->fileDir . $this->savePath)) {
            throw new Exception(sprintf('目录 %s 不存在', $this->fileDir .
                $this->savePath), 403);
        }

        @chmod($this->fileDir . $this->savePath);

        if (!is_writeable($this->fileDir . $this->savePath)) {
            throw new Exception(sprintf('目录 %s 不可写', $this->fileDir .
                $this->savePath), 403);
        }

        foreach ($this->fileArr as $k => $file) {
            if (!self::isUploadedFile($file['tmp_name'])) {
                $this->resultArr[$k]['errno'] = 1;
                $this->resultArr[$k]['description'] = '文件上传失败';
                continue;
            }

            $suffix = Str::suffix($file['name']);
            $isImage = in_array($suffix, $this->allowImageExtArr);

            if ($this->onlyAllowImage && !$isImage) {
                $this->resultArr[$k]['errno'] = 2;
                $this->resultArr[$k]['description'] = '不允许上传非图片类型文件';
                continue;
            }

            if (!in_array($suffix, $this->allowFileExtArr)) {
                $this->resultArr[$k]['errno'] = 3;
                $this->resultArr[$k]['description'] = '类型文件不允许';
                continue;
            }

            if ($file['size'] > $this->maxFileSize) {
                $this->resultArr[$k]['errno'] = 4;
                $this->resultArr[$k]['description'] = '文件大小超过限制';
                continue;
            }

            $filename = date('d') . Str::random(6) . '.' . $suffix;
            $target = $this->fileDir . $this->savePath . '/' . $filename;

            if (move_uploaded_file($file['tmp_name'], $target) || @copy($file['tmp_name'], $target)) {
                $this->resultArr[$k]['errno'] = 0;
                $this->resultArr[$k]['description'] = '文件上传成功';

                $this->uploadedFileArr[] = array(
                    'name' => $file['name'],
                    'url' => $this->savePath . '/' . $filename,
                    'type' => $file['type'],
                    'size' => $file['size'],
                    'description' => $file['description'],
                    'is_image' => $isImage,
                );
            } else {
                $this->resultArr[$k]['errno'] = 5;
                $this->resultArr[$k]['description'] = '文件上传失败';
            }

        }

        return $this;
    }

    public static function isUploadedFile($file)
    {
        return is_uploaded_file(str_replace('\\\\', '\\', $file));
    }

    public function setOnlyAllowImage()
    {
        $this->onlyAllowImage = true;
        return $this;
    }

    public function getResult()
    {
        return $this->resultArr;
    }

    public function getUploadedFile()
    {
        return $this->uploadedFileArr;
    }

}
