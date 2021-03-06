<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */
 
declare(strict_types=1);
 
namespace Tracker\Frontend\Recipe\Entity;

use Dot\Mapper\Entity\Entity;
use Tracker\Frontend\Product\Entity\ProductEntity;
use Zend\Form\Element\DateTime;

class RecipeEntity extends Entity implements \JsonSerializable
{
    /** @var  int */
    protected $id;

    /** @var  int */
    protected $userId;

    /** @var  string */
    protected $name;

    /** @var  string */
    protected $dateCreated;

    /** @var  string */
    protected $dateUpdated;

    /** @var  string */
    protected $status;

    /**
     * RecipeEntity constructor.
     * @param int         $userId
     * @param string      $name
     */
    public function __construct(
        int $userId = 0,
        string $name = "",
        string $status = 'active'
    ) {
        $this->userId = $userId;
        $this->name = $name;
        $this->status = $status;
    }

    /**
     * @param array $data
     * @return RecipeEntity
     */
    public static function fromArray(array $data)
    {
        if (!isset($data['userId'])) {
            throw new \InvalidArgumentException('Recipe userId is required.');
        }

        if (!isset($data['name'])) {
            throw new \InvalidArgumentException('Recipe name is required.');
        }

        return new RecipeEntity((int)$data['userId'], (string)$data['name'], 'active');
    }

    public static function emptyRecipe()
    {
        return new RecipeEntity(0, "", 'active');
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return (int)$this->id;
    }

    /**
     * @param int $id
     * @return RecipeEntity
     */
    public function setId($id): RecipeEntity
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return (int)$this->userId;
    }

    /**
     * @param int $userId
     * @return RecipeEntity
     */
    public function setUserId($userId): RecipeEntity
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return (string)$this->name;
    }

    /**
     * @param string $name
     * @return RecipeEntity
     */
    public function setName($name): RecipeEntity
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDateCreated(): string
    {
        return (string)$this->dateCreated;
    }

    /**
     * @param string $dateCreated
     * @return RecipeEntity
     */
    public function setDateCreated($dateCreated): RecipeEntity
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * @return string
     */
    public function getDateUpdated(): string
    {
        return (string)$this->dateUpdated;
    }

    /**
     * @param string $dateUpdated
     * @return RecipeEntity
     */
    public function setDateUpdated($dateUpdated): RecipeEntity
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return (string)$this->status;
    }

    /**
     * @param string $status
     * @return RecipeEntity
     */
    public function setStatus($status): RecipeEntity
    {
        $this->status = $status;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'userId' => $this->getUserId(),
            'name' => $this->getName(),
            'dateCreated' => $this->getDateCreated(),
            'dateUpdated' => $this->getDateUpdated(),
            'status' => $this->getStatus(),
        ];
    }
}
