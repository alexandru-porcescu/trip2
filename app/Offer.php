<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;

class Offer extends Model
{
    protected $fillable = ['id', 'user_id', 'title', 'body', 'status', 'price', 'data', 'start_at', 'end_at'];

    protected $dates = ['start_at', 'end_at', 'created_at', 'updated_at'];

    protected $casts = [
        'data' => 'object'
    ];

    protected $appends = [
        'style_formatted',
        'start_at_formatted',
        'end_at_formatted',
        'duration_formatted',
        'coordinates'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function bookings()
    {
        return $this->hasMany('App\Booking');
    }

    public function startDestinations()
    {
        return $this->belongsToMany('App\Destination', 'offer_destination')->wherePivot('type', 'start');
    }

    public function endDestinations()
    {
        return $this->belongsToMany('App\Destination', 'offer_destination')->wherePivot('type', 'end');
    }

    public function scopePublic($query)
    {
        return $query->where('status', 1);
    }

    public function getPriceAttribute($value)
    {
        return $this->attributes['price'] = $value . '€';
    }

    public function getStyleFormattedAttribute()
    {
        if ($style = trans("offer.style.$this->style")) {
            return $style;
        }
        return $this->style;
    }

    public function getUserNameAttribute()
    {
        return $this->user->name;
    }

    public function getDurationFormattedAttribute()
    {
        return Date::parse($this->end_at)->diffForHumans($this->start_at, true);
    }

    public function getStartAtFormattedAttribute()
    {
        return Date::parse($this->start_at)->format('j. M Y');
    }

    public function getEndAtFormattedAttribute()
    {
        return Date::parse($this->end_at)->format('j. M Y');
    }

    public function getCoordinatesAttribute()
    {
        return $this->endDestinations
            ->first()
            ->vars()
            ->coordinates();
    }
}