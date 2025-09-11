<?php
namespace App\Core;

use App\Controllers\AuthController;
use App\Controllers\AdminController;
use App\Controllers\CosturaController;
use App\Controllers\PageController;
use App\Models\UserModel;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\RoleMiddleware;
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

            case 'AdminController':
                return new AdminController();
            
            case 'CosturaController':
                return new CosturaController();

            case 'UserModel':
                return new UserModel(Database::getInstance());
            
            case 'AuthMiddleware':
                return new AuthMiddleware();

            case 'RoleMiddleware':
                return new RoleMiddleware();
            
            default:
                if (class_exists($className)) {
                    return new $className();
                }
                throw new Exception("Class $className not found");
        }
    }
}