<?php

Route::middleware(['auth'])->group(function () {

    Route::livewire('/', 'pages::users.index')->name('home');

});
