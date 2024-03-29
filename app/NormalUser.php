<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Role;
use App\Account;
use App\Country;
use App\Province;
use App\District;
use App\Phone;
use App\Photo;
use App\Comment;
use App\Post;
use App\Question;
class NormalUser extends Model
{
    /**
     * The attributes that are mass assignable.
    */

    protected $fillable = [
    	"fullName",
        "role_id",
    	"status",
    	"province",
    	"district",
    	"street",
    	"gender",
    	"DateOfBirth",
        "country_id",
        "province_id",
        "Bio",
        "district_id"
    ];

    /*
        Relationship with roles
    */
    public function role(){
        return $this->belongsTo(Role::class);
    }

    /*
        Relationship with accounts
    */
    public function account(){
        return $this->morphOne(Account::class,"owner");
    }

    // Relationship with country
    public function country(){
        return $this->belongsTo(Country::class);
    } 

    // Relationship with province
    public function province(){
        return $this->belongsTo(Province::class);
    } 
    
    // Relationship with district
    public function district(){
        return $this->belongsTo(District::class);
    } 

    // Relationship with the post  table based on adding them to favorite 
    public function favoritePosts(){
        return $this->morphedByMany(Post::class,"fav","favorites","normalUser_id")->withTimeStamps();
    }

    // Relationship with the qustions  table based on adding them to favorite 
    public function favoriteQuestions(){
        return $this->morphedByMany(Question::class,"fav","favorites","normalUser_id")->withTimeStamps();
    }

    // Relationship with doctors table based on following them
    public function following(){
        return $this->belongsToMany(Doctor::class,"follows","normal_user_id","doctor_id")->withTimeStamps();
    }

    // Relationship with qustions table
    public function questions(){
        return $this->hasMany(Question::class,"normal_user_id","id");
    }   
}
