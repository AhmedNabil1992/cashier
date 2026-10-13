<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_channels', function (Blueprint $table): void {
            $table->text('configuration')->nullable()->change();
        });

        DB::table('delivery_channels')->whereNotNull('configuration')->orderBy('id')->each(function (object $channel): void {
            DB::table('delivery_channels')->where('id', $channel->id)->update([
                'configuration' => Crypt::encryptString((string) $channel->configuration),
            ]);
        });
    }

    public function down(): void
    {
        DB::table('delivery_channels')->whereNotNull('configuration')->orderBy('id')->each(function (object $channel): void {
            DB::table('delivery_channels')->where('id', $channel->id)->update([
                'configuration' => Crypt::decryptString((string) $channel->configuration),
            ]);
        });

        Schema::table('delivery_channels', function (Blueprint $table): void {
            $table->json('configuration')->nullable()->change();
        });
    }
};
