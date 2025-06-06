<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Posts;

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

}
