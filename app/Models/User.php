<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'branch_id',
        'role_id',
        'phone',
        'address',
        'status',
        'fake_sale_percentage',
        'image',
    ];
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function isSuperAdmin()
    {
        return $this->role_id == 2 || ($this->role && in_array(strtolower(trim($this->role->slug)), ['superadmin', 'super-admin', 'super_admin']));
    }

    public function isAdmin()
    {
        return $this->role_id == 1 || ($this->role && in_array(strtolower(trim($this->role->slug)), ['admin']));
    }

    public function isAdminOrSuperAdmin()
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }

    public function getBranchIdAttribute($value)
    {
        if ($this->isSuperAdmin()) {
            return 1;
        }
        if (auth()->check() && auth()->id() === $this->id) {
            if (is_branch_switch_enabled() && ($this->isAdminOrSuperAdmin() || (function_exists('check_permission') && check_permission('switch.branch')))) {
                return 1;
            }
        }
        return $value;
    }
}

