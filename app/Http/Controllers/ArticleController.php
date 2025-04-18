<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Order;
use App\Models\Setup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if(!$this->checkRole(['developer', 'owner', 'manager', 'admin', 'accountant', 'guest']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.'); 
        };

        $articles = Article::all();

        foreach ($articles as $article) {
            $orders = Order::all();

            if ($orders) {
                foreach ($orders as $order) {
                    $articlesArray = json_decode($order->ordered_articles, true);
                }
            }

            $article["rates_array"] = json_decode($article->rates_array, true);
            $article['date'] = date('d-M-Y, D', strtotime($article['date']));
            $article['sales_rate'] = number_format($article['sales_rate'], 2, '.', ',');
        }

        $authLayout = $this->getAuthLayout($request->route()->getName());

        return view('articles.index', compact('articles', 'authLayout'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if(!$this->checkRole(['developer', 'owner', 'admin', 'accountant']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        }

        $lastRecord = Article::orderBy('id', 'desc')->first();

        if ($lastRecord) {
            $lastRecord->rates_array = json_decode($lastRecord->rates_array, true);
            $lastRecord->total_rate = 0;
        } else {
            $lastRecord = '';
        } 

        $categories = Setup::where('type', 'article_category')->pluck('title');
        if ($categories->isEmpty()) {
            $categories = collect();
        }
        $sizes = Setup::where('type', 'article_size')->pluck('title');
        if ($sizes->isEmpty()) {
            $sizes = collect();
        }
        $seasons = Setup::where('type', 'article_seasons')->pluck('title');
        if ($seasons->isEmpty()) {
            $seasons = collect();
        }

        $articles = Article::all();

        return view('articles.create', compact('lastRecord', 'categories', 'sizes', 'seasons', 'articles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(!$this->checkRole(['developer', 'owner', 'admin', 'accountant']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };

        // return $request;

        $validator = Validator::make($request->all(), [
            'article_no' => 'required|integer|unique:articles,article_no',
            'date' => 'required|date',
            'category' => 'required|string',
            'size' => 'required|string',
            'season' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'extra_pcs' => 'required|integer|min:0',
            'fabric_type' => 'required|string',
            'rates_array' => 'nullable|string',
            "sales_rate" => 'required|numeric|min:0',
            'image_upload' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // Prepare data for saving
        $data = $request->all();

        // Handle the image upload if present
        if ($request->hasFile('image_upload')) {
            $file = $request->file('image_upload');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/images', $fileName, 'public'); // Store in public disk

            $data['image'] = $fileName; // Save the file path in the database
        }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Article::create($data);

        foreach (['category' => 'article_category', 'size' => 'article_size', 'season' => 'article_seasons'] as $field => $type) {
            if (!empty($data[$field])) {
                Setup::firstOrCreate([
                    'type' => $type,
                    'title' => $data[$field],
                ]);
            }
        }

        return redirect()->route('articles.create')->with('success', 'Article added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        if(!$this->checkRole(['developer', 'owner', 'admin']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };
        
        if ($article->ordered_quantity != 0) {
            return redirect(route('articles.index'))->with("error", "This article can't be edited.");
        }

        $categories = Setup::where('type', 'article_category')->pluck('title');
        if ($categories->isEmpty()) {
            $categories = collect();
        }
        $sizes = Setup::where('type', 'article_size')->pluck('title');
        if ($sizes->isEmpty()) {
            $sizes = collect();
        }
        $seasons = Setup::where('type', 'article_seasons')->pluck('title');
        if ($seasons->isEmpty()) {
            $seasons = collect();
        }

        $article->rates_array = json_decode($article->rates_array, true);

        return view('articles.edit', compact('article' , 'categories', 'sizes', 'seasons'));
        // return $article;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        if(!$this->checkRole(['developer', 'owner', 'admin']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };

        $validator = Validator::make($request->all(), [
            'article_no' => 'required|integer|unique:articles,article_no,' . $article->id,
            'date' => 'required|date',
            'category' => 'required|string',
            'size' => 'required|string',
            'season' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'extra_pcs' => 'required|integer|min:0',
            'fabric_type' => 'required|string',
            'rates_array' => 'nullable|string',
            "sales_rate" => 'required|numeric|min:0',
            'image_upload' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // Prepare data for saving
        $data = $request->all();

        // Handle the image upload if present
        if ($request->hasFile('image_upload')) {
            if ($article->image && Storage::disk('public')->exists('uploads/images/' . $article->image)) {
                Storage::disk('public')->delete('uploads/images/' . $article->image);
            }

            $file = $request->file('image_upload');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/images', $fileName, 'public'); // Store in public disk

            $data['image'] = $fileName; // Save the file path in the database
        }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $article->update($data);

        foreach (['category' => 'article_category', 'size' => 'article_size', 'season' => 'article_seasons'] as $field => $type) {
            if (!empty($data[$field])) {
                Setup::firstOrCreate([
                    'type' => $type,
                    'title' => $data[$field],
                ]);
            }
        }

        return redirect()->route('articles.index')->with('success', 'Article edit successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        //
    }
    public function updateImage(Request $request)
    {
        $article = Article::where('id', $request->article_id)->first();

        if(!$this->checkRole(['developer', 'owner', 'admin', 'accountant']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };
        
        // Validate input first
        $validator = Validator::make($request->all(), [
            'article_id' => 'integer|required|exists:articles,id',
            'image_upload' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        // Prepare data for saving
        $data = [];
    
        // Handle the image upload if present
        if ($request->hasFile('image_upload')) {
            if ($article->image && Storage::disk('public')->exists('uploads/images/' . $article->image)) {
                Storage::disk('public')->delete('uploads/images/' . $article->image);
            }

            $file = $request->file('image_upload');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/images', $fileName, 'public'); // Store in public disk

            $data['image'] = $fileName; // Save the file path in the database
        }
    
        // Update only if image is set
        if (!empty($data['image'])) {
            $article->update(['image' => $data['image']]);
            return redirect()->route('articles.index')->with('success', 'Image added successfully');
        } else {
            return redirect()->back()->with('error', 'Please upload an image');
        }
    }
    public function addRate(Request $request)
    {
        if(!$this->checkRole(['developer', 'owner', 'admin', 'accountant']))
        {
            return redirect(route('home'))->with('error', 'You do not have permission to access this page.');
        };
        
        // Validate input first
        $validator = Validator::make($request->all(), [
            'article_id' => 'required|integer|exists:articles,id',
            "sales_rate" => 'required|numeric|min:0',
            "pcs_per_packet" => 'required|numeric|min:0',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        $data = $request->all();
    
        Article::where('id', $request->article_id)->update(['sales_rate' => $data['sales_rate'], 'rates_array' => $data['rates_array'], 'pcs_per_packet' => $data['pcs_per_packet']]);

        return redirect()->route('articles.index')->with('success', 'Rate added successfully');
    }
}
