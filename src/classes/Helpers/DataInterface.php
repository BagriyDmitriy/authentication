<?php

namespace App\Helpers;

interface DataInterface
{
    public function get($key);

    public function set($key, $value);

    public function update($key, $value);

    public function delete($key);
}