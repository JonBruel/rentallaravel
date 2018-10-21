<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 20-10-2018
 * Time: 17:05
 */
namespace App\Traits;
use Illuminate\Database\Eloquent\Builder;

trait CompositeKey {

    public function getIncrementing()
    {
        return false;
    }

    protected function setKeysForSaveQuery(Builder $query)
    {
        $keys = $this->getKeyName();

        if (!is_array($keys))
        {
            return parent::setKeysForSaveQuery($query);
        }

        foreach($keys as $keyName) {
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    protected function getKeyForSaveQuery($keyName = null)
    {
        if (is_null($keyName))
        {
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName]))
        {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }
}

