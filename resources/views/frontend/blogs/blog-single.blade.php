{{-- Thêm khối CSS để ghi đè màu khi hover.
     Sử dụng @push('styles') là cách làm chuẩn trong Laravel nếu layout của bạn có @stack('styles').
     Nếu không, bạn có thể thay @push('styles') và @endpush bằng <style> và </style>.
--}}
@push('styles')
<style>
    /* 
      Chúng ta nhắm mục tiêu vào các thẻ <a> nằm trong article.card
      để đảm bảo không ảnh hưởng tới các link khác trên trang.
    */
    .card .card-body a {
        /* Thêm hiệu ứng chuyển màu mượt mà trong 0.2 giây */
        transition: color 0.2s ease-in-out;
    }

    /* 
      Khi di chuột vào link (a:hover), đổi màu của nó.
      Sử dụng !important để đảm bảo quy tắc này được ưu tiên,
      ghi đè lên các class của Bootstrap như 'text-dark'.
    */
    .card .card-body a:hover {
        color: #50cd89 !important; /* MÀU XANH BẠN MUỐN */
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