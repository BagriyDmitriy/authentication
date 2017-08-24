<?php

namespace App\Models;

use App\Helpers\DataInterface as DataInterface;
use App\Helpers\DataLoginAttempts as DataLoginAttempts;

class User
{
    protected $dataInFile;
    protected $dataLoginAttempts;
    const MAX_BLOCKING_TIME = 300;
    const MAX_BLOCKING_COUNT = 3;

    private function sessionStart()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function __construct(DataInterface $dataInFile, DataLoginAttempts $dataLoginAttempts) {
         $this->dataInFile = $dataInFile;
         $this->dataLoginAttempts = $dataLoginAttempts;
    }

    public function emptyUserAttempts($username)
    {
        $this->dataLoginAttempts->delete($username . ',' . $_SERVER['REMOTE_ADDR']);
    }

    public function processAttemptsStatus($username)
    {
        if ($status = $this->dataLoginAttempts->get($username . ',' . $_SERVER['REMOTE_ADDR'])) {
            $statusData = explode(',', $status);

            if (self::MAX_BLOCKING_TIME < (time() - $statusData[1])) {

                $this->dataLoginAttempts->update($username . ',' . $_SERVER['REMOTE_ADDR'], '1' . ',' . time());

                return array('errors' => false, 'text' => '');
            } else if (self::MAX_BLOCKING_COUNT > $statusData[0]) {

                $this->dataLoginAttempts->update($username . ',' . $_SERVER['REMOTE_ADDR'], ($statusData[0] + 1) . ',' . $statusData[1]);

                return array('errors' => false, 'text'   => '');
            } else  {

                return array(
                    'errors' => true,
                    'text'   => 'Try again after ' . (self::MAX_BLOCKING_TIME - (time() - $statusData[1])) . ' Seconds'
                );
            }

        } else {
            $this->dataLoginAttempts->set($username . ',' . $_SERVER['REMOTE_ADDR'], '1' . ',' . time());

            return array(
                'errors' => false,
                'text'   => ''
            );
        }
    }

    public function getPasswordByName($username)
    {
        return $this->dataInFile->get($username);
    }

    public function getUserName()
    {
        $this->sessionStart();

        return isset($_SESSION['username']) ? $_SESSION['username'] : '';
    }
}