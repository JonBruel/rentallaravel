<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;
use App\Traits\CompositeKey;

/**
 * Class StandardemailI18n
 * 
 * @property int $id
 * @property string $culture
 * @property string $contents
 * 
 * @property \App\Models\Standardemail $standardemail
 *
 * @package App\Models
 */
class StandardemailI18n extends BaseModel
{
    use CompositeKey;
	protected $table = 'standardemail_i18n';
	public $incrementing = false;
	public $timestamps = false;

    protected $primaryKey = ['id', 'culture'];

	protected $casts = [
		'id' => 'int'
	];

	protected $fillable = [
		'contents'
	];

	public function standardemail()
	{
		return $this->belongsTo(\App\Models\Standardemail::class, 'id');
	}

	/*
	 * Load content into the I18n-part. Create if it does not exist
	 */
	public static function copyContent(int $id, array $contents)
    {
        foreach ($contents as $culture => $content)
        {
            $i18n = StandardemailI18n::where('id', $id)->where('culture', $culture)->first();
            if (!$i18n)  $i18n = new StandardemailI18n();
            $i18n->contents = $content;
            $i18n->culture = $culture;
            $i18n->id = $id;
            $i18n->save();
        }
    }
}
