<?php

namespace WP\Entities;

use WP\Entities\Attributes\Id;
use WP\Entities\Attributes\Name;
use Nette\SmartObject;

abstract class EntitySelect{

    use SmartObject;
    use Id;
    use Name;

    /**
     * @return array
     */
    public function jsonSerialize($type = null) : array{
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}