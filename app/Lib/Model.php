<?php
namespace App\Lib;


use App\Exceptions\ClassException;
use PDO;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

abstract class Model
{
    use Helper;


    public static function find($cond = null,
                                string|null $groupBy = null,
                                string|null $orderBy = null, $limit = null): array
    {
        $db = Database::getConnection();
        $sql = "SELECT * FROM `" . static::$table_name . "`";
        if (is_array($cond)) {
            $sql .= " WHERE ";
            $bindings = [];
            foreach ($cond as $key => $value) {
                $bindings[] = "$key = :$key";
            }
            $sql .= implode(" AND ", $bindings);
        } else if ($cond != null) {
            $sql .= "WHERE $cond";
        }
        if (isset($groupBy))
            $sql .= " GROUP BY $groupBy";
        if (isset($orderBy))
            $sql .= " ORDER BY $orderBy";
        if (isset($limit))
            $sql .= " LIMIT $limit";

        $results = $db->fetch($sql, get_called_class(), $cond);
        return $results;
    }

    public static function findFirst($cond = null, string|null $groupBy = null)
    {
        $objs = static::find($cond, $groupBy, null, 1);
        if (empty($objs)) throw new ClassException("Model not found.");
        return array_shift($objs);
    }

    public static function all($groupBy = null, string|null $orderBy = null): array
    {
        $objs = static::find(null, $groupBy, $orderBy, null);
        if (empty($objs)) throw new ClassException("Model Not Found.");
        return $objs;
    }
}