<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => ['auth:api', 'throttle:60,1']], function () {
    // profile routes
    Route::put('profile', 'Api\ProfileController@update');
    Route::post('profile/avatar', 'Api\ProfileController@updateAvatar');
    // channel routes
    Route::get('channels/{userId}', 'Api\ChannelController@showChannelsByUserId');
    Route::get('channels/{channelId}/users', 'Api\ChannelController@showUsersByChannelId');
    Route::delete('channels/{channelId}/users/{userId}', 'Api\ChannelController@deleteChannelMember');
    Route::post('channels', 'Api\ChannelController@storeChannel');
    Route::put('channels/{channelId}', 'Api\ChannelController@updateChannel');
    Route::delete('channels/{channelId}', 'Api\ChannelController@deleteChannel');
    Route::get('channels/{userId}/channel/{channelId}/messages', 'Api\ChannelController@showChannelMessages');
    Route::post('channels/{userId}/channel/{channelId}/messages', 'Api\ChannelController@storeMessage');
    // invitation routes
    Route::post('invitations', 'Api\InvitationController@queue');
    // chat routes
    Route::get('chats/{userId}', 'Api\ChatController@fetchChatsByUserId');
    Route::post('chats/{userId}', 'Api\ChatController@pokeByUserId');
    Route::get('chats/{userId}/messages/{recipientId}', 'Api\ChatController@messagesByRecipientId');
    Route::post('chats/{userId}/messages/{recipientId}/seen', 'Api\ChatController@makeMessagesRead');
    Route::get('chats/{userId}/recipients', 'Api\ChatController@fetchRecipientsByUserId');
    Route::post('chats/{userId}/recipients', 'Api\ChatController@startChat');
    Route::post('chats/{userId}/messages/{recipientId}', 'Api\ChatController@storeChatMessage');
    Route::delete('chats/{userId}/messages/{recipientId}', 'Api\ChatController@deleteChat');
});

