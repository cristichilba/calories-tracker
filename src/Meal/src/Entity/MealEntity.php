<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */
 
declare(strict_types=1);
 
namespace Tracker\Frontend\Meal\Entity;

use Dot\Mapper\Entity\Entity;

class MealEntity extends Entity implements \JsonSerializable
{
    /** @var  int */
    protected $id;
    /** @var  int */
    protected $userId;
    /** @var  string */
    protected $type;
    /** @var  string */
    protected $date;

    /**
     * MealEntity constructor.
     * @param int         $userId
     * @param string      $type
     * @param string      $date
     */
    public function __construct(
        int $userId = 0,
        string $type = "",
        string $date = ""
    ) {
        $this->userId = $userId;
        $this->type = $type;
        $this->date = $date;
    }

    /**
     * @param array $data
     * @return MealEntity
     */
    public static function fromArray(array $data)
    {
        if (!isset($data['userId'])) {
            throw new \InvalidArgumentException('Meal userId is required.');
        }
        if (!isset($data['type'])) {
            throw new \InvalidArgumentException('Meal type is required.');
        }
        if (!isset($data['date'])) {
            throw new \InvalidArgumentException('Meal date is required.');
        }
        return new MealEntity((int)$data['userId'], (string)$data['type'], (string)$data['date']);
    }

    /**
     * @return MealEntity
     */
    public static function emptyMeal()
    {
        return new MealEntity(0, "", "");
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return (int)$this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getUserId(): ?int
    {
        return (int)$this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getDate(): ?string
    {
        return $this->date;
    }

    /**
     * @param string $date
     */
    public function setDate(string $date)
    {
        $this->date = $date;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'userId' => $this->getUserId(),
            'type' => $this->getType(),
            'date' => $this->getDate(),
        ];
    }
}
