@extends(Config::get('chatter.master_file_extend'))

@section('title', $discussion->title.' | Foro Vivetix')

@section(Config::get('chatter.yields.head'))

	<meta name="description" content="{{substr(strip_tags($first_post), 0, 250)}}...">

	<meta property="og:site_name" content="Foro Vivetix">
	<meta property="og:title" content="{{$discussion->title}} | Foro Vivetix">
	
	@php 
		$haveImg = preg_match_all( '@src="([^"]+)"@' , $first_post, $result );
	@endphp
		
	@if ($haveImg)
		<meta property="og:image" itemprop="image" content="{{ $result[1][0] }}"/>
	@else
		<meta property="og:image" content="{{ 'https:'.cdn('/img/web/logo-squared.png')}}"/>
	@endif
	<meta property="og:description" content="{{substr(strip_tags($first_post), 0, 250)}}...">
	<meta property="og:type" content="website">
	<meta property="og:url" content="{{ url('/'.Config::get('chatter.routes.home').'/'.Config::get('chatter.routes.discussion').'/'.$discussion->category->slug).'/'.$discussion->slug  }}"> 

	<meta name="twitter:card" content="summary" />
	<meta name="twitter:site" content="@vivetix" />
	<meta name="twitter:title" content="{{$discussion->title}}" />
	<meta name="twitter:description" content="{{substr(strip_tags($first_post), 0, 150)}}..." />
	@if ($haveImg)
		<meta property="twitter:image" content="{{ $result[1][0] }}"/>
	@else
		<meta property="twitter:image" content="{{ 'https:'.cdn('/img/web/logo-squared.png')}}"/>
	@endif
	
    @if(Config::get('chatter.sidebar_in_discussion_view'))
        <link href="{{ url('/vendor/devdojo/chatter/assets/vendor/spectrum/spectrum.css') }}" rel="stylesheet">
    @endif
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
    
    	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/css/jquery.dataTables.min.css" />

    
	<style>
	
		#container { 
			margin-top: 10px; 
		}
		
		.mce-toolbar { 
			border-top: 0px !important; 
		}
		    
			
		@media only screen and (min-width : 767px) {
			#newDiscussion { 
			margin-left: 110px; 
			}
			#container { 
			margin-top: 40px; 
			}
		}
		
	
	</style>
	
@stop


@section('content')

<div id="chatter" class="discussion">

	<!--<div id="chatter_header" style="background-color:{{ $discussion->color }}">
		<div class="container">
			
		</div>
	</div>-->
	@if(config('chatter.errors'))
		@if(Session::has('chatter_alert') && $chatter_errors)
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

	<div id="container" class="container">

	    <div class="row">

			@if(! Config::get('chatter.sidebar_in_discussion_view'))
	        	<div class="col-md-12">
            @else
                <div class="col-md-3 left-column">
                    <!-- SIDEBAR -->
                    <div class="chatter_sidebar">
                        <button class="btn btn-primary hidden-xs hidden-sm" id="new_discussion_btn"><i class="chatter-new"></i>@lang('chatter::messages.discussion.new')</button>
                        <ul class="nav nav-pills nav-stacked" style="margin-top:10px;">
                        	<li><span class="visible-xs visible-sm"><a href="#" style="padding-left: 10px;" id="new_discussion_btn_mobile"><i class="chatter-new"></i>@lang('chatter::messages.discussion.new')</a></span></li>
                        	<li><a href="/{{ Config::get('chatter.routes.home') }}" style="padding-left: 5px;"><i class="chatter-bubble"></i> @lang('chatter::messages.words.all')</a></li>
                            <?php $categories = DevDojo\Chatter\Models\Models::category()->all(); ?>
                            @foreach($categories as $category)
                                <li><a href="/{{ Config::get('chatter.routes.home') }}/{{ Config::get('chatter.routes.category') }}/{{ $category->slug }}"><div class="chatter-box" style="background-color:{{ $category->color }}"></div> {{ $category->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    <hr class="visible-xs visible-sm">
                    <!-- END SIDEBAR -->
                </div>
                <div class="col-xs-12 col-md-9 right-column">
            @endif
				
					<div class="table">
						<table class="table">
							<tbody>
								<tr style="background-color: #f9f9f9;">
									<td colspan="3">
										<h2 style="padding-left: 45px; margin-top: 0px;">{{ $discussion->title }}</h2>
										<span class="chatter_head_details" style="padding-left: 45px;"> @lang('chatter::messages.discussion.head_details')
											<a class="chatter_cat" href="/{{ Config::get('chatter.routes.home') }}/{{ Config::get('chatter.routes.category') }}/{{ $discussion->category->slug }}" style="background-color:{{ $discussion->category->color }}; border-radius: 30px; font-size: 12px; padding: 3px 7px; display: inline; color: #fff; position: relative; top: -2px;">{{ $discussion->category->name }}</a>
										</span>
									</td>
								</tr>
								@forelse($posts as $post)
									<tr class="discussions no-bg">
										<td class="hidden-xs">
											@if(Config::get('chatter.user.avatar_image_database_field'))

												<?php $db_field = Config::get('chatter.user.avatar_image_database_field'); ?>
												<!-- If the user db field contains http:// or https:// we don't need to use the relative path to the image assets -->
												@if( (substr($post->user->{$db_field}, 0, 7) == 'http://') || (substr($post->user->{$db_field}, 0, 8) == 'https://') )
													<img style="width: 60px; height: 60px; border-radius: 50%;" src="{{ $post->user->{$db_field}  }}">
												@elseif($post->user->{$db_field})
													<img style="width: 60px; height: 60px; border-radius: 50%;" src="{{ Config::get('chatter.user.relative_url_to_image_assets') . $post->user->{$db_field}  }}">
												@else
													<img style="width: 60px; height: 60px; border-radius: 50%;" src="{{ Config::get('chatter.user.if_empty_avatar_img_url') }}">
												@endif

											@else

												<span class="chatter_avatar_circle" style="background-color:#<?= \DevDojo\Chatter\Helpers\ChatterHelper::stringToColorCode($discussion->user->email) ?>">
													{{ strtoupper(substr($discussion->user->email, 0, 1)) }}
												</span>

											@endif
										</td>
										<td id="content_{{ $post->id }}" data-id="{{ $post->id }}" data-markdown="{{ $post->markdown }}" style="width: 100%;">
											<!--<a class="discussion_list" href="/{{ Config::get('chatter.routes.home') }}/{{ Config::get('chatter.routes.discussion') }}/{{ $discussion->category->slug }}/{{ $discussion->slug }}"><h4 class="chatter_middle_title" style="margin-top: 0px;">{{ $discussion->title }} </h4></a>
											<span class="chatter_middle_details">@lang('chatter::messages.discussion.posted_by') <span data-href="/user">{{ ucfirst($discussion->user->{Config::get('chatter.user.database_field_with_user_name')}) }}</span> {{ \Carbon\Carbon::createFromTimeStamp(strtotime($discussion->created_at))->diffForHumans() }}</span>-->
										
											<div class="chatter_middle">
												<span class="visible-xs">
													<span class="chatter_middle_details">
														<?php $db_field = Config::get('chatter.user.avatar_image_database_field'); ?>

														<!-- If the user db field contains http:// or https:// we don't need to use the relative path to the image assets -->
														@if( (substr($post->user->{$db_field}, 0, 7) == 'http://') || (substr($post->user->{$db_field}, 0, 8) == 'https://') )
															<img style="width: 20px; height: 20px; border-radius: 50%;" src="{{ $post->user->{$db_field}  }}">
														@elseif($post->user->{$db_field})
															<img style="width: 20px; height: 20px; border-radius: 50%;" src="{{ Config::get('chatter.user.relative_url_to_image_assets') . $post->user->{$db_field}  }}">
														@else
															<img style="width: 20px; height: 20px; border-radius: 50%;" src="{{ Config::get('chatter.user.if_empty_avatar_img_url') }}">
														@endif
														<a href="{{ \DevDojo\Chatter\Helpers\ChatterHelper::userLink($post->user) }}">{{ ucfirst($post->user->{Config::get('chatter.user.database_field_with_user_name')}) }}</a> <span class="ago chatter_middle_details">{{ \Carbon\Carbon::createFromTimeStamp(strtotime($post->created_at))->diffForHumans() }}</span></span>
													</span>
												</span>
												
												<span class="hidden-xs">
													<span class="chatter_middle_details">
														<a href="{{ \DevDojo\Chatter\Helpers\ChatterHelper::userLink($post->user) }}">{{ ucfirst($post->user->{Config::get('chatter.user.database_field_with_user_name')}) }}</a> 
														<span class="ago">
															{{ \Carbon\Carbon::createFromTimeStamp(strtotime($post->created_at))->diffForHumans() }}
														</span>
													</span>
												</span>
												<div class="chatter_body">

													@if($post->markdown)
														<pre class="chatter_body_md">{{ $post->body }}</pre>
														<?= \DevDojo\Chatter\Helpers\ChatterHelper::demoteHtmlHeaderTags( GrahamCampbell\Markdown\Facades\Markdown::convertToHtml( $post->body ) ); ?>
														<!--?= GrahamCampbell\Markdown\Facades\Markdown::convertToHtml( $post->body ); ?-->
													@else
														<?= $post->body; ?>
													@endif

												</div>
											</div>
										
											@if(!Auth::guest() && ((Auth::user()->id == $post->user->id) || auth()->user()->isRole('admin')))
											<!-- Post actions -->
											<div class="chatter_post_actions">
												<a class="chatter_delete_btn" href="#/">
													<i class="chatter-delete"></i> @lang('chatter::messages.words.delete')
												</a>
												<a class="chatter_edit_btn" href="#/">
													<i class="chatter-edit"></i> @lang('chatter::messages.words.edit')
												</a>
											</div>
										
											<!-- Actions dialog -->
												<div id="delete_warning_{{ $post->id }}" data-id="{{ $post->id }}" class="chatter_warning_delete hide alert alert-danger">
													<i class="chatter-warning"></i>@lang('chatter::messages.response.confirm')
													<button class="btn btn-sm btn-danger pull-right delete_response">@lang('chatter::messages.response.yes_confirm')</button>
													<button class="btn btn-sm btn-default pull-right">@lang('chatter::messages.response.no_confirm')</button>
												</div>
											@endif
									
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
					<div id="pagination">
						{{ $posts->links() }}
					</div>

	            @if(!Auth::guest())

	            	<div id="" class="row">

	            		<div class="chatter_avatar hidden-xs col-sm-2" style="margin-left: 5px;">
		        			@if(Config::get('chatter.user.avatar_image_database_field'))

		        				<?php $db_field = Config::get('chatter.user.avatar_image_database_field'); ?>

		        				<!-- If the user db field contains http:// or https:// we don't need to use the relative path to the image assets -->
		        				@if( (substr(Auth::user()->{$db_field}, 0, 7) == 'http://') || (substr(Auth::user()->{$db_field}, 0, 8) == 'https://') )
		        					<img src="{{ Auth::user()->{$db_field}  }}">
		        				@elseif(Auth::user()->{$db_field})
		        					<img src="{{ Config::get('chatter.user.relative_url_to_image_assets') . Auth::user()->{$db_field}  }}">
		        				@else
		        					<img src="{{ Config::get('chatter.user.if_empty_avatar_img_url')  }}">
		        				@endif

		        			@else
		        				<span class="chatter_avatar_circle" style="background-color:#<?= \DevDojo\Chatter\Helpers\ChatterHelper::stringToColorCode(Auth::user()->email) ?>">
		        					{{ strtoupper(substr(Auth::user()->email, 0, 1)) }}
		        				</span>
		        			@endif
		        		</div>

			            <div id="newDiscussion" class="col-xs-12 col-sm-10" style="border: 1px solid #f1f5ff;">


					    	<div class="chatter_loader dark" id="new_discussion_loader">
							    <div></div>
							</div>

				            <form id="chatter_form_editor" action="/{{ Config::get('chatter.routes.home') }}/posts" method="POST">

						        <!-- BODY -->
						    	<div id="editor">
									@if( $chatter_editor == 'tinymce' || empty($chatter_editor) )
										<label id="tinymce_placeholder">@lang('chatter::messages.editor.tinymce_placeholder')</label>
					    				<textarea id="body" class="richText" name="body" placeholder="">{{ old('body') }}</textarea>
					    			@elseif($chatter_editor == 'simplemde')
					    				<textarea id="simplemde" name="body" placeholder="">{{ old('body') }}</textarea>
									@elseif($chatter_editor == 'trumbowyg')
										<textarea class="trumbowyg" name="body" placeholder="Type Your Discussion Here...">{{ old('body') }}</textarea>
									@endif
								</div>

						        <input type="hidden" name="_token" id="csrf_token_field" value="{{ csrf_token() }}">
						        <input type="hidden" name="chatter_discussion_id" value="{{ $discussion->id }}">
						        <input type="hidden" name="post_type" value="subpost">
						    </form>

						</div><!-- #new_discussion -->
						<div id="discussion_response_email">
							<button id="submit_response" class="btn btn-success pull-right"><i class="chatter-new"></i>@lang('chatter::messages.response.submit')</button>
							@if(Config::get('chatter.email.enabled'))
								<div id="notify_email">
									<img src="{{ url('/vendor/devdojo/chatter/assets/images/email.gif') }}" class="chatter_email_loader">
									<!-- Rounded toggle switch -->
									<span>@lang('chatter::messages.email.notify')</span>
									<label class="switch">
									  	<input type="checkbox" id="email_notification" name="email_notification" @if(!Auth::guest() && $discussion->users->contains(Auth::user()->id)){{ 'checked' }}@endif>
									  	<span class="on">@lang('chatter::messages.words.yes')</span>
										<span class="off">@lang('chatter::messages.words.no')</span>
									  	<div class="slider round"></div>
									</label>
								</div>
							@endif
						</div>
					</div>

				@else

					<div id="login_or_register">
						<p>
                            @lang('chatter::messages.auth', ['home' => Config::get('chatter.routes.home')])
                        </p>
					</div>

				@endif

	        </div>


	    </div>
	</div>

    @if(Config::get('chatter.sidebar_in_discussion_view'))
        <div id="new_discussion_in_discussion_view">

            <div class="chatter_loader dark" id="new_discussion_loader_in_discussion_view">
                <div></div>
            </div>

            <form id="chatter_form_editor_in_discussion_view" action="/{{ Config::get('chatter.routes.home') }}/{{ Config::get('chatter.routes.discussion') }}" method="POST">
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
                                @if(old('chatter_category_id') == $category->id)
                                    <option value="{{ $category->id }}" selected>{{ $category->name }}</option>
                                @else
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
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
                        <label id="tinymce_placeholder">Add the content for your Discussion here</label>
                        <textarea id="body_in_discussion_view" class="richText" name="body" placeholder="">{{ old('body') }}</textarea>
                    @elseif($chatter_editor == 'simplemde')
                        <textarea id="simplemde_in_discussion_view" name="body" placeholder="">{{ old('body') }}</textarea>
                    @elseif($chatter_editor == 'trumbowyg')
                        <textarea class="trumbowyg" name="body" placeholder="">{{ old('body') }}</textarea>
                    @endif
                </div>

                <input type="hidden" name="_token" id="csrf_token_field" value="{{ csrf_token() }}">

                <div id="new_discussion_footer">
                    <input type='text' id="color" name="color" /><span class="select_color_text">@lang('chatter::messages.editor.tinymce_placeholder')</span>
                    <button id="submit_discussion" class="btn btn-success pull-right"><i class="chatter-new"></i>@lang('chatter::messages.discussion.create')</button>
                    <a href="/{{ Config::get('chatter.routes.home') }}" class="btn btn-default pull-right" id="cancel_discussion">@lang('chatter::messages.words.cancel')</a>
                    <div style="clear:both"></div>
                </div>
            </form>

        </div><!-- #new_discussion -->
    @endif

</div>

@if($chatter_editor == 'tinymce' || empty($chatter_editor))
    <input type="hidden" id="chatter_tinymce_toolbar" value="{{ Config::get('chatter.tinymce.toolbar') }}">
    <input type="hidden" id="chatter_tinymce_plugins" value="{{ Config::get('chatter.tinymce.plugins') }}">
@endif
<input type="hidden" id="current_path" value="{{ Request::path() }}">

@stop

@section(Config::get('chatter.yields.footer'))

@if( $chatter_editor == 'tinymce' || empty($chatter_editor) )
	<script>var chatter_editor = 'tinymce';</script>
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
	<script>var chatter_editor = 'simplemde';</script>
    <script src="{{ url('/vendor/devdojo/chatter/assets/js/simplemde.min.js') }}"></script>
    <script src="{{ url('/vendor/devdojo/chatter/assets/js/chatter_simplemde.js') }}"></script>
@elseif($chatter_editor == 'trumbowyg')
	<script>var chatter_editor = 'trumbowyg';</script>
    <script src="{{ url('/vendor/devdojo/chatter/assets/vendor/trumbowyg/trumbowyg.min.js') }}"></script>
    <script src="{{ url('/vendor/devdojo/chatter/assets/vendor/trumbowyg/plugins/preformatted/trumbowyg.preformatted.min.js') }}"></script>
    <script src="{{ url('/vendor/devdojo/chatter/assets/js/trumbowyg.js') }}"></script>
@endif

@if(Config::get('chatter.sidebar_in_discussion_view'))
    <script src="/vendor/devdojo/chatter/assets/vendor/spectrum/spectrum.js"></script>
    <!--<script src="/vendor/devdojo/chatter/assets/js/chatter.js"></script>-->
@endif

<script>
	$('document').ready(function(){

		var simplemdeEditors = [];

		$('.chatter_edit_btn').click(function(){
			parent = $(this).parents('td');
			//parent.addClass('editing');
			id = parent.data('id');
			$('#content_'+id+' .chatter_middle_details, .chatter_body, .chatter_post_actions').addClass('hide');
			markdown = parent.data('markdown');
			container = parent.find('.chatter_middle');

			if(markdown){
				body = container.find('.chatter_body_md');
			} else {
				body = container.find('.chatter_body');
				markdown = 0;
			}

			details = container.find('.chatter_middle_details');

			// dynamically create a new text area
			container.prepend('<textarea id="post-edit-' + id + '"></textarea>');
            // Client side XSS fix
            $("#post-edit-"+id).text(body.html());
			container.append('<div class="chatter_update_actions"><button class="btn btn-success pull-right update_chatter_edit"  data-id="' + id + '" data-markdown="' + markdown + '"><i class="chatter-check"></i> @lang('chatter::messages.response.update')</button><button href="/" class="btn btn-default pull-right cancel_chatter_edit" data-id="' + id + '"  data-markdown="' + markdown + '">@lang('chatter::messages.words.cancel')</button></div>');

			// create new editor from text area
			if(markdown){
				simplemdeEditors['post-edit-' + id] = newSimpleMde(document.getElementById('post-edit-' + id));
			} else {
                @if($chatter_editor == 'tinymce' || empty($chatter_editor))
                    initializeNewTinyMCE('post-edit-' + id);
                @elseif($chatter_editor == 'trumbowyg')
                    initializeNewTrumbowyg('post-edit-' + id);
                @endif
			}

		});

		$('.discussions td').on('click', '.cancel_chatter_edit', function(e){
			post_id = $(e.target).data('id');
			markdown = $(e.target).data('markdown');
			parent_td = $(e.target).parents('td');
			parent_actions = $(e.target).parent('.chatter_update_actions');
			if(!markdown){
                @if($chatter_editor == 'tinymce' || empty($chatter_editor))
                    tinymce.remove('#post-edit-' + post_id);
                @elseif($chatter_editor == 'trumbowyg')
                    $(e.target).parents('td').find('.trumbowyg').fadeOut();
                @endif
			} else {
				$(e.target).parents('td').find('.editor-toolbar').remove();
				$(e.target).parents('td').find('.editor-preview-side').remove();
				$(e.target).parents('td').find('.CodeMirror').remove();
			}

			$('#post-edit-' + post_id).remove();
			parent_actions.remove();

			//parent_td.removeClass('editing');
			$('#content_'+id+' .chatter_middle_details, .chatter_body, .chatter_post_actions').removeClass('hide');

		});

		$('.discussions td').on('click', '.update_chatter_edit', function(e){
			post_id = $(e.target).data('id');
			markdown = $(e.target).data('markdown');

			if(markdown){
				update_body = simplemdeEditors['post-edit-' + post_id].value();
			} else {
                @if($chatter_editor == 'tinymce' || empty($chatter_editor))
                    update_body = tinyMCE.get('post-edit-' + post_id).getContent();
                @elseif($chatter_editor == 'trumbowyg')
                    update_body = $('#post-edit-' + id).trumbowyg('html');
                @endif
			}

			$.form('/{{ Config::get('chatter.routes.home') }}/posts/' + post_id, { _token: '{{ csrf_token() }}', _method: 'PATCH', 'body' : update_body }, 'POST').submit();
		});

		$('#submit_response').click(function(){
			$('#chatter_form_editor').submit();
		});

		// ******************************
		// DELETE FUNCTIONALITY
		// ******************************

		$('.chatter_delete_btn').click(function(){
			parent = $(this).parent().next();
			parent.addClass('delete_warning');
			id = parent.data('id');
			$('#delete_warning_' + id).removeClass('hide');
		});

		$('.chatter_warning_delete .btn-default').click(function(){
			$(this).parent('.chatter_warning_delete').addClass('hide');
			$(this).parent().prev().removeClass('delete_warning');
		});

		$('.delete_response').click(function(){
			post_id = $(this).parent().data('id');
			$.form('/{{ Config::get('chatter.routes.home') }}/posts/' + post_id, { _token: '{{ csrf_token() }}', _method: 'DELETE'}, 'POST').submit();
		});

		// logic for when a new discussion needs to be created from the slideUp
        @if(Config::get('chatter.sidebar_in_discussion_view'))
            $('.chatter-close, #cancel_discussion').click(function(){
                $('#new_discussion_in_discussion_view').slideUp();
            });
            $('#new_discussion_btn, #new_discussion_btn_mobile').click(function(){
                @if(Auth::guest())
                    window.location.href = "/{{ Config::get('chatter.routes.home') }}/login";
                @else
                    $('#new_discussion_in_discussion_view').slideDown();
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

            @if (count($errors) > 0 && old('post_type') != 'subpost')
                $('#new_discussion_in_discussion_view').slideDown();
                $('#title').focus();
            @endif
            

        @endif

	});
</script>

<script src="{{ url('/vendor/devdojo/chatter/assets/js/chatter.js') }}"></script>

<script>
	//get the number of `<script>` elements that have the correct `src` attribute
	var len = $('script').filter(function () {
		return ($(this).attr('src') == '//cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/jquery.dataTables.min.js');
	}).length;

	//if there are no scripts that match, the load it
	if (len === 0) {
		$.getScript('//cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/jquery.dataTables.min.js');
	}
</script>
@stop
