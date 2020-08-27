<?php

namespace Endouble\Badge;

use Laravel\Nova\Fields\Field;

class Badge extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'badge';

    /**
     * Possible classes to be added on the template depending on the field and its value.
     *
     * @var array
     */
    public $fieldMap = [
        'Module Log Status' => [
            'Error' => 'badge-danger',
            'Finished' => 'badge-success',
            'Started' => 'badge-info',
        ],
        'Finding Status' => [
            'False Positive' => 'badge-info',
            'Open' => 'badge-danger',
            'Fixed' => 'badge-success',
        ],
        'Severity' => [
            'Info' => 'badge-info',
            'Low' => 'badge-warning',
            'Medium' => 'badge-warning-dark',
            'High' => 'badge-danger',
            'Critical' => 'badge-danger-dark',
            'Safe' => 'badge-success',
        ],
        'Status' => [
            // Informational
            100 => 'badge-info',
            101 => 'badge-info',
            // Success
            200 => 'badge-success',
            201 => 'badge-success',
            202 => 'badge-success',
            203 => 'badge-success',
            204 => 'badge-success',
            205 => 'badge-success',
            206 => 'badge-success',
            // Redirection
            300 => 'badge-warning',
            301 => 'badge-warning',
            304 => 'badge-warning',
            307 => 'badge-warning',
            // Client Error
            400 => 'badge-warning-dark',
            401 => 'badge-warning-dark',
            403 => 'badge-warning-dark',
            404 => 'badge-warning-dark',
            409 => 'badge-warning-dark',
            429 => 'badge-warning-dark',
            // Server error
            500 => 'badge-danger',
            501 => 'badge-danger',
            502 => 'badge-danger',
            503 => 'badge-danger',
            504 => 'badge-danger',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->type($this->name);
    }

    /**
     * Returns a classMap with the available classes given the possible values of a specific field.
     *
     * @param  string $field
     *
     * @return \Endouble\Badge\Badge
     */
    public function type($field)
    {
        $classMap = !is_null($field) && array_key_exists($field, $this->fieldMap) ? $this->fieldMap[$field] : null;

        return $this->withMeta($classMap ? ['classMap' => $classMap] : []);
    }

    /**
     * Sets the default value for a field to display if none specified.
     *
     * @param  string $value
     *
     * @return \Endouble\Badge\Badge
     */
    public function default($value)
    {
        return $this->withMeta($value ? ['default' => $value] : []);
    }
}
