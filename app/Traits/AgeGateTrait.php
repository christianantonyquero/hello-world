<?php

namespace App\Traits;

trait AgeGateTrait
{
    public function scopeAgeGate($query)
    {
        return $query->where(function($query){
            return $query->where('minimum_age', '<=', auth()->user()->age())
                         ->orWhere('minimum_age', null);
        });
    }

    public function ageGateAccess()
    {
        return $this->minimum_age == null ||
               $this->minimum_age <= auth()->user()->age();
    }
}
