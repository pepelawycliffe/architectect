<?php

namespace App;

use Carbon\Carbon;
use Common\Auth\BaseUser;
use Common\Domains\CustomDomain;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\User
 *
 * @property int $id
 * @property string $email
 * @property string|null $password
 * @property array $permissions
 * @property int $activated
 * @property string|null $activation_code
 * @property string|null $activated_at
 * @property string|null $last_login
 * @property string|null $persist_code
 * @property string|null $reset_password_code
 * @property string|null $first_name
 * @property string|null $last_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $remember_token
 * @property string $avatar
 * @property string|null $language
 * @property string|null $country
 * @property string|null $timezone
 * @property int $confirmed
 * @property string|null $confirmation_code
 * @property-read string $display_name
 * @property-read bool $has_password
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @mixin Eloquent
 * @property string|null $stripe_id
 * @property string|null $card_brand
 * @property string|null $card_last_four
 * @property string|null $trial_ends_at
 * @property-read Collection $subscriptions
 * @property-read Collection $projects
 */
class User extends BaseUser
{
    use HasApiTokens;

    /**
     * @return BelongsToMany
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'users_projects');
    }

    public function custom_domains()
    {
        return $this->hasMany(CustomDomain::class);
    }
}
