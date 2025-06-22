<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function index()
    {
        $member = Auth::user();
        return view('user.account-setting', compact('member'));
    }

    public function dt(Request $request)
    {
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'fullName' => 'required|string|max:255',
            'phoneNumber' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'cropped_photo' => 'nullable|string',
        ]);

        $member = Auth::user();
        $emailFolder = strtolower($member->master_data_email);

        $newPhotoPath = $this->handleFileUpload($request, $emailFolder);
        if ($newPhotoPath) {
            $member->master_data_photo = $newPhotoPath;
        }

        $member->master_data_fullname = $validatedData['fullName'];
        $member->master_data_mobile_phone = $validatedData['phoneNumber'] ?? $member->master_data_mobile_phone;
        $member->master_data_address = $validatedData['address'] ?? $member->master_data_address;
        $member->save();

        return response()->json([
            'message' => 'Profile updated successfully.',
            'photo' => asset('storage/' . $member->master_data_photo)
        ]);
    }
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currentPassword' => 'required',
            'newPassword' => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z]).+$/',
                'regex:/[\d\W\s]/',
            ],
            'confirmPassword' => 'required|same:newPassword',
        ], [
            'newPassword.regex' => 'Password must contain uppercase, lowercase, and at least one number/symbol/whitespace.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $member = Auth::user();

        if (!Hash::check($request->currentPassword, $member->master_data_password)) {
            return response()->json([
                'errors' => ['currentPassword' => 'Your current password is incorrect.']
            ], 422);
        }

        DB::transaction(function () use ($member, $request) {
            $member->master_data_password = Hash::make($request->newPassword);
            $member->save();
        });

        return response()->json([
            'message' => 'Password updated successfully.'
        ]);
    }

    private function handleFileUpload(Request $request, string $userEmail): ?string
    {
        $folderPath = 'public/' . $userEmail;
        Storage::makeDirectory($folderPath);

        if ($request->has('cropped_photo')) {
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->cropped_photo));

            $filename = 'profile.jpg';
            $filePath = $userEmail . '/' . $filename;

            Storage::put('public/' . $filePath, $imageData);

            return $filePath;
        }

        return null;
    }
    public function deletePhoto()
    {
        $member = Auth::user();
        if ($member->master_data_photo) {
            Storage::delete('public/' . $member->master_data_photo);
        }
        $member->master_data_photo = null;
        $member->save();

        return redirect()->back()->with('success', 'Profile photo removed successfully.');
    }



}
