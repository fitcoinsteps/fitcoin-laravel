<?php

namespace App\Console\Commands;

use App\Models\GiftCard;
use App\Services\GiftCardService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncGiftCards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gift-cards:sync
                            {--provider= : Sync only specific provider}
                            {--dry-run : Show what would be synced without actually syncing}
                            {--force : Force sync even if cards exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync gift cards from external providers';

    /**
     * The gift card service instance.
     *
     * @var GiftCardService
     */
    protected $giftCardService;

    /**
     * Create a new command instance.
     */
    public function __construct(GiftCardService $giftCardService)
    {
        parent::__construct();
        $this->giftCardService = $giftCardService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Starting gift card sync...');

        $provider = $this->option('provider');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        try {
            // In a real implementation, you would fetch gift cards from an external API
            // For example: Tango Card API, Prepaid API, etc.
            
            $cards = $this->fetchGiftCardsFromProvider($provider);

            if (empty($cards)) {
                $this->warn('No gift cards found to sync.');
                return 0;
            }

            $this->info("Found " . count($cards) . " gift cards to sync.");

            if ($dryRun) {
                $this->info('📋 DRY RUN - No changes will be made');
                $this->table(
                    ['Provider', 'Value', 'Currency', 'FIT Cost', 'Code'],
                    array_map(function ($card) {
                        return [
                            $card['provider'],
                            $card['value'],
                            $card['currency'],
                            $card['fitcoin_cost'] ?? $this->calculateFitCost($card['value']),
                            substr($card['code'], 0, 10) . '...',
                        ];
                    }, $cards)
                );
                return 0;
            }

            $synced = 0;
            $skipped = 0;
            $errors = 0;

            foreach ($cards as $cardData) {
                try {
                    // Check if gift card already exists
                    $exists = GiftCard::where('code', $cardData['code'])->exists();

                    if ($exists && !$force) {
                        $this->line("⏭️  Skipping duplicate gift card: {$cardData['code']}");
                        $skipped++;
                        continue;
                    }

                    if ($exists && $force) {
                        // Update existing card
                        $giftCard = GiftCard::where('code', $cardData['code'])->first();
                        $giftCard->update([
                            'value' => $cardData['value'],
                            'currency' => $cardData['currency'],
                            'fitcoin_cost' => $cardData['fitcoin_cost'] ?? $this->calculateFitCost($cardData['value']),
                            'expires_at' => $cardData['expires_at'] ?? null,
                        ]);
                        $this->line("🔄 Updated gift card: {$cardData['code']}");
                    } else {
                        // Create new gift card
                        $this->giftCardService->createGiftCard([
                            'provider' => $cardData['provider'],
                            'code' => $cardData['code'],
                            'pin' => $cardData['pin'] ?? null,
                            'value' => $cardData['value'],
                            'currency' => $cardData['currency'],
                            'fitcoin_cost' => $cardData['fitcoin_cost'] ?? $this->calculateFitCost($cardData['value']),
                            'sku' => $cardData['sku'] ?? null,
                            'expires_at' => $cardData['expires_at'] ?? null,
                            'purchased_at' => now(),
                        ]);
                        $this->line("✅ Added gift card: {$cardData['code']}");
                    }

                    $synced++;

                } catch (\Exception $e) {
                    $this->error("❌ Error syncing gift card {$cardData['code']}: " . $e->getMessage());
                    $errors++;
                    Log::error('Gift card sync error', [
                        'code' => $cardData['code'] ?? 'unknown',
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $this->newLine();
            $this->info("📊 Sync Summary:");
            $this->table(
                ['Status', 'Count'],
                [
                    ['✅ Synced', $synced],
                    ['⏭️  Skipped', $skipped],
                    ['❌ Errors', $errors],
                    ['📦 Total', count($cards)],
                ]
            );

            Log::info('Gift card sync completed', [
                'synced' => $synced,
                'skipped' => $skipped,
                'errors' => $errors,
                'total' => count($cards),
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Sync failed: ' . $e->getMessage());
            Log::error('Gift card sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return 1;
        }
    }

    /**
     * Fetch gift cards from provider API.
     * This is a mock implementation - replace with actual API calls.
     */
    protected function fetchGiftCardsFromProvider(?string $provider = null): array
    {
        // In real implementation, you would call external APIs like:
        // - Tango Card API
        // - Prepaid API
        // - GiftCardAPI
        // - Gyft API

        // Mock data for demonstration
        $providers = $provider ? [$provider] : ['amazon', 'google_play', 'steam', 'apple'];
        
        $cards = [];
        $mockCodes = [
            'amazon' => ['AMZN-', 'AMZ-', 'AMA-'],
            'google_play' => ['GP-', 'GOOG-', 'PLAY-'],
            'steam' => ['STM-', 'STEAM-', 'GAME-'],
            'apple' => ['APL-', 'APPLE-', 'MAC-'],
        ];

        foreach ($providers as $prov) {
            $prefix = $mockCodes[$prov] ?? ['GC-'];
            for ($i = 0; $i < 5; $i++) {
                $cards[] = [
                    'provider' => $prov,
                    'code' => $prefix[array_rand($prefix)] . strtoupper(substr(md5(uniqid()), 0, 12)),
                    'pin' => rand(1000, 9999),
                    'value' => [5, 10, 20, 25, 50][array_rand([5, 10, 20, 25, 50])],
                    'currency' => 'USD',
                    'fitcoin_cost' => $this->calculateFitCost($cards[$i]['value'] ?? 10),
                    'expires_at' => now()->addMonths(12),
                ];
            }
        }

        return $cards;
    }

    /**
     * Calculate FIT coin cost based on value.
     */
    protected function calculateFitCost(float $value): int
    {
        // 100 FIT = $1 USD
        return (int) ($value * 100);
    }

    /**
     * Get available providers.
     */
    protected function getProviders(): array
    {
        return ['amazon', 'google_play', 'steam', 'apple'];
    }
}