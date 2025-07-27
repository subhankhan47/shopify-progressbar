<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Osiset\ShopifyApp\Actions\CancelCurrentPlan;
use Osiset\ShopifyApp\Contracts\Commands\Shop as IShopCommand;
use Osiset\ShopifyApp\Contracts\Queries\Shop as IShopQuery;
use Osiset\ShopifyApp\Messaging\Events\AppUninstalledEvent;
use Osiset\ShopifyApp\Objects\Values\ShopDomain;
use Osiset\ShopifyApp\Util;
use stdClass;

class AppUninstalledJob extends \Osiset\ShopifyApp\Messaging\Jobs\AppUninstalledJob
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The shop domain.
     *
     * @var ShopDomain|string
     */
    protected $domain;

    /**
     * The webhook data.
     *
     * @var object
     */
    protected $data;

    /**
     * Create a new job instance.
     *
     * @param string   $domain The shop domain.
     * @param stdClass $data   The webhook data (JSON decoded).
     *
     * @return void
     */
    public function __construct(string $domain, stdClass $data)
    {
        $this->domain = $domain;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @param IShopCommand      $shopCommand             The commands for shops.
     * @param IShopQuery        $shopQuery               The querier for shops.
     * @param CancelCurrentPlan $cancelCurrentPlanAction The action for cancelling the current plan.
     *
     * @return bool
     */
    public function handle(
        IShopCommand $shopCommand,
        IShopQuery $shopQuery,
        CancelCurrentPlan $cancelCurrentPlanAction
    ): bool {
        // Convert the domain
        $this->domain = ShopDomain::fromNative($this->domain);
        $shopUser = User::where('name', $this->domain->toNative())->first();
        $data = $this->data;
        if ($shopUser){
            try {
                SendUninstallEmail::dispatch($data);
            } catch (\Exception $e) {
                Log::error('Failed to send uninstall email: ' . $e->getMessage());
            }
            $shopUser->update(['email_sent' => false]);
        }

        // Get the shop
        $shop = $shopQuery->getByDomain($this->domain);
        $shopId = $shop->getId();

        // Cancel the current plan
        $cancelCurrentPlanAction($shopId);

        // Purge shop of token, plan, etc.
        $shopCommand->clean($shopId);

        // Check freemium mode
        if (Util::getShopifyConfig('billing_freemium_enabled') === true) {
            // Add the freemium flag to the shop
            $shopCommand->setAsFreemium($shopId);
        }

        // Soft delete the shop.
        $shopCommand->softDelete($shopId);

        event(new AppUninstalledEvent($shop));

        return true;
    }
}
