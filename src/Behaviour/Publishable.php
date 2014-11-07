<?php

    namespace Oxygen\Data\Behaviour;

    trait Publishable {

        /**
         * @ORM\Column(type="integer", nullable=true)
         */

        protected $stage;

        /**
         * Determines if the page is published.
         *
         * @return boolean
         */

        public function isPublished() {
            return $this->stage == self::STAGE_PUBLISHED;
        }

        /**
         * Publishes the page.
         *
         * @return boolean
         */

        public function publish() {
            $this->stage = self::STAGE_PUBLISHED;
            return $this;
        }

        /**
         * Unpublishes the page.
         *
         * @return boolean
         */

        public function unpublish() {
            $this->stage = self::STAGE_DRAFT;
            return $this;
        }

        /**
         * Clones the entity.
         *
         * @return void
         */

        public function __clone() {
            $this->id = null;
            if($this->stage === self::STAGE_PUBLISHED) {
                $this->stage = self::STAGE_DRAFT;
            }
        }

    }


