<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 22-10-2018
 * Time: 19:41
 */

namespace App\Events;
use App\Models\Contract;
use Illuminate\Queue\SerializesModels;
use App\Helpers\ShowCalendar;

/**
 * Class ContractUpdated controls task(s) to be done when the contract is updated or created as
 * controlled by the $dispatchesEvents in Contract.
 *
 * @package App\Events
 */
class ContractUpdated
{
    use SerializesModels;

    public $contract;

    /**
     * Deletes the file cash. This could be refined ro only update the calendar involved, not all of them.
     *
     * @todo Refine method if the system is used for a large number of houses with lot's of activity.
     *
     * @param  \App\Models\Contract  $contract
     * @return void
     */
    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
        ShowCalendar::cache_delete('cache', 0);
    }
}