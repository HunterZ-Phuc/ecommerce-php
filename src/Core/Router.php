<?php

declare(strict_types=1);

namespace Core;

class Router
{
    protected $routes = [];

    public function __construct()
    {
        // Không yêu cầu tham số nữa
    }

    public function add($route, $params = [])
    {
        // Chuyển đổi route pattern thành regex
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[^\/]+)', $route);
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
        $route = '/^' . $route . '$/i';

        $this->routes[$route] = $params;
    }

    public function match($url)
    {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                // Lấy các tham số động từ URL
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        if (!isset($params['params'])) {
                            $params['params'] = [];
                        }
                        $params['params'][$key] = $match;
                    }
                }
                return $params;
            }
        }
        return false;
    }
}