<?php

namespace Endouble\Expiration;

use Laravel\Nova\Fields\Field;

class Expiration extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'expiration';

    /**
     * {@inheritdoc}
     */
    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $daysToExpire = $resolveCallback();

        $this->setExpirationText($daysToExpire);
        $this->getClassName($daysToExpire);
    }

    /**
     * Defines the expiration text to be displayed in the field given the number of days remaining for expiration.
     *
     * @param  number $daysToExpire
     *
     * @return null
     */
    private function setExpirationText($daysToExpire)
    {
        $expiration = 'Expired';

        if (is_null($daysToExpire)) {
            $expiration = '-';
        } elseif ($daysToExpire > 0) {
            $expiration = $daysToExpire . ($daysToExpire > 1 ? ' days' : ' day');
        }

        $this->withMeta(['expiration' => $expiration]);
    }

    /**
     * Returns a classMap with the available classes given the possible values of a specific field.
     *
     * @param  number $daysToExpire
     *
     * @return null
     */
    private function getClassName($daysToExpire)
    {
        $className = 'text-danger';

        if (is_null($daysToExpire)) {
            $className = '';
        } elseif ($daysToExpire > 15) {
            $className = 'text-success';
        } elseif ($daysToExpire > 0) {
            $className = 'text-warning-dark';
        }

        $this->withMeta(['className' => $className]);
    }
}
