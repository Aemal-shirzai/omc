<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function __construct(){
        $this->middleware("auth");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return "done";
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        //
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




} // End of the controller class
