<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 02 Oct 2018 10:33:32 +0000.
 */

namespace App\Models;


/**
 * Class Errorlog
 * 
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $stack
 * @property string $customermessage
 * @property string $situation
 *
 * @package App\Models
 */
class Errorlog extends BaseModel
{
	protected $table = 'errorlog';

    public function modelFilter()
    {
        return $this->provideFilter(Filters\ErrorlogFilter::class);
    }

	protected $fillable = [
		'stack',
		'customermessage',
		'situation'
	];
}
