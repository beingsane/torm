<?php
/**
 * Methods to use with validations
 *
 * PHP version 5.5
 *
 * @category Validations
 * @package  TORM
 * @author   Eustáquio Rangel <taq@bluefish.com.br>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     http://github.com/taq/torm
 */
namespace TORM;

trait Scopes
{
    /**
     * Create a scope
     *
     * @param string $name       of scope
     * @param mixed  $conditions to avail
     *
     * @return null
     */
    public static function scope($name, $conditions)
    {
        $cls = get_called_class();

        if (!array_key_exists($cls, self::$_scopes)) {
            self::$_scopes[$cls] = array();
        }

        if (!array_key_exists($name, self::$_scopes[$cls])) {
            self::$_scopes[$cls][$name] = array();
        }

        self::$_scopes[$cls][$name] = $conditions;
    }

    /**
     * Resolve a scope, returning a collection
     *
     * @param string $name of scope
     * @param mixed  $args arguments to use on callable scopes
     *
     * @return mixed collection
     */
    public static function resolveScope($name, $args=null)
    {
        $cls = get_called_class();

        if (!array_key_exists($cls, self::$_scopes)
            || !array_key_exists($name, self::$_scopes[$cls])
        ) {
            return null;
        }

        $conditions = self::$_scopes[$cls][$name];
        if (!$conditions) {
            return null;
        }

        if (is_callable($conditions)) {
            $conditions = $conditions($args);
        }
        return self::where($conditions);
    }

    /**
     * Call static methods as scopes
     *
     * @param string $method method
     * @param mixed  $args   arguments
     *
     * @return scope result or null
     */
    public static function __callStatic($method, $args)
    {
        $scope = self::resolveScope($method, $args);
        if ($scope) {
            return $scope;
        }
        return null;
    }
}
