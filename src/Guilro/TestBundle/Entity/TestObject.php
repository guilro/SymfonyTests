<?php

namespace Guilro\TestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TestObject
 */
class TestObject
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $testField;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set testField
     *
     * @param string $testField
     * @return TestObject
     */
    public function setTestField($testField)
    {
        $this->testField = $testField;
    
        return $this;
    }

    /**
     * Get testField
     *
     * @return string 
     */
    public function getTestField()
    {
        return $this->testField;
    }
}
