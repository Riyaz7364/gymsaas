<?php
use App\Models\Invoice;
if (!function_exists('gym_route')) {
    function gym_route($name, $params = [])
    {
        $gym = auth()->user()->gym;
        // ✅ normalize to array
        if (!is_array($params)) {
            $params = [$params];
        }

        if (!$gym) {
            return route($name, $params);
        }

        // numeric array (like [$invoice])
        if (array_values($params) === $params) {
            array_unshift($params, $gym);
            return route($name, $params);
        }

        // associative array
        return route($name, array_merge(['gym' => $gym], $params));
    }
}