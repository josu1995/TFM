<?php

// Rutas de login
Route::get('login', ['as' => 'auth.login', 'uses' => 'Auth\LoginController@showLoginForm']);
Route::post('login', ['as' => 'auth.login', 'uses' => 'Auth\LoginController@login']);
Route::get('logout', ['as' => 'auth.logout', 'uses' => 'Auth\LoginController@logout']);

Route::get('/', ['as' => 'blog_admin_inicio', 'uses' => 'blog\BlogController@index']);

Route::get('/new', ['as' => 'blog_admin_new', 'uses' => 'blog\BlogController@getNewPost']);

Route::post('/new', ['as' => 'blog_admin_post_new', 'uses' => 'blog\BlogController@postNewPost']);

Route::get('/posts', ['as' => 'blog_admin_posts', 'uses' => 'blog\BlogController@getPosts']);

Route::get('/posts/{id}/editar', ['as' => 'blog_admin_editar', 'uses' => 'blog\BlogController@getEditPost']);

Route::get('/posts/{id}/publicar', ['as' => 'blog_admin_post_publish', 'uses' => 'blog\BlogController@publish']);

Route::get('/posts/{id}/ocultar', ['as' => 'blog_admin_post_unpublish', 'uses' => 'blog\BlogController@unpublish']);

Route::get('/posts/{id}/eliminar', ['as' => 'blog_admin_post_delete', 'uses' => 'blog\BlogController@delete']);

Route::put('/posts/{id}/editar', ['as' => 'blog_admin_post_edit', 'uses' => 'blog\BlogController@putPost']);

Route::get('/imagenes', ['as' => 'blog_admin_images', 'uses' => 'blog\BlogController@getImages']);

Route::get('/imagen/{id}', ['as' => 'blog_admin_image_delete', 'uses' => 'blog\BlogController@deleteImage']);

Route::get('/imagen', ['as' => 'blog_admin_image_new', 'uses' => 'blog\BlogController@getNewImage']);

Route::post('/imagen', ['as' => 'blog_admin_image_post_new', 'uses' => 'blog\BlogController@postImage']);
