<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class FaqController extends Controller
{
    // GET /api/faqs → list for quick‑reply buttons
    public function index(): JsonResponse
    {
        try {
            $faqs = Faq::select('id', 'question')->orderBy('id')->get();
            
            Log::info('FAQ list requested', ['count' => $faqs->count()]);
            
            return response()->json($faqs);
        } catch (\Exception $e) {
            Log::error('FAQ list failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to load FAQs'], 500);
        }
    }
    
    // POST /api/faqs/chat { message:"…" } → best answer
    public function chat(Request $request): JsonResponse
    {
        try {
            $q = trim($request->input('message', ''));
            
            if (!$q) {
                return response()->json(['answer' => 'Please type a question.']);
            }

            Log::info('Chat query received', ['query' => $q]);

            // Simple search (LIKE) - case insensitive
            $match = Faq::whereRaw('LOWER(question) LIKE ?', ['%' . strtolower($q) . '%'])
                ->orWhereRaw('LOWER(answer) LIKE ?', ['%' . strtolower($q) . '%'])
                ->first();

            $answer = $match 
                ? $match->answer 
                : "Sorry, I don't have an answer for that yet. You can try rephrasing your question or contact our support team for assistance.";

            Log::info('Chat response', ['found_match' => (bool)$match]);

            return response()->json(['answer' => $answer]);
        } catch (\Exception $e) {
            Log::error('Chat query failed', ['error' => $e->getMessage()]);
            return response()->json([
                'answer' => 'I apologize, but I encountered an error. Please try again later.'
            ], 500);
        }
    }
}