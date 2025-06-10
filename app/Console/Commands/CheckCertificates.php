<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Models\Certificate;
use App\Models\Target;
use App\Notifications\SendAlertNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CheckCertificates extends Command
{
    protected $signature = 'check:certificates';
    protected $description = 'Check SSL and domain expiry dates for all targets';


    public function handle()
    {
        foreach (Target::all() as $target) {
            $sslExpiry = $this->getSSLCertificateExpiry($target->url);
            $domainExpiry = $this->getDomainExpiry($target->url);
            $SSLDaysToExpiry = abs(round($sslExpiry?->diffInDays(now())));
            $DomainDaysToExpiry = abs(round($domainExpiry?->diffInDays(now())));

            Certificate::updateOrCreate(
                ['target_id' => $target->id],
                [
                    'ssl_expiry_date' => $sslExpiry,
                    'domain_expiry_date' => $domainExpiry,
                    'days_to_ssl_expiry' => $SSLDaysToExpiry,
                    'days_to_domain_expiry' => $DomainDaysToExpiry,
                    'checked_at' => now(),
                ]
            );

            if ($sslExpiry && $SSLDaysToExpiry <= 14) {
                $message = "SSL for $target->url expires in ". $SSLDaysToExpiry ." days.";
                Alert::create([
                    'target_id' => $target->id,
                    'type' => 'ssl',
                    'message' => $message
                ]);
                Notification::route('mail', config('notifications.admin'))
                    ->route('slack', config('notifications.slack'))
                    ->notify(new SendAlertNotification($message));
            }

            if ($domainExpiry && $DomainDaysToExpiry <= 14) {
                $message = "Domain for $target->url expires in ". $DomainDaysToExpiry ." days.";
                Alert::create([
                    'target_id' => $target->id,
                    'type' => 'domain',
                    'message' => $message
                ]);
                Notification::route('mail', config('notifications.admin'))
                    ->route('slack', config('notifications.slack'))
                    ->notify(new SendAlertNotification($message));
            }
        }
    }

    private function getSSLCertificateExpiry($url)
    {
        try {
            $stream = stream_context_create(["ssl" => ["capture_peer_cert" => true]]);
            $read = @stream_socket_client("ssl://" . parse_url($url, PHP_URL_HOST) . ":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $stream);
            if ($read === false) return null;
            $cont = stream_context_get_params($read);
            $cert = openssl_x509_parse($cont["options"]["ssl"]["peer_certificate"]);
            return Carbon::createFromTimestamp($cert['validTo_time_t']);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    private function getDomainExpiry($url)
    {
        try {
            // Extract domain and remove the www subdomain if present
            $domain = parse_url($url, PHP_URL_HOST);
            $domain = preg_replace('/^www\./', '', $domain);

            // Check cache first
            $cacheKey = "domain-expiry:$domain";
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            // Execute whois with timeout
            $whois = shell_exec("timeout 5 whois $domain 2>&1");

            // If whois command failed
            if (str_contains($whois, 'connect: Connection timed out')) {
                throw new Exception("WHOIS lookup timed out for $domain");
            }

            $expiryDate = null;

            // Check for multiple possible date formats
            if (preg_match('/Expiry Date:\s*(.+)/i', $whois, $matches)) {
                $expiryDate = Carbon::parse(trim($matches[1]));
            } elseif (preg_match('/Registry Expiry Date:\s*(.+)/i', $whois, $matches)) {
                $expiryDate = Carbon::parse(trim($matches[1]));
            } elseif (preg_match('/expire:\s*(\d{2}\.\d{2}\.\d{4})/i', $whois, $matches)) {
                $expiryDate = Carbon::createFromFormat('d.m.Y', trim($matches[1]));
            } elseif (preg_match('/expires:\s*(.+)/i', $whois, $matches)) {
                $expiryDate = Carbon::parse(trim($matches[1]));
            } elseif (preg_match('/Expiration Date:\s*(.+)/i', $whois, $matches)) {
                $expiryDate = Carbon::parse(trim($matches[1]));
            }

            // Cache the result for 1 day if found
            if ($expiryDate) {
                Cache::put($cacheKey, $expiryDate, now()->addDay());
                return $expiryDate;
            }

            Log::warning("No expiry date found in WHOIS data for {$domain}");
            return null;

        } catch (Exception $e) {
            Log::error("WHOIS lookup failed for {$domain}: " . $e->getMessage());
            return null;
        }
    }
}
