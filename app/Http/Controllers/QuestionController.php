<?php

namespace App\Http\Controllers;

use App\Question;
use App\Doctor;
use App\DiseaseCategory;
use Illuminate\Http\Request;
use App\Http\Requests\QuestionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
 use App\Notifications\Admin\questionAdd;

class QuestionController extends Controller
{
    public function __construct(){
        $this->middleware("auth")->except(["index","sortBy","show","searchResult","search"]);
        $this->middleware("activeUsers")->only("create","store","edit","update","vote","delete","favorite");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questions = Question::join("normal_users","normal_users.id","=","questions.normal_user_id")->where("normal_users.status",1)->orderBy("questions.created_at","desc")->select("questions.*")->paginate(20);
        $mostVotedDoctors = Doctor::where("status",1)->orderBy("followers","desc")->get();
        // This number is for blade to show how many doctors
        $numberOfDoctors = 1;
        return view("questions.index",compact("questions","mostVotedDoctors","numberOfDoctors"));
    }

// Beggining of the function which retrn the result of the questions search using ajax
    public function searchResult(Request $req){
        // $questions = Question::where("title","like","%$req->data%")->select("title")->distinct()->get();
        $questions = Question::join("normal_users","questions.normal_user_id","=","normal_users.id")->where("normal_users.status",1)->where("questions.title","like","%$req->data%")->select("questions.title")->get();
        if(count($questions) > 0){
            return response()->json(["resultFound"=>$questions]);
        }else{
            return response()->json(["resultNotFound"=>"Result Not Found"]);
        }
    }
// End of the function which retrn the result of the questions search using ajax

// Beggining of the function which search the questions
    public function search(Request $req){
        $this->validate($req,[
            "searchFor" => "bail|required|string|max:60",
        ]);
        // return $req->searchFor;
        $questions = Question::join("normal_users","normal_users.id","=","questions.normal_user_id")->where("normal_users.status",1)->where("questions.title","like","%$req->data%")->orderBy("questions.created_at","desc")->select("questions.*")->paginate(20);
        $mostVotedDoctors = Doctor::where("status",1)->orderBy("followers","desc")->get();
        // This number is for blade to show how many doctors
        $numberOfDoctors = 1;
        return view("questions.questionsSearch",compact("questions","mostVotedDoctors","numberOfDoctors"));
    }
// Beggining of the function which search the questions


    public function sortBy($type){
        if($type == "top"){
            $questions = Question::orderBy("upVotes","desc")->paginate(20);
            $questions = Question::join("normal_users","normal_users.id","=","questions.normal_user_id")->where("normal_users.status",1)->orderBy("questions.upVotes","desc")->select("questions.*")->paginate(20);
        }else if($type == "down"){
            $questions = Question::orderBy("downVotes","desc")->paginate(20);
            $questions = Question::join("normal_users","normal_users.id","=","questions.normal_user_id")->where("normal_users.status",1)->orderBy("questions.downVotes","desc")->select("questions.*")->paginate(20);   
        }else if($type == "mostFollowed"){
            $questions = Question::orderBy("follower","desc")->paginate(20);
            $questions = Question::join("normal_users","normal_users.id","=","questions.normal_user_id")->where("normal_users.status",1)->orderBy("questions.follower","desc")->select("questions.*")->paginate(20);
        }
        $mostVotedDoctors = Doctor::where("status",1)->orderBy("followers","desc")->get();
        // This number is for blade to show how many doctors
        $numberOfDoctors = 1;
        return view("questions.index",compact("questions","mostVotedDoctors","numberOfDoctors","type"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if($this->authorize("normalUser_related",Auth::user())){
            $d_categories = DiseaseCategory::orderBy("category","asc")->get(); 
            return view("questions.create",compact("d_categories"));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(QuestionRequest $request)
    {
         if($this->authorize("normalUser_related",Auth::user())){
            $user = Auth::user()->owner;
            $question = $user->questions()->create($request->all());

            if(Auth::user()->where("owner_type","App\NormalUser")->count() > 0){
                foreach(Auth::user()->where("owner_type","App\NormalUser")->get() as $normalUser){
                    if($normalUser->owner->role->role == "admin"){
                        $normalUser->notify(new questionAdd($question));
                    }
                }
            }


            if($request->tags){
                foreach($request->tags as $tagId){
                    $tag = DiseaseCategory::findOrFail($tagId);
                     $question->tags()->save($tag);
                 }
            } // end of adding tags part

            if($request->hasFile("photo")){
                $photo = $request->file("photo");
                $fullName = $photo->getClientOriginalName();
                $onlyExtentsion = $photo->getClientOriginalExtension();
                $onlyName = pathinfo($fullName,PATHINFO_FILENAME);
                $nameToBeStored = $onlyName . time() . "." .$onlyExtentsion;

                $photo->storeAs("public/images/questions/",$nameToBeStored);
                $question->photos()->create(["path"=>$nameToBeStored,"status"=>1]);

            } // End of adding photo
            if($question){
                return back()->with("postAddSuccess","Your question was added!");
            }
        } // end of authorization if statement
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function show(Question $question)
    {
        $mostVotedDoctors = Doctor::where("status",1)->orderBy("followers","desc")->get();
        // This number is for blade to show how many doctors
        $numberOfDoctors = 1;
        return view("questions.show",compact('question','mostVotedDoctors','numberOfDoctors'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function edit(Question $question)
    {
        if($this->authorize("normalUser_related",Auth::user())){
            $d_categories = DiseaseCategory::orderBy("category","asc")->get();
            return view("questions.edit",compact("question","d_categories"));   
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function update(QuestionRequest $request, Question $question)
    {
        if($this->authorize("normalUser_related",Auth::user())){
            // return $request->all();
            $questionAdd = $question->update($request->all());

           if($request->tags){
                $question->tags()->sync([]);
                foreach($request->tags as $tagId){
                    $tag = DiseaseCategory::findOrFail($tagId);
                     $question->tags()->save($tag);
                 }
            }else{ // it has two possibilities one if the question does not have tags already and second one is that the user disselected all tags
                if($question->tags()->count() > 0){
                    $question->tags()->sync([]);
                }
             // end of adding tags part
            }

            // to check if the user has removed the photo or not
            if($request->has("fileRemoved")){
                $question->photos()->delete();
            }

            if($request->hasFile("photo")){
                // delete old photos
                if($question->photos()->count() > 0){
                    foreach($question->photos as $photo){
                        Storage::delete("public/images/questions/".$photo->path);
                        $photo->delete();
                    }
                }
                // add photos
                $photo = $request->file("photo");
                $fullName = $photo->getClientOriginalName();
                $onlyExtentsion = $photo->getClientOriginalExtension();
                $onlyName = pathinfo($fullName,PATHINFO_FILENAME);
                $nameToBeStored = $onlyName . time() . "." .$onlyExtentsion;

                $photo->storeAs("public/images/questions/",$nameToBeStored);
                $question->photos()->create(["path"=>$nameToBeStored,"status"=>1]);
            } // End of adding photo

            if($questionAdd){
                return redirect()->action("QuestionController@show",$question->id)->with(["questionEditSuccess"=>"Your Question has been edited"]);
            }else{
                return back()->with("error","Something Went Wrong pleas try again!");
            }
        } // the authorization if end
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function destroy(Question $question)
    {
        //
    }

    // The funcion which add and update votes to qustion using ajax request
    public function vote(Request $request){
        $type = $request->voteType; // the type of vote whether user click up vote or down vote
        $question_id = $request->question_id; // get the id of qustion 
    
        $question = Question::find($question_id); // To Find the the qustion to which the vote is added or deleted 
        if(!$question){     
            return null;
        }
        $user = Auth::user(); // to find the current authenticated user who click the upvote and downvote buttons

        $userLikedQuestions = $user->questionsVotes->where("id",$question_id)->first(); // the variable which stores the recoreds of the current user in pivot table , note: only the recoreds which belongs to the qustion id  which the we found above


        if($userLikedQuestions){ // if the user already voted that qustion
            if($type === "upVote"){ // if the use is clicking the upvote button
                if($userLikedQuestions->pivot->type == 0){ // if the user already voted and the qustion is downvote
                    $userLikedQuestions->pivot->update(["type"=>"1"]); //then come and just update that to upvote by changin 0 to 1

                 }else{  //if the user already voted and the vote is upvote
                    $user->questionsVotes()->detach($userLikedQuestions); // then come and remove the vote from the user
                }
            }else{ // if the user is clicking the downVote Button
                if($userLikedQuestions->pivot->type == 0){ // the user is clickin downvote and the user already vote is downvote
                    $user->questionsVotes()->detach($userLikedQuestions); // then remove the already vote
                }else{ // the user is clicking downvote and the user already vote is upvote
                    $userLikedQuestions->pivot->update(["type"=>"0"]); // then just update the vote type by changing 1 to 0
                }
            }
        }else{ // if the user has not aleardy voted that post
            if($type === "upVote"){ // if the user has not aleardy voted that qustion and clicking the upVote button
                $user->questionsVotes()->save($question,["type"=>"1"]); // Then add a new record into the database with up vote type
            }else{ // if the user has not aleardy voted that qustion and clicking the upVote button
                $user->questionsVotes()->save($question,["type"=>"0"]); // Then add a new record into the database with down vote type
            }
        }

        $upVotes = $question->votedBy()->where('type',1)->count();
        $downVotes = $question->votedBy()->where('type',0)->count();
        $question->update(["UpVotes"=>$upVotes,"DownVotes"=>$downVotes]);

    } 
// end of vote function

// Beggining of : The function which add the question favorites by normal usrs   
    public function favorite(Request $request){
    
       $user = Auth::user(); // Current Authenticated user
       $question = Question::findOrFail($request->question_id); // The qustion which is going to be added as favorites
 
       if($user->owner_type == "App\NormalUser"){

            // if the currnet user has already added the qustion to favorite
            if($user->owner->favoriteQuestions()->where("questions.id",$request->question_id)->first()){
                $user->owner->favoriteQuestions()->detach($question);
            }else{// if the user has not already added the qustion to favorite
                $user->owner->favoriteQuestions()->save($question);
            }
       }

       $followers = $question->favoritedBy()->count();
       $question->update(["follower"=>$followers]);
    } 
// End of :The function which add the question favorites by normal usrs

// Beggining fo the function wich delete posts using ajax
    public function delete(Request $request){
        
        if((Auth::user()->owner_type == "App\NormalUser" && Auth::user()->owner->role->role == "admin" )||$this->authorize("normalUser_related",Auth::user())){

            // find question
            $question = Question::findOrFail($request->question_id);
            //  if the user which is requesting to delte the post really has this question or not

                // To delte the photo for the question
                if($question->photos()->count() > 0){
                    foreach($question->photos as $photo){
                        Storage::delete("public/images/questions/".$photo->path);
                        $photo->delete();
                    }
                }

                // To delte the tags for the post
                if($question->tags()->count() > 0){
                    $question->tags()->sync([]);
                }

                // To delte the post
                $question->delete();

        

        } // End of authorize function
    }
// End fo the function wich delete posts using ajax

}
