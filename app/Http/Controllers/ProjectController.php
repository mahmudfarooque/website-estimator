<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Mail\ProjectSubmitted;
use Illuminate\Support\Facades\Mail;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the user's projects.
     */
    public function index(Request $request)
    {
        $projects = $request->user()
                            ->projects()
                            ->with('packages')
                            ->latest()
                            ->get();

        return Inertia::render('Projects/Index', [
            'projects' => $projects
        ]);
    }

    public function checkDomain(Request $request)
    {
        $validated = $request->validate(['domain' => 'required|string|min:4']);

        $domainToCheck = preg_replace('/^(https?:\/\/)?(www\.)?/i', '', $validated['domain']);
        $domainToCheck = explode('/', $domainToCheck, 2)[0];

        $apiKey = config('services.apininjas.key');
        if (!$apiKey) {
            return response()->json(['error' => 'Domain check API is not configured.'], 500);
        }

        try {
            $response = Http::withHeaders(['X-Api-Key' => $apiKey])
                            ->get('https://api.api-ninjas.com/v1/whois', ['domain' => $domainToCheck]);

            if ($response->successful() && !empty($response->json())) {
                return response()->json(['is_registered' => true]);
            }

            return response()->json(['is_registered' => false]);

        } catch (\Exception $e) {
            Log::error('API Ninjas domain check failed: ' . $e->getMessage());
            return response()->json(['is_registered' => false]);
        }
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        return Inertia::render('Projects/Create', [
            'websitePackages' => Package::where('type', 'website')->where('is_active', true)->get(),
            'hostingPackages' => Package::where('type', 'hosting')->where('is_active', true)->get(),
            'emailPackages' => Package::where('type', 'email')->where('is_active', true)->get(),
        ]);
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'projectName' => 'required|string|max:255',
            'website_package_id' => 'required|exists:packages,id',
            'hosting_package_id' => 'required|exists:packages,id',
            'email_package_id' => 'required|exists:packages,id',
        ]);

        $project = $request->user()->projects()->create([
            'name' => $validated['projectName'],
            'status' => 'design',
            'current_step' => 2,
        ]);

        $project->packages()->attach([
            $validated['website_package_id'],
            $validated['hosting_package_id'],
            $validated['email_package_id'],
        ]);

        return redirect()->route('projects.design', $project)->with('success', 'Project created successfully! Ready for the design phase.');
    }

    /**
     * Show the form for designing a project.
     */
    public function showDesignForm(Project $project)
    {
        $this->authorize('update', $project);

        $project->load(['uploads']);
        $project->uploads->each(function ($upload) {
            $upload->public_url = Storage::url($upload->file_path);
        });

        return Inertia::render('Projects/Design', [
            'project' => $project
        ]);
    }

    /**
     * Display the project summary and invoice details.
     */
    public function summary(Project $project)
    {
        $this->authorize('update', $project);

        $project->load(['packages', 'uploads']);
        $project->uploads->each(function ($upload) {
            $upload->public_url = Storage::url($upload->file_path);
        });

        return Inertia::render('Projects/Summary', [
            'project' => $project
        ]);
    }

    /**
     * Submit the project order and send a notification email.
     */
    public function submitOrder(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        // Load all the necessary data for the email
        $project->load(['user', 'packages', 'uploads']);

        // 1. Send the email notification
        Mail::to('admin@yourapp.com')->send(new ProjectSubmitted($project));
        Mail::to($project->user)->send(new ProjectSubmitted($project));

        // 2. Update the project's status
        $project->status = 'submitted';
        $project->save();

        // 3. Redirect back with a success message
        return redirect()->route('projects.summary', $project)->with('success', 'Your order has been submitted successfully!');
    }
}
