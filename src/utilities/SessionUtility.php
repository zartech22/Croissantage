<?php

namespace Src\Utilities;

class SessionUtility
{
    private const FLASH_PREFIX = '_flashBag';

    public function __construct() {}

    public function start()
    {
        if(session_status() !== PHP_SESSION_ACTIVE)
            session_start();
    }

    public function destroy() : void
    {
        session_destroy();
    }

    public function has(string $var) : bool
    {
        return isset($_SESSION[$var]);
    }

    public function get(string $var)
    {
        return (isset($_SESSION[$var]) ? $_SESSION[$var] : false);
    }

    public function set(string $var, $val) : void
    {
        $_SESSION[$var] = $val;
    }

    public function addFlash(string $key, $value) : void
    {
        $_SESSION[self::FLASH_PREFIX][$key] = $value;
    }

    public function hasFlash(string $key) : bool
    {
        return isset($_SESSION[self::FLASH_PREFIX][$key]);
    }

    public function getFlash(string $key)
    {
        $msg = false;

        if(isset($_SESSION[self::FLASH_PREFIX][$key]))
        {
            $msg = $_SESSION[self::FLASH_PREFIX][$key];
            unset($_SESSION[self::FLASH_PREFIX][$key]);
        }

        return $msg;
    }

    public function unset($var)
    {
        unset($_SESSION[$var]);
    }
}