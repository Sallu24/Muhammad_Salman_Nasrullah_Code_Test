<?php

if (!function_exists('getAdminRoleId')) {
    function getAdminRoleId()
    {
        return env('ADMIN_ROLE_ID');
    }
}

if (!function_exists('getSuperAdminRoleId')) {
    function getSuperAdminRoleId()
    {
        return env('SUPERADMIN_ROLE_ID');
    }
}

if (!function_exists('getCustomerRoleId')) {
    function getCustomerRoleId()
    {
        return env('CUSTOMER_ROLE_ID');
    }
}

if (!function_exists('getDateDifference')) {
    function getDateDifference($due, $completedDate)
    {
        $start = date_create($due);
        $end = date_create($completedDate);
        return date_diff($end, $start);
    }
}
