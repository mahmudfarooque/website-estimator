<?php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProjectSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    // Make the project data available to the email template
    public Project $project;

    /**
     * Create a new message instance.
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Project Estimate Submitted: ' . $this->project->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // This tells Laravel to use a view file for the email's content
        return new Content(
            view: 'emails.projects.submitted',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
