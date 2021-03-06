<?php

namespace App\Http\Regions;

class FrontpageFlight
{
    public function render($flights)
    {
        return component('FlexGrid')
            ->with('cols', 3)
            ->with(
                'items',
                $flights->map(function ($flight, $index) {
                    $destination = $flight->destinations->first();

                    if ($destination) {
                        return region('DestinationBar', $destination, ['purple', 'yellow', 'red'][$index]) .
                            region('FlightCard', $flight);
                    }
                })
            );
    }
}
