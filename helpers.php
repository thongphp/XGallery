<?php

if (!function_exists('jav_build_route_has_filter')) {
    /**
     * @param string $filterName
     * @param mixed $value
     * @param bool $isMultiple
     * @param bool $isMovie
     *
     * @return string
     */
    function jav_build_route_has_filter(
        string $filterName,
        $value,
        bool $isMultiple = true,
        bool $isMovie = true
    ): string {
        $routeParams = request()->except(['page', '_token']);

        if ($isMultiple) {
            $routeParams[$filterName][] = $value;
        } else {
            $routeParams[$filterName] = $value;
        }

        $routeName = $isMovie ? 'jav.dashboard.view' : 'jav.idols.dashboard.view';

        return route($routeName, $routeParams);
    }
}
