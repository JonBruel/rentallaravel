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

class ContractUpdated
{
    use SerializesModels;

    public $contract;

    /**
     * Create a new event instance.
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