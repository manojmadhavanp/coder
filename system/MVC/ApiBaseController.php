<?php

namespace System\MVC;


abstract class ApiBaseController
{
    private $collection = [];
    private $collectionname;
    
    

        public function collection($collectionname){
        return $this->collectionname = $collectionname;}
    
        public function get($filter = null){
            return $this->collection[$this->collectionname];
        }

        public function create($data){
            $this->collection[$this->collectionname] = $data;
        }

        public function update($id, $data){
            $this->collection[$this->collectionname][$id] = $data;
        }

        public function delete($id){    
            unset($this->collection[$this->collectionname][$id]);   
        }
}
?>