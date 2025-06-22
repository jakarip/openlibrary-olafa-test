<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Collaboration\CollaborationReviewModel;


use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class CollaborationReview extends Controller
{
    public function index()
    {
        return view('config.collaboration.collaborationReview');
    }

    public function dt(Request $request) {}

    public function toggle_data(Request $request) {}
}
