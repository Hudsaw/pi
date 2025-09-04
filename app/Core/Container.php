<?php
class Container
{
    private $instances = [];

    public function __construct()
    {
        $this->instances[Database::class] = Database::getInstance();
    }

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
                return new AuthController($this->get(UserModel::class));
            case 'PageController':
                return new PageController(
                    $this->get(UserModel::class)
                );
            case 'UserModel':
                return new UserModel($this->get(Database::class));
            case 'AuthMiddleware':
                return new AuthMiddleware($this->get(UserModel::class));
            default:
                if (class_exists($className)) {
                    return new $className();
                }
                throw new Exception("Class $className not found");
        }
    }
}