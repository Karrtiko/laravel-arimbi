<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable(); // Added
            $table->string('whatsapp_number')->nullable(); // Keep legacy or merge? Let's keep for backward compat/specific usage if needed, or mapping. ER map says customer_phone. Let's keep existing and add new.
            // Actually ERD says customer_phone. I'll map whatsapp_number to customer_phone logic conceptually later, but for schema let's match ERD strongly.
            // ERD: customer_name, customer_phone, receiver_name, receiver_phone, receiver_address, total_price, status, notes
            // My code previously had whatsapp_number. I will rename it or keep it. Let's match existing code + new requirements.
            // I'll keep whatsapp_number as it was requested before, but maybe alias it or just use customer_phone. 
            // The ERD is explicit: customer_phone. I'll add customer_phone AND receiver details.
            $table->string('receiver_name')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->text('receiver_address')->nullable();
            $table->decimal('total_price', 15, 2);
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
