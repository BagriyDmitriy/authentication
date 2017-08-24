<?php

namespace App\Helpers;

class DataLoginAttempts implements DataInterface
{
    private $file;

    public function __construct($file)
    {
        if (!file_exists($file)) {
            throw new Exception("Data login file Not Found");
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

    public function delete($key)
    {
        try {
            $data = file($this->file, FILE_IGNORE_NEW_LINES);
        } catch (Exception $e) {
            throw new Exception("Data file read error: " . $e->getMessage());
        }

        $out = array();

        foreach($data as $line) {
            $explode = explode(":", $line);
            if (empty($explode[1]) || $explode[0] == $key) {
                continue;
            } else {
                $out[] = $line;
            }
        }

        $newFileContent = implode(PHP_EOL, $out);
        try {
            $data = fopen($this->file, "w");
        } catch (Exception $e) {
            throw new Exception("Data file read error: " . $e->getMessage());
        }
        if (flock($data, LOCK_EX)){
            fwrite($data, $newFileContent);
            flock($data, LOCK_UN);
        } else {
            throw new Exception("Login attempts file blocking fail");
        }
        fclose($data);
    }

    public function update($key, $value)
    {
        try {
            $data = file($this->file, FILE_IGNORE_NEW_LINES);
        } catch (Exception $e) {
            throw new Exception("Data file read error: " . $e->getMessage());
        }

        $out = array();

        foreach($data as $line) {
            $explode = explode(":", $line);
            if (empty($explode[1])) {
                continue;
            } else if ($explode[0] == $key) {
                $out[] = $key . ':' . $value;
            } else {
                $out[] = $line;
            }
        }

        $newFileContent = implode(PHP_EOL, $out);
        try {
            $data = fopen($this->file, "w");
        } catch (Exception $e) {
            throw new Exception("Data file read error: " . $e->getMessage());
        }
        if (flock($data, LOCK_EX)){
            fwrite($data, $newFileContent);
            flock($data, LOCK_UN);
        } else {
            throw new Exception("Login attempts file blocking fail");
        }
        fclose($data);
    }

    public function set($key, $value)
    {
        try {
            $fp = fopen($this->file, 'a');
        } catch (Exception $e) {
            throw new Exception("Data file read error: " . $e->getMessage());
        }

        if (flock($fp, LOCK_EX)){
            $data = PHP_EOL . $key . ':' . $value;
            fwrite($fp, $data);
            flock($fp, LOCK_UN);
        } else {
            throw new Exception("Login attempts file blocking fail");
        }
        fclose($fp);
    }
}