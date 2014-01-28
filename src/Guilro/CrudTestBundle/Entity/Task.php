<?php

namespace Guilro\CrudTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Task
 */
class Task
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $tags;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     * @return Task
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set tags
     *
     * @param array $tags
     * @return Task
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }
    /**
     * @var string
     */
    private $oneToMany;




    /**
     * Add tags
     *
     * @param \Guilro\CrudTestBundle\Entity\Tag $tags
     * @return Task
     */
    public function addTag(\Guilro\CrudTestBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;
        $tags->setTask($this);

        return $this;
    }

    /**
     * Remove tags
     *
     * @param \Guilro\CrudTestBundle\Entity\Tag $tags
     */
    public function removeTag(\Guilro\CrudTestBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
    }
}
