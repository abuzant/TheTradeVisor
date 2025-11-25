<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SecureConfigService;
use App\Services\SecureLogAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SecurityAuditController extends Controller
{
    protected $configService;
    protected $logService;

    public function __construct(SecureConfigService $configService, SecureLogAccessService $logService)
    {
        $this->configService = $configService;
        $this->logService = $logService;
    }

    /**
     * Display security audit dashboard.
     */
    public function index()
    {
        try {
            // Perform security audit
            $configAudit = $this->configService->auditConfigSecurity();
            $exposedFiles = $this->configService->scanForExposedFiles();
            
            // Get recent security-related log entries
            $recentLogs = $this->getDashboardSecurityLogs();

            return view('admin.security.audit', compact(
                'configAudit', 
                'exposedFiles', 
                'recentLogs'
            ));
        } catch (\Exception $e) {
            Log::error('Security audit failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            // Provide default values to prevent view errors
            $configAudit = [
                'issues' => [],
                'critical_count' => 0,
                'warning_count' => 0,
                'total_issues' => 0,
                'audit_time' => now()->toDateTimeString()
            ];
            
            $exposedFiles = [
                'exposed_files' => [],
                'total_count' => 0,
                'scan_time' => now()->toDateTimeString()
            ];
            
            $recentLogs = [];

            return view('admin.security.audit', compact(
                'configAudit', 
                'exposedFiles', 
                'recentLogs'
            ))->with('warning', 'Security audit encountered errors: ' . $e->getMessage());
        }
    }

    /**
     * Secure configuration files.
     */
    public function secureConfigs()
    {
        try {
            $results = $this->configService->secureConfigFiles();
            
            return response()->json([
                'success' => true,
                'results' => $results,
                'message' => 'Configuration security updated'
            ]);
        } catch (\Exception $e) {
            Log::error('Configuration security failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to secure configurations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get security-related log entries.
     */
    public function getSecurityLogs(Request $request)
    {
        try {
            $limit = $request->get('limit', 50);
            
            // Get Laravel logs for security events
            $laravelLogs = $this->logService->getLaravelLogs($limit);
            
            // Filter for security-related entries
            $securityLogs = array_filter($laravelLogs['lines'], function($log) {
                $securityKeywords = [
                    'unauthorized',
                    'forbidden',
                    'authentication',
                    'login',
                    'logout',
                    'permission',
                    'security',
                    'audit',
                    'access denied',
                    'csrf',
                    'token',
                    'breach',
                    'attack',
                    'suspicious'
                ];
                
                $message = strtolower($log['message']);
                foreach ($securityKeywords as $keyword) {
                    if (strpos($message, $keyword) !== false) {
                        return true;
                    }
                }
                
                return false;
            });

            return response()->json([
                'success' => true,
                'logs' => array_values($securityLogs),
                'total' => count($securityLogs)
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get security logs', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve security logs'
            ], 500);
        }
    }

    /**
     * Get recent security logs for dashboard.
     */
    private function getDashboardSecurityLogs(): array
    {
        try {
            $laravelLogs = $this->logService->getLaravelLogs(20);
            
            // Filter for recent security events
            $securityLogs = array_filter($laravelLogs['lines'], function($log) {
                $message = strtolower($log['message']);
                return strpos($message, 'unauthorized') !== false 
                    || strpos($message, 'security') !== false
                    || strpos($message, 'audit') !== false
                    || strpos($message, 'permission') !== false;
            });

            return array_slice(array_values($securityLogs), 0, 10);
        } catch (\Exception $e) {
            Log::error('Failed to get security logs for dashboard', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Generate security audit report.
     */
    public function generateReport()
    {
        try {
            $configAudit = $this->configService->auditConfigSecurity();
            $exposedFiles = $this->configService->scanForExposedFiles();
            
            $report = [
                'generated_at' => now()->toDateTimeString(),
                'generated_by' => auth()->user()->email,
                'summary' => [
                    'total_issues' => $configAudit['total_issues'],
                    'critical_issues' => $configAudit['critical_count'],
                    'warning_issues' => $configAudit['warning_count'],
                    'exposed_files' => $exposedFiles['total_count']
                ],
                'config_issues' => $configAudit['issues'],
                'exposed_files' => $exposedFiles['exposed_files'],
                'recommendations' => $this->generateRecommendations($configAudit, $exposedFiles)
            ];

            return response()->json($report);
        } catch (\Exception $e) {
            Log::error('Failed to generate security report', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate security report'
            ], 500);
        }
    }

    /**
     * Generate security recommendations based on audit results.
     */
    private function generateRecommendations(array $configAudit, array $exposedFiles): array
    {
        $recommendations = [];

        if ($configAudit['critical_count'] > 0) {
            $recommendations[] = [
                'priority' => 'critical',
                'title' => 'Fix Critical Security Issues',
                'description' => 'Immediate action required for critical security vulnerabilities.',
                'action' => 'Use the "Secure Configurations" button to fix file permissions.'
            ];
        }

        if ($exposedFiles['total_count'] > 0) {
            $recommendations[] = [
                'priority' => 'high',
                'title' => 'Remove Exposed Sensitive Files',
                'description' => $exposedFiles['total_count'] . ' sensitive files found in web-accessible directories.',
                'action' => 'Move or remove exposed files immediately.'
            ];
        }

        if ($configAudit['warning_count'] > 0) {
            $recommendations[] = [
                'priority' => 'medium',
                'title' => 'Review File Permissions',
                'description' => $configAudit['warning_count'] . ' files have non-standard permissions.',
                'action' => 'Review and update file permissions to follow security best practices.'
            ];
        }

        $recommendations[] = [
            'priority' => 'low',
            'title' => 'Regular Security Audits',
            'description' => 'Schedule regular security audits to maintain system security.',
            'action' => 'Set up monthly security audits and log reviews.'
        ];

        return $recommendations;
    }
}
