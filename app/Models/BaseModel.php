<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 11-07-2018
 * Time: 09:38
 */

namespace App\Models;

//Chosen to validate AFTER mutators:
use \Esensi\Model\Model;

//Used to allow for sort colums in the views.
use Kyslik\ColumnSortable\Sortable;

//Used as a method to create filters which are controlled in a separate class for each table.
//Also: allows for search forms, using query parameters (get).
use EloquentFilter\Filterable;

/* Common Laravel Form support.
 * Laravel's Eloquent Accessor allow you to manipulate a model attribute before returning it.
 * This can be extremely useful for defining global date formats, for example.
 * However, the date format used for display might not match the date format used for form elements.
 * You can solve this by creating two separate accessors: a standard accessor, and/or a form accessor.
 */
use Collective\Html\Eloquent\FormAccessible;

/* The number helpers are simple conversion methods to cope with international number formats.
 *
 */
use App\Helpers\NumberHelpers;

class BaseModel extends Model
{
    public static $ajax = false;

    use Sortable;
    use Filterable;
    use FormAccessible;
    use NumberHelpers;

    public $rules = [];

    /*
     * This function is used to show the relevant associated
     * user-friendly value as opposed to showing the id.
     * Performance: as we are making up to 4 queries, it does take some time.
     * Measured to around 5 ms.
     * To be overwritten.
     */
    public function withBelongsTo($fieldname)
    {
        return $this->$fieldname;
    }

    /*
     * Retuns an array of keys and values to be used in forms for select boxes. Typical uses
     * are filters, e.g selection housed owner by a specific owner.
     *
     * Returns null if no select boxes are to be used.
     * To be overwritten.
     */
    public function withSelect($fieldname)
    {
        return null;
    }
}