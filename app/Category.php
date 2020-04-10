<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['user', 'name', 'limit', 'recurring'];

    public function getTotalForMonth($month) {
        if($this->recurring) return $this->limit;
        return Expense::where('user', auth()->user()->id)->where('category', $this->id)->whereMonth('created_at', $month)->sum('amount');
    }

    public function getTotalForWeek() {
        return Expense::where('user', auth()->user()->id)->where('category', $this->id)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('amount');
    }

    public static function weekOfMonth($date, $rollover)
    {
        $cut = substr($date, 0, 8);
        $daylen = 86400;

        $timestamp = strtotime($date);
        $first = strtotime($cut . "00");
        $elapsed = ($timestamp - $first) / $daylen;

        $weeks = 1;

        for ($i = 1; $i <= $elapsed; $i++)
        {
            $dayfind = $cut . (strlen($i) < 2 ? '0' . $i : $i);
            $daytimestamp = strtotime($dayfind);

            $day = strtolower(date("l", $daytimestamp));

            if($day == strtolower($rollover))  $weeks ++;
        }

        return $weeks;
    }
}
