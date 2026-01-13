<?php

namespace AperturePro\Http\Rest;

use WP_REST_Request;
use AperturePro\Domain\Jobs\JobRepository;
use AperturePro\Domain\Jobs\JobScheduler;
use AperturePro\Http\Middleware\Permissions;
use AperturePro\Support\Response;

class JobsController
{
    public function register_routes(): void
    {
        register_rest_route('aperture-pro/v1', '/jobs', [
            'methods'             => 'GET',
            'callback'            => [$this, 'index'],
            'permission_callback' => [Permissions::class, 'admin_only'],
        ]);

        register_rest_route('aperture-pro/v1', '/jobs/(?P<id>\d+)/retry', [
            'methods'             => 'POST',
            'callback'            => [$this, 'retry'],
            'permission_callback' => [Permissions::class, 'admin_only'],
        ]);
    }

    public function index(WP_REST_Request $req)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'ap_jobs';

        $per_page = 20;
        $page     = (int) ($req['page'] ?? 1);
        $offset   = ($page - 1) * $per_page;

        $jobs = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$table} ORDER BY created_at DESC LIMIT %d OFFSET %d", $per_page, $offset)
        );

        $total = $wpdb->get_var("SELECT COUNT(*) FROM {$table}");

        return Response::success([
            'jobs'  => $jobs,
            'total' => (int) $total,
            'pages' => ceil($total / $per_page),
        ]);
    }

    public function retry(WP_REST_Request $req)
    {
        $job_id = (int) $req['id'];
        $job    = JobRepository::find($job_id);

        if (!$job) {
            return Response::notFound('Job not found');
        }

        JobScheduler::scheduleRetry($job);

        return Response::success(['message' => 'Job rescheduled']);
    }
}
