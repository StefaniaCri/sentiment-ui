<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SentimentController extends Controller
{
    //
    public function fetchTitle(Request $request)
    {
        $link = $request->input('link');

        if (!$link) {
            return response()->json(['error' => 'Link-ul este invalid.'], 400);
        }

        try {
            $flaskEndpoint = 'http://192.168.101.142:5000/get-title';
            $response = Http::post($flaskEndpoint, [
                'url' => $link
            ]);

            if ($response->failed()) {
                return response()->json(['error' => 'Nu s-a putut obține titlul de la serverul Flask.'], 400);
            }

            $data = $response->json();

            if (isset($data['title'])) {
                return response()->json(['title' => $data['title']]);
            } else {
                return response()->json(['error' => $data], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'A apărut o eroare: ' . $e->getMessage()], 500);
        }
    }

    public function analyze(Request $request)
    {
        $title = $request->input('title');
        $language = $request->input('language');
        $model = $request->input('model');
        $link = $request->input('link');
        $preprocess = $request->input('preprocess')?1:0;
        $response = Http::post('http://192.168.101.142:5000/predict', [
            'text' => $title,
            'language'=> $language,
            'model'=> $model,
            'preprocess'=> $preprocess
        ]);

        $result = $response->json();
        $label = $result[0]['label'] ?? 'N/A';
        $score = $result[0]['score'] ?? 0;

        return view('welcome', [
            'label' => $label,
            'score' => $score,
            'title' => $title,
            'link'=>$link,
            'language' => $language,
            'model' => $model,
            'preprocess' => $preprocess
        ]);
    }
}
