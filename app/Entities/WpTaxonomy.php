<?php
    namespace WP\Entities;

    use WP\Entities\Attributes\Id;
    use WP\Entities\Attributes\Name;
    use WP\Entities\Attributes\Slug;
    use WP\Entities\Attributes\Lang;

    class WpTaxonomy{

        use \Nette\SmartObject;

        use Id;
        use Name;
        use Slug;
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