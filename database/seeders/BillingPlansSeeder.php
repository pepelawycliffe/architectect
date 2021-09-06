<?php

namespace Database\Seeders;

use Arr;
use Common\Auth\Permissions\Permission;
use Common\Billing\BillingPlan;
use Illuminate\Database\Seeder;
use Str;

class BillingPlansSeeder extends Seeder
{
    /**
     * @var BillingPlan
     */
    private $plan;

    /**
     * BillingPlansSeeder constructor.
     * @param BillingPlan $plan
     */
    public function __construct(BillingPlan $plan)
    {
        $this->plan = $plan;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createPlanWithIntervalAlternatives([
            'name' => 'Basic',
            'amount' => 10,
            'currency' => 'USD',
            'uuid' => Str::random(36),
            'features' => ["Drag & Drop Builder", "500MB Storage", "Code Editors", "No Ads", "Free Subdomain"],
            'permissions' => ['editors.enable'],
            'position' => 1,
        ]);

        $this->createPlanWithIntervalAlternatives([
            'name' => 'Standard',
            'amount' => 15,
            'currency' => 'USD',
            'uuid' => Str::random(36),
            'features' => ["Drag & Drop Builder", "500MB Storage", "Code Editors", "No Ads", "Custom Domains", "Download Projects"],
            'permissions' => ['editors.enable', 'projects.download'],
            'recommended' => 1,
            'position' => 2,
        ]);

        $this->createPlanWithIntervalAlternatives([
            'name' => 'Pro',
            'amount' => 25,
            'currency' => 'USD',
            'uuid' => Str::random(36),
            'features' => ["Drag & Drop Builder", "2GB Storage", "Code Editors", "No Ads", "Custom Domains", 'Download Projects', "Export Projects to FTP", "Priority Support"],
            'permissions' => ['editors.enable', "projects.download", 'projects.export'],
            'position' => 3,
        ]);
    }

    private function createPlanWithIntervalAlternatives($params)
    {
        //bail if plan already exists
        if ($this->plan->where('name', $params['name'])->first()) return;

        $plan = $this->plan->create(Arr::except($params, 'permissions'))->fresh();

        $permissions = app(Permission::class)->pluck('id', 'name');
        $permissionIds = array_map(function($permission) use($permissions) {
            return $permissions[$permission];
        }, $params['permissions']);
        $plan->permissions()->sync($permissionIds);

        $this->create6monthsSubscription($plan);
        $this->create1YearSubscription($plan);
    }

    private function create1YearSubscription(BillingPlan $plan)
    {
        $this->plan->create([
            'name' => '1 year subscription',
            'amount' => ($plan->amount - ($plan->amount * 0.20)) * 12, //30% discounted price * 12 months
            'currency' => 'USD',
            'interval' => $plan->interval,
            'interval_count' => 12,
            'uuid' => Str::random(36),
            'parent_id' => $plan->id,
        ]);
    }

    private function create6monthsSubscription(BillingPlan $plan)
    {
        $this->plan->create([
            'name' => '6 months subscription',
            'amount' => ($plan->amount - ($plan->amount * 0.10)) * 6, //20% discounted price * 6 months
            'currency' => 'USD',
            'interval' => $plan->interval,
            'interval_count' => 6,
            'uuid' => Str::random(36),
            'parent_id' => $plan->id,
        ]);
    }
}
