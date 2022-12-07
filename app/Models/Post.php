<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Cviebrock\EloquentSluggable\Sluggable;



class Post extends Model
{
    use CrudTrait;
  use Sluggable;
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'posts';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
//     protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
public function category()
{
    return $this->belongsTo(Category::class,'category_id');
}

public function tag()
{
    return $this->belongsToMany(Tag::class);
}
public function user(){
    return $this->belongsTo(User::class,'author');
}
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeActive($query)
    {
        return $query->where('active', '1');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function setBase64Attribute($value)
    {

        $this->attributes['base64'] = $value;

        $attribute_name = "thumb";
            // or use your own disk, defined in config/filesystems.php
            $disk = 'public';
            // destination path relative to the disk above
            $destination_path = "public/uploads/thumbs";

            // if the image was erased
            if ($value == null) {
                // delete the image from disk
                \Storage::disk($disk)->delete($this->{$attribute_name});

                // set null in the database column
                $this->attributes[$attribute_name] = null;
            }

            // if a base64 was sent, store it in the db
            if (Str::startsWith($value, 'data:image')) {
                // 0. Make the image
                $image = \Image::make($value)->encode('jpg', 90);

                // 1. Generate a filename.
                $filename = md5($value . time()) . '.jpg';

                // 2. Store the image on disk.
                \Storage::disk($disk)->put($destination_path . '/' . $filename, $image->stream());

                // 3. Delete the previous image, if there was one.
                \Storage::disk($disk)->delete($this->{$attribute_name});

                // 4. Save the public path to the database
                // but first, remove "public/" from the path, since we're pointing to it
                // from the root folder; that way, what gets saved in the db
                // is the public URL (everything that comes after the domain name)
                $public_destination_path = Str::replaceFirst('public/', '', $destination_path);
                $this->attributes[$attribute_name] = $public_destination_path . '/' . $filename;
            }

    }
    protected function getThumbAttribute($value)
    {
       return $value;
    }
    protected static function booted()
{
    static::deleting(function ($post) {
        PostTag::where('post_id', $post->id)->delete();
        // ...
    });

}

}
