<?php

class VslFieldFactroy{
    private $name;
    private $id;
    private $css;
    private $label;
    private $value;
    private $type;


    public function __construct(Array $field)
    {
        // Init Field Values
        // Field Name
       
       $this->name = (isset($field['name']) && !empty($field['name']))
       ?  $field['name'] 
       : new WP_Error( 'required_value', __( "Field name is required to create a new field object", "vue-store-locator" ) );

        // Field Type
       $this->type = isset($field['type']) && !empty($field['type']) && in_array($field['type'], $this->getAllowedTypes())
       ?  $field['type'] 
       : new WP_Error( 'required_value', __( "Field type is not set or is not an allowed field", "vue-store-locator" ) );

       // Field Label
       $this->label = isset($field['label']) && !empty($field['label']) 
       ?  $field['label'] 
       : new WP_Error( 'required_value', __( "Field label is required to create a new field object", "vue-store-locator" ) );
       
       $this->id = isset($field['id']) && !empty($field['id']) ?  $field['id'] : $field['name'];
       $this->css = isset($field['css']) && !empty($field['css']) ?  $field['css'] : $field['name'];
       $this->value = isset($field['value']) && !empty($field['value']) ?  $field['value'] : '';
    }

    private function getAllowedTypes(){
        return array('text', 'url', 'textarea', 'tel', 'number', 'email');
    }

    private function getInputTypes(){
        return array('text', 'url', 'tel', 'number', 'submit', 'email');
    }

    public function getHtmlField(){
        // error_log($this->label);
        switch ($this->type){
            case (in_array($this->type, $this->getInputTypes())):
                include VSL_PLUGIN_DIR.'/templates/fields/input.php';
                break;
            case "textarea":
                include VSL_PLUGIN_DIR.'/templates/fields/textarea.php';
                break;
        }

        //  include 'header.php'
    }

    // Setters
    public function setValue($value){
        $this->value = $value;
    }

    // Getters
    public function getName(){
        return $this->name;
    }
    public function getValue(){
        return $this->value;
    }
    public function getLabel(){
        return $this->label;
    }
    public function getId(){
        return $this->id;
    }
    public function getCss(){
        return $this->css;
    }
    public function getType(){
        return $this->type;
    }
}