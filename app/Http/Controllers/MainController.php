<?php

namespace App\Http\Controllers;

use App\Models\Comments;
use App\Models\FileUploader;
use App\Models\Media_files;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Posts;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
class MainController extends Controller
{
    private $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });

        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }

    public function timeline()
    {

        //First 10 posts
        $posts = Posts::where(function ($query) {
            $query->whereJsonContains('users.friends', [$this->user->id])
                ->where('posts.privacy', '!=', 'private')
                ->orWhere('posts.user_id', $this->user->id)



                //if folowing any users, pages, groups and others if not friend listed
                ->orWhere(function ($query3) {
                    $query3->where('posts.privacy', 'public')
                        ->where(function ($query4) {
                            $query4->where('posts.publisher', 'post')
                                ->join('followers', function (JoinClause $join) {
                                    $join->on('posts.publisher_id', '=', 'followers.follow_id')
                                        ->where('followers.user_id', $this->user->id);
                                });
                        })
                        ->orWhere(function ($query5) {
                            $query5->where('posts.publisher', 'profile_picture')
                                ->join('followers', function (JoinClause $join1) {
                                    $join1->on('posts.publisher_id', '=', 'followers.follow_id')
                                        ->where('followers.user_id', $this->user->id);
                                });
                        })
                        ->orWhere(function ($query6) {
                            $query6->where('posts.publisher', 'page')
                                ->join('followers', function (JoinClause $join2) {
                                    $join2->on('posts.publisher_id', '=', 'followers.page_id')
                                        ->where('followers.user_id', $this->user->id);
                                });
                        })
                        ->orWhere(function ($query7) {
                            $query7->where('posts.publisher', 'group')
                                ->join('followers', function (JoinClause $join3) {
                                    $join3->on('posts.publisher_id', '=', 'followers.group_id')
                                        ->where('followers.user_id', $this->user->id);
                                });
                        });
                });
        })
            ->where('posts.status', 'active')
            ->where('posts.report_status', '0')
            ->where('publisher', '!=', 'paid_content') // post type can not be paid content
            ->join('users', 'posts.user_id', '=', 'users.id')

            ->where(function ($query) {
                $query->where('posts.publisher', '!=', 'video_and_shorts')
                    ->orWhere(function ($query2) {
                        $query2->join('group_members', function (JoinClause $join) {
                            $join->on('posts.publisher_id', '=', 'group_members.group_id')
                                ->where('posts.publisher', '=', 'group')
                                ->where('group_members.user_id', '=', $this->user->id);
                        });
                    });
            })

            ->select('posts.*', 'users.name', 'users.photo', 'users.friends', 'posts.created_at as created_at')
            ->take(15)->orderBy('posts.post_id', 'DESC')->get();

        // $page_data['stories'] = $stories;
        $page_data['posts'] = $posts;
        $page_data['view_path'] = 'frontend.main_content.index';
        return view('frontend.index', $page_data);
    }

    public function load_post_by_scrolling(Request $request)
    {

        $posts = Posts::where(function ($query) {
            $query->whereJsonContains('users.friends', [$this->user->id])
                ->where('posts.privacy', '!=', 'private')
                ->orWhere('posts.user_id', $this->user->id)

                //if following any users, pages, groups and others if not friend listed
                ->orWhere(function ($query3) {
                    $query3->where('posts.privacy', 'public')
                        ->where(function ($query4) {
                            $query4->where('posts.publisher', 'post')
                                ->join('followers', function (JoinClause $join) {
                                    $join->on('posts.publisher_id', '=', 'followers.follow_id')
                                        ->where('followers.user_id', $this->user->id);
                                });
                        })
                        ->orWhere(function ($query5) {
                            $query5->where('posts.publisher', 'profile_picture')
                                ->join('followers', function (JoinClause $join1) {
                                    $join1->on('posts.publisher_id', '=', 'followers.follow_id')
                                        ->where('followers.user_id', $this->user->id);
                                });
                        })
                        ->orWhere(function ($query6) {
                            $query6->where('posts.publisher', 'page')
                                ->join('followers', function (JoinClause $join2) {
                                    $join2->on('posts.publisher_id', '=', 'followers.page_id')
                                        ->where('followers.user_id', $this->user->id);
                                });
                        })
                        ->orWhere(function ($query7) {
                            $query7->where('posts.publisher', 'group')
                                ->join('followers', function (JoinClause $join3) {
                                    $join3->on('posts.publisher_id', '=', 'followers.group_id')
                                        ->where('followers.user_id', $this->user->id);
                                });
                        });
                });
        })
            ->where('posts.status', 'active')
            ->where('posts.publisher', 'post')
            ->where('posts.report_status', '0')
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->select('posts.*', 'users.name', 'users.photo', 'users.friends', 'posts.created_at as created_at')
            ->skip($request->offset)->take(3)->orderBy('posts.post_id', 'DESC')->get();

        $page_data['user_info'] = $this->user;
        $page_data['posts'] = $posts;
        $page_data['type'] = 'user_post';
        return view('frontend.main_content.posts', $page_data);
    }

    public function create_post(Request $request)
    {

        //Data validation

        $rules = array('privacy' => ['required', Rule::in(['private', 'public', 'friends'])]);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return json_encode(array('validationError' => $validator->getMessageBag()->toArray()));
        }

        if (is_array($request->multiple_files) && $request->multiple_files[0] != null) {
            //Data validation

            $rules = array('multiple_files.*' => 'mimes:jpeg,png,jpg,gif,svg,mp4,mov,wmv,avi,webm|max:500000');
            $rules = array('multiple_files.*' => 'mimes:mp4,mov,wmv,avi,WEBM,mkv|max:20048');
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $validation_errors = $validator->getMessageBag()->toArray();
                foreach ($validation_errors as $key => $validation_error) {
                    $fileIndex = explode('.', $key);
                    if (array_key_exists('multiple_files.' . $fileIndex[1], $validation_errors)) {
                        $validation_errors['multiple_files'] = $validation_errors['multiple_files.' . $fileIndex[1]];
                    }
                    unset($validation_errors['multiple_files.' . $fileIndex[1]]);
                }

                return json_encode(array('validationError' => $validation_errors));
            }
        }

        $data['user_id'] = $this->user->id;
        $data['privacy'] = $request->privacy;

        if (isset($request->publisher) && !empty($request->publisher)) {
            $data['publisher'] = $request->publisher;
        } else {
            $data['publisher'] = 'post';
        }

        if (isset($request->event_id) && !empty($request->event_id)) {
            $data['publisher_id'] = $request->event_id;
        } elseif (isset($request->page_id) && !empty($request->page_id)) {
            $data['publisher_id'] = $request->page_id;
        } elseif (isset($request->group_id) && !empty($request->group_id)) {
            $data['publisher_id'] = $request->group_id;
        } else {
            $data['publisher_id'] = $this->user->id;
        }
        //post type
        if (isset($request->post_type) && !empty($request->post_type)) {
            $data['post_type'] = $request->post_type;
        } else {
            $data['post_type'] = 'general';
        }

        if (isset($request->tagged_users_id) && is_array($request->tagged_users_id)) {
            $tagged_users = $request->tagged_users_id;
        } else {
            $tagged_users = array();
        }
        $data['tagged_user_ids'] = json_encode($tagged_users);

        if (isset($request->feeling_and_activity_id) && !empty($request->feeling_and_activity_id)) {
            $data['activity_id'] = $request->feeling_and_activity_id;
        } else {
            $data['activity_id'] = 0;
        }

        if (isset($request->address) && !empty($request->address)) {
            $data['location'] = $request->address;
        } else {
            $data['location'] = '';
        }


        if (isset($request->description) && !empty($request->description)) {
            preg_match_all('/#(\w+)/', $request->description, $matchesHashtags); // Extract hashtags
            preg_match_all('/\b(?:https?|ftp):\/\/\S+/', $request->description, $matchesUrls); // Extract URLs
        
            $data['description'] = nl2br($request->description);
        
            
        
            if (!empty($matchesUrls[0])) {
                foreach ($matchesUrls[0] as $url) {
                    $urlLink = '<a href="' . $url . '" class="url-link hashtag-link" target="_blank">' . $url . '</a>';
                    $data['description'] = str_replace($url, $urlLink, $data['description']);
                }
            }

            if (!empty($matchesHashtags[1])) {
                $hashtags = '#' . implode(', #', $matchesHashtags[1]);
                $data['hashtag'] = $hashtags;
        
                foreach ($matchesHashtags[1] as $tag) {
                    $tagLink = '<a href="' . route('search', ['search' => $tag]) . '" class="hashtag-link">#' . $tag . '</a>';
                    $data['description'] = str_replace("#$tag", $tagLink, $data['description']);
                }
            } else {
                $data['hashtag'] = '';
            }
        } else {
            $data['description'] = '';
            $data['hashtag'] = '';
        }
        // Mobile App View Image
        $mobile_app_image = FileUploader::upload($request->mobile_app_image,'public/storage/post/images/');
        $data['mobile_app_image'] = $mobile_app_image;


        $data['status'] = 'active';
        $data['user_reacts'] = json_encode(array());
        $data['shared_user'] = json_encode(array());
        $now = date('Y-m-d H:i:s');
        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        $post_id = Posts::insertGetId($data);

        //add media files
        if (is_array($request->multiple_files) && $request->multiple_files[0] != null) {
            //Data validation

            $rules = array('multiple_files.*' => 'mimes:jpeg,png,jpg,gif,svg,mp4,mov,wmv,avi,webm|max:500000');
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $validation_errors = $validator->getMessageBag()->toArray();
                foreach ($validation_errors as $key => $validation_error) {
                    $fileIndex = explode('.', $key);
                    if (array_key_exists('multiple_files.' . $fileIndex[1], $validation_errors)) {
                        $validation_errors['multiple_files'] = $validation_errors['multiple_files.' . $fileIndex[1]];
                    }
                    unset($validation_errors['multiple_files.' . $fileIndex[1]]);
                }

                return json_encode(array('validationError' => $validation_errors));
            }

            foreach ($request->multiple_files as $key => $media_file) {
                $file_name = random(40);
                $file_extention = strtolower($media_file->getClientOriginalExtension());
                if ($file_extention == 'avi' || $file_extention == 'mp4' || $file_extention == 'webm' || $file_extention == 'mov' || $file_extention == 'wmv' || $file_extention == 'mkv') {
                    $file_name = FileUploader::upload($media_file, 'public/storage/post/videos/' . $file_name . '.' . $file_extention);
                    $file_type = 'video';
                } else {
                    $file_name = FileUploader::upload($media_file, 'public/storage/post/images/' . $file_name . '.' . $file_extention, 1000, null, 300);
                    $file_type = 'image';
                }
                $file_name = $file_name . '.' . $file_extention;

                $media_file_data = array('user_id' => $this->user->id, 'post_id' => $post_id, 'file_name' => $file_name, 'file_type' => $file_type, 'privacy' => $request->privacy);

                if (isset($request->page_id) && !empty($request->page_id)) {
                    $media_file_data['page_id'] = $request->page_id;
                } elseif (isset($request->group_id) && !empty($request->group_id)) {
                    $media_file_data['group_id'] = $request->group_id;
                } else {
                }
                $media_file_data['created_at'] = time();
                $media_file_data['updated_at'] = $media_file_data['created_at'];
                Media_files::create($media_file_data);
            }
        }

        // if ($data['post_type'] == 'live_streaming') {
        //     //Live streaming
        //     $live['publisher'] = $data['publisher'];
        //     $live['publisher_id'] = $post_id;
        //     $live['user_id'] = auth()->user()->id;
        //     $live['details'] = json_encode(['link' => url('/streaming/live/' . $post_id), 'status' => TRUE]);
        //     $live['created_at'] = date('Y-m-d H:i:s', time());
        //     $live['updated_at'] = $live['created_at'];
  
        //     // Live_streamings::insert($live);
        //     $response = array('open_new_tab' => url('/streaming/live/' . $post_id), 'reload' => 0, 'status' => 1, 'function' => 0, 'messageShowOn' => '[name=about]', 'message' => get_phrase('Post has been added to your timeline'));
        // } else {
        //     //Ajax flush message
        //     Session::flash('success_message', get_phrase('Your post has been published'));
        //     $response = array('reload' => 1);
        // }
        // return json_encode($response);
    }

    public function edit_post_form($id)
    {
        $page_data['post'] = Posts::where('post_id', $id)->first();
        return view('frontend.main_content.edit_post_modal', $page_data);
    }

    public function edit_post($id, Request $request)
    {
        //$posts = Posts::where('id', $id)->first();

        $rules = array('privacy' => ['required', Rule::in(['private', 'public', 'friends'])]);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return json_encode(array('validationError' => $validator->getMessageBag()->toArray()));
        }

        if (is_array($request->multiple_files) && $request->multiple_files[0] != null) {
            //Data validation

            $rules = array('multiple_files.*' => 'mimes:jpeg,png,jpg,gif,svg,mp4,mov,wmv,avi,webm|max:20480');
            // $rules = array('multiple_files.*' => 'mimes:mp4,mov,wmv,avi,WEBM,mkv|max:20048');
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $validation_errors = $validator->getMessageBag()->toArray();
                foreach ($validation_errors as $key => $validation_error) {
                    $fileIndex = explode('.', $key);
                    if (array_key_exists('multiple_files.' . $fileIndex[1], $validation_errors)) {
                        $validation_errors['multiple_files'] = $validation_errors['multiple_files.' . $fileIndex[1]];
                    }
                    unset($validation_errors['multiple_files.' . $fileIndex[1]]);
                }

                return json_encode(array('validationError' => $validation_errors));
            }
        }

        $data['privacy'] = $request->privacy;

        if (isset($request->tagged_users_id) && is_array($request->tagged_users_id)) {
            $tagged_users = $request->tagged_users_id;
            $data['tagged_user_ids'] = json_encode($tagged_users);
        }

        if (isset($request->feeling_and_activity_id) && !empty($request->feeling_and_activity_id)) {
            $data['activity_id'] = $request->feeling_and_activity_id;
        }

          //Hashtag   

        //   if (isset($request->description) && !empty($request->description)) {
        //     preg_match_all('/#(\w+)/', $request->description, $matches);
        
        //     $data['description'] =  nl2br($request->description);
        
        //     if (!empty($matches[1])) {
        //         $hashtags = '#' . implode(', #', $matches[1]);
        //         $data['hashtag'] = $hashtags;
        
        //         foreach ($matches[1] as $tag) {
        //             $tagLink = '<a href="' . route('search', ['search' => $tag]) . '" class="hashtag-link">#' . $tag . '</a>';
        //             $data['description'] = Str::replaceFirst("#$tag", $tagLink, $data['description']);
        //         }
        //     } else {
        //         $data['hashtag'] = '';
        //     }
        // } else {
        //     $data['description'] = '';
        //     $data['hashtag'] = '';
        // }


        
        if (isset($request->description) && !empty($request->description)) {
            preg_match_all('/#(\w+)/', $request->description, $matchesHashtags); // Extract hashtags
            preg_match_all('/\b(?:https?|ftp):\/\/\S+/', $request->description, $matchesUrls); // Extract URLs
        
            $data['description'] = nl2br($request->description);
        
            if (!empty($matchesUrls[0])) {
                foreach ($matchesUrls[0] as $url) {
                    $urlLink = '<a href="' . $url . '" class="url-link hashtag-link" target="_blank">' . $url . '</a>';
                    $data['description'] = str_replace($url, $urlLink, $data['description']);
                }
            }

            if (!empty($matchesHashtags[1])) {
                $hashtags = '#' . implode(', #', $matchesHashtags[1]);
                $data['hashtag'] = $hashtags;
        
                foreach ($matchesHashtags[1] as $tag) {
                    $tagLink = '<a href="' . route('search', ['search' => $tag]) . '" class="hashtag-link">#' . $tag . '</a>';
                    $data['description'] = str_replace("#$tag", $tagLink, $data['description']);
                }
            } else {
                $data['hashtag'] = '';
            }
        } else {
            $data['description'] = '';
            $data['hashtag'] = '';
        }

        $data['updated_at'] = time();

        Posts::where('post_id', $id)->update($data);

        //add media files
        if (is_array($request->multiple_files) && $request->multiple_files[0] != null) {
            //Data validation

            $rules = array('multiple_files.*' => 'mimes:jpeg,png,jpg,gif,svg,mp4,mov,wmv,avi,webm|max:20480');
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $validation_errors = $validator->getMessageBag()->toArray();
                foreach ($validation_errors as $key => $validation_error) {
                    $fileIndex = explode('.', $key);
                    if (array_key_exists('multiple_files.' . $fileIndex[1], $validation_errors)) {
                        $validation_errors['multiple_files'] = $validation_errors['multiple_files.' . $fileIndex[1]];
                    }
                    unset($validation_errors['multiple_files.' . $fileIndex[1]]);
                }

                return json_encode(array('validationError' => $validation_errors));
            }

            foreach ($request->multiple_files as $key => $media_file) {
                $file_name = random(40);
                $file_extention = strtolower($media_file->getClientOriginalExtension());
                if ($file_extention == 'avi' || $file_extention == 'mp4' || $file_extention == 'webm' || $file_extention == 'mov' || $file_extention == 'wmv' || $file_extention == 'mkv') {
                    $media_file->move('public/storage/post/videos/', $file_name . '.' . $file_extention);
                    $file_type = 'video';
                } else {
                    FileUploader::upload($media_file, 'public/storage/post/images/' . $file_name . '.' . $file_extention, 1000, null, 300);
                    $file_type = 'image';
                }
                $file_name = $file_name . '.' . $file_extention;

                $media_file_data = array('user_id' => Auth::user()->id, 'post_id' => $id, 'file_name' => $file_name, 'file_type' => $file_type, 'privacy' => $request->privacy);

                if (isset($request->page_id) && !empty($request->page_id)) {
                    $media_file_data['page_id'] = $request->page_id;
                } elseif (isset($request->group_id) && !empty($request->group_id)) {
                    $media_file_data['group_id'] = $request->group_id;
                } else {
                }
                $media_file_data['created_at'] = time();
                $media_file_data['updated_at'] = $media_file_data['created_at'];
                Media_files::create($media_file_data);
            }
        }

        //Ajax flush message
        Session::flash('success_message', get_phrase('Your post has been updated'));
        $response = array('reload' => 1);
        return json_encode($response);
    }

    public function my_react(Request $request)
    {
        $form_data = $request->all();

        if ($form_data['type'] == 'post') {
            $post_data = Posts::where('post_id', $form_data['post_id'])->get()->first();

            $all_reacts = json_decode($post_data['user_reacts'], true);

            if ($form_data['request_type'] == 'update') {
                $all_reacts[$this->user->id] = $form_data['react'];
            }

            if ($form_data['request_type'] == 'toggle') {
                if (array_key_exists($this->user->id, $all_reacts)) {
                    unset($all_reacts[$this->user->id]);
                } else {
                    $all_reacts[$this->user->id] = 'like';
                }
            }

            $data['user_reacts'] = json_encode($all_reacts);
            Posts::where('post_id', $form_data['post_id'])->update($data);

            $page_data['user_reacts'] = $all_reacts;
            $page_data['user_info'] = $this->user;
            $page_data['ajax_call'] = true;
            $page_data['my_react'] = true;
            $page_data['post_react'] = true;

            if ($form_data['response_type'] == 'number') {
                return count($all_reacts);
            } else {
                return view('frontend.main_content.post_reacts', $page_data);
            }
        }
    }
    public function my_comment_react(Request $request)
    {
        $form_data = $request->all();

        $comment_data = Comments::where('comment_id', $form_data['comment_id'])->get()->first();

        $all_reacts = json_decode($comment_data['user_reacts'], true);

        if ($form_data['request_type'] == 'update') {
            $all_reacts[$this->user->id] = $form_data['react'];
        }

        if ($form_data['request_type'] == 'toggle') {
            if (array_key_exists($this->user->id, $all_reacts)) {
                unset($all_reacts[$this->user->id]);
            } else {
                $all_reacts[$this->user->id] = 'like';
            }
        }

        $data['user_reacts'] = json_encode($all_reacts);
        Comments::where('comment_id', $form_data['comment_id'])->update($data);

        $page_data['user_comment_reacts'] = $all_reacts;
        $page_data['user_info'] = $this->user;
        $page_data['ajax_call'] = true;
        $page_data['my_react'] = true;
        $page_data['comment_react'] = true;
        return view('frontend.main_content.comment_reacts', $page_data);
    }

    public function load_post_comments(Request $request)
    {
        $post = Posts::where('posts.status', 'active')
            ->where('posts.post_id', $request->post_id)
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->select('posts.*', 'users.name', 'users.photo', 'users.friends', 'posts.created_at as created_at')->get()->first();

        $comments = DB::table('comments')
            ->join('users', 'comments.user_id', '=', 'users.id')
            ->where('comments.is_type', $request->type)
            ->where('comments.id_of_type', $request->post_id)
            ->where('comments.parent_id', $request->parent_id)
            ->select('comments.*', 'users.name', 'users.photo')
            ->orderBy('comment_id', 'DESC')->skip($request->total_loaded_comments)->take(3)->get();

        $page_data['post'] = $post;
        $page_data['type'] = $request->type;
        $page_data['post_id'] = $request->post_id;
        if ($request->parent_id == 0) {
            $page_data['comments'] = $comments;
            return view('frontend.main_content.comments', $page_data);
        } else {
            $page_data['child_comments'] = $comments;
            return view('frontend.main_content.child_comments', $page_data);
        }
    }
    public function post_comment(Request $request)
    {
        $form_data = $request->all();

        $data['description'] = $form_data['description'];

        if ($form_data['comment_id'] > 0) {
            $data['updated_at'] = time();
            Comments::where('comment_id', $form_data['comment_id'])->where('user_id', $this->user->id)->update($data);
            $comment_id = $form_data['comment_id'];
        } else {
            $data['parent_id'] = $form_data['parent_id'];
            $data['user_id'] = $this->user->id;
            $data['is_type'] = $form_data['type'];
            $data['id_of_type'] = $form_data['post_id'];
            $data['user_reacts'] = json_encode(array());
            $data['created_at'] = time();
            $data['updated_at'] = $data['created_at'];
            $comment_id = Comments::insertGetId($data);
        }

        $post = Posts::where('posts.post_id', $form_data['post_id'])
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->select('posts.*', 'users.name', 'users.photo', 'users.friends', 'posts.created_at as created_at')->get()->first();

        $comments = DB::table('comments')
            ->join('users', 'comments.user_id', '=', 'users.id')
            ->where('comments.is_type', $form_data['type'])
            ->where('comments.comment_id', $comment_id)->get();

        $page_data['post'] = $post;
        $page_data['type'] = $form_data['type'];
        $page_data['post_id'] = $form_data['post_id'];

        $total_comments = Comments::where('is_type', $form_data['type'])->where('id_of_type', $form_data['post_id'])->get()->count();

        if ($request->parent_id == 0) {
            $page_data['comments'] = $comments;
            return view('frontend.main_content.comments', $page_data);
        } else {
            $page_data['child_comments'] = $comments;
            return view('frontend.main_content.child_comments', $page_data);
        }
    }

    public function preview_post(Request $request)
    {

        //Previw post
        $posts = Posts::where(function ($query) {
            $query->where('posts.privacy', '!=', 'private')
                ->orWhere('posts.user_id', $this->user->id);
        })
            ->where('posts.post_id', $request->post_id)
            ->where('posts.status', 'active')
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->select('posts.*', 'users.name', 'users.photo', 'users.friends', 'posts.created_at as created_at')
            ->take(1)->orderBy('posts.post_id', 'DESC')->get();

        $page_data['posts'] = $posts;
        $page_data['file_name'] = $request->file_name;
        $page_data['user_info'] = $this->user;
        return view('frontend.main_content.preview_post', $page_data);
    }

    public function post_comment_count(Request $request)
    {
        $form_data = $request->all();
        return $total_child_comments = Comments::where('is_type', $form_data['type'])->where('id_of_type', $form_data['post_id'])->get()->count();
    }

    public function single_post($id, $type = null)
    {

        $post = Posts::where('post_id', $id)->first();
        if (!empty($post)) {
            $page_data['post'] = $post;
            $page_data['user_info'] = Auth::user();
            $page_data['type'] = 'user_post';
            $page_data['image_id'] = $type;
            $page_data['view_path'] = 'frontend.main_content.single-post';
     
            if (isset($_GET['shared'])) {
                return view('frontend.main_content.custom_shared_view', $page_data);
            } else {
                return view('frontend.index', $page_data);
            }
        } else {

            if (isset($_GET['shared'])) {
                $page_data['post'] = '';
                return view('frontend.main_content.custom_shared_view', $page_data);
            } else {
                $page_data['post'] = '';
                $page_data['view_path'] = 'frontend.main_content.custom_shared_view';
                return view('frontend.index', $page_data);
            }
        }
    }
}
