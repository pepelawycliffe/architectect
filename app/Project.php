<?php namespace App;

use Carbon\Carbon;
use Common\Domains\CustomDomain;
use Common\Search\Searchable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * App\Project
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $uuid
 * @property int $published
 * @property int $public
 * @property string $template
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|BuilderPage[] $pages
 * @property-read Collection|User[] $users
 * @property array settings
 * @mixin \Eloquent
 */
class Project extends Eloquent
{
    use Searchable;

    protected $guarded = ['id'];

    protected $casts = [
        'published' => 'boolean'
    ];

    public function pages()
    {
        return $this->morphMany(BuilderPage::class, 'pageable');
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'users_projects',
            'project_id',
            'user_id',
        );
    }

    public function domain()
    {
        return $this->morphOne(CustomDomain::class, 'resource')->select(
            'id',
            'host',
            'resource_id',
            'resource_type',
        );
    }

    public function getSettingsAttribute(?string $value): array
    {
        return $value ? json_decode($value, true) : [];
    }

    public function setSettingsAttribute(array $value)
    {
        $this->attributes['settings'] = json_encode($value);
    }

    public function formsEmail(): string
    {
        return $this->settings['formsEmail'] ?? $this->users->first()->email;
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->username,
            'created_at' => $this->created_at->timestamp ?? '_null',
            'updated_at' => $this->updated_at->timestamp ?? '_null',
        ];
    }

    public static function filterableFields(): array
    {
        return ['id', 'created_at', 'updated_at'];
    }
}
