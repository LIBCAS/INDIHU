<?php

namespace WP\Entities;

use WP\Entities\Attributes\Id;
use WP\Entities\Attributes\Created;
use WP\Entities\Attributes\Modified;
use WP\Entities\Attributes\CreatedBy;
use Nette\SmartObject;

abstract class Entity{

    use SmartObject;
    use Id;
    use Created;
    use Modified;
    use CreatedBy;

}