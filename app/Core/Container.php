<?php
namespace App\Core;

use App\Controllers\AuthController;
use App\Controllers\PageController;
use App\Models\UserModel;
use App\Middlewares\AuthMiddleware;
use Exception;

class Container
{
    private $instances = [];

    public function get($className)
    {
        if (!isset($this->instances[$className])) {
            $this->instances[$className] = $this->createInstance($className);
        }
        return $this->instances[$className];
    }

    private function createInstance($className)
    {
        switch ($className) {
            case 'AuthController':
                return new AuthController();
            
            case 'PageController':
                return new PageController();
            
            case 'UserModel':
                return new UserModel(Database::getInstance());
            
            case 'AuthMiddleware':
                return new AuthMiddleware();
            
            default:
                if (class_exists($className)) {
                    return new $className();
                }
                throw new Exception("Class $className not found");
        }
    }
}