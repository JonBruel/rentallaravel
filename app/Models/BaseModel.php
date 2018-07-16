<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 11-07-2018
 * Time: 09:38
 */

namespace App\Models;
use \Esensi\Model\Model;
use Kyslik\ColumnSortable\Sortable;
use EloquentFilter\Filterable;
use Collective\Html\Eloquent\FormAccessible;

class BaseModel extends Model
{
    use Sortable;
    use Filterable;
    use FormAccessible;
}