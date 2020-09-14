<?php

if (! function_exists('jav_build_route_with_filter_param')) {
    /**
     * @param string $filterName
     * @param mixed $value
     * @param bool $isMultiple
     *
     * @return string
     */
    function jav_build_route_with_filter_param(string $filterName, $value, bool $isMultiple = true): string
    {
        $routeParams = request()->except(['page', '_token']);

        if ($isMultiple) {
            $routeParams[$filterName][] = $value;
        } else {
            $routeParams[$filterName] = $value;
        }

        return route('jav.dashboard.view', $routeParams);
    }
}
