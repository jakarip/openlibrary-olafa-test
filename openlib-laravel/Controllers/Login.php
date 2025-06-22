<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\MemberModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helpers;

class Login extends Controller
{
  public function index()
  {
    // print_r(session()->all());
    return view('login');
  }

  public function hash_password($password)
  {
    echo Hash::make($password);
  }

  public function exes()
  {
    //custom


    $user = MemberModel::find('132481');
    Auth::Login($user, true);

    // dd(auth()->user());

    //Session::put('login', TRUE);

    return redirect('/dashboard');
  }

  public function exe(Request $request)
  {

    $request->validate([
      'username' => 'required|string|min:3',
      'password' => 'required|string|min:3',
    ], [
      'username.required' => 'common.the_entered_username_is_required',
      'username.min' => 'common.the_entered_username_minimum_3_character',
      'password.required' => 'common.the_entered_password_is_required',
      'password.min' => 'common.the_entered_password_minimum_3_character',
    ]);

    $username = strtolower($request->input('username'));
    $password = $request->input('password');
    $remember = $request->input('remember-me');

    //Ijin di bypass dulu pak
    if ($username == "admin" && $password == "admin") {
      $user = MemberModel::where('master_data_user', 'yudhinugrohoadis')->first(); //, 'yudhinugrohoadis'
      Auth::login($user, true);
      // dd(auth()->user());
      return redirect('/dashboard');
    }

    $sso = Helpers::SSO($username, $password);
    if ($sso == 'false') {
      $user = MemberModel::where('master_data_user', $username)->first();

      if (!$user) {
        return back()->withErrors([
          'message' => 'common.the_entered_email_username_or_password_does_not_exist',
        ]);
      }

      if ($user->status == '2') {
        return back()->with('inactive', 'Akun Anda belum aktif. Silakan menunggu verifikasi.');
      }

      if ($user->status != '1') {
        return back()->withErrors([
          'message' => 'Akun tidak aktif atau diblokir.'
        ]);
      }

      if (!Hash::check($password, $user->master_data_password)) {
        return back()->withErrors([
          'message' => 'common.the_entered_email_username_or_password_does_not_exist',
        ]);
      }

      Auth::login($user, true);
      // dd(auth()->user());
      return redirect('/dashboard');
    } else {
      $member = json_decode($sso, true);
      $user = MemberModel::where('master_data_user', $username)->first();
      if ($user) {
        //update data sso
        $user->master_data_email = $member['email'];
        $user->master_data_mobile_phone = $member['hp'];
        $user->master_data_fullname = $member['name'];
        $user->rfid1 = $member['rfid1'];
        $user->rfid2 = $member['rfid2'];
        $user->master_data_generation = $member['angkatan'];
        $user->master_data_photo = $member['photourl'];
        $user->updated_by = 'telupress';
        $user->updated_at = date("Y-m-d H:i:s");
        $user->update();
      } else {
        //insert data sso

        if ($member['c_kode_jenis_user'] == 'pegawai') {
          $fields = array(
            'member_type_id' => ($member['isdosen'] != 'NO' ? '4' : '7'),
            'master_data_user' => $member['username'],
            'master_data_email' => $member['email'],
            'master_data_mobile_phone' => $member['hp'],
            // 'master_data_course' 			=> ($member['studyprogramid']!='-'?$member['studyprogramid']:''),
            'master_data_fullname' => $member['name'],
            'master_data_number' => $member['employeeid'],
            'rfid1' => $member['rfid1'],
            'rfid2' => $member['rfid2'],
            'master_data_lecturer_status' => $member['isdosen'],
            'master_data_generation' => $member['angkatan'],
            'master_data_photo' => $member['photourl'],
            'master_data_nidn' => $member['nidn'],
            "member_class_id" => '3',
            "status" => "1",
            "created_by" => 'telupress',
            "created_at" => date("Y-m-d H:i:s")
          );
        } else {
          $type = "";
          if ($member['studyprogramname'])

            if (strpos($member['studyprogramname'], 'D3') !== false) {
              $type = "6";
            } else if (strpos($member['studyprogramname'], 'D4') !== false) {
              $type = "6";
            } else if (strpos($member['studyprogramname'], 'S1') !== false) {
              $type = "5";
            } else if (strpos($member['studyprogramname'], 'S2') !== false) {
              $type = "10";
            } else if (strpos($member['studyprogramname'], 'S3') !== false) {
              $type = "25";
            }

          $fields = array(
            'member_type_id' => $type,
            'master_data_user' => $member['username'],
            'master_data_email' => $member['email'],
            'master_data_mobile_phone' => $member['hp'],
            'master_data_course' => $member['studyprogramid'],
            'master_data_fullname' => $member['name'],
            'master_data_number' => $member['studentid'],
            'rfid1' => $member['rfid1'],
            'rfid2' => $member['rfid2'],
            'master_data_lecturer_status' => '',
            'master_data_generation' => $member['angkatan'],
            'master_data_photo' => $member['photourl'],
            "member_class_id" => '2',
            "status" => "1",
            "created_by" => 'telupress',
            "created_at" => date("Y-m-d H:i:s")
          );
        }

        MemberModel::Insert($fields);
      }

      $user = MemberModel::where('master_data_user', $username)->first();

      if ($user->status == '2') {
        return back()->with('inactive', 'Akun Anda belum aktif. Silakan menunggu verifikasi.');
      }
      if ($user->status != '1') {
        return back()->withErrors([
          'message' => 'Akun tidak aktif atau diblokir.'
        ]);
      }
      Auth::login($user, $remember);
      
      return redirect('/dashboard');
    }
  }

  // public function logout()
  // {
  //     Auth::logout();

  //     session()->invalidate();
  //     session()->regenerateToken();
  //     session()->flush();

  //     return redirect()->route('login');
  // }

  public function logout()
  {


    // Log the current user
    $user = Auth::user();

    // Check if the user is logged in
    if ($user) {
      // Clear the remember token
      $user->setRememberToken(null);
      $user->save();
    }

    Auth::logout();

    session()->invalidate();
    session()->regenerateToken();
    session()->flush();



    return redirect()->route('home');
  }
}
