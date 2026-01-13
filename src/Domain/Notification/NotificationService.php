<?php

namespace AperturePro\Domain\Notification;

use AperturePro\Domain\Logs\Logger;

class NotificationService
{
    /**
     * Send an email to the client when the ZIP file is ready.
     *
     * @param int $project_id
     * @param string $zipUrl
     * @return bool
     */
    public function sendClientZipReadyEmail(int $project_id, string $zipUrl): bool
    {
        $email = $this->getClientEmail($project_id);
        if (empty($email)) {
            Logger::info("No client email found for project {$project_id}. Skipping notification.", [], $project_id);
            return false;
        }

        $project_title = get_the_title($project_id);
        $subject = "Your photos are ready for download - {$project_title}";
        $message = "Hello,\n\nYour photos for '{$project_title}' are now ready for download.\n\n";
        $message .= "You can download the ZIP file using the link below:\n";
        $message .= $zipUrl . "\n\n";
        $message .= "Best regards,\nThe Team";

        return $this->sendEmail($email, $subject, $message);
    }

    /**
     * Generic method to send an email.
     *
     * @param string $to
     * @param string $subject
     * @param string $message
     * @return bool
     */
    public function sendEmail(string $to, string $subject, string $message): bool
    {
        $headers = ['Content-Type: text/plain; charset=UTF-8'];
        $result = wp_mail($to, $subject, $message, $headers);

        if ($result) {
            Logger::info("Email sent to {$to}", ['subject' => $subject]);
        } else {
            Logger::error("Failed to send email to {$to}", ['subject' => $subject]);
        }

        return $result;
    }

    /**
     * Get the client email for a project.
     *
     * @param int $project_id
     * @return string|null
     */
    public function getClientEmail(int $project_id): ?string
    {
        $email = get_post_meta($project_id, '_ap_client_email', true);

        if (empty($email) || !is_email($email)) {
            return null;
        }

        return $email;
    }

    /**
     * Send a batch of emails.
     *
     * @param array  $recipients List of email addresses.
     * @param string $subject    Email subject.
     * @param string $message    Email message.
     * @return array Array of failed recipients.
     */
    public function sendBatch(array $recipients, string $subject, string $message): array
    {
        $failed = [];
        foreach ($recipients as $to) {
            if (!$this->sendEmail($to, $subject, $message)) {
                $failed[] = $to;
            }
        }
        return $failed;
    }
}
