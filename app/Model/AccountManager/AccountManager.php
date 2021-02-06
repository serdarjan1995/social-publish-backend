<?php

namespace App\Model\AccountManager;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Post
 *
 * @mixin Builder
 */
class AccountManager extends Model
{
    use SoftDeletes;

    public $table = 'account_manager';

    /**
     * @var HigherOrderBuilderProxy|mixed
     */
    private $social_network_id;

    /**
     * @var HigherOrderBuilderProxy|mixed
     */
    private $user_id;

    /**
     * @var HigherOrderBuilderProxy|mixed
     */
    private $login_type;

    /**
     * @var HigherOrderBuilderProxy|mixed
     */
    private $can_post;

    /**
     * @var HigherOrderBuilderProxy|mixed
     */
    private $name;

    /**
     * @var HigherOrderBuilderProxy|mixed
     */
    private $username;

    /**
     * @var HigherOrderBuilderProxy|mixed
     */
    private $auth_token;

    /**
     * @var HigherOrderBuilderProxy|mixed
     */
    private $profile_id;

    /**
     * @var HigherOrderBuilderProxy|mixed
     */
    private $avatar_url;

    /**
     * @var HigherOrderBuilderProxy|mixed
     */
    private $status;

    /**
     * @var HigherOrderBuilderProxy|mixed
     */
    private $category_id;

    /**
     * @var HigherOrderBuilderProxy|mixed
     */
    private $data;

    protected $fillable = [
        'social_network_id',
        'user_id',
        'login_type',
        'can_post',
        'name',
        'username',
        'auth_token',
        'profile_id',
        'parent_id',
        'avatar_url',
        'account_url',
        'status',
        'category_id',
        'data'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
