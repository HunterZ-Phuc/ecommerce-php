<?php

declare(strict_types=1);

namespace Core;
//sửa ở đây point 1
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
        $route = '/^' . $route . '(?:\?.*)?$/i';

        $this->routes[$route] = $params;
    }

    public function match($url)
    {
        // Tách query string khỏi URL
        $urlParts = explode('?', $url);
        $path = $urlParts[0];

        // Nếu URL rỗng hoặc chỉ có '/', chuyển thành '/'
        if (empty($path)) {
            $path = '/';
        }

        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $path, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params['params'][$key] = $match;
                    }
                }
                return $params;
            }
        }
        return false;
    }
}