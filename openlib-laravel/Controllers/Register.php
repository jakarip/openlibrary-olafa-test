<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Member\Member;

class Register extends Controller
{
    /**
     * Tampilkan halaman register.
     */
    public function index()
    {
        return view('register');
    }

    /**
     * Fungsi utama untuk proses registrasi.
     */
    public function reg(Request $request)
    {
        // Pastikan form berisi key 'inp'
        if (!$request->has('inp')) {
            return redirect()->back();
        }

        DB::beginTransaction();
        try {
            $inp = $request->input('inp');
            $type = $request->input('type'); // Contoh: 'umum', 'alumni', 'internasional', 'ptasuh', 'lemdikti'

            [$userEmail, $institution, $address] = $this->parseTypeEmailInstitution($type, $inp);
            $userEmail = strtolower($userEmail);

            if ($this->checkExistingUser($userEmail, $inp)) {
                return redirect()->back();
            }

            if (!$this->validateDomain($type, $userEmail, $inp)) {
                return redirect()->back();
            }

            $data = $this->prepareMemberData($type, $userEmail, $institution, $address, $inp, $request);

            $this->handleFileUpload($type, $request, $userEmail, $data);

            $member = Member::create($data);

            DB::commit();

            // 7. (Opsional) Kirim email aktivasi / verifikasi
            $emailData = [
                'email' => $userEmail,
                'password' => $request->input('password'),
                'encode' => $this->urlQueryEncode($userEmail) // contoh enkode
            ];
            // Mail::to($userEmail)->send(new ActivationMail($emailData, $subject));

            // 8. Tampilkan pesan sukses
            $callbackMessage = $this->successMessageByType($type);

            Session::flash('login_log', [
                'status' => 'success',
                'text' => $callbackMessage
            ]);
            return back();

        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('login_log', [
                'status' => 'danger',
                'text' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
            return back();
        }
    }

    private function parseTypeEmailInstitution(string $type, array $inp): array
    {
        $email = '';
        $institution = $inp['institution'] ?? '';
        $address = $inp['address'] ?? '';

        switch ($type) {
            case 'umum':
                $email = $inp['email_umum'] ?? '';
                $institution = $inp['institution_umum'] ?? '';
                break;
            case 'internasional':
                $email = $inp['email_internasional'] ?? '';
                $institution = $inp['institution_internasional'] ?? '';
                break;
            case 'alumni':
                $email = $inp['email_alumni'] ?? '';
                break;
            case 'ptasuh':
                $email = $inp['email_ptasuh'] ?? '';
                $institution = $inp['institution_ptasuh'] ?? '';
                break;
            case 'lemdikti':
                $email = $inp['email_lemdikti'] ?? '';
                $institution = $inp['institution_lemdikti'] ?? '';
                break;
            default:
                Session::flash('login_log', [
                    'status' => 'danger',
                    'text' => 'Jenis anggota tidak valid.'
                ]);
                return ['', '', ''];
        }

        return [$email, $institution, $address];
    }

    private function checkExistingUser(string $userEmail, array $inp): bool
    {
        $existingUser = Member::where('master_data_user', $userEmail)->first();
        if ($existingUser) {
            Session::flash('reg_log', $inp);
            Session::flash('login_log', [
                'status' => 'danger',
                'text' => 'Email anda sudah terdaftar di data kami'
            ]);
            return true;
        }
        return false;
    }

    private function validateDomain(string $type, string $userEmail, array $inp): bool
    {
        // Jika tipe 'umum', cek blacklist
        if ($type === 'umum' && !$this->blacklistMail($userEmail)) {
            Session::flash('reg_log', $inp);
            Session::flash('login_log', [
                'status' => 'danger',
                'text' => 'Email yang digunakan bukan email institusi'
            ]);
            return false;
        }

        // Contoh cek domain 'ptasuh' / 'lemdikti'
        // if (in_array($type, ['ptasuh', 'lemdikti']) && !$this->checkEmailDomain($type, $userEmail)) {
        //     Session::flash('reg_log', $inp);
        //     Session::flash('login_log', [
        //         'status' => 'danger',
        //         'text'   => 'Email yang digunakan tidak sesuai domain institusi'
        //     ]);
        //     return false;
        // }

        return true;
    }

    private function prepareMemberData(
        string $type,
        string $userEmail,
        string $institution,
        string $address,
        array $inp,
        Request $request
    ): array {
        $data = [];
        $data['master_data_user'] = $userEmail;
        $data['master_data_email'] = $userEmail;
        $data['master_data_mobile_phone'] = preg_replace('/\D/', '', $inp['phone']);
        $data['master_data_address'] = $address;
        $data['master_data_fullname'] = ucwords(strtolower($inp['name']));
        $data['master_data_institution'] = ucwords(strtolower($institution));
        $data['created_at'] = now();
        $data['updated_at'] = now();
        $data['status'] = '2';
        $data['master_data_password'] = Hash::make($request->input('password'));

        if (empty($data['created_by'])) {
            $data['created_by'] = 'admin';
        }
        if (empty($data['updated_by'])) {
            $data['updated_by'] = 'admin';
        }

        $data['member_type_id'] = match ($type) {
            'umum' => 23,
            'internasional' => 24,
            'alumni' => 20,
            'ptasuh' => 22,
            'lemdikti' => 21,
            default => 0,
        };

        $data['member_class_id'] = 2;
        $data['master_data_type'] = $type;

        $prefix = str_pad($data['member_type_id'], 2, '0', STR_PAD_LEFT);
        $data['master_data_number'] = date('ymd') . $prefix . '0001';

        return $data;
    }

    private function handleFileUpload(string $type, Request $request, string $userEmail, array &$data): void
    {
        $folderPath = 'public/' . $userEmail;
        Storage::makeDirectory($folderPath);

        // Alumni
        if ($type === 'alumni') {
            if ($request->hasFile('ktp_alumni') && $request->file('ktp_alumni')->isValid()) {
                $ktpPath = $request->file('ktp_alumni')
                    ->storeAs($userEmail, 'ktp.' . $request->file('ktp_alumni')->extension(), 'public');
                $data['master_data_ktp'] = $ktpPath;
            }
            if ($request->hasFile('ijasah') && $request->file('ijasah')->isValid()) {
                $ijasahPath = $request->file('ijasah')
                    ->storeAs($userEmail, 'ijasah.' . $request->file('ijasah')->extension(), 'public');
                $data['master_data_ijasah'] = $ijasahPath;
            }
        }
        // Umum
        elseif ($type === 'umum') {
            if ($request->hasFile('ktp_umum') && $request->file('ktp_umum')->isValid()) {
                $ktpPath = $request->file('ktp_umum')
                    ->storeAs($userEmail, 'ktp.' . $request->file('ktp_umum')->extension(), 'public');
                $data['master_data_ktp'] = $ktpPath;
            }
            if ($request->hasFile('idcard_umum') && $request->file('idcard_umum')->isValid()) {
                $idPath = $request->file('idcard_umum')
                    ->storeAs($userEmail, 'idcard.' . $request->file('idcard_umum')->extension(), 'public');
                $data['master_data_idcard'] = $idPath;
            }
        }
        // PT Asuh
        elseif ($type === 'ptasuh') {
            if ($request->hasFile('ktp_ptasuh') && $request->file('ktp_ptasuh')->isValid()) {
                $ktpPath = $request->file('ktp_ptasuh')
                    ->storeAs($userEmail, 'ktp.' . $request->file('ktp_ptasuh')->extension(), 'public');
                $data['master_data_ktp'] = $ktpPath;
            }
            if ($request->hasFile('idcard_ptasuh') && $request->file('idcard_ptasuh')->isValid()) {
                $idPath = $request->file('idcard_ptasuh')
                    ->storeAs($userEmail, 'idcard.' . $request->file('idcard_ptasuh')->extension(), 'public');
                $data['master_data_idcard'] = $idPath;
            }
        }
        // Lemdikti
        elseif ($type === 'lemdikti') {
            if ($request->hasFile('ktp_lemdikti') && $request->file('ktp_lemdikti')->isValid()) {
                $ktpPath = $request->file('ktp_lemdikti')
                    ->storeAs($userEmail, 'ktp.' . $request->file('ktp_lemdikti')->extension(), 'public');
                $data['master_data_ktp'] = $ktpPath;
            }
            if ($request->hasFile('idcard_lemdikti') && $request->file('idcard_lemdikti')->isValid()) {
                $idPath = $request->file('idcard_lemdikti')
                    ->storeAs($userEmail, 'idcard.' . $request->file('idcard_lemdikti')->extension(), 'public');
                $data['master_data_idcard'] = $idPath;
            }
        }
        // Internasional => tidak ada file khusus
    }

    /**
     * 8. Fungsi untuk menampilkan pesan sukses sesuai tipe.
     */
    private function successMessageByType(string $type): string
    {
        if ($type === 'alumni') {
            return 'Terimakasih sudah mendaftar sebagai Alumni. Kami akan verifikasi data Anda terlebih dahulu dan akan konfirmasi via email.';
        }
        return 'Akun anda sudah berhasil dibuat. <br> Silahkan verifikasi akun anda melalui email.';
    }

    /**
     * Contoh fungsi untuk mengecek apakah email termasuk blacklist.
     */
    private function blacklistMail($user)
    {
        $blacklist = [
            '@gmail.',
            '@yahoo.',
            '@hotmail.',
            '@rocketmail.',
            '@kompas.',
            '@facebook.',
            '@tandex.',
            '@fastmail.',
            '@ymail.'
        ];

        foreach ($blacklist as $domain) {
            if (strpos($user, $domain) !== false) {
                return false;
            }
        }
        return true;
    }

    /**
     * (Opsional) Contoh fungsi untuk mengecek domain email PT Asuh / Lemdikti.
     */
    private function checkEmailDomain($type, $email)
    {
        // Misal:
        // if ($type == 'ptasuh' && str_contains($email, '@ptasuh.ac.id')) return true;
        // if ($type == 'lemdikti' && str_contains($email, '@lemdikti.or.id')) return true;
        // return false;
        return true; // Silakan isi logika sesuai kebutuhan
    }

    /**
     * Fungsi untuk mengenkode string, mirip dengan url_query_encode di CodeIgniter.
     */
    private function urlQueryEncode($string)
    {
        return rtrim(str_replace('/', '_', base64_encode(gzcompress(serialize($string)))), '=');
    }

    /**
     * Contoh fungsi tambahan untuk menghasilkan string acak.
     */
    private function randomStr($length)
    {
        $keyspace = str_shuffle('ACDEFGHJKLMNPQRTUVWXY123456789');
        $pieces = [];
        $max = strlen($keyspace) - 1;
        for ($i = 0; $i < $length; $i++) {
            $pieces[] = $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    /**
     * Contoh fungsi untuk menghasilkan UUID.
     */
    private function generateUuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0x0C2f) | 0x4000,
            random_int(0, 0x3fff) | 0x8000,
            random_int(0, 0x2Aff),
            random_int(0, 0xffD3),
            random_int(0, 0xff4B)
        );
    }
}
