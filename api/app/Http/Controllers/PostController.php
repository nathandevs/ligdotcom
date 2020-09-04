<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Posts;
use App\Comment;

class PostController extends Controller
{
    public function index(Request $request)
    {
        return Posts::paginate($request->get('page'));
    }

    public function show($title)
    {
       
        $post = Posts::where('title', '=', $title)->get();
       
        if(!empty($post))
        {
            return response()->json($post, 200);
        }else{
            return response()->json(['status' => 'Record not found']);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'content' => 'required',
            'title' => 'required'
        ]);

        $user = $this->getBearer($request);
        
        if($user)
        {
            $request->request->add(['user_id' => $user->id, 'slug'=> $request->input('title')]);            
            $post = Posts::create($request->all());
            return response()->json($post, 200);
        }else{
            return response()->json(['message' => 'Token not valid', 'error'=>true]);
        }
       
    }

    public function patch(Request $request)
    {
        
        $user = $this->getBearer($request);

        if($user)
        {
            $post = Posts::where(['title' => $request->input('title'), 'user_id' => $user->id])->first();
            return response()->json($post, 200);
        }else{
            return response()->json(['message' => 'Token not valid', 'error'=>true]);
        }
       
    }

    public function delete($title)
    {
        $title = str_replace('-', ' ', $title);
        $post = Posts::where('title', '=', $title)->first();
       
        if(!empty($post))
        {
            $post->delete();
            return response()->json(['status' => 'Record deleted successfully']);
        }else{
            return response()->json(['status' => 'Record not found']);
        }
    }


    public function postComment(Request $request)
    {
        $this->validate($request, [
            'body' => 'required',
        ]);

        $user = $this->getBearer($request);
        $title = str_replace('-', ' ', $request->segment(3));
        $post = Posts::where('title', '=', $title)->first();
        $request->request->add(['parent_id' => $post->id, 'creator_id'=> $post->user_id, 'commentable_type'=> '"App\\Post"']); 

        if($user)
        {
            
            $comment = Comment::create($request->all());
            $comment->{'commnetable_id'} = $comment->id;
            return response()->json($comment, 200);
        }else{
            return response()->json(['message' => 'Token not valid', 'error'=>true]);
        }
    }


    public function patchComment(Request $request)
    {
        $this->validate($request, [
            'body' => 'required',
        ]);

        $user = $this->getBearer($request);

        if($user)
        {  
           
            $comment = DB::table('posts')
                    ->leftjoin('comments', 'comments.parent_id', '=', 'posts.id')
                    ->select('comments.id','posts.title', 'comments.*')
                    ->where(['posts.title' => $request->input('body'), 'comments.id' => $request->segment(5)])
                    ->get();
            return response()->json($comment, 200);
        }else{
            return response()->json(['message' => 'Token not valid', 'error'=>true]);
        }
    }


    public function deleteComment(Request $request)
    {
        $user = $this->getBearer($request);

        if($user)
        {  
            $comment = Comment::where('id', '=', $request->segment(5))->first();
            $comment->delete();
            return response()->json(['status' => 'Record deleted successfully']);
        }else{
            return response()->json(['message' => 'Record not found']);
        }
    }

    private function getBearer(Request $request)
    {
        $header = $request->header('authorization', '');
        if (Str::startsWith($header, 'Bearer ')) {
            $token = Str::substr($header, 7);
            $user = User::where('api_token', '=', $token)->first();
            if(!isset($user->id) && empty($user->id))
            {
                return FALSE;
            }else{
                return $user;
            }
       }
        return FALSE;
       
    }
}
