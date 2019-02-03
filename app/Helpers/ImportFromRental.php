<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 18-10-2018
 * Time: 08:18
 */

namespace App\Helpers;

use DB;
use App\Models\Batchtask;
use App\Models\Customer;
use App\Models\Contract;
use App\Models\Contractline;
use App\Models\House;
use App\Models\HouseI18n;
use App\Models\Period;
use App\Models\Accountpost;
use App\Models\Standardemail;
use App\Models\StandardemailI18n;
use App\Models\Emaillog;
use App\Models\Batchlog;
use App\Models\Testimonial;

use Illuminate\Support\Facades\Hash;

/**
 * Class ImportFromRental takes all relevant tables from the old rental system and copies it to
 * the new system. Structural tables such as customertype are not imported. Currency tables are not imported.
 *
 * Presently the following tables are imported:
 * * customer
 * * house
 * * house_i18n
 * * period
 * * contract
 * * contractlines
 * * accountposts
 * * batchlog
 * * standardemail
 * * standardemail_i18n
 * * testimonials
 * * batchtask
 * * emaillog
 * @package App\Helpers
 */
class ImportFromRental
{

    public static function import()
    {

        $filename = base_path().'/storage/logs/migration.txt';
        $handle = fopen($filename, 'w+');
        fwrite($handle, "Has started importing table customer \n");
        foreach(Customer::all() as $customer) $customer->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        $Rcustomers = DB::connection('rental')->table('customer')->orderBy('id')->get();


        //DB::statement('ALTER TABLE customer AUTO_INCREMENT = 1');
        try
        {
            $points = 0;
            $maxpoints = 150;
            foreach ($Rcustomers as $Rcustomer)
            {
                $new = new Customer();
                $new->setRules([]);
                foreach($Rcustomer  as $field => $value)
                {
                    $new->$field = $value;
                }
                if ($Rcustomer->id == 0)
                {
                    $new->password = '9Bukkelo!';
                    $new->email = 'jbr@consiglia.dk';
                }
                $new->plain_password = $new->password;
                $new->password = Hash::make($new->plain_password );
                $new->plain_password = '';
                $new->verified = 1;
                $new->save();
                if ($Rcustomer->id == 0)
                {
                    $new->id = 0;
                    $new->save();
                }
                $points++;
                if ($points > $maxpoints) {
                    fwrite($handle, ".\n");
                    $points = 0;
                }
                else fwrite($handle, ".");
            }

            fwrite($handle, "\nHas imported table customer \n");

            static::copyTable('house', House::class, $handle);
            static::copyTable('house_i18n', HouseI18n::class, $handle);
            static::copyTable('period', Period::class, $handle);
            static::copyTable('contract', Contract::class, $handle);
            static::copyTable('contractlines', Contractline::class, $handle);
            static::copyTable('accountposts', Accountpost::class, $handle);
            static::copyTable('batchlog', Batchlog::class, $handle);
            static::copyTable('standardemail', Standardemail::class, $handle);
            static::copyTable('standardemail_i18n', StandardemailI18n::class, $handle);
            static::copyTable('testimonials', Testimonial::class, $handle);
            static::copyTable('batchtask', Batchtask::class, $handle);
            static::copyTable('emaillog', Emaillog::class, $handle);

            //Update quantity field in contractlines
            $contracts = Contract::with('contractlines')->get();
            foreach ($contracts as $contract) {
                foreach ($contract->contractlines as $contractline)  {
                    $contractline->quantity = $contract->persons;
                    $contractline->save();
                }
            }

            //die('Name of last customer in rental: '.var_dump($new));
        }
        catch(Exception $e)
        {
            fwrite($handle, "Error: ".$e->getMessage()."\n");
            fwrite($handle, "Trace: ".$e->getTrace()."\n");
        }
        fwrite($handle, "Finished\n");
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        fclose($handle);

    }

    protected static function copyTable($table, $model, $handle)
    {
        $points = 0;
        $maxpoints = 150;
        $Rrecords = DB::connection('rental')->table($table)->get();
        foreach ($Rrecords as $Rrecord)
        {
            $new = new $model();
            $new->setRules([]);
            $new::$ajax = true;
            $id = $Rrecord->id;
            foreach($Rrecord  as $field => $value)
            {
                $new->$field = $value;
            }
            $new->save();
            if ($id == 0) {
                $new->id = 0;
                $new->save();
            }
            $points++;
            if ($points > $maxpoints) {
                fwrite($handle, ".\n");
                $points = 0;
            }
            else fwrite($handle, ".");
        }

        unset($Rrecords);
        fwrite($handle, "\nHas imported table $table \n");
    }
}
