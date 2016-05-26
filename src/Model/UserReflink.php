<?php

namespace KodiCMS\Users\Model;

use Illuminate\Database\Eloquent\Model;

class UserReflink extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_reflinks';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'token';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['handler', 'properties', 'user_id'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'handler' => 'string',
        'token' => 'string',
        'properties' => 'json',
    ];

    /**
     * @return string
     */
    public function linkToken()
    {
        return route('reflink.token', ['token' => $this->token]);
    }

    /**
     * @return string
     */
    public function link()
    {
        return route('reflink.form');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
