<?php

/**
 * @package Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace Duplicator\Core\Models;

use VendorDuplicator\Amk\JsonSerialize\JsonSerialize;
use Duplicator\Libs\Snap\SnapWP;
use Exception;
use ReflectionClass;
use ReflectionObject;
use wpdb;

/**
 * Abstract Entity
 */
abstract class AbstractEntity
{
    /** @var int<-1,max> */
    protected $id = -1;

    /**
     * Return entity type identifier
     *
     * @return string
     */
    public static function getType()
    {
        // This to avoid warnign in PHP 5.6 do cleare and abstract static method.
        throw new Exception('This method must be extended');
    }

    /**
     * Return entity id
     *
     * @return int
     */
    final public function getId()
    {
        return $this->id;
    }

    /**
     * Get entity by id
     *
     * @param int<0, max> $id entity id
     *
     * @return static|false Return entity istance of false on failure
     */
    public static function getById($id)
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        $query = $wpdb->prepare("SELECT * FROM " . self::getTableName() . " WHERE ID = %d", $id);
        if (($row = $wpdb->get_row($query, ARRAY_A)) === null) {
            return false;
        }

        if ($row['type'] !== static::getType()) {
            return false;
        }

        return static::getEntityFromJson($row['data'], (int) $row['id']);
    }

    /**
     * Delete entity by id
     *
     * @param int<0, max> $id entity id
     *
     * @return bool true on success of false on failure
     */
    public static function deleteById($id)
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        if ($id < 0) {
            return true;
        }

        if (
            $wpdb->delete(
                self::getTableName(),
                ['id' => $id],
                ['%d']
            ) === false
        ) {
            return false;
        }

        return true;
    }

    /**
     * Get all entities of current type
     *
     * @param int<0, max>                          $page           current page, if $pageSize is 0 o 1 $pase is the offset
     * @param int<0, max>                          $pageSize       page size, 0 return all entities
     * @param callable                             $sortCallback   sort function on items result
     * @param callable                             $filterCallback filter on items result
     * @param array{'col': string, 'mode': string} $orderby        query ordder by
     *
     * @return static[]|false return entities list of false on failure
     */
    public static function getAll(
        $page = 0,
        $pageSize = 0,
        $sortCallback = null,
        $filterCallback = null,
        $orderby = ['col' => 'id', 'mode' => 'ASC']
    ) {
        /** @var wpdb $wpdb */
        global $wpdb;

        $offset   = $page * max(1, $pageSize);
        $pageSize = ($pageSize ? $pageSize : PHP_INT_MAX);
        $orderCol = isset($orderby['col']) ? $orderby['col'] : 'id';
        $order    = isset($orderby['mode']) ? $orderby['mode'] : 'ASC';

        $query = $wpdb->prepare(
            "SELECT * FROM " . self::getTableName() . " WHERE type = %s ORDER BY {$orderCol} {$order} LIMIT %d OFFSET %d",
            static::getType(),
            $pageSize,
            $offset
        );

        if (($rows = $wpdb->get_results($query, ARRAY_A)) === null) {
            return false;
        }

        $instances = array();
        foreach ($rows as $row) {
            $instances[] = static::getEntityFromJson($row['data'], (int) $row['id']);
        }

        if (is_callable($filterCallback)) {
            $instances = array_filter($instances, $filterCallback);
        }

        if (is_callable($sortCallback)) {
            usort($instances, $sortCallback);
        } else {
            $instances = array_values($instances);
        }

        return $instances;
    }

    /**
     * Delete all entity of current type
     *
     * @return int<0,max>|false The number of rows updated, or false on error.
     */
    public static function deleteAll()
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        return $wpdb->delete(
            self::getTableName(),
            ['type' => static::getType()],
            ['%s']
        );
    }

    /**
     * Set props by array key inpust data
     *
     * @param mixed[]   $data             input data
     * @param ?callable $sanitizeCallback sanitize values callback
     * @return void
     */
    protected function setFromArrayKey($data, $sanitizeCallback = null)
    {
        $reflect = new ReflectionClass($this);
        $props   = $reflect->getProperties();

        foreach ($props as $prop) {
            if (!isset($data[$prop->getName()])) {
                continue;
            }

            if (is_callable($sanitizeCallback)) {
                $value = call_user_func($sanitizeCallback, $prop->getName(), $data[$prop->getName()]);
            } else {
                $value = $data[$prop->getName()];
            }
            $prop->setValue($this, $value);
        }
    }

    /**
     * Initizalize entity from JSON
     *
     * @param string     $json  JSON string
     * @param int<0,max> $rowId Entity row id
     *
     * @return static
     */
    protected static function getEntityFromJson($json, $rowId)
    {
        /** @var static $obj */
        $obj     = JsonSerialize::unserializeToObj($json, static::class);
        $reflect = new ReflectionObject($obj);
        $prop    = $reflect->getProperty('id');
        $prop->setAccessible(true);
        $prop->setValue($obj, $rowId);

        return $obj;
    }

    /**
     * Save entity
     *
     * @return bool  True on success, or false on error.
     */
    public function save()
    {
        $saved = false;
        if ($this->id < 0) {
            $saved = ($this->insert() !== false);
        } else {
            $saved = $this->update();
        }
        return $saved;
    }

    /**
     * Insert entity
     *
     * @return int|false The number of rows inserted, or false on error.
     */
    protected function insert()
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        if ($this->id > -1) {
            throw new Exception('Entity already exists');
        }

        $result = $wpdb->insert(
            self::getTableName(),
            [
                'type' => $this->getType(),
                'data' => false // First I create a row without an object to generate the id, and then I update the row create
            ],
            ['%s', '%s']
        );
        if ($result === false) {
            return false;
        }
        $this->id = $wpdb->insert_id;

        if ($this->update() === false) {
            $this->delete();
            return false;
        }
        return $this->id;
    }

    /**
     * Update entity
     *
     * @return bool True on success, or false on error.
     */
    protected function update()
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        if ($this->id < 0) {
            throw new Exception('Entity don\'t exists in database');
        }

        return ($wpdb->update(
            self::getTableName(),
            [
                'type' => $this->getType(),
                'data' => JsonSerialize::serialize($this, JsonSerialize::JSON_SKIP_CLASS_NAME | JSON_PRETTY_PRINT)
            ],
            ['id' => $this->id],
            ['%s', '%s'],
            ['%d']
        ) !== false);
    }

    /**
     * Delete current entity
     *
     * @return bool True on success, or false on error.
     */
    public function delete()
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        if ($this->id < 0) {
            return true;
        }

        if (
            $wpdb->delete(
                self::getTableName(),
                ['id' => $this->id],
                ['%d']
            ) === false
        ) {
            return false;
        }

        $this->id = -1;
        return true;
    }

    /**
     * Entity table name
     *
     * @return string
     */
    public static function getTableName()
    {
        /** @var wpdb $wpdb */
        global $wpdb;
        return $wpdb->prefix . 'duplicator_pro_entities';
    }

    /**
     * Init entity table
     *
     * @return array Strings containing the results of the various update queries.
     */
    final public static function initTable()
    {
        /** @var wpdb $wpdb */
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name      = static::getTableName();

        //PRIMARY KEY must have 2 spaces before for dbDelta to work
        $sql = "CREATE TABLE `{$table_name}` (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            type VARCHAR(100) NOT NULL,
            data LONGTEXT NOT NULL,
            PRIMARY KEY  (id),
            KEY type_idx (type)) 
            $charset_collate;";

        return SnapWP::dbDelta($sql);
    }
}
