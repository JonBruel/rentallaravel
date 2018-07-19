@extends('layouts.app')
@section('content')
    <h3>Bookings</h3>



    <p class="header"><strong>{{$house->name}}</strong></p>
    <p class="header" style="margin-right:70px"><?php echo __('The bookings from now on and 52 weeks ahead.') . ' '
            . __('Pink weeks are occupied.') . ' '
            . __('Blue days are not yet scheduled for rental, but you are welcome to make an equiry.') . ' '
            . __('Green days are change days.') . ' '
            . __('Turquoise days are for private use.');?></p>
    <p class="header" id="tip"><?php echo __('For prices: move cursor to date, click to order.');?>
    <br/>
    <div class="row align-items-center justify-content-center">
        <div class="pagination-centered">{!! $pager->links('vendor/pagination/bootstrap-4', ['elements' => $elements, 'offset' => $offset]) !!}</div>
    </div>


    <div id="calendar">
        <table>

            <tr>
                <td colspan="4">
                    <?php echo __('Period');?>: <span id="period">N/A</span><br/>
                    <?php echo __('Price');?>: <span id="price">N/A</span><br/>
                </td>
            </tr>
            <tr style="margin-top: 15px">
                <?php
                $j = 1;
                for ($i = 0; $i < 12; $i++)
                {
                    echo '<td height="160" valign="top">'.$cal[$i].'</td>';
                    if (($j==4) OR ($j==8)) echo '</tr><tr>';
                    $j++;
                }
                ?>
            </tr>
        </table>
    </div>
    <p class="header" id="tipfixed"><?php echo __('For prices: move cursor to date, click to order.');?></p>

@endsection