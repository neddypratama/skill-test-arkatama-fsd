<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;
use App\Http\Controllers\ArsipController;

Volt::route('/', 'index');                          // Home 
Volt::route('/users', 'users.index');               // User (list) 
Volt::route('/users/create', 'users.create');       // User (create) 
Volt::route('/users/{user}/edit', 'users.edit');    // User (edit) 

Volt::route('/pet', 'pet.index');               // pet (list) 
Volt::route('/pet/create', 'pet.create');       // pet (create) 
Volt::route('/pet/{pet}/edit', 'pet.edit');    // pet (edit) 

Volt::route('/owner', 'owner.index');               // owner (list) 
Volt::route('/owner/create', 'owner.create');       // owner (create) 
Volt::route('/owner/{owner}/edit', 'owner.edit');    // owner (edit) 

Volt::route('/treatment', 'treatment.index');               // treatment (list) 
Volt::route('/treatment/create', 'treatment.create');       // treatment (create) 
Volt::route('/treatment/{treatment}/edit', 'treatment.edit');    // treatment (edit) 

Volt::route('/checkup', 'checkup.index');               // checkup (list) 
Volt::route('/checkup/create', 'checkup.create');       // checkup (create) 
Volt::route('/checkup/{checkup}/edit', 'checkup.edit');    // checkup (edit) 
Volt::route('/checkup/{checkup}/show', 'checkup.show');        // checkup (show)




