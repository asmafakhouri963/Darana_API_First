Schema::create('properties', function (Blueprint $table) {
    $table->id();
    $table->foreignId('office_id')->constrained()->cascadeOnDelete();
    $table->foreignId('governorate_id')->constrained()->cascadeOnDelete();
    $table->string('title');
    text('description');
    $table->decimal('price', 15, 2);
    $table->string('currency')->default(Currency::USD->value);
    $table->string('status')->default(PropertyStatus::AVAILABLE->value);
    $table->string('type')->default(PropertyType::SALE->value);
    $table->boolean('is_featured')->default(false);
    
    // حقول حساسة داخلية لا يجب أن تظهر للعامة
    $table->decimal('commission_rate', 5, 2)->nullable();
    $table->text('internal_notes')->nullable();
    $table->string('owner_phone')->nullable();

    $table->timestamps();

    // Indexes لتحسين أداء الفلترة والبحث
    $table->index(['governorate_id', 'status', 'type']);
    $table->index('is_featured');
});