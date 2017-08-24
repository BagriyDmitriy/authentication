<?php

namespace App\Helpers;

class DataInFile implements DataInterface
{
    private $file;

    public function __construct($file)
    {
        if (!file_exists($file)) {
            throw new Exception("Data file Not Found");
        }

        $this->file = $file;
    }

    public function get($key)
    {
        try {
            $data = file($this->file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        } catch (Exception $e) {
            throw new Exception("Data file read error: " . $e->getMessage());
        }

        foreach($data as $line) {
            $explode = explode(":", $line);
            if (isset($explode[0]) && isset($explode[1]) && $explode[0] == $key) {

                return $explode[1];
            }
        }

        return null;
    }

    public function set($key, $value)
    {
        // to do in next version
    }

    public function update($key, $value)
    {
        // to do in next version
    }

    public function delete($key)
    {
        // to do in next version
    }
}