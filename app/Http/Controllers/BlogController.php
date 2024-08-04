<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->filter;
        $authorFilter = $request->authorFilter;

        $blogs = Blog::when($filter !== "All", function ($query) use ($filter) {
            return $query->where("category_id", $filter);
        })->when($authorFilter !== "All", function ($query) use ($authorFilter) {
            return $query->where("user_id", $authorFilter);
        })->orderBy('created_at', "desc")->with([
                    "category",
                    "user"
                ])->paginate(2);
        return response()->json([
            "blogs" => $blogs,
        ]);
    }

    public function get_blog_by_id($id)
    {
        $blog = Blog::where("id", $id)->first();
        return response()->json([
            "status" => "success",
            "blog" => $blog
        ]);
    }

    public function create(Request $request)
    {
        $credentials = Validator::make($request->all(), [
            "title" => ["required", 'string', "min:10"],
            "description" => ["required", "string", "min:20"],
            "category" => ["required", "string"],
            "image" => 'file|mimes:png,jpeg,jpg'
        ]);

        if ($credentials->fails()) {
            return response()->json([
                "status" => "failed",
                "message" => $credentials->errors()
            ], 400);
        }
        $imageName = "";

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . "_" . $image->getClientOriginalName();
            if (!Storage::exists('public/uploads')) {
                Storage::makeDirectory('public/uploads');
            }
            $image->storeAs('public/uploads', $imageName);
        }

        $user = auth()->user()->id;
        $blog = Blog::create([
            "title" => $request->title,
            "description" => $request->description,
            "image" => $imageName,
            "category_id" => $request->category,
            "user_id" => $user
        ]);

        return response()->json([
            "status" => "success",
            "blog" => $blog
        ]);
    }

    public function update_blog(Request $request, $id)
    {
        $valid = Validator::make($request->all(), [
            "title" => ["required", 'string', "min:10"],
            "description" => ["required", "string", "min:20"],
            "category" => ["required", "string"],
            "image" => 'nullable|file|mimes:png,jpeg,jpg',
        ]);

        if ($valid->fails()) {
            return response()->json([
                "status" => 'failed',
                "message" => $valid->errors()
            ], 400);
        }
        $blog = Blog::where('id', $id)->first();

        if ($blog) {
            $oldImage = $blog->image;
            $imageName = $oldImage;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . "_" . $image->getClientOriginalName();
                if (!Storage::exists("public/uploads")) {
                    Storage::makeDirectory("public/uploads");
                }

                $image->storeAs('public/uploads', $imageName);
                $oldImagePath = public_path("storage/uploads/{$oldImage}");
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }
            $blog->title = $request->input("title");
            $blog->description = $request->input("description");
            $blog->category_id = $request->input("category");
            $blog->image = $imageName;
            $blog->save();

            return response()->json([
                "status" => 'failed',
                "message" => "succefully updated."
            ], 200);
        }

        return response()->json([
            "status" => "failed",
            "message" => "No record found with the given id."
        ], 404);
    }
    public function update_view_count($id)
    {
        $blog = Blog::where("id", $id)->first();
        if ($blog) {
            $blog->view_count = $blog->view_count + 1;
            $blog->save();
            return response()->json([
                "status" => "success"
            ], 200);
        }

        return response()->json([
            'status' => "failed",
            "message " => "failed to update view count."
        ], 400);
    }

    public function delete($id)
    {
        $data = Blog::where("id", $id)->first();
        $imagePath = public_path("storage/uploads/{$data->image}");
        if ($imagePath) {
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }
        Blog::where("id", $id)->delete();
        return response()->json([
            'status' => "success",
            'message' => "Successfully deleted."
        ]);
    }
}