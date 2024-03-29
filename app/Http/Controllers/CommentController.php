<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Post;
use App\Doctor;
use App\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Storage;
use App\Notifications\commentToPosts;
use App\Notifications\commentForQuestion;


use Validator;

class CommentController extends Controller
{
    public function __construct(){
        $this->middleware("auth");
        $this->middleware("activeUsers");
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
        // Validations part start
        $validator = Validator::make($request->all(),[
            "content" => "bail|max:65500",
            "photo" => "bail|image|max:10240|min:1",
        ],[
            "content.max" => "Too long comment is not allowed", 
            "photo.image" => "Invalid file. Only photos are allowed...",
            "photo.max"   => "File too large. max 10MB...",
        ]);

        if($validator->fails()){
            if($request->ajax()){
                return response()->json(['errors'=>$validator->errors()]);
            }else{
                return back()->withInput()->withErrors($validator->errors());
            }
        }

        if($request->content == "" && !$request->hasFile("photo")){
            if($request->ajax()){
                return response()->json(['overAllError'=>"The comment content can not be null"]);
            }else{
                return back()->withInput()->with("error","The comment content can not be null");
            }
        }

        // Validation part End

        $user = Auth::user();
        // in order to store the correct account_id column in the commments table we need to know the current account_id the is stroed in the id column of the current loged in user account table
        $account_id  = $user->id;


        // to find the post to which the comment is added by hellping the post_id which is comming from a hidden input in the comment section
        $post = Post::findOrFail($request->post_id);

        //add column account_id to array request
        $request->merge(["account_id"=>$account_id]);
        // create the comment for the post
        $comment = $post->comments()->create($request->all());

        // if the request has photo then add it by the help of comment and photos relationship (photos)
        if($request->hasFile("photo")){
            $photo = $request->file("photo");
            $fullName  = $photo->getClientOriginalName();
            $onlyName = pathinfo($fullName,PATHINFO_FILENAME);
            $extension = $photo->getClientOriginalExtension();
            $nameToBeStored = $onlyName.time(). "." .$extension;
            $folder = "public/images/comments/";
            $photo->storeAs($folder,$nameToBeStored);
            $comment->photos()->create(["path"=>$nameToBeStored,"status"=>"1"]);
        }

        if(Auth::user()->isNot($post->owner->account)){
            $post->owner->account->notify(new commentToPosts($comment,$post));
        }

        if($comment){
            if($request->ajax()){
                $CreateTime = $comment->created_at->diffForHumans();
                $comment['createTime'] = $CreateTime; 
                $comment['fullName'] = $comment->comment_owner->owner->fullName;
                $comment['username'] = $comment->comment_owner->username;
                $comment['ownerPhoto'] = ($comment->comment_owner->photos()->where("status",1)->first() ? $comment->comment_owner->photos()->where("status",1)->first()->path : "");
                $comment['ownerType'] = $comment->comment_owner->owner_type;
                $comment['photo'] = ($comment->photos()->where("status",1)->first() ? $comment->photos()->where("status",1)->first()->path : "");
                return response()->json(["comment"=>$comment]);
            }
            return back()->with(["commentSuccess"=>"Your Comment has been Added.","post_id"=>$request->post_id]);
        }else{
            return back()->with(["error"=>"Comment Not"]);
        }
    }

    public function storeQuestion(Request $request){

        // Validations part start
        $validator = Validator::make($request->all(),[
            "content" => "bail|max:65500",
            "photo" => "bail|image|max:10240|min:1",
        ],[
            "content.max" => "Too long comment is not allowed", 
            "photo.image" => "Invalid file. Only photos are allowed...",
            "photo.max"   => "File too large. max 10MB...",
        ]);

        if($validator->fails()){
            if($request->ajax()){
                return response()->json(['errors'=>$validator->errors()]);
            }else{
                return back()->withInput()->withErrors($validator->errors());
            }
        }

        if($request->content == "" && !$request->hasFile("photo")){
            if($request->ajax()){
                return response()->json(['overAllError'=>"The comment content can not be null"]);
            }else{
                return back()->withInput()->with("error","The comment content can not be null");
            }
        }

        // Validation part End

        $user = Auth::user();
        // in order to store the correct account_id column in the commments table we need to know the current account_id the is stroed in the id column of the current loged in user account table
        $account_id  = $user->id;


        // to find the post to which the comment is added by hellping the post_id which is comming from a hidden input in the comment section
        $question = Question::findOrFail($request->question_id);
        //add column account_id to array request
        $request->merge(["account_id"=>$account_id]);
        // create the comment for the post
        $comment = $question->comments()->create($request->all());

        // if the request has photo then add it by the help of comment and photos relationship (photos)
        if($request->hasFile("photo")){
            $photo = $request->file("photo");
            $fullName  = $photo->getClientOriginalName();
            $onlyName = pathinfo($fullName,PATHINFO_FILENAME);
            $extension = $photo->getClientOriginalExtension();
            $nameToBeStored = $onlyName.time(). "." .$extension;
            $folder = "public/images/comments/";
            $photo->storeAs($folder,$nameToBeStored);
            $comment->photos()->create(["path"=>$nameToBeStored,"status"=>"1"]);
        }

        if(Auth::user()->isNot($question->owner->account)){
            $question->owner->account->notify(new commentForQuestion($comment,$question));
        }
        if($comment){
            if($request->ajax()){
                $CreateTime = $comment->created_at->diffForHumans();
                $comment['createTime'] = $CreateTime; 
                $comment['fullName'] = $comment->comment_owner->owner->fullName;
                $comment['username'] = $comment->comment_owner->username;
                $comment['ownerPhoto'] = ($comment->comment_owner->photos()->where("status",1)->first() ? $comment->comment_owner->photos()->where("status",1)->first()->path : "");
                $comment['ownerType'] = $comment->comment_owner->owner_type;
                $comment['photo'] = ($comment->photos()->where("status",1)->first() ? $comment->photos()->where("status",1)->first()->path : "");
                return response()->json(["comment"=>$comment]);
            }
            return back()->with(["commentSuccess"=>"Your Comment has been Added.","question_id"=>$request->question_id]);
        }else{
            return back()->with(["error"=>"Comment Not"]);
        }   
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment)
    {
        $commentOwner = $comment->comment_owner_type;
        return view("comments.edit",compact("comment",'commentOwner'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(CommentRequest $request, Comment $comment)
    {
        if($request->content == "" && !$request->hasFile("photo")){
             // to check if the user has removed the photo or not
            if($request->has("fileRemoved")){
                return back()->withInput()->with("error1","The comment content can not be null");
            }
            
        }

        if(Auth::user()->is($comment->comment_owner)){
            $commentAdd = $comment->update($request->all());

            // to check if the user has removed the photo or not
            if($request->has("fileRemoved")){
                $comment->photos()->delete();
            }

            if($request->hasFile("photo")){
                // delete old photos
                if($comment->photos()->count() > 0){
                    foreach($comment->photos as $photo){
                        Storage::delete("public/images/comments/".$photo->path);
                        $photo->delete();
                    }
                }
                // add photos
                $photo = $request->file("photo");
                $fullName = $photo->getClientOriginalName();
                $onlyExtentsion = $photo->getClientOriginalExtension();
                $onlyName = pathinfo($fullName,PATHINFO_FILENAME);
                $nameToBeStored = $onlyName . time() . "." .$onlyExtentsion;

                $photo->storeAs("public/images/comments/",$nameToBeStored);
                $comment->photos()->create(["path"=>$nameToBeStored,"status"=>1]);
            } // End of adding photo

            if($commentAdd){
                if($comment->to_type == "App\Post"){
                    return redirect()->action("PostController@show",$comment->to_id)->with(["commentSuccess"=>"Your comment has been edited"]);
                }else{
                    return redirect()->action("QuestionController@show",$comment->to_id)->with(["commentSuccess"=>"Your comment has been edited"]);
                }
            }else{
                return back()->with("error","Something Went Wrong pleas try again!");
            }
        } //checking authorization condition end
        else{
            abort(403);
        }

    } //manin update fun end

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        //
    } 

// Beggining of: function which is responsible for deleteing comments using ajax request
    public function delete(Request $request)
    {
        $user = Auth::user();
        
        $comment = Comment::findOrFail($request->comment_id);

        if($user->comments()->where("comments.id",$request->comment_id)->first()){
            if($comment->photos()->count() > 0){
                foreach($comment->photos as $comment_photo){
                    Storage::delete("public/images/comments/".$comment_photo->path);
                    $comment_photo->delete();
                }
            }
        
            $user->comments()->where("comments.id",$request->comment_id)->first()->delete();
        }
    }
// End of : function which is responsible for deleteing comments using ajax request




   // The funcion which add and update votes to post using ajax request
    public function Vote(Request $request){
        $type = $request->voteType; // the type of vote whether user click up vote or down vote
        $comment_id = $request->comment_id; // get the id of comment 
        $comment = Comment::find($comment_id); // To Find the the comment to which the vote is added or deleted 
        if(!$comment){     
            return null;
        }
        $user = Auth::user(); // to find the current authenticated user who click the upvote and downvote buttons
        $userLikedComments = $user->commentsVotes->where("id",$comment_id)->first(); // the variable which stores the recoreds of the current user in pivot table , note: only the recoreds which belongs to the comment id  which the we found above


        if($userLikedComments){ // if the user already voted that post
            if($type === "upVote"){ // if the use is clicking the upvote button
                if($userLikedComments->pivot->type == 0){ // if the user already voted and the vote is downvote
                    $userLikedComments->pivot->update(["type"=>"1"]); //then come and just update that to upvote by changin 0 to 1
                 }else{  //if the user already voted and the vote is upvote
                    $user->commentsVotes()->detach($userLikedComments); // then come and remove the vote from the user
                }
            }else{ // if the user is clicking the downVote Button
                if($userLikedComments->pivot->type == 0){ // the user is clickin downvote and the user already vote is downvote
                    $user->commentsVotes()->detach($userLikedComments); // then remove the already vote
                }else{ // the user is clicking downvote and the user already vote is upvote
                    $userLikedComments->pivot->update(["type"=>"0"]); // then just update the vote type by changing 1 to 0
                }
            }
        }else{ // if the user has not aleardy voted that comment
            if($type === "upVote"){ // if the user has not aleardy voted that post and clicking the upVote button
                $user->commentsVotes()->save($comment,["type"=>"1"]); // Then add a new record into the database with up vote type
            }else{ // if the user has not aleardy voted that comment and clicking the downVote button
                $user->commentsVotes()->save($comment,["type"=>"0"]); // Then add a new record into the database with down vote type
            }
        }

        $upVotes = $comment->votedBy()->where('type',1)->count();
        $downVotes = $comment->votedBy()->where('type',0)->count();
        $comment->update(["UpVotes"=>$upVotes,"DownVotes"=>$downVotes]);


    }// end of vote fuction

}// end of controller function
