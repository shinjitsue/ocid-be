<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
     // GET /api/faqs  → list for quick‑reply buttons
    public function index()
    {
        return Faq::select('id','question')->orderBy('id')->get();
    }
    
    // POST /api/faqs/chat  { message:"…" } → best answer
    public function chat(Request $request)
    {
        $q = trim($request->input('message',''));
        if (!$q) return response()->json(['answer' => '‣ Please type a question.']);

        // simple search (LIKE).
        $match = Faq::where('question','LIKE',"%{$q}%")->first();

        return response()->json([
            'answer' => $match->answer ?? "Sorry, I don’t have an answer yet. Try re‑phrasing or visit the help desk."
        ]);
    }
}
