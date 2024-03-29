<div id="adminSidebar">
	<h5>Admin Panel</h5>
	<div id="adminMenus" >
		<a href="{{route('admin.dashboard')}}" class="{{ (Route::currentRouteName() === 'admin.dashboard' ? 'adminActive' : '' ) }}">
			<span class="fal fa-bell"></span> 
			<span class="adminMunuText">Notifications</span> 
			<span class="badge badge-danger">
				{{
					Auth::user()->unreadnotifications()->where("type","=","App\Notifications\Admin\postAdd")->count() 
					+ 
					Auth::user()->unreadnotifications()->where("type","=","App\Notifications\Admin\questionAdd")->count()
					+ 
					Auth::user()->unreadnotifications()->where("type","=","App\Notifications\Admin\userAdd")->count()
				}}
			</span>
		</a>
		<a href="{{route('contact.manage')}}" class="{{ (Route::currentRouteName() === 'contact.manage' ? 'adminActive' : '' ) }}">
			<span class="fal fa-envelope"></span>
			<span class="adminMunuText">Messages</span>
		</a>
		<a href="{{route('doctors.manage.index')}}" class="{{ (Route::currentRouteName() === 'doctors.manage.index' ? 'adminActive' : '' ) }}">
			<span class="fal fa-user-md"></span> 
			<span class="adminMunuText">Manage Doctors</span>
		</a>
		<a href="{{route('nusers.manage.index')}}" class="{{ (Route::currentRouteName() === 'nusers.manage.index' ? 'adminActive' : '' ) }}">
			<span class="fal fa-user-cog"></span> 
			<span class="adminMunuText">Manage Normal Users</span>
		</a>
		<a href="{{route('posts.index')}}">
			<span class="fal fa-th"></span> 
			<span class="adminMunuText">Manage Posts</span>
		</a>
		<a href="{{route('questions.index')}}">
			<span class="fal fa-question"></span> 
			<span class="adminMunuText">Manage Questions</span> 
		</a>
		<a href="{{route('dcategories.manage')}}" class="{{ (Route::currentRouteName() === 'dcategories.manage' ? 'adminActive' : '' ) }}">
			<span class="fal fa-bell"></span> 
			<span class="adminMunuText">Manage Doctor Category</span> 
		</a>
		<a href="{{route('tags.manage')}}" class="{{ (Route::currentRouteName() === 'tags.manage' ? 'adminActive' : '' ) }}">
			<span class="fal fa-tags"></span> 
			<span class="adminMunuText">Manage Tags</span>
		</a>
		<a href="{{route('ads.index')}}" class="{{ (Route::currentRouteName() === 'ads.index' ? 'adminActive' : '' ) }}">
			<span class="fal fa-ad"></span> 
			<span class="adminMunuText">Manage Advertisementss</span>
		</a>
		<a href="{{route('roles.manage')}}" class="{{ (Route::currentRouteName() === 'roles.manage' ? 'adminActive' : '' ) }}">
			<span class="fal fa-bell"></span>
			<span class="adminMunuText">Roles</span>
		</a>
	</div>
</div>
