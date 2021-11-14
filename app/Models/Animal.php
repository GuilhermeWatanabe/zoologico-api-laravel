<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'scientific_name',
        'zoo_wing',
        'image_url',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function user()
    {
        return $this->morphOne(User::class, 'profileable');
    }

    /**
     * Change the is_enable value to a string.
     *
     * @param boolean $isEnabled
     * @return string
     */
    public function getIsEnabledAttribute(bool $isEnabled)
    {
        if($isEnabled == true) {
            return 'ATIVADO';
        }

        return 'DESATIVADO';
    }
}
