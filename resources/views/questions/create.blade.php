@extends("../layouts.MainLayout")

@section("title","Add your question")

@section("content")

<div class="" id="addPostParent">
	
	<h3 id="mainTitle">Ask your Question</h3>
	<!-- Beggingon of : PART ONE  TIPS -->
	<div id="tips" class="card">
		<div class="card-header" id="headerForLarge"><span id="noteHeading">Tips for asking a good question</span></div>
		<div class="card-header" id="headerForSmall"  onclick="showTipsContent();"><span id="noteHeading">Make Your Question More Efficient <i class="far fa-chevron-down float-right" id="tipsIconForSmall"></i></span></div>
		
		<div class="card-body" id="tipsContent">
			<span id="note">Find and ask qustions ralated to medical and share your answers with others.</span>
			<span id="caution">Avoid asking irrelevent question</span>
			<div id="points">
				<ol>
					<li>
						<span onclick="openTIpsContent('duplicate','1')" class="tipsButton">Make sure it is not asked before <i class="far fa-chevron-down float-right icon" id="icon-1"></i></span>
						<ul class="tipsContent" id="duplicate">
							<li>Always search the title before asking, to make sure it is not already asked</li>
							<li>Asking duplicate question are not favorites to be answred</li>
							<li>Make sure to add extra references and inforamtion if the question are duplicate</li>
						</ul>
					</li>
					<div class="dropdown-divider"></div>
					<li>
						<span onclick="openTIpsContent('summarized','2')" class="tipsButton">Summarize your question <i class="far fa-chevron-down float-right icon" id="icon-2"></i></span>
						<ul class="tipsContent" id="summarized">
							<li>Make your question summerized , if it is posible to be described in short terms</li>
							<li>It is more frequent for users to read & answer summarized question rather than long </li>
						</ul>
					</li>
					<div class="dropdown-divider"></div>
					<li>
						<span onclick="openTIpsContent('addTags','3')" class="tipsButton"> Add tags to your Question <i class="far fa-chevron-down float-right icon" id="icon-3"></i> </span>
						<ul class="tipsContent" id="addTags">
							<li>Add tags to question, and it will describe your question content</li>
							<li>Help people find your question by tags you attached to your question</li>
						</ul>
					</li>
				</ol>
			</div><!-- End of points div -->
		</div> <!-- End of card body -->
	</div>	<!-- End of card -->
	<!-- End of : PART ONE  TIPS -->

	<!-- Second part Form -->
	<div id="formParent">
		@if(session("postAddSuccess"))
			<div class="alert alert-success messages" id="questionSuccess">
				<button class="close" data-dismiss="alert" area-hidden="true">&times;</button>
				{{ session("postAddSuccess") }} you can view it by <a href="{{route('profile',Auth::user()->username)}}">visiting your profile</a> or by <a href="{{route('questions.index')}}">visiting qustions page</a>
			</div>
		@endif
		@include('../layouts.messages')
		{!! Form::open(["method"=>"POST","action"=>"QuestionController@store","files"=>"true","id"=>"postAddingForm"]) !!}
			<div class="form-elements">
				{!! Form::label("title","Title",["class"=>"labels"]) !!}
				<small class="smallNotes">Be specific in choosing the title for your question</small>
				{!! Form::text("title",null,["class"=>"form-control postFormInputs". ($errors->has('title') ? ' formErrorForFields' : ''),"placeholder"=>"e.g. How to use decadran injection?","id"=>"title","onkeyup"=>"validateTitleAndEnableButton()"]) !!}
				<span class="ErrorMessage" id="errorForTitle">
					@error('title')
						{{ $message }}
					@enderror
				</span>
			</div>
			<div class="form-elements">
				{!! Form::label("content","Content",["class"=>"labels"]) !!}
				<small class="smallNotes">Add the description of the title and any optional extra preference links</small>
				{!! Form::textarea("content",null,["class"=>"form-control postFormInputs". ($errors->has('content') ? ' formErrorForFields' : ''),"id"=>"content","placeholder"=>"The shorter the better","maxLength"=>"65500","onkeyup"=>"validateContentAndEnableButton()"]) !!}
				<span class="ErrorMessage" id="errorForContent">
					@error('content')
						{{ $message }}
					@enderror
				</span>
			</div>
			<div class="form-elements">
				<div id="invalidFile" class="container text-center alert alert-danger alert-sm">
				<button class="close" data-dismiss="alert" area-hidden="true">&times;</button>
				<span id="invalidFileMessage">
					
				</span>
			</div>
			<div class="container text-center" id="PhotoParentDiv">
				<span id="changePhoto" onclick="openPostPhotoField()"><i class="far fa-edit" ></i> <span class="photoText">Change photo</span></span>
				<span id="removePhoto" onclick="removePhoto()"><i class="far fa-times" ></i> <span class="photoText">Remove photo</span></span>
				<div id="photoDiv">
					<img src="" id="img-placeholder" class="img-fluid">
				</div>
			</div>
				{!! Form::file("photo",["class"=>"form-control","id"=>"PhotoField","disabled"=>"true","style"=>"display:none;"]) !!}
				<a href="javascrip:void(0)" class="fal fa-image" id="imageIcon" onclick="openPostPhotoField()"></a>
				<span class="ErrorMessage ml-1">
					@error('photo')
						{{ $message }}
					@enderror
				</span>
			</div>
			<div class="dropdown-divider" id="dividerAfterPhoto"></div>
			<div class="form-elements">
				{!! Form::label("tags","Tags",["class"=>"labels"]) !!}
				<small class="smallNotes">Add up to 5 tags to your question which will describe what your question is about</small>
				<span class="far fa-question float-right mr-1" id="tagInfoIcon" onclick="showTagInfo()"></span>
				<div id="tagInfo">
					<h6>How to add tags</h6>
					<span>Tags help poeple find your question, and describe your question</span>
					<ul>
						<li>Click the (click here to select tags) button bellow the content part</li>
						<li>You will be opend a list of tags you can select maximum 5 tags for your question</li>
						<li>Choose the tags which are the most relevent to your question</li>
					</ul>
					<h6>If your desired tag is not in list:</h6>
					<span>Then just add your question without tags and <a href="#">ask US to add one for you</a></span>
				</div>

				<a href="javascrip:void(0)" onclick="showTags()" id="addTagLink" class="btn  btn-sm {{($errors->has('tags') ? 'errorButton' : '')}}">
					<span id="tagsCount" class="badge badge-pill badge-light">0</span> &nbsp;
					click here to select tags
				</a>
				
				<div class="clearfix"></div>
				<span class="ErrorMessage float-right mr-1 mb-2" id="tagsErrorMessage">
					@error('tags')
						{{ $message }}
					@enderror
				</span>
				<div id="tags">
					<table class="table">
						<thead>
							<tr>
								<th>Tag Name</th>
								<th>Select</th>
							</tr>
						</thead>
						<tbody>
							<span id="tagsNote">Choose with maximum of 5 tags</span>
							<a href="javascript:void(0)" onclick="showTags()" class="btn btn-sm" id="tagsDoneBtn">Done</a>
							@if(count($d_categories) > 0)
							@foreach($d_categories as $d_category)
							<tr>
								<td><label> {{$d_category->category}} </label></td>
								<td>{!! Form::checkbox("tags[]",$d_category->id,null,["class"=>"tagsCheckboxes ml-4","onchange"=>"showAndValidateTagsCount(event)"]) !!}</td>
							</tr>
							@endforeach
							@endif
						</tbody>
					</table>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="form-elements">
				<div class="dropdown-divider"></div>
				{!! Form::submit("Add Question",["class"=>"btn btn-sm","disabled"=>"true","id"=>"submitButton","onclick"=>"validatePostForm()"]) !!}
			</div>
		{!! Form::close() !!}
	</div>
	<!-- End of Second par form -->

	<div class="clearfix"></div>
</div>

@endsection