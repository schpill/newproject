<?php
    namespace Thin;

    loader("model");

    class BlazzSystemDbModel extends ModelLib {
        /* Make hooks of model */
        public function _hooks()
        {
            $obj = $this;
            // $this->_hooks['beforeCreate'] = function () use ($obj) {};
            // $this->_hooks['beforeRead'] = ;
            // $this->_hooks['beforeUpdate'] = ;
            // $this->_hooks['beforeDelete'] = ;
            // $this->_hooks['afterCreate'] = ;
            // $this->_hooks['afterRead'] = ;
            // $this->_hooks['afterUpdate'] = ;
            // $this->_hooks['afterDelete'] = ;
            // $this->_hooks['validate'] = function () use ($data) {
            //     return true;
            // };
        }
    }