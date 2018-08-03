<?php
declare(strict_types=1);

namespace UCRM\Data\Models;


/**
 * Class Country
 *
 * @package UCRM\Data\Models
 * @author  Ryan Spaeth <rspaeth@mvqn.net>
 * @version Auto-Generated on 08/02/2018 @ 19:09:06 (GMT-07:00) by MAPPER  
 */
final class Country extends \UCRM\Data\Model
{
    /** @const string The column name of the PRIMARY KEY of this Model. */
    protected const PRIMARY_KEY = "country_id"; 
    
    /** @const string The table name of this Model. */
    protected const TABLE_NAME = "country";



    /**
     * @var int
     */
    protected $country_id;
    
    /**
     * @return int|null
     */
    public function getCountryId(): ?int
    {
        return $this->country_id;
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
    protected $code;
    
    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }
    
    /**
     * @param string $value
     */
    public function setCode(string $value): void
    {
        $this->code = $value;
    }

    
}
