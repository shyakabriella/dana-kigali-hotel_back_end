<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\HomePages\HeroSectionController;
use App\Http\Controllers\Api\HomePages\SectionOneController;
use App\Http\Controllers\Api\HomePages\SectionTwoController;
use App\Http\Controllers\Api\HomePages\SectionThreeController;
use App\Http\Controllers\Api\HomePages\SectionFourController;
use App\Http\Controllers\Api\HomePages\SectionFiveController;
use App\Http\Controllers\Api\HomePages\SectionSixController;
use App\Http\Controllers\Api\HomePages\SectionSevenController;
use App\Http\Controllers\Api\HomePages\SectionEightController;
use App\Http\Controllers\Api\About\AboutHeroController;
use App\Http\Controllers\Api\About\AboutSectionOneController;
use App\Http\Controllers\Api\About\AboutSectionTwoController;
use App\Http\Controllers\Api\About\AboutSectionThreeController;
use App\Http\Controllers\Api\About\AboutSectionFourController;
use App\Http\Controllers\Api\About\AboutSectionFiveController;
use App\Http\Controllers\Api\Rooms\RoomsHeroController;
use App\Http\Controllers\Api\Rooms\RoomsSectionOneController;
use App\Http\Controllers\Api\Rooms\RoomsSectionTwoController;
use App\Http\Controllers\Api\Experiences\ExperiencesHeroController;
use App\Http\Controllers\Api\Experiences\ExperiencesSectionOneController;
use App\Http\Controllers\Api\Experiences\ExperiencesSectionTwoController;
use App\Http\Controllers\Api\Contact\ContactHeroController;
use App\Http\Controllers\Api\Contact\ContactSectionOneController;
use App\Http\Controllers\Api\Contact\ContactSectionTwoController;
use App\Http\Controllers\Api\Contact\ContactSectionThreeController;
use App\Http\Controllers\Api\FooterController;
use Illuminate\Support\Facades\Route;

// Public routes (No authentication required)
Route::post('/dana/login', [AuthController::class, 'login']);

// Hero Section
Route::get('/dana/hero', [HeroSectionController::class, 'index']);
Route::get('/dana/hero/{id}', [HeroSectionController::class, 'show']);

// Section One
Route::get('/dana/section-one', [SectionOneController::class, 'index']);
Route::get('/dana/section-one/{id}', [SectionOneController::class, 'show']);

// Section Two
Route::get('/dana/section-two', [SectionTwoController::class, 'index']);
Route::get('/dana/section-two/{id}', [SectionTwoController::class, 'show']);

// Section Three
Route::get('/dana/section-three', [SectionThreeController::class, 'index']);
Route::get('/dana/section-three/{id}', [SectionThreeController::class, 'show']);

// Section Four
Route::get('/dana/section-four', [SectionFourController::class, 'index']);
Route::get('/dana/section-four/{id}', [SectionFourController::class, 'show']);

// Section Five
Route::get('/dana/section-five', [SectionFiveController::class, 'index']);
Route::get('/dana/section-five/{id}', [SectionFiveController::class, 'show']);

// Section Six
Route::get('/dana/section-six', [SectionSixController::class, 'index']);
Route::get('/dana/section-six/{id}', [SectionSixController::class, 'show']);

// Section Seven
Route::get('/dana/section-seven', [SectionSevenController::class, 'index']);
Route::get('/dana/section-seven/{id}', [SectionSevenController::class, 'show']);

// Section Eight
Route::get('/dana/section-eight', [SectionEightController::class, 'index']);
Route::get('/dana/section-eight/{id}', [SectionEightController::class, 'show']);

//About page hero section
Route::get('/dana/about/hero', [AboutHeroController::class, 'index']);
Route::get('/dana/about/hero/{id}', [AboutHeroController::class, 'show']);

//About page section one
Route::get('/dana/about/section-one', [AboutSectionOneController::class, 'index']);
Route::get('/dana/about/section-one/{id}', [AboutSectionOneController::class, 'show']);
//About page section two

Route::get('/dana/about/section-two', [AboutSectionTwoController::class, 'index']);
Route::get('/dana/about/section-two/{id}', [AboutSectionTwoController::class, 'show']);
//About page section three
Route::get('/dana/about/section-three', [AboutSectionThreeController::class, 'index']);
Route::get('/dana/about/section-three/{id}', [AboutSectionThreeController::class, 'show']);
//About page section four
Route::get('/dana/about/section-four', [AboutSectionFourController::class, 'index']);
Route::get('/dana/about/section-four/{id}', [AboutSectionFourController::class, 'show']);
//About page section five
Route::get('/dana/about/section-five', [AboutSectionFiveController::class, 'index']);
Route::get('/dana/about/section-five/{id}', [AboutSectionFiveController::class, 'show']);
//Rooms page hero section
Route::get('/dana/rooms/hero', [RoomsHeroController::class, 'index']);
Route::get('/dana/rooms/hero/{id}', [RoomsHeroController::class, 'show']);
//Rooms page section one
Route::get('/dana/rooms/section-one', [RoomsSectionOneController::class, 'index']);
Route::get('/dana/rooms/section-one/{id}', [RoomsSectionOneController::class, 'show']);
//Rooms page section two
Route::get('/dana/rooms/section-two', [RoomsSectionTwoController::class, 'index']);
Route::get('/dana/rooms/section-two/{id}', [RoomsSectionTwoController::class, 'show']);
//Experiences page hero section
Route::get('/dana/experiences/hero', [ExperiencesHeroController::class, 'index']);
Route::get('/dana/experiences/hero/{id}', [ExperiencesHeroController::class, 'show']);

//Experiences page section one
Route::get('/dana/experiences/section-one', [ExperiencesSectionOneController::class, 'index']);
Route::get('/dana/experiences/section-one/{id}', [ExperiencesSectionOneController::class, 'show']);
//Experiences page section two
Route::get('/dana/experiences/section-two', [ExperiencesSectionTwoController::class, 'index']);
Route::get('/dana/experiences/section-two/{id}', [ExperiencesSectionTwoController::class, 'show']);

//Contact page hero section
Route::get('/dana/contact/hero', [ContactHeroController::class, 'index']);
Route::get('/dana/contact/hero/{id}', [ContactHeroController::class, 'show']);

//Contact page section one
Route::get('/dana/contact/section-one', [ContactSectionOneController::class, 'index']);
Route::get('/dana/contact/section-one/{id}', [ContactSectionOneController::class, 'show']);

//Contact page section two
Route::get('/dana/contact/section-two', [ContactSectionTwoController::class, 'index']);
Route::get('/dana/contact/section-two/{id}', [ContactSectionTwoController::class, 'show']);

//Contact page section three
Route::get('/dana/contact/section-three', [ContactSectionThreeController::class, 'index']);
Route::get('/dana/contact/section-three/{id}', [ContactSectionThreeController::class, 'show']);
//Footer
Route::get('/dana/footer', [FooterController::class, 'index']);



// Protected routes (Admin only - Authentication required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/dana/logout', [AuthController::class, 'logout']);
    Route::get('/dana/me', [AuthController::class, 'me']);
    
    // Hero Section CRUD
    Route::post('/dana/hero', [HeroSectionController::class, 'store']);
    Route::put('/dana/hero/{id}', [HeroSectionController::class, 'update']);
    Route::delete('/dana/hero/{id}', [HeroSectionController::class, 'destroy']);
    
    // Section One CRUD
    Route::post('/dana/section-one', [SectionOneController::class, 'store']);
    Route::put('/dana/section-one/{id}', [SectionOneController::class, 'update']);
    Route::delete('/dana/section-one/{id}', [SectionOneController::class, 'destroy']);
    
    // Section Two CRUD
    Route::post('/dana/section-two', [SectionTwoController::class, 'store']);
    Route::put('/dana/section-two/{id}', [SectionTwoController::class, 'update']);
    Route::delete('/dana/section-two/{id}', [SectionTwoController::class, 'destroy']);
    Route::post('/dana/section-two/upload-room-image', [SectionTwoController::class, 'uploadRoomImage']);
    
    // Section Three CRUD
    Route::post('/dana/section-three', [SectionThreeController::class, 'store']);
    Route::put('/dana/section-three/{id}', [SectionThreeController::class, 'update']);
    Route::delete('/dana/section-three/{id}', [SectionThreeController::class, 'destroy']);
    
    // Section Four CRUD
    Route::post('/dana/section-four', [SectionFourController::class, 'store']);
    Route::put('/dana/section-four/{id}', [SectionFourController::class, 'update']);
    Route::delete('/dana/section-four/{id}', [SectionFourController::class, 'destroy']);
    
    // Section Five CRUD
    Route::post('/dana/section-five', [SectionFiveController::class, 'store']);
    Route::put('/dana/section-five/{id}', [SectionFiveController::class, 'update']);
    Route::delete('/dana/section-five/{id}', [SectionFiveController::class, 'destroy']);
    Route::post('/dana/section-five/upload-image', [SectionFiveController::class, 'uploadImage']);
    Route::delete('/dana/section-five/{id}/image', [SectionFiveController::class, 'deleteImage']);
    
    // Section Six CRUD
    Route::post('/dana/section-six', [SectionSixController::class, 'store']);
    Route::put('/dana/section-six/{id}', [SectionSixController::class, 'update']);
    Route::delete('/dana/section-six/{id}', [SectionSixController::class, 'destroy']);
    Route::post('/dana/section-six/upload-images', [SectionSixController::class, 'uploadImages']);
    Route::post('/dana/section-six/add-image', [SectionSixController::class, 'addImage']);
    Route::delete('/dana/section-six/{id}/image/{index}', [SectionSixController::class, 'deleteImage']);
    Route::delete('/dana/section-six/{id}/images', [SectionSixController::class, 'deleteAllImages']);
    
    // Section Seven CRUD
    Route::post('/dana/section-seven', [SectionSevenController::class, 'store']);
    Route::put('/dana/section-seven/{id}', [SectionSevenController::class, 'update']);
    Route::delete('/dana/section-seven/{id}', [SectionSevenController::class, 'destroy']);
    Route::delete('/dana/section-seven/{id}/testimonial/{index}', [SectionSevenController::class, 'deleteTestimonial']);

    // Section Eight CRUD
    Route::post('/dana/section-eight', [SectionEightController::class, 'store']);
    Route::put('/dana/section-eight/{id}', [SectionEightController::class, 'update']);
    Route::delete('/dana/section-eight/{id}', [SectionEightController::class, 'destroy']);

    //About page hero section CRUD
    Route::post('/dana/about/hero', [AboutHeroController::class, 'store']);
    Route::put('/dana/about/hero/{id}', [AboutHeroController::class, 'update']);
    Route::delete('/dana/about/hero/{id}', [AboutHeroController::class, 'destroy']);

    //About page section one CRUD
    Route::post('/dana/about/section-one', [AboutSectionOneController::class, 'store']);
    Route::put('/dana/about/section-one/{id}', [AboutSectionOneController::class, 'update']);
    Route::delete('/dana/about/section-one/{id}', [AboutSectionOneController::class, 'destroy']);
    //About page section two CRUD

    Route::post('/dana/about/section-two', [AboutSectionTwoController::class, 'store']);
    Route::put('/dana/about/section-two/{id}', [AboutSectionTwoController::class, 'update']);
    Route::delete('/dana/about/section-two/{id}', [AboutSectionTwoController::class, 'destroy']);
    Route::delete('/dana/about/section-two/{id}/value/{index}', [AboutSectionTwoController::class, 'deleteValue']);
    //About page section three CRUD
    Route::post('/dana/about/section-three', [AboutSectionThreeController::class, 'store']);
    Route::put('/dana/about/section-three/{id}', [AboutSectionThreeController::class, 'update']);
    Route::delete('/dana/about/section-three/{id}', [AboutSectionThreeController::class, 'destroy']);
    Route::delete('/dana/about/section-three/{id}/timeline/{index}', [AboutSectionThreeController::class, 'deleteTimelineItem']);
    //About page section four CRUD
    Route::post('/dana/about/section-four', [AboutSectionFourController::class, 'store']);
Route::put('/dana/about/section-four/{id}', [AboutSectionFourController::class, 'update']);
Route::delete('/dana/about/section-four/{id}', [AboutSectionFourController::class, 'destroy']);
Route::post('/dana/about/section-four/upload-member-image', [AboutSectionFourController::class, 'uploadMemberImage']);
Route::delete('/dana/about/section-four/{id}/member/{index}/image', [AboutSectionFourController::class, 'deleteMemberImage']);
//About page section five CRUD
Route::post('/dana/about/section-five', [AboutSectionFiveController::class, 'store']);
Route::put('/dana/about/section-five/{id}', [AboutSectionFiveController::class, 'update']);
Route::delete('/dana/about/section-five/{id}', [AboutSectionFiveController::class, 'destroy']);
Route::post('/dana/about/section-five/upload-image', [AboutSectionFiveController::class, 'uploadImage']);
Route::delete('/dana/about/section-five/{id}/image', [AboutSectionFiveController::class, 'deleteImage']);
//Rooms page hero section CRUD
Route::post('/dana/rooms/hero', [RoomsHeroController::class, 'store']);
Route::put('/dana/rooms/hero/{id}', [RoomsHeroController::class, 'update']);
Route::delete('/dana/rooms/hero/{id}', [RoomsHeroController::class, 'destroy']);

//Rooms page section one CRUD
Route::post('/dana/rooms/section-one', [RoomsSectionOneController::class, 'store']);
Route::put('/dana/rooms/section-one/{id}', [RoomsSectionOneController::class, 'update']);
Route::delete('/dana/rooms/section-one/{id}', [RoomsSectionOneController::class, 'destroy']);

//Rooms page section two CRUD
Route::post('/dana/rooms/section-two', [RoomsSectionTwoController::class, 'store']);
Route::put('/dana/rooms/section-two/{id}', [RoomsSectionTwoController::class, 'update']);
Route::delete('/dana/rooms/section-two/{id}', [RoomsSectionTwoController::class, 'destroy']);
//Experiences page hero section CRUD
Route::post('/dana/experiences/hero', [ExperiencesHeroController::class, 'store']);
Route::put('/dana/experiences/hero/{id}', [ExperiencesHeroController::class, 'update']);
Route::delete('/dana/experiences/hero/{id}', [ExperiencesHeroController::class, 'destroy']);
//Experiences page section one CRUD
Route::post('/dana/experiences/section-one', [ExperiencesSectionOneController::class, 'store']);
Route::put('/dana/experiences/section-one/{id}', [ExperiencesSectionOneController::class, 'update']);
Route::delete('/dana/experiences/section-one/{id}', [ExperiencesSectionOneController::class, 'destroy']);
//Experiences page section two CRUD
Route::post('/dana/experiences/section-two', [ExperiencesSectionTwoController::class, 'store']);
Route::put('/dana/experiences/section-two/{id}', [ExperiencesSectionTwoController::class, 'update']);
Route::delete('/dana/experiences/section-two/{id}', [ExperiencesSectionTwoController::class, 'destroy']);
//Contact page hero section CRUD
Route::post('/dana/contact/hero', [ContactHeroController::class, 'store']);
Route::put('/dana/contact/hero/{id}', [ContactHeroController::class, 'update']);
Route::delete('/dana/contact/hero/{id}', [ContactHeroController::class, 'destroy']);

//Contact page section one CRUD
Route::post('/dana/contact/section-one', [ContactSectionOneController::class, 'store']);
Route::put('/dana/contact/section-one/{id}', [ContactSectionOneController::class, 'update']);
Route::delete('/dana/contact/section-one/{id}', [ContactSectionOneController::class, 'destroy']);
//Contact page section two CRUD
Route::post('/dana/contact/section-two', [ContactSectionTwoController::class, 'store']);
Route::put('/dana/contact/section-two/{id}', [ContactSectionTwoController::class, 'update']);
Route::delete('/dana/contact/section-two/{id}', [ContactSectionTwoController::class, 'destroy']);

//Contact page section three CRUD
Route::post('/dana/contact/section-three', [ContactSectionThreeController::class, 'store']);
Route::put('/dana/contact/section-three/{id}', [ContactSectionThreeController::class, 'update']);
Route::delete('/dana/contact/section-three/{id}', [ContactSectionThreeController::class, 'destroy']);
//Footer CRUD
Route::post('/dana/footer', [FooterController::class, 'store']);
Route::put('/dana/footer/{id}', [FooterController::class, 'update']);
Route::delete('/dana/footer/{id}', [FooterController::class, 'destroy']);



});