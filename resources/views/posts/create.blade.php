@extends("../layouts.MainLayout")

@section("title","Add your post")

@section("content")

<div class="" id="addPostParent">
	<h3 id="mainTitle">Add your post</h3>
	<!-- Beggingon of : PART ONE  TIPS -->
	<div id="tips" class="card">
		<div class="card-header" id="headerForLarge"><span id="noteHeading">Make Your Post More Efficient</span></div>
		<div class="card-header" id="headerForSmall"  onclick="showTipsContent();"><span id="noteHeading">Make Your Post More Efficient <i class="far fa-chevron-down float-right" id="tipsIconForSmall"></i></span></div>
		
		<div class="card-body" id="tipsContent">
			<span id="note">Share your knowladege regarding health with others to help them with their health problems.</span>
			<span id="caution">Avoid posting irrelevent topics</span>
			<div id="points">
				<ol>
					<li>
						<span onclick="openTIpsContent('duplicate','1')" class="tipsButton">Make sure it is not postd before <i class="far fa-chevron-down float-right icon" id="icon-1"></i></span>
						<ul class="tipsContent" id="duplicate">
							<li>Always search the title before posting, to make sure it is not duplicate</li>
							<li>Posting duplicate topics are not favorites to be viewd</li>
							<li>Make sure to add extra references and inforamtion if the topics are duplicate</li>
						</ul>
					</li>
					<div class="dropdown-divider"></div>
					<li>
						<span onclick="openTIpsContent('summarized','2')" class="tipsButton">Summarize your post <i class="far fa-chevron-down float-right icon" id="icon-2"></i></span>
						<ul class="tipsContent" id="summarized">
							<li>Make your topic shorter, if it is posible to be described in short terms</li>
							<li>It is more frequent for users to read summarized topics rather than long topics </li>
						</ul>
					</li>
					<div class="dropdown-divider"></div>
					<li>
						<span onclick="openTIpsContent('addTags','3')" class="tipsButton"> Add tags to your post <i class="far fa-chevron-down float-right icon" id="icon-3"></i> </span>
						<ul class="tipsContent" id="addTags">
							<li>Add tags to post, and it will describe your post content</li>
							<li>Help people find your post by tags you attached to your post</li>
						</ul>
					</li>
				</ol>
			</div><!-- End of points div -->
		</div> <!-- End of card body -->
	</div>	<!-- End of card -->
	<!-- End of : PART ONE  TIPS -->

	<!-- Second part Form -->
	<div id="formParent">
		{!! Form::open(["id"=>"postAddingForm"]) !!}
			<div class="form-elements">
				{!! Form::label("title","Title",["class"=>"labels"]) !!}
				<small class="smallNotes">Be specific in choosing the title for your post</small>
				{!! Form::text("title",null,["class"=>"form-control postFormInputs","placeholder"=>"e.g. The side effects of alchahol on hearth"]) !!}
			</div>
			<div class="form-elements">
				{!! Form::label("content","Content",["class"=>"labels"]) !!}
				<small class="smallNotes">Add the description of the title and any optional extra preference links</small>
				{!! Form::textarea("content",null,["class"=>"form-control postFormInputs","id"=>"postTextarea","placeholder"=>"The shorter the better","maxLength"=>"65500"]) !!}
			</div>
			<div class="form-elements">
				<!-- {!! Form::label("photo","Add Photo",["class"=>"labels"]) !!}
				<small class="smallNotes">Adding photo is not always recommanded</small> -->
				{!! Form::file("photo",["class"=>"form-control","id"=>"postPhotoField",'accept'=>"image/*","disabled"=>"true","style"=>"display:none;"]) !!}
				<span class="fal fa-image" id="imageIcon" onclick="openPostPhotoField()"></span>
			</div>
			<div class="form-elements">
				{!! Form::label("tags","Tags",["class"=>"labels"]) !!}
				<small class="smallNotes">Add up to 3 tags to your post which will describe what your post is about</small>
				<span class="far fa-question float-right mr-1" id="tagInfoIcon" onclick="showTagInfo()"></span>
				<div id="tagInfo">
					<h6>How to add tags</h6>
					<span>Tags help poeple find your post, and describe your post</span>
					<ul>
						<li>Click the (click here to select tags) button bellow the content part</li>
						<li>You will be opend a list of tags you can select maximum 3 tags for your post</li>
						<li>Choose the tags which are the most relevent to your post</li>
					</ul>
					<h6>If your desired tag is not in list:</h6>
					<span>Then just add your post without tags and <a href="#">ask US to add one for you</a></span>
				</div>
				<a href="javascrip:void(0)" onclick="showTags()" id="addTagLink" class="btn  btn-sm">click here to select tags</a>
				<div id="tags">
					<table class="table">
						<thead>
							<tr>
								<th>Tag Name</th>
								<th>Select</th>
							</tr>
						</thead>
						<tbody>
							<span>Choose with maximum of 3 tags</span>
							<a href="javascript:void(0)" onclick="showTags()" class="btn btn-sm" id="tagsDoneBtn">Done</a>
							<tr>
								<td><label> Headic </label></td>
								<td>{!! Form::checkbox("tag[]",null,null) !!}</td>
							</tr>
							<tr>
								<td><label> Cancer </label></td>
								<td>{!! Form::checkbox("tag[]",null,null) !!}</td>
							</tr>
							<tr>
								<td><label> Diabate </label></td>
								<td>{!! Form::checkbox("tag[]",null,null) !!}</td>
							</tr>
							<tr>
								<td><label> Skin </label></td>
								<td>{!! Form::checkbox("tag[]",null,null) !!}</td>
							</tr>
							<tr>
								<td><label> Malarya </label></td>
								<td>{!! Form::checkbox("tag[]",null,null) !!}</td>
							</tr>
							<tr>
								<td><label> Golokoz </label></td>
								<td>{!! Form::checkbox("tag[]",null,null) !!}</td>
							</tr>
							<tr>
								<td><label> HalfDying </label></td>
								<td>{!! Form::checkbox("tag[]",null,null) !!}</td>
							</tr>
							<tr>
								<td><label> FullDying </label></td>
								<td>{!! Form::checkbox("tag[]",null,null) !!}</td>
							</tr>
							<tr>
								<td><label> NotINControl </label></td>
								<td>{!! Form::checkbox("tag[]",null,null) !!}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="form-elements text-center">
				{!! Form::submit("Add Post",["class"=>"btn btn-sm","disabled"=>"true","id"=>"submitButton"]) !!}
			</div>
		{!! Form::close() !!}
	</div>
	<!-- End of Second par form -->

	<div class="clearfix"></div>
</div>

@endsection