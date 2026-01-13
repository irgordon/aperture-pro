<?php

namespace AperturePro\Storage;

use AperturePro\Core\Settings;

class ImageKitAdapter implements StorageAdapterInterface
{
    protected array $config;

    public function __construct()
    {
        $this->config = Settings::getImageKitConfig();
    }

    public function store(string $localPath, string $targetPath): string
    {
        if (empty($this->config['privateKey'])) {
            throw new \Exception('ImageKit private key is not configured.');
        }

        if (!file_exists($localPath)) {
            throw new \Exception("File not found at $localPath");
        }

        $fileContent = file_get_contents($localPath);
        if ($fileContent === false) {
            throw new \Exception("Failed to read file at $localPath");
        }

        // Use base64 for reliable transmission via WP remote post
        $base64File = base64_encode($fileContent);
        $fileName = basename($targetPath);
        $folder = dirname($targetPath);

        // Normalize folder
        if ($folder === '.' || $folder === '\\') {
            $folder = '/';
        }

        // Construct multipart boundary and body
        $boundary = wp_generate_password(24);
        $headers = [
            'Authorization' => 'Basic ' . base64_encode($this->config['privateKey'] . ':'),
            'Content-Type' => 'multipart/form-data; boundary=' . $boundary,
        ];

        $payload = '';

        // file
        $payload .= "--" . $boundary . "\r\n";
        $payload .= 'Content-Disposition: form-data; name="file"' . "\r\n\r\n";
        $payload .= $base64File . "\r\n";

        // fileName
        $payload .= "--" . $boundary . "\r\n";
        $payload .= 'Content-Disposition: form-data; name="fileName"' . "\r\n\r\n";
        $payload .= $fileName . "\r\n";

        // useUniqueFileName
        $payload .= "--" . $boundary . "\r\n";
        $payload .= 'Content-Disposition: form-data; name="useUniqueFileName"' . "\r\n\r\n";
        $payload .= 'false' . "\r\n";

        // folder
        if ($folder && $folder !== '/') {
            $payload .= "--" . $boundary . "\r\n";
            $payload .= 'Content-Disposition: form-data; name="folder"' . "\r\n\r\n";
            $payload .= $folder . "\r\n";
        }

        $payload .= "--" . $boundary . "--\r\n";

        $response = wp_remote_post('https://upload.imagekit.io/api/v1/files/upload', [
            'headers' => $headers,
            'body' => $payload,
            'timeout' => 60,
        ]);

        if (is_wp_error($response)) {
            throw new \Exception('ImageKit Upload Failed: ' . $response->get_error_message());
        }

        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!is_array($data)) {
            $data = [];
        }

        if ($code < 200 || $code >= 300) {
            $msg = $data['message'] ?? 'Unknown error';
            throw new \Exception("ImageKit Upload Error ($code): $msg");
        }

        if (empty($data['filePath'])) {
            // Fallback if filePath not present (unlikely)
            if (!empty($data['url'])) {
                return $data['url'];
            }
            throw new \Exception("ImageKit Upload Error: No filePath or URL in response");
        }

        // Construct URL using configured endpoint + filePath
        return rtrim($this->config['urlEndpoint'], '/') . '/' . ltrim($data['filePath'], '/');
    }

    public function delete(string $targetPath): bool
    {
        return true;
    }

    public function path(string $targetPath): string
    {
        return ''; // No local path
    }

    public function url(string $targetPath): string
    {
        return rtrim($this->config['urlEndpoint'], '/') . '/' . ltrim($targetPath, '/');
    }

    public function health(): array
    {
        $ok = !empty($this->config['publicKey']) && !empty($this->config['privateKey']) && !empty($this->config['urlEndpoint']);

        return [
            'status' => $ok ? 'ok' : 'error',
            'message' => $ok ? 'ImageKit configured' : 'ImageKit credentials missing',
        ];
    }
}
