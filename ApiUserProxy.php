<?php

namespace RunetId\ApiClientBundle;

use RunetId\ApiClient\Model\User as ApiUser;

/**
 * Class ApiUserProxy
 *
 * @property string        $firstName
 * @property string        $lastName
 * @property string        $fatherName
 * @property string        $email
 * @property string        $phone
 * @property bool          $visible
 * @property bool          $verified
 * @property string        $gender
 * @property ApiUser\Photo $photo
 * @property ApiUser\Work  $work
 */
class ApiUserProxy
{
    /**
     * @var int
     */
    protected $runetId;

    /**
     * @var ApiUser
     */
    private $apiData;

    /**
     * @param int $runetId
     */
    public function __construct($runetId)
    {
        $this->runetId = $runetId;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        $name = ucfirst($name);

        return isset($this->getApiData()->$name);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $name = ucfirst($name);

        return $this->getApiData()->$name;
    }

    /**
     * @return int
     */
    public function getRunetId()
    {
        return $this->runetId;
    }

    /**
     * @param ApiUser $user
     * @return $this
     */
    public function setApiData(ApiUser $user)
    {
        $this->apiData = $user;

        return $this;
    }

    /**
     * @return ApiUser
     */
    public function getApiData()
    {
        return $this->apiData;
    }
}
