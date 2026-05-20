<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $user = Auth::user();

        return view('admin.settings.index', compact('settings', 'user'));
    }

    public function updateGlobal(Request $request)
    {
        if (!Auth::user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'login_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'dashboard_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'favicon' => 'nullable|image|mimes:png,ico|max:1024',
        ]);

        $this->handleUpload($request, 'login_logo', 'img/logo_login');
        $this->handleUpload($request, 'dashboard_logo', 'img/logo_dashboard');
        $this->handleUpload($request, 'favicon', 'img/favicon');

        return redirect()->back()->with('success', __('Global settings updated successfully.'));
    }

    public function updatePersonal(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'sidebar_color' => 'nullable|string|max:20',
            'sidebar_font_color' => 'nullable|string|max:20',
            'navbar_color' => 'nullable|string|max:20',
            'navbar_font_color' => 'nullable|string|max:20',
            'content_color' => 'nullable|string|max:20',
            'content_font_color' => 'nullable|string|max:20',
            'font_family' => 'nullable|string|max:50',
            'language' => 'required|in:id,en',
        ]);

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img/avatars'), $filename);
            
            // Delete old avatar if exists
            if ($user->avatar && File::exists(public_path('img/avatars/' . $user->avatar))) {
                File::delete(public_path('img/avatars/' . $user->avatar));
            }
            
            $user->avatar = $filename;
        }

        $user->sidebar_color = $request->sidebar_color;
        $user->sidebar_font_color = $request->sidebar_font_color;
        $user->navbar_color = $request->navbar_color;
        $user->navbar_font_color = $request->navbar_font_color;
        $user->content_color = $request->content_color;
        $user->content_font_color = $request->content_font_color;
        $user->font_family = $request->font_family;
        $user->language = $request->language;
        $user->save();

        // Update application locale immediately if possible (often needs to happen in middleware though)
        
        return redirect()->back()->with('success', __('Personal settings updated successfully.'));
    }

    private function handleUpload(Request $request, $key, $directory)
    {
        if ($request->hasFile($key)) {
            $file = $request->file($key);
            $filename = $key . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($directory), $filename);

            // Get old setting
            $oldSetting = Setting::where('key', $key)->first();
            if ($oldSetting && $oldSetting->value) {
                if (File::exists(public_path($directory . '/' . $oldSetting->value))) {
                    File::delete(public_path($directory . '/' . $oldSetting->value));
                }
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $filename]
            );
        }
    }

    public function lmsIndex()
    {
        $user = Auth::user();
        return view('lms.settings', compact('user'));
    }

    public function updateLmsPersonal(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'sidebar_color' => 'nullable|string|max:20',
            'sidebar_font_color' => 'nullable|string|max:20',
            'navbar_color' => 'nullable|string|max:20',
            'navbar_font_color' => 'nullable|string|max:20',
            'content_color' => 'nullable|string|max:20',
            'content_font_color' => 'nullable|string|max:20',
            'font_family' => 'nullable|string|max:50',
            'language' => 'required|in:id,en',
        ]);

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img/avatars'), $filename);
            
            // Delete old avatar if exists
            if ($user->avatar && File::exists(public_path('img/avatars/' . $user->avatar))) {
                File::delete(public_path('img/avatars/' . $user->avatar));
            }
            
            $user->avatar = $filename;
        }

        $user->lms_sidebar_color = $request->sidebar_color;
        $user->lms_sidebar_font_color = $request->sidebar_font_color;
        $user->lms_navbar_color = $request->navbar_color;
        $user->lms_navbar_font_color = $request->navbar_font_color;
        $user->lms_content_color = $request->content_color;
        $user->lms_content_font_color = $request->content_font_color;
        $user->lms_font_family = $request->font_family;
        $user->language = $request->language;
        $user->save();

        return redirect()->back()->with('success', __('Personal settings updated successfully.'));
    }
}
