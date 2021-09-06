<?php namespace App;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * App\BuilderPage
 *
 * @property int $id
 * @property string $name
 * @property string|null $html
 * @property string|null $css
 * @property string|null $js
 * @property string $theme
 * @property int $pageable_id
 * @property string $pageable_type
 * @property string|null $description
 * @property string|null $tags
 * @property string|null $title
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $pageable
 * @mixin \Eloquent
 */
class BuilderPage extends Eloquent {

    protected $guarded = ['id'];

   	public function pageable()
    {
        return $this->morphTo();
    }
}