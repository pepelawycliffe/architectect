<?php

use Common\Settings\Settings;
use Illuminate\Database\Migrations\Migration;

class MigrateLandingPageConfigTo20 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $config = app(Settings::class)->get('homepage.appearance');
        if ($config) {
            $config = str_replace('client/assets/images/landing/inline-feature-1.svg', 'custom-domain.svg', $config);
            $config = str_replace('client/assets/images/landing/inline-feature-2.svg', 'website-builder.svg', $config);
            $config = str_replace('client/assets/images/landing/inline-feature-3.svg', 'pen-tool.svg', $config);
            app(Settings::class)->save([
                'homepage.appearance' => $config
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
