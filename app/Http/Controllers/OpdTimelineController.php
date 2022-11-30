<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOpdTimelineRequest;
use App\Http\Requests\UpdateOpdTimelineRequest;
use App\Models\OpdTimeline;
use App\Repositories\OpdTimelineRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class OpdTimelineController extends AppBaseController
{
    /** @var OpdTimelineRepository */
    private $opdTimelineRepository;

    public function __construct(OpdTimelineRepository $opdTimelineRepo)
    {
        $this->opdTimelineRepository = $opdTimelineRepo;
    }

    /**
     * Display a listing of the OpdTimeline.
     *
     * @param  Request  $request
     *
     * @throws Throwable
     *
     * @return array|string
     */
    public function index(Request $request)
    {
        $opdTimelines = $this->opdTimelineRepository->getTimeLines($request->get('id'));

        return view('opd_timelines.index', compact('opdTimelines'))->render();
    }

    /**
     * Store a newly created OpdTimeline in storage.
     *
     * @param  CreateOpdTimelineRequest  $request
     *
     * @return JsonResponse
     */
    public function store(CreateOpdTimelineRequest $request)
    {
        $input = $request->all();
        $this->opdTimelineRepository->store($input);

        return $this->sendSuccess( __('messages.flash.OPD_timeline_saved'));
    }

    /**
     * Show the form for editing the specified OpdTimeline.
     *
     * @param  OpdTimeline  $opdTimeline
     *
     * @return JsonResponse
     */
    public function edit(OpdTimeline $opdTimeline)
    {
        if(!canAccessRecord(OpdTimeline::class , $opdTimeline->id)){
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }
        
        return $this->sendResponse($opdTimeline, __('messages.flash.OPD_timeline_retrieved'));
    }

    /**
     * Update the specified OpdTimeline in storage.
     *
     * @param  OpdTimeline  $opdTimeline
     * @param  UpdateOpdTimelineRequest  $request
     *
     * @return JsonResponse
     */
    public function update(OpdTimeline $opdTimeline, UpdateOpdTimelineRequest $request)
    {
        $this->opdTimelineRepository->updateOpdTimeline($request->all(), $opdTimeline->id);

        return $this->sendSuccess( __('messages.flash.OPD_timeline_updated'));
    }

    /**
     * Remove the specified OpdTimeline from storage.
     *
     * @param  OpdTimeline  $opdTimeline
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function destroy(OpdTimeline $opdTimeline)
    {
        if(!canAccessRecord(OpdTimeline::class , $opdTimeline->id)){
            return $this->sendError(__('messages.flash.opd_timeline_not_found'));
        }
        
        $this->opdTimelineRepository->deleteOpdTimeline($opdTimeline->id);

        return $this->sendSuccess( __('messages.flash.OPD_timeline_deleted'));
    }

    /**
     * @param  OpdTimeline  $opdTimeline
     *
     * @return Media
     */
    public function downloadMedia(OpdTimeline $opdTimeline)
    {
        $media = $opdTimeline->getMedia(OpdTimeline::OPD_TIMELINE_PATH)->first();
        if ($media) {
            return $media;
        }

        return '';
    }
}
