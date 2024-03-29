// Enable popper tooltip everywhere
$(()=>{
	$("[data-toggle='tooltip']").tooltip();
})
// Call the function of which chagne size of search box , large navbar and header when scroll
window.onscroll = function() {
	changeMenuSize();
	showToButton();
};
// Beggining of the function which change the size of the search box , largenavbar and the header when scroll
function changeMenuSize(){
	if(document.body.scrollTop > 10 || document.documentElement.scrollTop > 10){
		document.getElementById("header-div").style.padding = "7px";
		document.getElementById("sidebar-large").style.marginTop = "-12px";
		document.getElementById("sidebar-large").style.height = "65x";
		document.getElementById("logodiv").style.width = "150px";
		document.getElementById("logodiv").style.marginTop= "6px";
		document.getElementById("search-box1").style.height= "36px";
		document.getElementById("search-close-button").style.height= "36px";
		document.getElementById("search-close-button").style.paddingTop= "5px";
		if($(window).width() > 1000){
			$(".AddPostAskQuestionSmall").attr("style","margin-top: -10px !important");
			
		}else{
			// $(".AddPostAskQuestionSmall").attr("style","margin-top: -10px !important");
		}
		
	}else{
		document.getElementById("header-div").style.padding = "10px";
		document.getElementById("sidebar-large").style.marginTop = "-2px";
		document.getElementById("sidebar-large").style.height = "70x";
		document.getElementById("logodiv").style.width = "140px";
		document.getElementById("search-box1").style.height= "46px";
		document.getElementById("search-close-button").style.height= "46px";
		document.getElementById("search-close-button").style.paddingTop= "9px";
		if($(window).width() > 1000){
			$(".AddPostAskQuestionSmall").attr("style","margin-top: -4px !important");
			
		}else{
			// $(".AddPostAskQuestionSmall").attr("style","margin-top: -10px !important");
		}
		
	}
}
// End of the function which change the size of the search box , largenavbar and the header

function showToButton(){
	if(document.body.scrollTop > 400 || document.documentElement.scrollTop > 400){
		document.getElementById("topButton").style.display = "inline-block";
	}else{
		document.getElementById("topButton").style.display = "none";
	}
}

function goToTop(){
	$("html,body").animate({scrollTop:0},"slow");
}


// Begining of: function which open the small sidebar
function openNav() {
	document.getElementById("side").style.width = "230px";	
	document.getElementById("side").className += " sidebar-small-shadow";
	document.getElementById("header-div").style.marginLeft = "230px";
	document.getElementById("sidebar-large").style.marginLeft = "230px";
	$("#dropdownContentNotifications").hide();
}
// End of: function which open the small sidebar

// Begining of: function which close the small sidebar
function closeNav() {
  document.getElementById("side").style.width = "0";
  document.getElementById("header-div").style.marginLeft= "0";
  document.getElementById("sidebar-large").style.marginLeft= "0";
}
// End of: function which open the small sidebar


// Beggining of : function which enable the contact us button

function enableContactButton(){
	var fullName = document.getElementById("fullName");
	var phoneNumber = document.getElementById("phoneNumber");
	var emailAddress = document.getElementById("emailAddress");
	var message = document.getElementById("message");
	var button = document.getElementById("contactButton");
	if(fullName.value.trim().length > 0 && phoneNumber.value.trim().length > 0 && emailAddress.value.trim().length > 0 && message.value.trim().length > 0){
		button.disabled = false;
		button.className += " activeContactButton";
	}else{
		button.disabled = true;
		button.className = "btn btn-sm offset-lg-2";
	}
}

function enableContactButton1(){
	var fullName = document.getElementById("fullName");
	var phoneNumber = document.getElementById("phoneNumber");
	var emailAddress = document.getElementById("emailAddress");
	var message = document.getElementById("message");
	var button = document.getElementById("contactButton");
	if(fullName.value.trim().length > 0 && phoneNumber.value.trim().length > 0 && emailAddress.value.trim().length > 0 && message.value.trim().length > 0){
		button.disabled = false;
		
	}else{
		button.disabled = true;
		
	}
}

// End of : function which enable the contact us button

// Beggining of : function which validate contact form

function validateContactForm(){
	var fullName = document.getElementById("fullName");
	var fullNameErrorSmall = document.getElementById("fullNameErrorSmall");
	var fullNameError = document.getElementById("fullNameError");
	var phoneNumber = document.getElementById("phoneNumber");
	var phoneNumberError = document.getElementById("phoneNumberError");
	var phoneNumberErrorSmall = document.getElementById("phoneNumberErrorSmall");
	var emailAddress = document.getElementById("emailAddress");
	var emailAddressError = document.getElementById("emailAddressError");
	var emailAddressErrorSmall = document.getElementById("emailAddressErrorSmall");
	var message = document.getElementById("message");
	var messageError = document.getElementById("messageErrorForBothScreen");

// Beggining : to validate the message part of contact form
	if(message.value.trim().length < 1){
		message.focus();
		message.style.border = "1px solid red";
		messageError.innerHTML = "Message Field is required...";
		message.placeholder = "Message field is required....";
		event.preventDefault();
	}else if(message.value.trim().length > 500){
		message.focus();
		message.style.border = "1px solid red";
		messageError.innerHTML = "Not more than 500 chars are allowed...";
		message.placeholder = "max 500 chars....";
		event.preventDefault();
	}
	else{
		messageError.innerHTML = "";
		message.style.border = "1px solid #efefef";
	}
// End : to validate the message part of contact form

// Beggining : to validate the Email part of contact form
	if(emailAddress.value.trim().length < 1){
		emailAddress.focus();
		emailAddress.style.border = "1px solid red";
		emailAddressError.innerHTML = "Email field is required....";
		emailAddressErrorSmall.innerHTML = "Email field is required....";
		event.preventDefault();
	}else if(!emailAddress.value.trim().match(/^[^<>]*$/ig)){
		emailAddress.focus();
		emailAddress.style.border = "1px solid red";
		emailAddressError.innerHTML = "Invalid Email Address..."
		emailAddressErrorSmall.innerHTML = "Invalid Email Address..."
		event.preventDefault();
	}
	else{
		emailAddressError.innerHTML = "";
		emailAddressErrorSmall.innerHTML = "";
		emailAddress.style.border = "1px solid #efefef";
	}
// End : to validate the Email part of contact form

// Beggining : to validate the phone part of contact form
	if(phoneNumber.value.trim().length < 1){
		phoneNumber.focus();
		phoneNumber.style.border = "1px solid red";
		phoneNumberError.innerHTML = "Phone number is required...";
		phoneNumberErrorSmall.innerHTML = "Phone number is required...";
		event.preventDefault();
	}else if(!phoneNumber.value.trim().match(/^([0-9+() ]+)-*([ 0-9-]+)$/ig)){
		phoneNumber.focus();
		phoneNumber.style.border = "1px solid red";
		phoneNumberError.innerHTML = "Invalid phone number...";
		phoneNumberErrorSmall.innerHTML = "Invalid phone number...";
		event.preventDefault();
	}
	else{
		phoneNumberError.innerHTML = "";
		phoneNumberError.innerHTMLSmall = "";
		phoneNumber.style.border = "1px solid #efefef";
	}
// End : to validate the phone part of contact form

// Beggining : to validate the fullName part of contact form
	if(fullName.value.trim().length < 1){
		fullName.focus();
		fullName.style.border = "1px solid red";
		fullNameError.innerHTML = "Full Name file is required...";
		fullNameErrorSmall.innerHTML = "Full Name file is required...";
		event.preventDefault();
	}else if(!fullName.value.match(/^[^<>]*$/ig)){
		fullName.focus();
		fullName.style.border = "1px solid red";
		fullNameError.innerHTML = "Invalid Name...";
		fullNameErrorSmall.innerHTML = "Invalid Name...";
		event.preventDefault();
	}else if(fullName.value.trim().length < 3){
		fullName.focus();
		fullName.style.border = "1px solid red";
		fullNameError.innerHTML = "Full Name file is must be altleas 3 chars...";
		fullNameErrorSmall.innerHTML = "Full Name file is must be altleas 3 chars...";
		event.preventDefault();
	}
	else{
		fullNameError.innerHTML = "";
		fullNameErrorSmall.innerHTML = "";
		fullName.style.border = "1px solid #efefef";
	}
// End : to validate the fullName part of contact form

}
// End of : function which validate contact form


// Beggining of the jquery ready function
$("document").ready(function(){
// Beggining of the function which open search box and close the large nav
	$("#search-icon").click(function(){
		$("#header-div").hide();
		$(".search-box-button").show();
		event.preventDefault();
	});
// End of the function which open search box and close the large nav

// Beggining of the function which open the large nav and close search box  
	$("#search-close-button").click(function(){	
		$(".search-box-button").hide();
		$("#header-div").show();
	});
// End of the function which open the large nav and close search box 

// beginning of :  to automatically hide the small slidebar when the screen gets large  
	$(window).resize(function(){
		if($(window).width() > 1000){
			$("#side").css("width","0px");
			$("#header-div").css("margin-left","0px");
			$("#sidebar-large").css("margin-left","0px");
		}else{
			// This else hide the dropdown content menue when screen is resized
			$("#dropdownContent").hide();
			$("#dropdownContentNotifications").hide();
			$(".AddPostAskQuestionSmall").attr("style","margin-top: 0px !important");
		}
	});
// End of :  to automatically hide the small slidebar when the screen gets large 

// Begenning of : to scroll smooth to forusers, by omc
	$("#forUsersBtn").click(function(){
		$("html,body").animate({
			scrollTop: $("#section2").offset().top-74},"slow");
	});
// End of : to scroll smooth to forusers, by omc

// Begenning of : to scroll smooth to for doctors, by omc
	$("#forDoctorsBtn").click(function(){
		$("html,body").animate({
			scrollTop: $("#section3").offset().top-74},"slow");
	});
// End of : to scroll smooth to for doctors, by omc

// Beggining of : to scroll to contact form when its resposnse is returned back
// Note the status variable is comming from the main page
if(status === "on"){
	$("html,body").animate({
	scrollTop: $("#section5").offset().top+320},"slow");
}
// End of : to scroll to contact form when its resposnse is returned back

// Begenning of : to scroll smooth to for contact us 
	$(".contactUs").click(function(){
		closeNav();
		$("html,body").animate({
			scrollTop: $("#section5").offset().top-74},900);
		$("#fullName").focus();
	});
// End of : to scroll smooth to for contact us

// Beggining of : the function which hide the small slidebar and user menu list when the screen is clicked
$("#mainParent,#footer").click(function(){
	$("#dropdownContent").hide();
	$("#dropdownContentNotifications").hide();
	closeNav();
});
// End of : the function which hide the small slidebar and user menu list when the screen is clicked

$("#userProfileParent").click(function(){
	$("#dropdownContentNotifications").hide();
	$("#dropdownContent").toggle();
});

// Begenning of : to scroll smooth to for ads from for users, by omc
	
	$("#forUsersViewAds").click(function(){
		$("html,body").animate({
			scrollTop: $("#section4").offset().top-160},"slow");
	});

// End of : to scroll smooth to for ads from for users, by omc

//  slideshow for latest news in main page
	var s = $(".owl-carousel").find(".adsItems").length;
	$(".owl-carousel").owlCarousel({
		autoplay:true,
		autoplaySpeed: 1000,
		autoplayTimeout:4000,
		responsiveClass:true,
		autoplayHoverPause:true,
		navText : ["<i class='fad fa-chevron-left navLatestNews'></i>","<i class='fad fa-chevron-right navLatestNews'></i>"],
		responsive:{
			0:{
	            items:1,
	            nav:true,
	            loop:true,
	        },
	        750:{
	            items:(s > 1)  ? '2' : '1',
	            nav:true,
	            loop:(s > 3),

	        },
	        1024:{
	        	items:(s > 1)  ? '3' : '1',
	            nav:true,
	            loop:(s > 3),
	        }
		}
	});
// sllide how for lates new in main page end


});
// jquery ready function end


$("#userNotificationsParent").click(function(){
	$("#dropdownContentNotifications").toggle();
	$("#dropdownContent").hide();
});

function markAsRead(value){
	// if($("#notification-"+value).hasClass("notRead")){
		$.ajax({
			method: "POST",
			url: readMark,
			data:{notification_id:value, _token:token}
		}).done(function(){
			$("#notification-"+value).removeClass("notRead");
		});
	// }

}


function closeMessage(){
	$("#notAllowedDiv").fadeOut("slow");
}




//BO: this function get the value from the ajax return search result and put that into the textfield
function getdata1(value){
	$("#search-box1").val(value);
	$("#search-box1").focus();
	$("#sidebar-large").css("z-index","1");
	$("#searchResult1").hide();
}
//EO: this function get the value from the ajax return search result and put that into the textfield
// BO:  function show the avaibible questions based on user search 
function searchQuestions1(){
	$("#sidebar-large").css("z-index","0");
	var value = $("#search-box1").val();
	var resultBox = $("#searchResult1");
	var resultsDiv = $("#allResultsDiv1");
	var imageLoad = $("#searchLoad1");
	var searchText = $("#searchText1");
	
	resultBox.show();
	imageLoad.show();
	searchText.text("searching");

	if(value.trim().length < 1){
		resultBox.hide();
		$("#sidebar-large").css("z-index","1");
		resultsDiv.text("");
		imageLoad.hide();
		searchText.text("");
		return false;	
	}else{
		$("#searchForField1").css("border","1px solid #ced4da");
		$("#searchForField1").attr("placeholder","Search doctors");
	}

	$.ajax({
		method: "GET",
		url:questionsSearchResult,
		data:{data:value, _token:token},
	}).done(function(response){
		imageLoad.hide();
		searchText.text("resutls");
		if(!$.isEmptyObject(response.resultFound)){
			resultsDiv.text("");
			$.each(response.resultFound,function(index,value){
				resultsDiv.append($("<a href='javascript:void(0)' onclick='getdata1(" + '"' + value.title  + '"' + ")'>").text(value.title));
			});
		}else{
			if(!$.isEmptyObject(response.resultNotFound)){
				resultsDiv.text(response.resultNotFound);
				resultsDiv.css("font-size","12px");
			}
		}

	}).fail(function(response){
		imageLoad.hide();
		searchText.text("resutls");
		resultsDiv.text("oops! Someting went wrong!");
		resultsDiv.css("font-size","12px");
	});
}
// EO: This function show the avaibible normal users based on user search

$(document).ready(function(e){
	$("#searchForm1").submit(function(e){
		if($("#search-box1").val().trim().length < 1){
			$("#search-box1").css("border","1px solid red");
			$("#search-box1").attr("placeholder","You need to type");
			event.preventDefault();
		}else if($("#search-box1").val().trim().length > 60){
			$("#search-box1").css("border","1px solid red");
			$("#search-box1").attr("placeholder","Too long search");
			event.preventDefault();
		}
	});

	// delete box
	$('#deleteBox').on('show.bs.modal', function (event) {
	  var button = $(event.relatedTarget) // Button that triggered the modal
	  var id = button.data('id')
	  var type = button.data('type') 
	  var modal = $(this)
	  var title = modal.find('.modal-title').text(`Delete ${type}`)
	  var formDeleteButton = modal.find('.modal-footer #DeleteButton');
	  if(type == 'post'){
	  	formDeleteButton.attr("onclick",`deletePosts(${id})`)
	  }else if(type == 'comment'){
	  	var postId = button.data('post');
	  	formDeleteButton.attr("onclick",`deleteComments(${id}, ${postId})`)
	  }else if(type == 'reply'){
	  	var commentId = button.data('comment');
	  	formDeleteButton.attr("onclick",`deleteReplies(${id}, ${commentId})`)
	  }else if(type == "achievement"){
	  	formDeleteButton.attr("onclick",`deleteAch(${id})`)	
	  }else if(type == "follower"){
	  	modal.find('.modal-title').text(`Remove ${type}`)
	  	formDeleteButton.attr("onclick",`removeFollowers(${id})`)
	  }else if(type == 'question'){	
	  	var qType = button.data("qtype");
	  	formDeleteButton.attr("onclick",`deleteQuestions(${id},'${qType}')`)
	  }else if(type == "favPost"){
	  	var title = modal.find('.modal-title').text(`Delete post`)
	  	formDeleteButton.attr("onclick",`deleteQPosts(${id})`)	
	  }else if (type == 'listQuestions'){
	  	modal.find('.modal-title').text("Delete Question");
	  	formDeleteButton.attr("onclick",`deleteQuestions(${id})`)
	  }
	  
	})


});
