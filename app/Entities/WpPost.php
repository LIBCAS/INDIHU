<?php
    namespace WP\Entities;

    use WP\Entities\Attributes\Id;
    use WP\Entities\Attributes\Name;
    use WP\Entities\Attributes\Content;
    use WP\Entities\Attributes\Slug;
    use WP\Entities\Attributes\Created;
    use WP\Entities\Attributes\Modified;
    use WP\Entities\Attributes\Lang;

    class WpPost{

        use \Nette\SmartObject;

        use Id;
        use Name;
        use Content;
        use Slug;
        use Created;
        use Modified;
        use Lang;

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
?>