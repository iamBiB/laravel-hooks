<?php

if (!function_exists('execute_hook'))
{
    function execute_hook($hook = null)
    {
        if ($hook != null)
        {
            return Hooks::execute_hook($hook);
        }
    }
}
