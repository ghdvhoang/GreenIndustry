
<div class="message-box chat_control bg-white border radius-8">
    @if(!empty($reciver_data))
    <div class="modal-header d-flex">
       
        <div class="avatar d-flex">
            <a href="#" class="d-flex align-items-center">
                <div class="avatar avatar-lg me-2">
                    <img src="{{ get_user_image($reciver_data->photo,'optimized') }}" class="rounded-circle h-45" alt="">
                    @if ($reciver_data->isOnline())
                        <span class="online-status active"></span>
                    @endif
                </div>
                <div class="name">
                    <h4 class="m-0 h6">{{ $reciver_data->name }}</h4>
                    @if ($reciver_data->isOnline())
                        <small class="d-block">{{ get_phrase('Active now') }}</small>   
                    @else
                        <small class="d-block"> {{ \Carbon\Carbon::parse($reciver_data->lastActive)->diffForHumans();  }}</small>   
                    @endif
                </div>
            </a>
        </div>
        <div class="chat-actions">
            <a class="dropdown-toggle" id="dropdownMenuButton1" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="fa-solid fa-ellipsis-vertical"></i>
            </a>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                <li><a class="dropdown-item" href="{{ route('user.profile.view',$reciver_data->id) }}"><i class="fa fa-user"></i>
                        {{ get_phrase('View Profile') }}</a></li>
            </ul>

        </div>
    </div>
    <div class="modal-body">
        <div class="modal-inner" id="messageShowDiv">
            <div class="message-body" id="message_body">
                @include('frontend.chat.single-message')
            </div>
        </div>

     @endif   
        
        @php
            if(session()->has('product_ref_id')){
                $product_url =  url('/')."/product/view/".session('product_ref_id');
            }
        @endphp
        
        <div class="mt-action"> 
            @if(!empty($reciver_data))
            <!-- Chat textarea -->
            <form class="ajaxForm" id="chatMessageFieldForm" action="{{ route('chat.save') }}" method="POST" enctype="multipart/form-data">
                @csrf
                 <div class="nm_footer d-flex">
                    
                    <div class="d-flex w-100 message_b">
                            <input type="hidden" name="reciver_id" value="{{ $reciver_data->id }}" id="">
                            @if ($product!=null)
                                <input type="hidden" name="product_id" value="{{ $product }}" >
                            @endif
                            <input type="hidden" name="thumbsup" value="1" id="ChatthumbsUpInput">
                            <input type="text" class="form-control mb-sm-0 mb-3 ms-1" name="message" id="ChatmessageField" value="@if(isset($product_url)&&$product_url!=null) {{ $product_url }} @endif" placeholder="Type a message">
                            <button class="btn btn-primary send no-processing d-none no-uploading" id="ChatsentButton"><i class="fa-solid fa-paper-plane"></i></button>
                            <button type="submit" class="btn btn-primary  send  no_loading no-processing no-uploading"  id="ChatthumbsUp"><i class="fa-solid fa-thumbs-up"></i> </button>
                    </div>
                   
                 </div>
                <button type="reset" id="messageResetBox" class="visibility-hidden">{{get_phrase('Reset')}}</button>
                <div class="mt-footer">
                    <div class="input-images d-hidden  image-uploader_custom_css" id="messageFileUploder">
                    </div>
                    <a href="javascript:void(0)" id="messgeImageUploader"><img src="{{ asset('assets/frontend/images/image-a.png') }}" alt=""></a>
                    
                </div>
            </form>
            <!-- Button -->
            @php
                Session::forget('product_ref_id')
            @endphp
             
              @else
              <div style="width: 100%; height: 500px; display:flex; justify-content:center; align-items:center; font-size:20px;">
                 <p>{{get_phrase('No Conversion Start!')}}</p>
              </div> 
              @endif
        </div>
      
    </div>
</div>



@section('custom_js_code_for_chat')
<script>
    "use strict";
    
    $(document).ready(function(){
        var elem = document.getElementById('messageShowDiv');
        if(elem) { // Kiểm tra xem phần tử có tồn tại không trước khi dùng
            elem.scrollTop = elem.scrollHeight;
        }

        // 2. Tự động gọi để tải tin nhắn mới sau mỗi 4 giây
        setInterval(ajaxCallForDataLoad, 4000);   

        // 3. Khởi tạo plugin upload ảnh
        $('.input-images:not(.initialized)').imageUploader({
            imagesInputName:'multiple_files',
            extensions: ['.jpg','.jpeg','.png','.gif','.svg'],
            mimes: ['image/jpeg','image/png','image/gif','image/svg+xml'],
            label: 'Drag & Drop files here or click to browse'
        });

        // -- CÁC HÀNH ĐỘNG CỦA NGƯỜI DÙNG --

        // 4. Xử lý khi người dùng gõ chữ vào ô chat
        $('#ChatmessageField').keyup(function() {
            let value = $(this).val();
            if(value.length > 0){
                $('#ChatsentButton').removeClass('d-none');
                $('#ChatthumbsUp').addClass('d-none');
                $('#ChatthumbsUpInput').val('0');
            }else{
                $('#ChatsentButton').addClass('d-none');
                $('#ChatthumbsUp').removeClass('d-none');
                $('#ChatthumbsUpInput').val('1');
            }
        });

        // 5. Xử lý khi người dùng click vào icon upload ảnh
        $("#messgeImageUploader").click(function() {
            $('#ChatsentButton').removeClass('d-none');
            $('#ChatthumbsUp').addClass('d-none');
            $('#messageFileUploder').toggle();
        });

        // 6. Xử lý khi tìm kiếm bạn bè trong danh sách chat
        $("#chatSearch").keyup(function(){
            let value= $(this).val();
            $.ajax({
                type : 'get',
                url : '{{URL::to('/chat/profile/search/')}}',
                // không cần header CSRF cho request GET
                data:{'search':value},
                success:function(response){
                    $('#chatFriendList').html(response);
                }
            });
        });

        // 7. [PHẦN SỬA LỖI QUAN TRỌNG] Lắng nghe sự kiện sau khi form được gửi thành công
        $(document).on('ajaxForm.afterSubmit', function(e) {
            if (e.target.id == 'chatMessageFieldForm') {
                // Xóa nội dung trong ô nhập liệu chat.
                $('#ChatmessageField').val(''); 
                
                // Xóa các ảnh đã chọn để upload (nếu có).
                $('#messageFileUploder').find('.uploaded').remove();

                // Chuyển nút "Gửi" trở lại thành nút "Thích".
                $('#ChatsentButton').addClass('d-none');
                $('#ChatthumbsUp').removeClass('d-none');
                $('#ChatthumbsUpInput').val('1');

                // Cuộn xuống tin nhắn mới nhất.
                var elem = document.getElementById('messageShowDiv');
                if(elem){
                    elem.scrollTop = elem.scrollHeight;
                }

                console.log('Chat form đã được reset thành công!');
            }
        });

    }); // <-- Kết thúc của khối $(document).ready() DUY NHẤT


    // -- CÁC HÀM RIÊNG LẺ (có thể đặt bên ngoài document.ready) --

    function ajaxCallForDataLoad() {
        var currentURL = $(location).attr('href'); 
        var id = currentURL.substring(currentURL.lastIndexOf('/') + 1);

        // Chỉ gọi ajax nếu id là một số hợp lệ
        if(!isNaN(id) && id){
            $.ajax({
                type : 'get',
                url : '{{URL::to('/chat/inbox/load/data/ajax/' )}}',
                data:{'id':id},
                success:function(response){
                    distributeServerResponse(response);
                    if(response.content !== undefined && response.content.trim() !== ''){
                        var elem = document.getElementById('messageShowDiv');
                        if(elem){
                           elem.scrollTop = elem.scrollHeight;
                        }
                    }
                },
                error: function(error) {
                    // Ngừng gọi lại nếu có lỗi (ví dụ: người dùng đăng xuất)
                    // Hoặc bạn có thể thêm logic xử lý lỗi ở đây
                }
            });
        }
    }

    // Hàm này không được gọi ở đâu cả, nhưng giữ lại nếu bạn cần
    function ajaxCallForReadData() {
        var currentURL = $(location).attr('href'); 
        var id = currentURL.substring(currentURL.lastIndexOf('/') + 1);
        if(!isNaN(id) && id){
            $.ajax({
                type : 'get',
                url : '{{URL::to('/chat/inbox/read/message/ajax/' )}}',
                data:{'id':id},
                success:function(response){
                    console.log(response);
                }
            });
        }
    }

</script>
@endsection