Schema::create('images', function (Blueprint $table) {
    $table->id();
    $table->morphs('imageable'); // لتغطية صور العقارات وشعار المكتب
    $table->string('url');
    $table->timestamps();
});