<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = [
        'page_id',
        'page_name',
        'page_url',
        'copy',
        'description',
        'headline',
        'cta',
        'url',
        'creative_type',
        'creative_url',
        'thumbnail_url',
        'start_date',
        'active_time',
    ];

    protected $casts = [
        'start_date' => 'date',
    ];

    public function users()
{
    return $this->belongsToMany(User::class, 'user_ad');
}


}

?>