<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 18-10-2018
 * Time: 08:18
 */

namespace App\Helpers;

use App\Models\Customer;
use Carbon\Carbon;
use Schema;

/**
 * Class GDPRDelete aiming at deleting customer data not required.
 * @package App\Helpers
 */
class GDPRDelete
{

    /**
     * In the present solutions we delete in the following way:
     *
     * 1) Customers with no contracts are completely deleted after 1 year.
     * 2) Customers with contracts older than 6 years will be partially where all customer data will be deleted, but the id will remain as well as the contracts and accountposts. The mails, which will include the customer name will also be deleted.
     * At this stage possible backups are not deleted. Also the log, which stores changes, is not deleted.
     */
    public static function gdprdelete()
    {
        $filename = base_path().'/storage/logs/gdprdelete.txt';
        $handle = fopen($filename, 'w+');
        fwrite($handle, "Starting gdpr delete.\n");
        $customers = Customer::where('customertypeid', 1000)->whereDate('created_at','<', Carbon::now()->subYears(1))->where('status',1)->where('id', '!=', 10)
            ->with('accountposts')
            ->with('emaillogs')->get();
        fwrite($handle, "Found  " . sizeof($customers) . " for further analysis.\n");

        foreach($customers as $customer) {
            fwrite($handle, "Analyzing customer with id: " . $customer->id . ".\n");
            //Delete customers without purchases
            $accountposts = $customer->accountposts();
            $count = $accountposts->where('posttypeid', 10)->count();
            if ($count == 0) {
                fwrite($handle, "Deleting customer with id: " . $customer->id . ".\n");
                $customer->delete();
                continue;
            }

            //Annonymise customers with purchases older than 6 years.
            $latest = $accountposts->where('posttypeid', 10)
                ->whereDate('created_at', '>', Carbon::now()->subYears(6))
                ->orderBy('created_at', 'desc')->first();
            if (!$latest) {
                fwrite($handle, "Annonymising customer with id: " . $customer->id . " and deleting mails.\n");
                $customer->status = 2;
                $customer->login = $customer->id;
                $customer->email = $customer->id;
                $customer->setRules([]);
                $fields = array_diff(Schema::getColumnListing($customer->getTable()), ['email', 'id', 'ownerid', 'login', 'cultureid', 'status', 'created_at', 'updated_at']);
                foreach ($fields as $field) $customer->$field = '';
                $customer->save();

                //Delete Emails as they include the name of the customer
                $customer->emaillogs()->delete();
            }
        }
        fwrite($handle, "Finished \n");
        fclose($handle);
    }
}