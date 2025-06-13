
    <div class="page-wrap">
        @if(isset($is_hashtag_search) && $is_hashtag_search)
            <div class="card search-card p-4 border-none bg-white radius-8 p-20 mb-3">
                <h3 class="sub-title mb-0">#{{ $hashtag }} </h3>
                <p class="mb-0">{{ $hashtag_count }} {{get_phrase('posts')}}</p>
            </div>
        @endif

        <div class="card search-card p-4 border-none bg-white radius-8 p-20">
            <h3 class="sub-title">{{ get_phrase('Search Results') }}</h3>
            @include('frontend.search.header')
        </div> <!-- Search Card End -->

        <div class="card page-card border-none bg-white radius-8 p-20 mt-4">
            <h3 class="sub-title mb-3">{{ get_phrase('People') }}</h3>
            @foreach ($peoples as $key=> $people)
            @php
                if($people->id==auth()->user()->id){
                    continue;
                }
            @endphp
            <div class="people-wrap sust_entery">
                <div class="people-item d-sm-flex mb-3 justify-content-between w-100">
                    <div class="d-flex align-items-center">
                        <!-- Avatar -->
                        <div class="avatar">
                            <a href="{{ route('user.profile.view',$people->id) }}"><img class="avatar-img rounded-circle user_image_show_on_modal" src="{{ get_user_image($people->photo,'optimized') }}" alt=""
                                    ></a>
                        </div>
                        <div class="avatar-info ms-2">
                            <h6 class="mb-1"><a href="{{ route('user.profile.view',$people->id) }}">{{ $people->name }}</a></h6>
                            <div class="activity-time small-text text-muted">{{ ellipsis($people->about,'30') }}
                            </div>
                        </div>
                    </div>
                    
                    @php
                        $user_id = $people->id;
                        $friend = \App\Models\Friendships::where(function($query) use ($user_id){
                            $query->where('requester', auth()->user()->id);
                            $query->where('accepter', $user_id);
                        })
                        ->orWhere(function($query) use ($user_id) {
                            $query->where('accepter', auth()->user()->id);
                            $query->where('requester', $user_id);
                        })
                        ->count();

                        $friendAccepted = \App\Models\Friendships::where(function($query) use ($user_id){
                            $query->where('requester', auth()->user()->id);
                            $query->where('accepter', $user_id);
                            $query->where('is_accepted',1);
                        })
                        ->orWhere(function($query) use ($user_id) {
                            $query->where('accepter', auth()->user()->id);
                            $query->where('requester', $user_id);
                            $query->where('is_accepted',1);
                        })
                        ->count();

                        
                    @endphp
                    
                    

                    @if ($friend>0)
                        @if ($friendAccepted>0)
                            <a href="#" class="btn common_btn align-self-start"><i class="fa-solid fa-user-group"></i> {{ get_phrase('Friend') }} </a>
                        @else
                            <a href="javascript:void(0)" onclick="ajaxAction('<?php echo route('user.unfriend',$people->id); ?>')" data-bs-toggle="tooltip" data-bs-placement="top" title="Cancle Friend Request" class="btn common_btn align-self-start"><i class="fa-solid fa-xmark"></i> {{ get_phrase('Cancel') }}</a>
                        @endif
                    @else   
                        <a href="javascript:void(0)" onclick="ajaxAction('<?php echo route('user.friend',$people->id); ?>')" class="btn common_btn align-self-start mt-2"><i class="fa-solid fa-plus"></i> {{ get_phrase('Add Friend') }} </a>
                    @endif

                    
                </div>
            </div>
                @if ($key > 2)
                    @break 
                @endif
            @endforeach
            @if (count($peoples)>4)
                <a href="{{ url('search/people?search='.$_GET['search']) }}" class="btn common_btn btn-sm mt-3 ">{{ get_phrase('See More') }}</a>
                {{-- <a href="{{ url('search/people?search='.request('search')) }}" class="btn btn-secondary btn-sm mt-3 ">{{ get_phrase('See More') }}</a> --}}
            @endif
        </div> <!-- Add Friend Card End -->
        <div class="card people-card border-none bg-white radius-8 p-4 mt-4 mb-3">
            <h3 class="sub-title">{{ get_phrase('Posts') }}</h3>
            @include('frontend.main_content.posts',['posts'=>$posts,'search'=>'search','type'=>'user_post'])
            @if (count($posts)>2)
                <a href="{{ url('search/post?search='.$_GET['search']) }}" class="btn common_btn btn-sm mt-3 ">{{ get_phrase('See More') }}</a>
                {{-- <a href="{{ url('search/post?search='.request('search')) }}" class="btn btn-secondary btn-sm mt-3 ">{{ get_phrase('See More') }}</a> --}}
            @endif
        </div>



        <div class="card border-none bg-white radius-8 p-3 mt-4">
            <h3 class="sub-title mb-3">{{ get_phrase('Groups') }}</h3>
            <div class="suggest-wrap sust_entery row g-2">
                @foreach ($groups as $key => $group )
                <div class="col-md-3 col-lg-4 col-6">
                    <div class="card sugg-card p-2 rounded">
                        <div class="mb-2 thumbnail-133" style="background-image: url('{{ get_group_logo($group->logo,'logo') }}');"></div>
                       <div class="pl_con">
                            <a href="{{ route('single.group',$group->id) }}"><h4>{{ ellipsis($group->title,20) }}</h4></a>
                            @php $joined = \App\Models\Group_member::where('group_id',$group->id)->where('is_accepted','1')->count(); @endphp
                            <span class="small text-muted">{{ $joined }} {{ get_phrase('Member') }} @if($joined>1) s @endif</span>
                            @php $join = \App\Models\Group_member::where('group_id',$group->id)->where('user_id',auth()->user()->id)->count(); @endphp
                            @if ($join>0)
                            <a href="javascript:void(0)" onclick="ajaxAction('<?php echo route('group.rjoin',$group->id); ?>')" class="btn common_btn">{{ get_phrase('Joined') }}</a>
                            @else
                                <a href="javascript:void(0)" onclick="ajaxAction('<?php echo route('group.join',$group->id); ?>')" class="btn common_btn">{{ get_phrase('Join') }}</a>
                            @endif
                       </div>
                    </div>
                </div>
                    @if ($key==3)
                        @break
                    @endif
                @endforeach
            </div>
            @if (count($groups)>3)
                <a href="{{ url('search/group?search='.$_GET['search']) }}" class="btn common_btn btn-sm mt-3">{{ get_phrase('See More') }}</a>
                {{-- <a href="{{ url('search/group?search='.request('search')) }}" class="btn btn-secondary btn-sm mt-3">{{ get_phrase('See More') }}</a> --}}
            @endif
        </div><!--  Group Card End -->
       

        
        
    </div>







    @include('frontend.main_content.scripts')
    @include('frontend.initialize')
    @include('frontend.common_scripts')
