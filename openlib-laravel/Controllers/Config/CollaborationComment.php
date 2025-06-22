<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Collaboration\CollaborationCommentModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class CollaborationComment extends Controller
{
    public function index()
    {
        return view('config.collaboration.collaborationComment');
    }

    public function dt(Request $request) {}

    public function toggle_data(Request $request) {}
}
