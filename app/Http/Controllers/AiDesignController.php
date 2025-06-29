<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiDesignController extends Controller
{

    public function generate(Request $request, Project $project)
    {
        $validated = $request->validate([
            'business_name' => 'required|string',
            'business_type' => 'required|string',
            'target_audience' => 'required|string',
            'style_adjectives' => 'required|string',
            'color_palette' => 'required|array',
        ]);

        try {
            // 1. Retrieve the OpenAI API key securely
            $apiKey = config('services.openai.key');
            if (!$apiKey) {
                throw new \Exception('OpenAI API key is not configured.');
            }

            // 2. Construct the detailed prompt for DALL-E 3
            $colorString = implode(', ', $validated['color_palette']);
            $prompt = "A photorealistic, ultra high-resolution 8k website mockup for a company called '{$validated['business_name']}'. "
                    . "The business is a {$validated['business_type']}. "
                    . "The website's target audience is {$validated['target_audience']}. "
                    . "The visual style must be {$validated['style_adjectives']}. "
                    . "The main brand colors to feature in the design are {$colorString}. "
                    . "The design must show the entire webpage layout, featuring a clean and modern hero section, clear navigation, a compelling call-to-action, and relevant placeholder imagery.";

            // 3. Call the OpenAI DALL-E 3 API using Laravel's HTTP Client
            $response = \Illuminate\Support\Facades\Http::withToken($apiKey)->post('https://api.openai.com/v1/images/generations', [
                'model' => 'dall-e-3',
                'prompt' => $prompt,
                'n' => 1,
                'size' => '1024x1024', // DALL-E 3 supports 1024x1024, 1792x1024, or 1024x1792
                'quality' => 'standard', // or 'hd' for higher detail
            ]);

            // 4. Check for a successful response and extract the image URL
            if ($response->successful() && isset($response->json()['data'][0]['url'])) {
                $imageUrl = $response->json()['data'][0]['url'];

                // 5. Return the image URL in a JSON response
                return response()->json([
                    'imageUrl' => $imageUrl
                ]);
            } else {
                // Throw an exception if the API call failed
                throw new \Exception('Failed to generate image with OpenAI API. Response: ' . $response->body());
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('OpenAI generation failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Design generation failed. Please check your prompt or API key and try again.'
            ], 500);
        }
    }




}