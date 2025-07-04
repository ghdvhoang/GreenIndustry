
@push('styles')
<style>
    .card .card-body a {
        transition: color 0.2s ease-in-out;
    }

    .card .card-body a:hover {
        color: #50cd89 !important; 
    }
</style>
@endpush


@foreach ($blogs as $blog )
    <div class="col-12 my-2" id="blog-{{ $blog->id }}">
        <article class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex flex-column">
                <div>
                    <h3 class="h5 card-title">
                        <a href="{{ route('single.blog', $blog->id) }}" class="text-dark text-decoration-none">{{$blog->title}}</a>
                    </h3>
                </div>

                <div class="d-flex align-items-center mt-auto pt-3">
                    <img src="{{ get_user_image($blog->user_id, 'optimized') }}" class="rounded-circle" width="40" height="40" alt="{{ $blog->getUser->name }}">
                    <div class="ms-2">
                        <h6 class="mb-0 fs-sm">
                            <a href="{{ route('user.profile.view', $blog->getUser->id) }}" class="text-decoration-none">{{ $blog->getUser->name }}</a>
                        </h6>
                        <small class="text-muted">
                            {{ $blog->created_at->timezone(optional(Auth::user())->timezone ?? config('app.timezone'))->diffForHumans() }}
                            
                            <span class="mx-1">•</span> 
                            
                            {{ $blog->created_at->timezone(optional(Auth::user())->timezone ?? config('app.timezone'))->format("d-M-Y") }}
                        </small>
                    </div>
                </div>
            </div>
        </article>
    </div> 
@endforeach