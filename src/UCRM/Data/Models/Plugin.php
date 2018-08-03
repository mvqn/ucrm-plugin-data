<?php
declare(strict_types=1);

namespace UCRM\Data\Models;


/**
 * Class Plugin
 *
 * @package UCRM\Data\Models
 * @author  Ryan Spaeth <rspaeth@mvqn.net>
 * @version Auto-Generated on 08/02/2018 @ 19:59:10 (GMT-07:00) by MAPPER  
 */
final class Plugin extends \UCRM\Data\Model
{
    /** @const string The column name of the PRIMARY KEY of this Model. */
    protected const PRIMARY_KEY = "id"; 
    
    /** @const string The table name of this Model. */
    protected const TABLE_NAME = "plugin";



    /**
     * @var int
     */
    protected $id;
    
    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
    protected $display_name;
    
    /**
     * @return string|null
     */
    public function getDisplayName(): ?string
    {
        return $this->display_name;
    }
    
    /**
     * @param string $value
     */
    public function setDisplayName(string $value): void
    {
        $this->display_name = $value;
    }

    /**
     * @var string
     */
    protected $description;
    
    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
    
    /**
     * @param string $value
     */
    public function setDescription(string $value): void
    {
        $this->description = $value;
    }

    /**
     * @var string
     */
    protected $url;
    
    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }
    
    /**
     * @param string $value
     */
    public function setUrl(string $value): void
    {
        $this->url = $value;
    }

    /**
     * @var string
     */
    protected $author;
    
    /**
     * @return string|null
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }
    
    /**
     * @param string $value
     */
    public function setAuthor(string $value): void
    {
        $this->author = $value;
    }

    /**
     * @var string
     */
    protected $version;
    
    /**
     * @return string|null
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }
    
    /**
     * @param string $value
     */
    public function setVersion(string $value): void
    {
        $this->version = $value;
    }

    /**
     * @var string
     */
    protected $min_ucrm_version;
    
    /**
     * @return string|null
     */
    public function getMinUcrmVersion(): ?string
    {
        return $this->min_ucrm_version;
    }
    
    /**
     * @param string $value
     */
    public function setMinUcrmVersion(string $value): void
    {
        $this->min_ucrm_version = $value;
    }

    /**
     * @var string
     */
    protected $max_ucrm_version;
    
    /**
     * @return string|null
     */
    public function getMaxUcrmVersion(): ?string
    {
        return $this->max_ucrm_version;
    }
    
    /**
     * @param string $value
     */
    public function setMaxUcrmVersion(string $value): void
    {
        $this->max_ucrm_version = $value;
    }

    /**
     * @var bool
     */
    protected $enabled;
    
    /**
     * @return bool|null
     */
    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }
    
    /**
     * @param bool $value
     */
    public function setEnabled(bool $value): void
    {
        $this->enabled = $value;
    }

    /**
     * @var string
     */
    protected $execution_period;
    
    /**
     * @return string|null
     */
    public function getExecutionPeriod(): ?string
    {
        return $this->execution_period;
    }
    
    /**
     * @param string $value
     */
    public function setExecutionPeriod(string $value): void
    {
        $this->execution_period = $value;
    }

    
}
