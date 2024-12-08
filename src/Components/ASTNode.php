<?php

namespace ShanjaGlinka\Interpre\Components;

class ASTNode
{

    /**
     * @param string $type
     * @param mixed $value
     * @param array $children
     */
    public function __construct(
        public string $type,
        public mixed $value = null,
        public array $children = []
    ) {}

    /**
     * @return string
     */
    public function __toString(): string
    {
        return var_export($this->value, true);
    }
}
