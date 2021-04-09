<?php
declare(strict_types=1);

namespace CodingLiki\GrammarParser\Rule;

class RulePart
{
    public const TYPE_MAY_BE_ONCE_OR_MORE = '*';
    public const TYPE_NORMAL    = '';
    public function __construct(private string $data, private string $type)
    {
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @param string $data
     * @return RulePart
     */
    public function setData(string $data): RulePart
    {
        $this->data = $data;
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
     * @return RulePart
     */
    public function setType(string $type): RulePart
    {
        $this->type = $type;
        return $this;
    }
}