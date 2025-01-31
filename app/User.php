<?php

namespace App;

use App\Models\Extension;
use App\Models\Permission;
use App\Models\Server;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UsesUuid;

/**
 * App\User
 *
 * @property-read mixed $id
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User find($value)
 */
class User extends Authenticatable
{
    use UsesUuid, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'status',
        'forceChange',
        'objectguid',
        'auth_type',
        'last_login_at',
        'last_login_ip',
        'locale'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function isAdmin()
    {
        // Very simply check status, this function created for more human like code write experience.
        return $this->status == 1;
    }

    public function servers()
    {
        return Server::get()->filter(function ($server) {
            return Permission::can(user()->id, 'server', 'id', $server->id);
        });
    }

    public function extensions()
    {
        return Extension::get()->filter(function ($extension) {
            return Permission::can(
                user()->id,
                'extension',
                'id',
                $extension->id
            );
        });
    }

    public function widgets()
    {
        return $this->hasMany("\App\Models\Widget");
    }

    public function tokens()
    {
        return $this->hasMany('\App\Models\Token');
    }

    public function settings()
    {
        return $this->hasMany('\App\Models\UserSettings');
    }

    public function keys()
    {
        return $this->hasMany('\App\Models\ServerKey');
    }

    public function notifications()
    {
        return $this->hasMany('\App\Models\Notification');
    }

    public function favorites()
    {
        return $this->belongsToMany('\App\Models\Server', 'user_favorites')
            ->get()
            ->filter(function ($server) {
                return Permission::can(user()->id, 'server', 'id', $server->id);
            });
    }

    public function permissions()
    {
        return $this->morphMany('App\Models\Permission', 'morph');
    }

    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', "role_users");
    }

    public function accessTokens()
    {
        return $this->hasMany('\App\Models\AccessToken');
    }
}
