<?php
namespace App\Service\SchemeService;

/**
 * Class Scheme
 *
 * @package App\Service\SchemeStorage
 * @author Nikita Gerasimov <tariel-x@ya.ru>
 */
class Scheme implements \JsonSerializable
{
    /**
     * @var bool In or out
     */
    private $in;

    /**
     * @var array
     */
    private $scheme;

    /**
     * @var string
     */
    private $type;

    /**
     * @var integer
     */
    private $priority = 0;

    /**
     * Scheme constructor.
     *
     * @param bool $in
     * @param array $scheme
     * @param string $type
     */
    public function __construct(bool $in, array $scheme, string $type)
    {
        $this->in = $in;
        $this->scheme = $scheme;
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isIn(): bool
    {
        return $this->in;
    }

    /**
     * @param bool $in
     * @return Scheme
     */
    public function setIn(bool $in): Scheme
    {
        $this->in = $in;
        return $this;
    }

    /**
     * @return array
     */
    public function getScheme(): array
    {
        return $this->scheme;
    }

    /**
     * @param array $scheme
     * @return Scheme
     */
    public function setScheme(array $scheme): Scheme
    {
        $this->scheme = $scheme;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Scheme
     */
    public function setType(string $type): Scheme
    {
        $this->type = $type;
        return $this;
    }

    

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'type' => $this->getType(),
            'in' => $this->isIn(),
            'scheme' => $this->getScheme(),
        ];
    }

    /**
     * Get the value of priority
     *
     * @return  integer
     */ 
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set the value of priority
     *
     * @param  integer  $priority
     * @return  self
     */ 
    public function setPriority(int $priority)
    {
        $this->priority = $priority;
        return $this;
    }
}
