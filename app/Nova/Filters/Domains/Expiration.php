<?php

namespace App\Nova\Filters\Domains;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use App\Models\FindingStatus as Model;

class Expiration extends Filter
{
    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        $date = new \DateTime();

        // Valid
        if ($value == 'valid') {
            $from = $date->modify('+15 day')->format('Y-m-d H:i:s');

            return $query->whereHas('whois', function ($query) use ($from) {
                $query->where('whois.expiration_date', '>', $from);
            });
        // Close to expire
        } elseif ($value == 'close') {
            $from = $date->format('Y-m-d H:i:s');
            $to = $date->modify('+15 day')->format('Y-m-d H:i:s');

            return $query->whereHas('whois', function ($query) use ($from, $to) {
                $query->whereBetween('whois.expiration_date', [$from, $to]);
            });
        }

        // Expired
        return $query->whereHas('whois', function ($query) use ($date) {
            $query->where('whois.expiration_date', '<=', $date->format('Y-m-d H:i:s'));
        });
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return [
            'Valid' => 'valid',
            'Close to expire' => 'close',
            'Expired' => 'expired',
        ];
    }
}
