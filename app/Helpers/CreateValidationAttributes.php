<?php
namespace App\Helpers;

/**
 * Class CreateValidationAttributes is used in forms, where we want the javascript functions to
 * validate the input before the data is returned to the controller. There are several javascript
 * packages required, they are all included in the partial: client_validation.blade.php and the
 * scripts are similar or identical to the scripts used in typical .NET Core MVC solutions.
 *
 * @package App\Helpers
 */
class CreateValidationAttributes
{
    protected $model;
    protected $casts = [];

    /**
     * CreateValidationAttributes constructor.
     * @param \App\Models\BaseModel $model instance of the data
     */
    public function __construct(\App\Models\BaseModel $model) {
        $this->setModel($model);
    }

    /**
     * @param \App\Models\BaseModel $model instance of the data
     */
    public function setModel(\App\Models\BaseModel $model) {
        $this->model = $model;
        if (!$model) return;
        if ($model->getCasts()) $this->casts = $model->getCasts();
    }



    /**
     * @param string $field hte name of the field where the cast should be set.
     * @param string $type thee types are supported which will render the field differently: textarea, bool and hidden.
     * In general fields will be rendered as type=text fields, unless $model->withSelect($field) is an array, then it
     * will be rendered as a select field.
     * @return $this
     */
    public function setCast($field, $type)
    {
        if (array_key_exists($field, $this->casts)) $this->casts[$field] = $type;
        else $this->casts = $this->casts + [$field => $type];
        return $this;
    }

    /**
     * Is the pluralization of setCast
     * @param array $casts the array has the structure: [['field1' => 'cast1'], ['field2' => 'cast2']]
     * @return $this
     */
    public function setCasts($casts)
    {
        foreach ($casts as $field => $cast) $this->setCast($field, $cast);
        return $this;
    }

    /**
     * @param $field
     * @return string with the given cast
     */
    public function getCast($field)
    {
        if (!array_key_exists($field, $this->casts)) return 'text';
        else return $this->casts[$field];
    }

    /**
     * @return array  with the structure: [['field1' => 'cast1'], ['field2' => 'cast2']]
     */
    public function getCasts()
    {
        return $this->casts;
    }

    /**
     * The function changes the attributes of the input tag. The existing attibutes are included as the $options
     * and in the final rendering they will be kept. In addition more data-attributes will be generated and they
     * serve as markers the the javascript validation system. The present solution only supports a small subset
     * of all the possible validation methods. More may be required later. Why not now? Because the documentation
     * of the unobtrusive javascript validation is rather limited, and I have had to reverse engineer it to get
     * the present limited set of validations to work.
     *
     * @param $name of the field
     * @param array $options, typically a number of attributes to be rendered
     * @return array|null
     */
    public function validationOptions($name, $options = []) {

        //Modifications for jquery unobtrusive validation
        if (!$this->model) return null;
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
                                    if ($cast == 'string') {
                                        $options[$prefix.'-length'] = "The number of characters in $name must be between $parameters[0] and $parameters[1]";
                                        $options[$prefix.'-length-min'] = $parameters[0];
                                        $options[$prefix.'-length-max'] = $parameters[1];
                                    }
                                    else {
                                        $options[$prefix.'-range'] = "The value of $name must be between $parameters[0] and $parameters[1]";
                                        $options[$prefix.'-range-min'] = $parameters[0];
                                        $options[$prefix.'-range-max'] = $parameters[1];
                                    }
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