<?php

namespace App\Http\Controllers;

use App\Http\Requests\FrontSettingRequest;
use App\Http\Requests\UpdateFrontSettingRequest;
use App\Models\FrontSetting;
use App\Repositories\FrontSettingRepository;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

/**
 * Class FrontSettingController
 */
class FrontSettingController extends AppBaseController
{
    /** @var FrontSettingRepository */
    private $frontSettingRepository;

    public function __construct(FrontSettingRepository $frontSettingRepository)
    {
        $this->frontSettingRepository = $frontSettingRepository;
    }

    /**
     * @param  Request  $request
     *
     * @return Factory|View
     */
    public function index(Request $request)
    {
        $frontSettings = FrontSetting::pluck('value', 'key')->toArray();
        $sectionName = $request->section === null ? 'cms' : $request->section;

        return view("front_settings.$sectionName", compact('frontSettings', 'sectionName'));
    }


    /**
     * @param FrontSettingRequest $request
     *
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function update(FrontSettingRequest $request)
    {
        $this->frontSettingRepository->updateFrontSetting($request->all());

        Flash::success(__('messages.flash.front_setting_updated'));

        return redirect(route('front.settings.index'));
    }
}
