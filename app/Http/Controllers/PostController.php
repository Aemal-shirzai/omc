<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\Storage;
use App\Notifications\Admin\postAdd;
use App\Post;
use App\Doctor;
use App\NormalUser;
use App\DiseaseCategory;


class PostController extends Controller
{
    public function __construct(){
        $this->middleware("auth")->except(["index","sortBy","show","searchResult","search"]);
        $this->middleware("activeUsers")->only(["create","store","update","edit","delete","vote","favorite"]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $posts = Post::latest()->paginate(20);
        $posts = Post::join("doctors","posts.doctor_id","=","doctors.id")->where("doctors.status",1)->orderby("posts.created_at","desc")->select("posts.*")->paginate(20);
        $mostVotedDoctors = Doctor::where("status",1)->orderBy("followers","desc")->get();
        // This number is for blade to show how many doctors
        $numberOfDoctors = 1;
        return view("posts.index",compact("posts","mostVotedDoctors","numberOfDoctors")); 
    }


// Beggining of the function which retrn the result of the posts search using ajax
    public function searchResult(Request $req){
        $posts = Post::join("doctors","posts.doctor_id","=","doctors.id")->where("doctors.status",1)->where("posts.title","like","%$req->data%")->select("posts.title")->get();
        if(count($posts) > 0){
            return response()->json(["resultFound"=>$posts]);
        }else{
            return response()->json(["resultNotFound"=>"Result Not Found"]);
        }
    }
// End of the function which retrn the result of the posts search using ajax

// Beggining of the function which search the posts
    public function search(Request $req){
        $this->validate($req,[
            "searchFor" => "bail|required|string|max:200",
        ]);
        // return $req->searchFor;
        $mostVotedDoctors = Doctor::where("status",1)->orderBy("followers","desc")->get();
        // This number is for blade to show how many doctors
        $numberOfDoctors = 1;
        $posts = Post::join("doctors","posts.doctor_id","=","doctors.id")->where("doctors.status",1)->where("posts.title","like","%$req->data%")->orderby("posts.created_at","desc")->select("posts.*")->paginate(20);
        return view("posts.postsSearch",compact("posts","mostVotedDoctors","numberOfDoctors"));
    }
// Beggining of the function which search the posts


    public function sortBy($type){
        if($type == "top"){
            $posts = Post::join("doctors","posts.doctor_id","=","doctors.id")->where("doctors.status",1)->orderby("posts.upVotes","desc")->select("posts.*")->paginate(20);
        }else if($type == "down"){
            $posts = Post::join("doctors","posts.doctor_id","=","doctors.id")->where("doctors.status",1)->orderby("posts.downVotes","desc")->select("posts.*")->paginate(20);  
        }else if($type == "mostFollowed"){
             $posts = Post::join("doctors","posts.doctor_id","=","doctors.id")->where("doctors.status",1)->orderby("posts.follower","desc")->select("posts.*")->paginate(20);
        }
        $mostVotedDoctors = Doctor::where("status",1)->orderBy("followers","desc")->paginate(20);
        // This number is for blade to show how many doctors
        $numberOfDoctors = 1;
        return view("posts.index",compact("posts","mostVotedDoctors","numberOfDoctors","type"));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        if($this->authorize("doctor_related",Auth::user())){
            $d_categories = DiseaseCategory::orderBy("category","asc")->get();       
            return view("posts.create",compact("d_categories"));

        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        if($this->authorize("doctor_related",Auth::user())){

            $user = Auth::user()->owner;
            $post = $user->posts()->create($request->all());

            if(Auth::user()->where("owner_type","App\NormalUser")->count() > 0){
                foreach(Auth::user()->where("owner_type","App\NormalUser")->get() as $normalUser){
                    if($normalUser->owner->role->role == "admin"){
                        $normalUser->notify(new postAdd($post));
                    }
                }
            }

            if($request->tags){
                foreach($request->tags as $tagId){
                    $tag = DiseaseCategory::findOrFail($tagId);
                     $post->tags()->save($tag);
                 }
            } // end of adding tags part

            if($request->hasFile("photo")){
                $photo = $request->file("photo");
                $fullName = $photo->getClientOriginalName();
                $onlyExtentsion = $photo->getClientOriginalExtension();
                $onlyName = pathinfo($fullName,PATHINFO_FILENAME);
                $nameToBeStored = $onlyName . time() . "." .$onlyExtentsion;

                $photo->storeAs("public/images/posts/",$nameToBeStored);
                $post->photos()->create(["path"=>$nameToBeStored,"status"=>1]);

            } // End of adding photo
            if($post){
                return back()->with("postAddSuccess","Your post was added!");
            }
        } // end of checking authorization
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        $mostVotedDoctors = Doctor::orderBy("followers","desc")->get();
        // This number is for blade to show how many doctors
        $numberOfDoctors = 1;
        return view("posts.show",compact('post','mostVotedDoctors','numberOfDoctors'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        if($this->authorize("doctor_related",Auth::user())){
             if(Auth::user()->is($post->owner->account)){
                $d_categories = DiseaseCategory::orderBy("category","asc")->get(); 
                return view("posts.edit",compact("post",'d_categories'));
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, Post $post)
    {
        if($this->authorize("doctor_related",Auth::user())){
            // return $request->all();
            $postAdd = $post->update($request->all());

           if($request->tags){
                $post->tags()->sync([]);
                foreach($request->tags as $tagId){
                    $tag = DiseaseCategory::findOrFail($tagId);
                     $post->tags()->save($tag);
                 }
            }else{ // it has two possibilities one if the post does not have tags already and second one is that the user disselected all tags
                if($post->tags()->count() > 0){
                    $post->tags()->sync([]);
                }
             // end of adding tags part
            }

            // to check if the user has removed the photo or not
            if($request->has("fileRemoved")){
                $post->photos()->delete();
            }

            if($request->hasFile("photo")){
                // delete old photos
                if($post->photos()->count() > 0){
                    foreach($post->photos as $photo){
                        Storage::delete("public/images/posts/".$photo->path);
                        $photo->delete();
                    }
                }
                // add photos
                $photo = $request->file("photo");
                $fullName = $photo->getClientOriginalName();
                $onlyExtentsion = $photo->getClientOriginalExtension();
                $onlyName = pathinfo($fullName,PATHINFO_FILENAME);
                $nameToBeStored = $onlyName . time() . "." .$onlyExtentsion;

                $photo->storeAs("public/images/posts/",$nameToBeStored);
                $post->photos()->create(["path"=>$nameToBeStored,"status"=>1]);
            } // End of adding photo

            if($postAdd){
                return redirect()->action("PostController@show",$post->id)->with(["postEditSuccess"=>"Your Post has been edited"]);
            }else{
                return back()->with("error","Something Went Wrong pleas try again!");
            }
        } // the authorization if end
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //
    }


// The funcion which add and update votes to post using ajax request
    public function Vote(Request $request){
        $type = $request->voteType; // the type of vote whether user click up vote or down vote
        $post_id = $request->post_id; // get the id of post 
        $post = Post::find($post_id); // To Find the the post to which the vote is added or deleted 
        if(!$post){     
            return null;
        }
        $user = Auth::user(); // to find the current authenticated user who click the upvote and downvote buttons
        $userLikedPost = $user->postsVotes->where("id",$post_id)->first(); // the variable which stores the recoreds of the current user in pivot table , note: only the recoreds which belongs to the post id  which the we found above


        if($userLikedPost){ // if the user already voted that post
            if($type === "upVote"){ // if the use is clicking the upvote button
                if($userLikedPost->pivot->type == 0){ // if the user already voted and the vote is downvote
                    $userLikedPost->pivot->update(["type"=>"1"]); //then come and just update that to upvote by changin 0 to 1

                 }else{  //if the user already voted and the vote is upvote
                    $user->postsVotes()->detach($userLikedPost); // then come and remove the vote from the user
                }
            }else{ // if the user is clicking the downVote Button
                if($userLikedPost->pivot->type == 0){ // the user is clickin downvote and the user already vote is downvote
                    $user->postsVotes()->detach($userLikedPost); // then remove the already vote
                }else{ // the user is clicking downvote and the user already vote is upvote
                    $userLikedPost->pivot->update(["type"=>"0"]); // then just update the vote type by changing 1 to 0
                }
            }
        }else{ // if the user has not aleardy voted that post
            if($type === "upVote"){ // if the user has not aleardy voted that post and clicking the upVote button
                $user->postsVotes()->save($post,["type"=>"1"]); // Then add a new record into the database with up vote type
            }else{ // if the user has not aleardy voted that post and clicking the upVote button
                $user->postsVotes()->save($post,["type"=>"0"]); // Then add a new record into the database with down vote type
            }
        }

        $upVotes = $post->votedBy()->where('type',1)->count();
        $downVotes = $post->votedBy()->where('type',0)->count();
        $post->update(["UpVotes"=>$upVotes,"DownVotes"=>$downVotes]);

    } 
// end of vote function


// Beggining of : The function which add the post favorites by normal usrs   
    public function favorite(Request $request){
    
       $user = Auth::user(); // Current Authenticated user
       $post = Post::findOrFail($request->post_id); // The post which is going to be added as favorites
       // return $user->owner->favoritePosts()->where("posts.id",1)->first();
       if($user->owner_type == "App\NormalUser"){

            // if the currnet user has already added the post to favorite
            if($user->owner->favoritePosts()->where("posts.id",$request->post_id)->first()){
                $user->owner->favoritePosts()->detach($post);
            }else{// if the user has not already added the post to favorite
                $user->owner->favoritePosts()->save($post);
            }
       }

       $followers = $post->favoritedBy()->count();
       $post->update(["follower"=>$followers]);
    } 
// End of :The function which add the post favorites by normal usrs



// Beggining fo the function wich delete posts using ajax
    public function delete(Request $request){
        
        if((Auth::user()->owner_type == "App\NormalUser" && Auth::user()->owner->role->role == "admin")|| $this->authorize("doctor_related",Auth::user())){

            // find post
            $post = Post::findOrFail($request->post_id);
            //  if the user which is requesting to delte the post really has this post or not

                // To delte the photo for the post
                if($post->photos()->count() > 0){
                    foreach($post->photos as $photo){
                        Storage::delete("public/images/posts/".$photo->path);
                        $photo->delete();
                    }
                }

                // To delte the tags for the post
                if($post->tags()->count() > 0){
                    $post->tags()->sync([]);
                }

                // To delte the post
                $post->delete();


        } // End of authorize function
    }
// End fo the function wich delete posts using ajax




} 
// End of the controller class
