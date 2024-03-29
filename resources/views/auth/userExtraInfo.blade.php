@extends("../layouts.LoginSignUpLayout")

@section("title","more info")



@section("form")
	<a href="javascript:void(0);" id="userProfilePicLink">
		<div class="text-center mb-3">
			<div class="fal fa-user" id="userProfilePic" onclick="openFileUploadInput();">
				<span>Add Profile Picture</span>
			</div>
			<div id="countryError" class="moreInfoFormErrors col-lg-12">
				@error("photo")
					{{$message}}
				@enderror
			</div>
			<div id="main-img">
				<img src="" id="img-placeholder" alt="your image">
				<a href="javascript:void(0);" class="changeRemovePic" onclick="openFileUploadInput();" id="changePicture">Change picture</a>
				<a href="javascript:void(0);" class="changeRemovePic" id="removePicture">Remove Picture</a>
			</div>
			<div class="alert alert-danger mt-2 MoreInfoPageMsgs fileErrorMsgs" id="invalidFile">
				<button class="close" id="invalidFileCloseBtn">&times;</button>
				<span id="FileErrorMsg"></span>
			</div>
		</div>
	</a>
	<div>
		{!! Form::open(["method"=>"post","action"=>"Auth\RegisterController@moreInfoStore","files"=>"true"]) !!}
			<div>
				{!! Form::label("address","Your Address & contact:") !!}
				<div class="row row-for-input">
					{!! Form::select("country_id",$countries,null,["class"=>"form-control more_form_element col-lg-12 ".($errors->has('country') ? ' moreInfoFormErrorsInput' : ''),"placeholder"=>"Your Country...","autofocus"=>"true","id"=>"country","onchange"=>"selectProvinceAndDistrict('province')"]) !!}
					<div id="countryError" class="moreInfoFormErrors col-lg-12">
						@error("country")
							{{$message}}
						@enderror
					</div>
					{!! Form::select("province_id",[""=>"Province"],null,["class"=>"form-control more_form_element col-lg-6".($errors->has('province') ? ' moreInfoFormErrorsInput' : ''),"id"=>"province","disabled"=>"true","title"=>"choose a country first","onchange"=>"selectProvinceAndDistrict('district')"]) !!}
					<div class="col-12 ErrorsToBeVisisbleInSmallSize moreInfoFormErrors"  id="provinceErrorSmall">
						@error("province")
							{{$message}}
						@enderror
					</div>

					{!! Form::select("district_id",[""=>"District"],null,["class"=>"form-control more_form_element col-lg-6".($errors->has('district') ? ' moreInfoFormErrorsInput' : ''),"id"=>"district","title"=>"choose a province first","disabled"=>"true"]) !!}
					<div class="col-12 ErrorsToBeVisisbleInSmallSize moreInfoFormErrors" id="districtErrorSmall">
						@error("district")
							{{$message}}
						@enderror
					</div>

					<div class="row row-for-input col-lg-12 text-center ErrosToBeHiddenInSmallSize">
						<div id="provinceError" class="moreInfoFormErrors col-lg-6">
							@error("province")
								{{$message}}
							@enderror
						</div>
						<div id="districtError" class="moreInfoFormErrors col-lg-6">
							@error("district")
								{{$message}}
							@enderror
						</div>
					</div>

					{!! Form::text("street",null,["class"=>"form-control more_form_element col-lg-12" .($errors->has('street') ? ' moreInfoFormErrorsInput' : ''),"placeholder"=>"Enter your street address...","id"=>"street"]) !!}
					<div id="streetError" class="moreInfoFormErrors col-lg-12">
						@error("street")
							{{$message}}
						@enderror
					</div>

					{!! Form::text("phone",null,["class"=>"form-control more_form_element col-lg-12".($errors->has('phone') ? ' moreInfoFormErrorsInput' : ''),"placeholder"=>"Phone number ...","id"=>"phone"]) !!}
					<div id="phoneError" class="moreInfoFormErrors col-lg-12">
						@error("phone")
							{{$message}}
						@enderror
					</div>
					<div class="dropdown-divider col-lg-12"></div>
				</div>

				{!! Form::file("photo",["class"=>"form-control mor_form_element","disabled"=>"true","accept"=>"image/*","style"=>"display:none;","id"=>"fileUpload"]) !!}
				
				{!! Form::label("DOB","Date of birth:") !!}
				<div class="row row-for-input" id="DOBlabels">
					{!! Form::label("year","Year",["class"=>"col-lg-3"]) !!}
					<div class="col-lg-1"></div>
					{!! Form::label("month","Month",["class"=>"col-lg-3"]) !!}
					<div class="col-lg-1"></div>
					{!! Form::label("day","Day:",["class"=>"col-lg-3"]) !!}
				</div>
				<div class="row row-for-input">
					{!! Form::selectRange("year",1950,\Carbon\carbon::now()->format("Y"),null,["class"=>"form-control more_form_element col-lg-4".($errors->has('year') ? ' moreInfoFormErrorsInput' : ''),"id"=>"year"]) !!}
					<div class="col-12 ErrorsToBeVisisbleInSmallSize moreInfoFormErrors" id="yearErrorSmall">
						@error("year")
							{{$message}}
						@enderror
					</div>

					{!! Form::selectMonth("month",null,["class"=>"form-control more_form_element col-lg-4".($errors->has('month') ? ' moreInfoFormErrorsInput' : ''),"id"=>"month"]) !!}
					<div class="col-12 ErrorsToBeVisisbleInSmallSize moreInfoFormErrors" id="monthErrorSmall">
						@error("month")
							{{$message}}
						@enderror
					</div>

					{!! Form::selectRange("day",1,31,null,["class"=>"form-control more_form_element col-lg-4".($errors->has('day') ? ' moreInfoFormErrorsInput' : ''),"id"=>"day"]) !!}	
					<div class="col-12 ErrorsToBeVisisbleInSmallSize moreInfoFormErrors" id="dayErrorSmall">
						@error("day")
							{{$message}}
						@enderror
					</div>

				</div>
				<div class="row row-for-input col-lg-12 ErrosToBeHiddenInSmallSize text-center">
					<div id="yearError" class="moreInfoFormErrors col-lg-4">
						@error("year")
							{{$message}}
						@enderror
					</div>
					<div id="monthError" class="moreInfoFormErrors col-lg-4">
						@error("month")
							{{$message}}
						@enderror
					</div>
					<div id="dayError" class="moreInfoFormErrors col-lg-4">
						@error("day")
							{{$message}}
						@enderror
					</div>
				</div>
				<div class="dropdown-divider"></div>
				{!! Form::label("address","Bio information:") !!}
				{!! Form::textarea("Bio",null,["class"=>"form-control more_form_element col-lg-12" .($errors->has('Bio') ? ' moreInfoFormErrorsInput' : ''),"placeholder"=>"Enter your bio...","id"=>"biography","rows"=>"6","maxlength"=>"200","style"=>"resize:none"]) !!}
					<div id="biographyError" class="moreInfoFormErrors col-lg-12">
						@error("Bio")
							{{$message}}
						@enderror
					</div>
				<div class="row row-for-input">
					{!! Form::submit("Add Info",["class"=>"btn btn-primary btn-sm btn-block col-lg-12", "id"=>"form_button","onclick"=>"validateMoreInfoForm()"]) !!}
				</div>
			</div>
		{!! Form::close() !!}
	</div> 
@endsection


@section("forgot-privacy-part")
	This is our privacy and policy
@endsection



@section("secondOption")
	
	Set up later?<a href="{{route('profile',Auth::user()->username)}}" class=""> Skip</a>

<!-- To pass provinces and districts to js -->
@if($latestCountry)
	@for($index = 1 ; $index <= $latestCountry->id; $index++ )
         @if($country = App\Country::find($index))
		<script type="text/javascript">
			var {!! 'province' . $index !!} = {!! json_encode($country->provinces()->pluck("province","id")) !!};
		</script>
		@endif
	@endfor
@endif

@if($lastestProvince)
	@for($Pindex = 1 ; $Pindex <= $lastestProvince->id; $Pindex++ )
         @if($province = App\Province::find($Pindex))
		<script type="text/javascript">
			var {!! 'district' . $Pindex !!} = {!! json_encode($province->districts()->pluck("district","id")) !!};
		</script>
		@endif
	@endfor
@endif

	

@endsection