<?php

namespace WebSK\CRUD;

use WebSK\Config\ConfWrapper;

/**
 * Class FileManager
 * @package WebSK\CRUD
 */
class FileManager
{
    /** @var string */
    protected $root_folder;

    /**
     * FileManager constructor.
     * @param string $root_folder
     */
    public function __construct(string $root_folder = '')
    {
        if ($root_folder) {
            $this->root_folder = $root_folder;
        } else {
            $files_data_path = ConfWrapper::value('files_data_path');
            $this->root_folder = $files_data_path . DIRECTORY_SEPARATOR . $root_folder;
        }
    }

    /**
     * @param string $file_name
     * @return bool
     */
    public function removeFile(string $file_name)
    {
        $file_path = $this->getFilePath($file_name);

        if (!file_exists($file_path)) {
            return false;
        }

        return unlink($file_path);
    }

    /**
     * @param string $file_name
     * @param string $tmp_file_name
     * @param string $target_folder
     * @return string
     */
    public function storeUploadedFile(string $file_name, string $tmp_file_name, string $target_folder)
    {
        if (!\is_uploaded_file($tmp_file_name)) {
            return '';
        }

        return $this->storeFile($file_name, $tmp_file_name, $target_folder);
    }

    /**
     * @param string $file_name
     * @param string $tmp_file_name
     * @param string $target_folder
     * @return string
     */
    public function storeFile(string $file_name, string $tmp_file_name, string $target_folder)
    {
        $file_path_in_file_components_arr = [];
        if ($target_folder != '') {
            $file_path_in_file_components_arr[] = $target_folder;
        }

        $unique_filename = $this->getUniqueFileName($file_name);
        $file_path_in_file_components_arr[] = $unique_filename;

        $new_name = implode(DIRECTORY_SEPARATOR, $file_path_in_file_components_arr);

        $new_path = $this->getFilePath($new_name);

        $destination_file_path = pathinfo($new_path, PATHINFO_DIRNAME);
        if (!is_dir($destination_file_path)) {
            if (!mkdir($destination_file_path, 0777, true)) {
                throw new \Exception('Не удалось создать директорию: ' . $destination_file_path);
            }
        }

        if (!rename($tmp_file_name, $new_path)) {
            throw new \Exception('Не удалось переместить файл: ' . $tmp_file_name . ' -> ' . $new_path);
        }

        return $unique_filename;
    }

    /**
     * @param string $src_file_name
     * @return string
     */
    public function getUniqueFileName(string $src_file_name)
    {
        $ext = pathinfo($src_file_name, PATHINFO_EXTENSION);
        $file_name = str_replace(".", "", uniqid(md5($src_file_name), true)) . "." . $ext;

        return $file_name;
    }

    /**
     * @return string
     */
    public function getRootFolder()
    {
        return $this->root_folder;
    }

    /**
     * @param string $file_name
     * @return string
     */
    public function getFilePath(string $file_name)
    {
        return $this->getRootFolder() . DIRECTORY_SEPARATOR . $file_name;
    }
}
