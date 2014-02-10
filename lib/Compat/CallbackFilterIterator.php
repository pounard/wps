<?php

namespace {

    /**
     * Almost compatible with PHP 5.4 CallbackFilterIterator class.
     */
    class CallbackFilterIterator extends \FilterIterator
    {
        /**
         * @var callback
         */
        private $callback;

        /**
         * Default constructor
         *
         * @param \Iterator $iterator
         * @param callback $callback
         */
        public function __construct(\Iterator $iterator, $callback)
        {
            if (is_callable($callback)) {
                $this->callback = $callback;
            } else {
                trigger_error("Given callback is not callable");
            }

            parent::__construct($iterator);
        }

        public function accept()
        {
            if (null !== $this->callback) {
                return (bool)call_user_func(
                    $this->callback,
                    parent::current(),
                    parent::key(),
                    $this
                );
            }
            return true;
        }
    }
}
