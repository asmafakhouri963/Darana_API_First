Schema::create('governorates', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->timestamps();
});