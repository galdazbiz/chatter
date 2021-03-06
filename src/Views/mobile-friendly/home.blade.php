@extends(Config::get('chatter.master_file_extend'))

@section('title', 'Foro Vivetix | Donde los organizadores de eventos se reúnen, aprenden y comparten' )

@section(Config::get('chatter.yields.head'))
	<meta name="description" content="{{substr(strip_tags($first_post), 0, 250)}}...">
	<meta property="og:site_name" content="Foro Vivetix">
	<meta property="og:title" content="Foro Vivetix | Donde los organizadores de eventos se reúnen, aprenden y comparten">
	
    <link href="{{ url('/vendor/devdojo/chatter/assets/vendor/spectrum/spectrum.css') }}" rel="stylesheet">
	<link href="{{ url('/vendor/devdojo/chatter/assets/css/chatter.css') }}" rel="stylesheet">
	@if($chatter_editor == 'simplemde')
		<link href="{{ url('/vendor/devdojo/chatter/assets/css/simplemde.min.css') }}" rel="stylesheet">
	@elseif($chatter_editor == 'trumbowyg')
		<link href="{{ url('/vendor/devdojo/chatter/assets/vendor/trumbowyg/ui/trumbowyg.css') }}" rel="stylesheet">
		<style>
			.trumbowyg-box, .trumbowyg-editor {
				margin: 0px auto;
			}
		</style>
	@endif
@stop

@section('content')

<div id="chatter" class="chatter_home">

	<div id="chatter_hero">
		<div id="chatter_hero_dimmer" style="background-image: url(https://devdojo.com/assets/img/wood-pattern.svg); width: 100%; min-height: 150px; position: relative; background-size: cover; background-position: center center; text-align: center;">
			<?php $headline_logo = Config::get('chatter.headline_logo'); ?>
			@if( isset( $headline_logo ) && !empty( $headline_logo ) )
				<img src="{{ Config::get('chatter.headline_logo') }}">
			@else
				<h1 style="color: #333;">@lang('chatter::intro.headline')</h1>
				<p style="color: #777;">@lang('chatter::intro.description')</p>
			@endif
		</div>
	</div>
	
	@if(config('chatter.errors'))
		@if(Session::has('chatter_alert'))
			<div class="chatter-alert alert alert-{{ Session::get('chatter_alert_type') }}">
				<div class="container">
					<strong><i class="chatter-alert-{{ Session::get('chatter_alert_type') }}"></i> {{ Config::get('chatter.alert_messages.' . Session::get('chatter_alert_type')) }}</strong>
					{{ Session::get('chatter_alert') }}
					<i class="chatter-close"></i>
				</div>
			</div>
			<div class="chatter-alert-spacer"></div>
		@endif

		@if (count($errors) > 0)
			<div class="chatter-alert alert alert-danger">
				<div class="container">
					<p><strong><i class="chatter-alert-danger"></i> @lang('chatter::alert.danger.title')</strong> @lang('chatter::alert.danger.reason.errors')</p>
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			</div>
		@endif
	@endif

	<div class="container chatter_container">

	    <div class="row">

	    	<div class="col-md-3 left-column">
	    		<!-- SIDEBAR -->
	    		<div class="chatter_sidebar">
					<button class="btn btn-primary" id="new_discussion_btn"><i class="chatter-new"></i>@lang('chatter::messages.discussion.new')</button>
					<a href="/{{ Config::get('chatter.routes.home') }}"><i class="chatter-bubble"></i> @lang('chatter::messages.discussion.all')</a>
          {!! $categoriesMenu !!}
				</div>
				<!-- END SIDEBAR -->
	    	</div>
	    	
	    	<div class="hidden-xs">
				<div class="col-md-9">
					<div class="table table-responsive">
						<table class="table table-responsive table-striped tableEvents" id="tableEventParticiper">

							<tbody>
								@forelse($discussions as $discussion)
									<tr>
										<td>
											@if(Config::get('chatter.user.avatar_image_database_field'))

												<?php $db_field = Config::get('chatter.user.avatar_image_database_field'); ?>

												@if( (substr($discussion->user->{$db_field}, 0, 7) == 'http://') || (substr($discussion->user->{$db_field}, 0, 8) == 'https://') )
													<img style="width: 60px; height: 60px; border-radius: 50%;" src="{{ $discussion->user->{$db_field}  }}">
												@elseif($discussion->user->{$db_field})
													<img style="width: 60px; height: 60px; border-radius: 50%;" src="{{ Config::get('chatter.user.relative_url_to_image_assets') . $discussion->user->{$db_field}  }}">
												@else
													<img style="width: 60px; height: 60px; border-radius: 50%;" src="{{ Config::get('chatter.user.if_empty_avatar_img_url') }}">
												@endif

											@else

												<span class="chatter_avatar_circle" style="background-color:#<?= \DevDojo\Chatter\Helpers\ChatterHelper::stringToColorCode($discussion->user->email) ?>">
													{{ strtoupper(substr($discussion->user->email, 0, 1)) }}
												</span>

											@endif
										</td>
										<td>
											<a class="discussion_list" href="/{{ Config::get('chatter.routes.home') }}/{{ Config::get('chatter.routes.discussion') }}/{{ $discussion->category->slug }}/{{ $discussion->slug }}"><h4 class="chatter_middle_title" style="margin-top: 0px;">{{ $discussion->title }} </h4></a>
											<span class="chatter_middle_details">@lang('chatter::messages.discussion.posted_by') <span data-href="/user">{{ ucfirst($discussion->user->{Config::get('chatter.user.database_field_with_user_name')}) }}</span> {{ \Carbon\Carbon::createFromTimeStamp(strtotime($discussion->created_at))->diffForHumans() }}</span>
											@if($discussion->post[0]->markdown)
												<?php $discussion_body = GrahamCampbell\Markdown\Facades\Markdown::convertToHtml( $discussion->post[0]->body ); ?>
											@else
												<?php $discussion_body = $discussion->post[0]->body; ?>
											@endif
											<p>{{ substr(strip_tags($discussion_body), 0, 200) }}@if(strlen(strip_tags($discussion_body)) > 200){{ '...' }}@endif</p>
										</td>
										<td>
											<a href="/{{ Config::get('chatter.routes.home') }}/{{ Config::get('chatter.routes.category') }}/{{ $discussion->category->slug }}" class="chatter_cat" style="background-color:{{ $discussion->category->color }}; border-radius: 30px; font-size: 12px; padding: 3px 7px; display: inline; color: #fff; position: relative; top: -2px;">{{ $discussion->category->name }}</a>
										</td>
										<td style="font-size: 20px;"> <i class="chatter-bubble"></i>&nbsp;{{ $discussion->postsCount[0]->total }}</td>
									
									</tr>

								@empty
									<tr>
										<td>-</td>
										<td>-</td>
										<td>-</td>
										<td>-</td>
									</tr>
									@endforelse
							</tbody>
						</table>
					</div>
					<div id="pagination">
						{{ $discussions->links() }}
					</div>
	        	</div>
			</div>
	    	
	    	
	    	<div class="visible-xs">
				<div class="col-xs-12">
					<div class="table">
						<table class="table table-striped tableEvents" id="tableEventParticiper">

							<tbody>
								@forelse($discussions as $discussion)
									<tr>										
										<td>
											<a class="discussion_list" href="/{{ Config::get('chatter.routes.home') }}/{{ Config::get('chatter.routes.discussion') }}/{{ $discussion->category->slug }}/{{ $discussion->slug }}">
												<h4 class="chatter_middle_title" style="margin-top: 0px;">{{ $discussion->title }} <i class="chatter-bubble"></i>&nbsp;{{ $discussion->postsCount[0]->total }}</h4>
											</a>
											<span class="chatter_middle_details">
												<span data-href="/user">
												
												@if(Config::get('chatter.user.avatar_image_database_field'))

													<?php $db_field = Config::get('chatter.user.avatar_image_database_field'); ?>
													
													@if( (substr($discussion->user->{$db_field}, 0, 7) == 'http://') || (substr($discussion->user->{$db_field}, 0, 8) == 'https://') )
														<img style="width: 20px; height: 20px; border-radius: 50%;" src="{{ $discussion->user->{$db_field}  }}">
													@elseif($discussion->user->{$db_field})
														<img style="width: 20px; height: 20px; border-radius: 50%;" src="{{ Config::get('chatter.user.relative_url_to_image_assets') . $discussion->user->{$db_field}  }}">
													@else
														<img style="width: 20px; height: 20px; border-radius: 50%;" src="{{ Config::get('chatter.user.if_empty_avatar_img_url') }}">
													@endif

												@else

													<span class="chatter_avatar_circle" style="background-color:#<?= \DevDojo\Chatter\Helpers\ChatterHelper::stringToColorCode($discussion->user->email) ?>">
														{{ strtoupper(substr($discussion->user->email, 0, 1)) }}
													</span>

												@endif

												{{ ucfirst($discussion->user->{Config::get('chatter.user.database_field_with_user_name')}) }}
												</span> 
												{{ \Carbon\Carbon::createFromTimeStamp(strtotime($discussion->created_at))->diffForHumans() }}
												in <a href="#">{{ $discussion->category->name }}</a>
											</span>
											<div class="mt10" style="margin-left:22px">
												@if($discussion->post[0]->markdown)
													<?php $discussion_body = GrahamCampbell\Markdown\Facades\Markdown::convertToHtml( $discussion->post[0]->body ); ?>
												@else
													<?php $discussion_body = $discussion->post[0]->body; ?>
												@endif
												<p>{{ substr(strip_tags($discussion_body), 0, 200) }}@if(strlen(strip_tags($discussion_body)) > 200){{ '...' }}@endif</p>
											</div>
										</td>	
									</tr>

								@empty
									<tr>
										<td style="display: none">-</td>
										<td>-</td>
										<td>-</td>
										<td>-</td>
										<td>-</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>
					<div id="pagination" style="font-size: 35px;">
						{{ $discussions->links() }}
					</div>
	        	</div>
			</div>
	    	
	    	
	    	
	    	
	        <div class="col-md-9 right-column">



	        </div>
	    </div>
	</div>

	<div id="new_discussion">


    	<div class="chatter_loader dark" id="new_discussion_loader">
		    <div></div>
		</div>

    	<form id="chatter_form_editor" action="/{{ Config::get('chatter.routes.home') }}/{{ Config::get('chatter.routes.discussion') }}" method="POST">
        	<div class="row">
	        	<div class="col-md-7">
		        	<!-- TITLE -->
	                <input type="text" class="form-control" id="title" name="title" placeholder="@lang('chatter::messages.editor.title')" v-model="title" value="{{ old('title') }}" >
	            </div>

	            <div class="col-md-4">
		            <!-- CATEGORY -->
			            <select id="chatter_category_id" class="form-control" name="chatter_category_id">
			            	<option value="">@lang('chatter::messages.editor.select')</option>
				            @foreach($categories as $category)
				            	@if ($category->name == 'FAQ' || $category->name == 'Blog')
									@if(Auth::check() && auth()->user()->isRole('admin'))
										@if(old('chatter_category_id') == $category->id)
											<option value="{{ $category->id }}" selected>{{ $category->name }}</option>
										@else
											<option value="{{ $category->id }}">{{ $category->name }}</option>
										@endif
									@endif
								@else
									@if(old('chatter_category_id') == $category->id)
										<option value="{{ $category->id }}" selected>{{ $category->name }}</option>
									@else
										<option value="{{ $category->id }}">{{ $category->name }}</option>
									@endif
								@endif
				            @endforeach
			            </select>
		        </div>

		        <div class="col-md-1">
		        	<i class="chatter-close"></i>
		        </div>
	        </div><!-- .row -->

            <!-- BODY -->
        	<div id="editor">
        		@if( $chatter_editor == 'tinymce' || empty($chatter_editor) )
					<label id="tinymce_placeholder">@lang('chatter::messages.editor.tinymce_placeholder')</label>
    				<textarea id="body" class="richText" name="body" placeholder="">{{ old('body') }}</textarea>
    			@elseif($chatter_editor == 'simplemde')
    				<textarea id="simplemde" name="body" placeholder="">{{ old('body') }}</textarea>
				@elseif($chatter_editor == 'trumbowyg')
					<textarea class="trumbowyg" name="body" placeholder="@lang('chatter::messages.editor.tinymce_placeholder')">{{ old('body') }}</textarea>
				@endif
    		</div>

            <input type="hidden" name="_token" id="csrf_token_field" value="{{ csrf_token() }}">

            <div id="new_discussion_footer">
            	<input type='text' id="color" name="color" /><span class="select_color_text">@lang('chatter::messages.editor.select_color_text')</span>
            	<button id="submit_discussion" class="btn btn-success pull-right"><i class="chatter-new"></i>@lang('chatter::messages.discussion.create')</button>
            	<a href="/{{ Config::get('chatter.routes.home') }}" class="btn btn-default pull-right" id="cancel_discussion">@lang('chatter::messages.words.cancel')</a>
            	<div style="clear:both"></div>
            </div>
        </form>

    </div><!-- #new_discussion -->

</div>

@if( $chatter_editor == 'tinymce' || empty($chatter_editor) )
	<input type="hidden" id="chatter_tinymce_toolbar" value="{{ Config::get('chatter.tinymce.toolbar') }}">
	<input type="hidden" id="chatter_tinymce_plugins" value="{{ Config::get('chatter.tinymce.plugins') }}">
@endif
<input type="hidden" id="current_path" value="{{ Request::path() }}">

@endsection

@section(Config::get('chatter.yields.footer'))


@if( $chatter_editor == 'tinymce' || empty($chatter_editor) )
	<script src="{{ url('/vendor/devdojo/chatter/assets/vendor/tinymce/tinymce.min.js') }}"></script>
	<script src="{{ url('/vendor/devdojo/chatter/assets/js/tinymce.js') }}"></script>
	<script>
		var my_tinymce = tinyMCE;
		$('document').ready(function(){
			$('#tinymce_placeholder').click(function(){
				my_tinymce.activeEditor.focus();
			});
		});
	</script>
@elseif($chatter_editor == 'simplemde')
	<script src="{{ url('/vendor/devdojo/chatter/assets/js/simplemde.min.js') }}"></script>
	<script src="{{ url('/vendor/devdojo/chatter/assets/js/chatter_simplemde.js') }}"></script>
@elseif($chatter_editor == 'trumbowyg')
	<script src="{{ url('/vendor/devdojo/chatter/assets/vendor/trumbowyg/trumbowyg.min.js') }}"></script>
	<script src="{{ url('/vendor/devdojo/chatter/assets/vendor/trumbowyg/plugins/preformatted/trumbowyg.preformatted.min.js') }}"></script>
	<script src="{{ url('/vendor/devdojo/chatter/assets/js/trumbowyg.js') }}"></script>
@endif

<script src="{{ url('/vendor/devdojo/chatter/assets/vendor/spectrum/spectrum.js') }}"></script>
<script src="{{ url('/vendor/devdojo/chatter/assets/js/chatter.js') }}"></script>
<script>
	$('document').ready(function(){

		$('.chatter-close, #cancel_discussion').click(function(){
			$('#new_discussion').slideUp();
		});
		$('#new_discussion_btn').click(function(){
			@if(Auth::guest())
				window.location.href = "{{ route('login') }}";
			@else
				$('#new_discussion').slideDown();
				$('#title').focus();
			@endif
		});

		$("#color").spectrum({
		    color: "#333639",
		    preferredFormat: "hex",
		    containerClassName: 'chatter-color-picker',
		    cancelText: '',
    		chooseText: 'close',
		    move: function(color) {
				$("#color").val(color.toHexString());
			}
		});

		@if (count($errors) > 0)
			$('#new_discussion').slideDown();
			$('#title').focus();
		@endif


	});
</script>
@stop
