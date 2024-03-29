<?php

namespace App\Http\Controllers;

use App\Doctor;
use App\Dcategory;
use App\Account;
use App\DoctorAchievement;
use App\Post;
use App\Question;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\DoctorAchievementsRequest;
use App\Http\Requests\DoctorAchievementsUpdateRequest;
use Carbon\Carbon;

use Validator;

class DoctorController extends Controller
{
    public function __construct(){
        $this->middleware("auth")->only(["achAdd","achUpdate","achDelete","achEdit"]);
        $this->middleware("activeUsers")->only("removeFields","achAdd","achEdit","achUpdate","achDelete");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index()
    {

        $doctors = Doctor::where("status",1)->paginate(30);
        return view("doctors.index",compact("doctors"));
    }

// Beggining of the function which retrn the result of the user search using ajax
    public function searchResult(Request $req){
        if($req->type === "name"){
            $doctors = Doctor::where("fullName","like","%$req->data%")->where('status',1)->select("fullName")->distinct()->get();
        }else if($req->type === "username"){
            $doctors = Account::join('doctors',"accounts.owner_id","=","doctors.id")->where("accounts.username","like","%$req->data%")->where("accounts.owner_type","App\Doctor")->where("doctors.status",1)->select("accounts.username")->get();
        }else if($req->type === "field"){
             $doctors = Dcategory::where("category","like","%$req->data%")->select("category")->get();
        }else if($req->type === "location"){
            $doctors = Doctor::where("street","like","%$req->data%")->where('status',1)->select("street")->get();
        }
        if(count($doctors) > 0){
            return response()->json(["resultFound"=>$doctors]);
        }else{
            return response()->json(["resultNotFound"=>"Result Not Found"]);
        }
    }
// End of the function which retrn the result of the user search using ajax


// Beggining of the function which search the doctor
    public function search(Request $req){
        $this->validate($req,[
            "searchFor" => "bail|required|string|max:60",
            "searchType" => "bail|required",
        ]);
        // return $req->searchFor;
        if($req->searchType === "name"){
            $doctors = Doctor::where("fullName",'like',"%$req->searchFor%")->where('status',1)->paginate(30);
        }elseif($req->searchType === "username"){
            $account = Account::where("username",'like',"%$req->searchFor%")->first();
            if($account){
                if($account->owner->status != 1){
                   return view("doctors.doctorsSearch")->with("notFound","Not Found!");
                }

                return redirect()->route("profile",$account->username);
                
            }else{
                $notFound = "Not Found!";
                return view("doctors.doctorsSearch",compact('notFound'));
            }
            
        }elseif($req->searchType === "field"){
            $doctors = Doctor::join("dcategory_doctor","doctors.id","=","dcategory_doctor.doctor_id")->join("dcategories","dcategory_doctor.dcategory_id","=","dcategories.id")->where('dcategories.category',"like","%$req->searchFor%")->where("doctors.status",1)->groupBy("doctors.id")->select("doctors.*")->paginate(30);
        }else if($req->searchType === "location"){
            $doctors = Doctor::where("street",'like',"%$req->searchFor%")->where('status',1)->paginate(30);
        }

        return view("doctors.doctorsSearch",compact("doctors"));
    }
// Beggining of the function which search the doctor




    /**
     * order by method  from main 
     *
     * @return \Illuminate\Http\Response
    */
    public function sortBy($type){
        if($type == "top"){
            $doctors = Doctor::where("status",1)->orderBy("followers","desc")->paginate(30);
        }else if($type == "new"){
            $doctors = Doctor::where("status",1)->orderBy("created_at","desc")->paginate(30);   
        }else if($type == "mostposts"){
            $doctors = Doctor::leftJoin("posts","doctors.id","=","posts.doctor_id")->where("doctors.status",1)->groupBy("doctors.id")->orderBy('posts_count','desc')->selectRaw("doctors.*,count(posts.id) as posts_count")->paginate(30);
        }
        return view("doctors.index",compact("doctors","type"));
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
     * @param  \App\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function show(Doctor $doctor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function edit(Doctor $doctor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Doctor $doctor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Doctor $doctor)
    {
        //
    }



    //  To add achievemnts to docotrs
    public function achAdd(Request $request)
    {
        // return response()->json(["abc"=>$request->all()]);
        $validator = Validator::make($request->all(),[
            'ach_title' => "bail|required|max:100",
            'ach_content' => "bail|required|max:500",
            'ach_location' => "bail|required|max:100",
            'ach_photo' => "bail|required|image|max:10240",
            'ach_year' => "bail|required|regex:/^[0-9]+$/i",
            'ach_month' => "bail|required|regex:/^[0-9]+$/i",
            'ach_day' => "bail|required|regex:/^[0-9]+$/i",
        ],[
            'ach_title.required' => "The title can  not be empty...",
            'ach_title.max' => "Long title not allowed ...",
            'ach_content.required' => "The description can not be empty...",
            'ach_content.max' => "Long description  not  allowed ...",
            'ach_location.required' => "The location field can not be empty ...",
            'ach_location.max' => "Long location  not  allowed ...",
            'ach_photo.required' => "The photo is required",
            'ach_photo.image' => "Invalid file. Only photos are allowed...",
            'ach_photo.max' => "File too large. max 10MB...",
            'ach_year.required' => "The year can not be empty ...",
            'ach_year.regex' => "Invalid data for year...",
            'ach_month.required' => "The month can not be empty ...",
            'ach_month.regex' => "Invalid data from month ...",
            'ach_day.required' => "The day can not be empty ...",
            'ach_day.regex' => "Invalid data from day ...",
        ]);

        if($validator->fails()){
            if($request->ajax()){
                return response()->json(["validationErrors"=>$validator->errors()]);
            }else{
                return back()->withInput()->withErrors($validator->errors());
            }
        }


        // achievements
        if($this->authorize("Doctor_related",Auth::user()))
        {
             // grab the current doctor 
            $user = Auth::user();
            // store the year, month, and day fields in single variable
            $ach_date =  Carbon::createFromDate($request->ach_year,$request->ach_month,$request->ach_day)->format("Y-m-d"); 
             // add the newly created variable of date to request array
            $request->merge(["ach_date"=>$ach_date]);

             // // insert the selected user data
            $achievement = $user->owner->achievements()->create($request->all());
            

            // if photo is selected then add it to a folder and to db aswell
            if($request->hasFile("ach_photo")){
                $photo = $request->file("ach_photo");
                $fullName  = $photo->getClientOriginalName();
                $onlyName = pathinfo($fullName,PATHINFO_FILENAME);
                $extension = $photo->getClientOriginalExtension();
                $nameToBeStored = $onlyName.time(). "." .$extension;
                $folder = "public/images/achievements/";  
                // $photo->move($folder,$nameToBeStored);

                $photo->storeAs($folder,$nameToBeStored);
                $achievement->photos()->create(["path"=>$nameToBeStored,"status"=>"1"]);
            }

            if($achievement){
                if($request->ajax()){
                    $date = explode("-",$achievement->ach_date);
                    $year = $date[0];
                    $month = $date[1];
                    $day = explode(" ",$date[2])[0];
                    $mainDate = Carbon::createFromDate($year,$month,$day)->format("d-M-Y");
                    $photoPath = $achievement->photos()->where('status',1)->first()->path;
                    $achievement["mainDate"] = $mainDate;
                    $achievement["photoPath"] = $photoPath;
                    return response()->json(["ach"=>$achievement]);
                }
                return redirect()->route("profile",Auth::user()->username)->with("ach_success","Achievement Added");
            }else{
                 if($request->ajax()){
                    return response()->json(["ach_error"=>"OOps something went wrong try again"]);
                }
                return back()->withInput()->with("ach_error","OOps something went wrong try again");;
            }

        }
        // authorization statement end
    } 
    // mian functioin end

    // this function load the the achivement image using ajax
    public function loadAchImage(Request $request){
        $ach = DoctorAchievement::find($request->id);
        $photo = $ach->photos()->where("status",1)->first()->path;
        return response()->json(["photo"=>$photo]);   
    }
    // end of:this function load the the achivement image using ajax
    

    public function achEdit(DoctorAchievement $ach){
        if($this->authorize('doctor_related',Auth::user())){
            if(Auth::user()->isNot($ach->doctor->account)){
                abort(403);
            }
            //  in here we to explode the date stored in the database to year month and day seperratly in order to show that in form in the edit page
            $date = explode("-",$ach->ach_date);
            $year = $date[0];
            $month = $date[1];
            $day = explode(" ",$date[2])[0];

            // add the splited year to the array
            $ach["ach_year"] = $year;

            // in the form  the values are 1 2 3 and so on but the splite month is 01 02 03 04 up to 10 and so on so suppose it is 01 then if we pass 01 in the value in the edit form it will not select that becase it expects 1 not 01. so for that reason we check if the first digit is 0 then just add the second one else add both the 2 digigs
            if($month[0] === "0"){
                $ach["ach_month"] = $month[1];
            }else{
                $ach["ach_month"] = $month;
            }

            // same like month
            if($day[0] === "0"){
                $ach["ach_day"] = $day[1];
            }else{
                $ach["ach_day"] = $day;
            }
            
            

            return view("doctors.achEdit",compact('ach'));
        }
    }

    public function achUpdate(DoctorAchievementsUpdateRequest $request, DoctorAchievement $ach){
        if($this->authorize("doctor_related",Auth::user())){
            if(Auth::user()->isNot($ach->doctor->account)){
                abort(403);
            } 
            
            // store the year, month, and day fields in single variable
            $ach_date =  Carbon::createFromDate($request->ach_year,$request->ach_month,$request->ach_day)->format("Y-m-d"); 
             // add the newly created variable of date to request array
            $request->merge(["ach_date"=>$ach_date]);

            $updated = $ach->update($request->all());

            if($request->hasFile("ach_photo"))
            {
                $photo = $request->file("ach_photo");
                $fullName  = $photo->getClientOriginalName();
                $onlyName = pathinfo($fullName,PATHINFO_FILENAME);
                $extension = $photo->getClientOriginalExtension();
                $nameToBeStored = $onlyName.time(). "." .$extension;
                $folder = "public/images/achievements/";  

                Storage::delete("public/images/achievements/".$ach->photos()->where('status',1)->first()->path);

                $photo->storeAs($folder,$nameToBeStored);
                $ach->photos()->update(["path"=>$nameToBeStored]);
            }
            if($updated){
                 return redirect()->route("profile",Auth::user()->username)->with("achUpdate_success","Achievement updated");
            }else{
                return back()->withInput()->with("ach_error","OOps something went wrong while updating, try again");
            }




        } // authorization statemnt end
    } // main function end

    // function which deletes the achiemvent using ajax request
    public function achDelete(Request $request){
        if($this->authorize("Doctor_related",Auth::user())){
            $ach = DoctorAchievement::find($request->id);
            if(Auth::user()->is($ach->doctor->account)){
                if($ach->photos()->count() > 0){
                    foreach($ach->photos as $photo){
                        Storage::delete("public/images/achievements/".$photo->path);
                        $photo->delete();
                    }
                }
                $ach->delete();
            }
        }
        // end of authorization statement
    }
    // End of:function which deletes the achiemvent using ajax request


// function which removes doctor fields using ajax request
public function removeFields(Request $request){ 
   $categroy = Dcategory::find($request->id);
   Auth::user()->owner->fields()->detach($categroy);
}





}
//  main functino end