<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 27-06-2018
 * Time: 16:08
 */

namespace App\Helpers;


class CreateValidationAttributes
{
    protected $model;
    protected $casts = [];

    public function __contruct($model) {
        $this->setModel($model);
    }

    public function setModel($model) {
        $this->model = $model;
        if ($model->getCasts()) $this->casts = $model->getCasts();
    }

    public function validationOptions($name, $options = []) {

        //Modifications for jquery unobtrusive validation
        $prefix = 'data-val';
        if ($this->model) {
            $cast = 'string';

            if (array_key_exists($name, $this->casts)) $cast = $this->casts[$name];
            if ($this->model->rules) {

                if (array_key_exists($name, $this->model->rules)) {
                    $options[$prefix] = 'true';
                    $rule = $this->model->rules[$name];
                    foreach ($rule as $condition) {
                        $conditionArray = explode(':', $condition);
                        $conditionStart = $conditionArray[0];
                        $parameters = [];
                        if (array_key_exists(1, $conditionArray)) {
                            $parameters = explode(',',$conditionArray[1]);
                        }
                        switch ($conditionStart) {
                            case 'required':
                                $options[$prefix.'-required'] = "The $name field is required.";
                                break;
                            case 'between':
                                if (  sizeof($parameters) == 2) {
                                    $options[$prefix.'-range'] = "The value of $name must be between $parameters[0] and $parameters[1]";
                                    if ($cast == 'string') $options[$prefix.'-range'] = "The number of characters in $name must be between $parameters[0] and $parameters[1]";
                                    $options[$prefix.'-range-min'] = $parameters[0];
                                    $options[$prefix.'-range-max'] = $parameters[1];
                                }
                                break;
                            case 'numeric':
                                $options[$prefix.'-number'] = "The $name field must be a number.";
                                break;
                        }
                    }
                }
            }
        }

        return $options;
    }

}