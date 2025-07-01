<?php

namespace App\Modules\Settings\Panel\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Settings\Services\SettingsService;
use App\Modules\Settings\Requests\UpdateAppSettingsRequest;
use App\Modules\Settings\Requests\UpdateMailSettingsRequest;
use App\Modules\Settings\Requests\TestMailRequest;
use App\Modules\Settings\Exceptions\SettingsException;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    public function __construct(
        private SettingsService $settingsService
    ) {}

    /**
     * Ana ayarlar sayfası
     */
    public function index(): Response
    {
        $settings = $this->settingsService->getAllSettings();
        
        return Inertia::render('Settings/Panel/Index', [
            'settings' => $settings
        ]);
    }

    /**
     * Uygulama ayarları sayfası
     */
    public function app(): Response
    {
        $appSettings = $this->settingsService->getAppSettings();
        
        return Inertia::render('Settings/Panel/App/Index', [
            'appSettings' => $appSettings
        ]);
    }

    /**
     * Uygulama ayarlarını güncelle
     */
    public function updateApp(UpdateAppSettingsRequest $request): RedirectResponse
    {
        try {
            $this->settingsService->updateAppSettings($request->validated());
            return back()->with('success', 'Uygulama ayarları başarıyla güncellendi.');
        } catch (SettingsException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Uygulama ayarları güncellenirken beklenmeyen hata oluştu', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Uygulama ayarları güncellenirken bir hata oluştu.');
        }
    }

    /**
     * Mail ayarları sayfası
     */
    public function mail(): Response
    {
        $mailSettings = $this->settingsService->getMailSettings();
        
        return Inertia::render('Settings/Panel/Mail/Index', [
            'mailSettings' => $mailSettings
        ]);
    }

    /**
     * Mail ayarlarını güncelle
     */
    public function updateMail(UpdateMailSettingsRequest $request): RedirectResponse
    {
        try {
            $this->settingsService->updateMailSettings($request->validated());
            return back()->with('success', 'Mail ayarları başarıyla güncellendi.');
        } catch (SettingsException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Mail ayarları güncellenirken beklenmeyen hata oluştu', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Mail ayarları güncellenirken bir hata oluştu.');
        }
    }

    /**
     * Mail test gönder
     */
    public function testMail(TestMailRequest $request): RedirectResponse
    {

        try {
            $this->settingsService->testMail($request->email);
            return back()->with('success', 'Test mail başarıyla gönderildi.');
        } catch (SettingsException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Test mail gönderilirken beklenmeyen hata oluştu', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Test mail gönderilirken bir hata oluştu. Mail ayarlarınızı kontrol edin.');
        }
    }

    /**
     * Profil ayarları (eski method - geriye uyumluluk için)
     */
    public function profile(): Response
    {
        return Inertia::render('Settings/Panel/Profile');
    }

    /**
     * Güvenlik ayarları (eski method - geriye uyumluluk için)
     */
    public function security(): Response
    {
        return Inertia::render('Settings/Panel/Security');
    }
} 