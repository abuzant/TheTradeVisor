<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class UpdateGeoIPDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geoip:update {--license-key= : MaxMind license key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download and update the GeoIP database from MaxMind';

    protected string $downloadUrl = 'https://download.maxmind.com/app/geoip_download';
    protected string $editionId = 'GeoLite2-Country';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $licenseKey = $this->option('license-key') ?? config('services.maxmind.license_key');

        if (!$licenseKey) {
            $this->error('MaxMind license key is required!');
            $this->info('');
            $this->info('Please provide a license key using one of these methods:');
            $this->info('1. Add MAXMIND_LICENSE_KEY to your .env file');
            $this->info('2. Use --license-key option: php artisan geoip:update --license-key=YOUR_KEY');
            $this->info('');
            $this->info('Get a free license key at: https://www.maxmind.com/en/geolite2/signup');
            return 1;
        }

        $this->info('Downloading GeoIP database...');

        try {
            // Create geoip directory if it doesn't exist
            $geoipPath = storage_path('app/geoip');
            if (!is_dir($geoipPath)) {
                mkdir($geoipPath, 0755, true);
            }

            // Download the database
            $url = $this->downloadUrl . '?' . http_build_query([
                'edition_id' => $this->editionId,
                'license_key' => $licenseKey,
                'suffix' => 'tar.gz',
            ]);

            $this->info('Fetching from MaxMind...');
            
            $response = Http::timeout(300)->get($url);

            if (!$response->successful()) {
                $this->error('Failed to download GeoIP database. Status: ' . $response->status());
                $this->error('Please check your license key and try again.');
                return 1;
            }

            // Save the tar.gz file
            $tarPath = $geoipPath . '/GeoLite2-Country.tar.gz';
            file_put_contents($tarPath, $response->body());

            $this->info('Download complete. Extracting...');

            // Extract the tar.gz file
            $this->extractTarGz($tarPath, $geoipPath);

            // Find and move the .mmdb file
            $this->findAndMoveDatabase($geoipPath);

            // Clean up
            @unlink($tarPath);
            $this->cleanupExtractedFiles($geoipPath);

            $this->info('✓ GeoIP database updated successfully!');
            $this->info('Database location: ' . $geoipPath . '/GeoLite2-Country.mmdb');

            return 0;
        } catch (\Exception $e) {
            $this->error('Error updating GeoIP database: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Extract tar.gz file
     */
    protected function extractTarGz(string $tarPath, string $destination): void
    {
        // First decompress .gz
        $phar = new \PharData($tarPath);
        $phar->decompress();

        // Then extract .tar
        $tarFile = str_replace('.gz', '', $tarPath);
        $phar = new \PharData($tarFile);
        $phar->extractTo($destination, null, true);

        // Clean up tar file
        @unlink($tarFile);
    }

    /**
     * Find and move the .mmdb file to the correct location
     */
    protected function findAndMoveDatabase(string $geoipPath): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($geoipPath)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'mmdb') {
                $targetPath = $geoipPath . '/GeoLite2-Country.mmdb';
                
                // Backup old database if exists
                if (file_exists($targetPath)) {
                    @unlink($targetPath . '.old');
                    @rename($targetPath, $targetPath . '.old');
                }

                // Move new database
                rename($file->getPathname(), $targetPath);
                $this->info('Database file moved to: ' . $targetPath);
                break;
            }
        }
    }

    /**
     * Clean up extracted temporary files
     */
    protected function cleanupExtractedFiles(string $geoipPath): void
    {
        $dirs = glob($geoipPath . '/GeoLite2-Country_*', GLOB_ONLYDIR);
        
        foreach ($dirs as $dir) {
            $this->deleteDirectory($dir);
        }
    }

    /**
     * Recursively delete a directory
     */
    protected function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = array_diff(scandir($dir), ['.', '..']);
        
        foreach ($items as $item) {
            $path = $dir . '/' . $item;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        rmdir($dir);
    }
}
