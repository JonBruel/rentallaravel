<?php

/**
 * Created by Reliese Model.
 * Date: Sat, 16 Mar 2019 18:20:26 +0100.
 */

namespace App\Models;
use Carbon\Carbon;

/**
 * Class Identitypaper
 * 
 * @property int $id
 * @property int $contractid
 * @property string $forename
 * @property string $surname1
 * @property string $surname2
 * @property string $sex
 * @property string $passportnumber
 * @property string $country
 * @property \Carbon\Carbon $dateofissue
 * @property \Carbon\Carbon $dateofbirth
 * @property \Carbon\Carbon $arrivaldate
 * 
 * @property \App\Models\Contract $contract
 *
 * @package App\Models
 */
class Identitypaper extends BaseModel
{
	public $timestamps = false;

	protected $casts = [
		'contractid' => 'int'
	];

	protected $dates = [
		'dateofissue',
		'dateofbirth',
		'arrivaldate'
	];

	protected $fillable = [
		'contractid',
		'forename',
		'surname1',
		'surname2',
		'sex',
		'passportnumber',
		'country',
		'dateofissue',
		'dateofbirth',
		'arrivaldate'
	];

    /*
 * Retuns an array of keys and values to be used in forms for select boxes. Typical uses
 * are filters, e.g selection housed owner by a specific owner.
 *
 * Retuns null if no select boxes are to be used.
 */
    public function withSelect($fieldname)
    {
        switch ($fieldname)
        {
            case 'sex':
                return  ['M' => __('Male'), 'F' => __('Female')];
            case 'country':
                return  ['DINAMARCA' => __('Denmark'),
                         'ESPAÑA' => __('Spain'),
                         'INGLATERRA' => __('England'),
                         'SUECIA' => __('Sweden'),
                         'ALEMANIA' => __('Germany'),
                         'FRANCIA' => __('France'),
                         'ESTADOS UNIDOS' => __('USA'),
                         'OTRA' => __('Other')];
            default:
                return null;
        }
    }

	public function contract()
	{
		return $this->belongsTo(\App\Models\Contract::class, 'contractid');
	}
}
