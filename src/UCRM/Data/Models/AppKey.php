<?php
declare(strict_types=1);

namespace UCRM\Data\Models;



/**
 * Class AppKey
 *
 * @package UCRM\Data\Models
 * @author  Ryan Spaeth <rspaeth@mvqn.net>
 * @version Auto-Generated on 08/03/2018 @ 10:01:40 (GMT-07:00) by MAPPER  
 */
final class AppKey extends \UCRM\Data\Model
{
    /** @const string The column name of the PRIMARY KEY of this Model. */
    protected const PRIMARY_KEY = "key_id"; 
    
    /** @const string The table name of this Model. */
    protected const TABLE_NAME = "app_key";



    /**
     * @var int
     */
    protected $key_id;
    
    /**
     * @return int|null
     */
    public function getKeyId(): ?int
    {
        return $this->key_id;
    }

    /**
     * @var string
     */
    protected $name;
    
    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }
    
    /**
     * @param string $value
     */
    public function setName(string $value): void
    {
        $this->name = $value;
    }

    /**
     * @var string
     */
    protected $key;
    
    /**
     * @return string|null
     */
    public function getKey(): ?string
    {
        return $this->key;
    }
    
    /**
     * @param string $value
     */
    public function setKey(string $value): void
    {
        $this->key = $value;
    }

    /**
     * @var string
     */
    protected $type;
    
    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }
    
    /**
     * @param string $value
     */
    public function setType(string $value): void
    {
        $this->type = $value;
    }

    /**
     * @var string
     */
    protected $created_date;
    
    /**
     * @return string|null
     */
    public function getCreatedDate(): ?string
    {
        return $this->created_date;
    }
    
    /**
     * @param string $value
     */
    public function setCreatedDate(string $value): void
    {
        $this->created_date = $value;
    }

    /**
     * @var string
     */
    protected $last_used_date;
    
    /**
     * @return string|null
     */
    public function getLastUsedDate(): ?string
    {
        return $this->last_used_date;
    }
    
    /**
     * @param string $value
     */
    public function setLastUsedDate(string $value): void
    {
        $this->last_used_date = $value;
    }

    /**
     * @var int|null
     */
    protected $plugin_id;
    
    /**
     * @return Plugin|null
     * @throws \UCRM\Data\Exceptions\DatabaseQueryException
     */
    public function getPlugin(): ?Plugin
    {
        // TODO: Handle non-lazy loading also???
        /** @var Plugin $plugin */
        $plugin = Plugin::getById($this->plugin_id);
        return $plugin;
    }
    
    /**
     * @param Plugin $value
     */
    public function setPlugin(Plugin $value): void
    {
        // TODO: Determine best way to store foreign table here...
        //$this->plugin = $value;
    }   

}
