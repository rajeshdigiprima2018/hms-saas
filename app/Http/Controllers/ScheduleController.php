<?php

namespace App\Http\Controllers;

use App\Exports\ScheduleExport;
use App\Http\Requests\CreateScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Schedule;
use App\Models\ScheduleDay;
use App\Repositories\ScheduleRepository;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ScheduleController extends AppBaseController
{
    /** @var ScheduleRepository */
    private $scheduleRepository;

    public function __construct(ScheduleRepository $scheduleRepo)
    {
        $this->scheduleRepository = $scheduleRepo;
    }

    /**
     * Display a listing of the Schedule.
     *
     * @param  Request  $request
     *
     * @throws Exception
     *
     * @return Factory|View
     */
    public function index(Request $request)
    {

        if (getLoggedInUser()->hasRole('Doctor')) {
            $checkDoctorId = Doctor::where('user_id', getLoggedInUserId())->first();
            $checkDoctorSchedule = Schedule::where('doctor_id', $checkDoctorId->id)->get();

            return view('schedules.index', compact('checkDoctorSchedule'));
        }


        return view('schedules.index');
    }

    /**
     * Show the form for creating a new Schedule.
     * @return Factory|View
     */
    public function create()
    {
        $data = $this->scheduleRepository->getData();

        return view('schedules.create', compact('data'));
    }

    /**
     * Store a newly created Schedule in storage.
     *
     * @param  CreateScheduleRequest  $request
     *
     * @return RedirectResponse|Redirector
     */
    public function store(CreateScheduleRequest $request)
    {
        $input = $request->all();

        $schedule = $this->scheduleRepository->store($input);

        Flash::success( __('messages.flash.schedule_saved'));

        return redirect(route('schedules.index'));
    }


    /**
     * @param Schedule $schedule
     *
     *
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function show(Schedule $schedule)
    {
        if (!canAccessRecord(Schedule::class, $schedule->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        if (getLoggedInUser()->hasRole('Doctor')) {
            if (!(getLoggedInUser()->owner_id == $schedule->doctor_id)) {
                return Redirect::back();
            }
        }
        
        $scheduleDays = ScheduleDay::whereScheduleId($schedule->id)->get();

        return view('schedules.show', compact('scheduleDays', 'schedule'));
    }


    /**
     * @param Schedule $schedule
     *
     *
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(Schedule $schedule)
    {
        if (!canAccessRecord(Schedule::class, $schedule->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        if (getLoggedInUser()->hasRole('Doctor')) {
            if (!(getLoggedInUser()->owner_id == $schedule->doctor_id)) {
                return Redirect::back();
            }
        }
        
        $data = $this->scheduleRepository->getData();
        $scheduleDays = ScheduleDay::whereScheduleId($schedule->id)->get();

        return view('schedules.edit', compact('schedule', 'data', 'scheduleDays'));
    }

    /**
     * Update the specified Schedule in storage.
     *
     * @param  Schedule  $schedule
     *
     * @param  UpdateScheduleRequest  $request
     *
     * @return RedirectResponse|Redirector
     */
    public function update(Schedule $schedule, UpdateScheduleRequest $request)
    {
        $schedule = $this->scheduleRepository->update($request->all(), $schedule->id);

        Flash::success( __('messages.flash.schedule_updated'));

        return redirect(route('schedules.index'));
    }

    /**
     * Remove the specified Schedule from storage.
     *
     * @param  Schedule  $schedule
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function destroy(Schedule $schedule)
    {
        if(!canAccessRecord(Schedule::class , $schedule->id)){
            return $this->sendError(__('messages.flash.schedule_not_found'));
        }

        if (getLoggedInUser()->hasRole('Doctor')) {
            if(! (getLoggedInUser()->owner_id == $schedule->doctor_id)){
                return $this->sendError(__('messages.flash.schedule_not_found'));   
            }
        }
        
        $doctorModule = [Appointment::class];
        $result = canDelete($doctorModule, 'doctor_id', $schedule->doctor_id);
        if ($result) {
            return $this->sendError( __('messages.flash.schedule_cant_deleted'));
        }

        $this->scheduleRepository->delete($schedule->id);
        ScheduleDay::whereScheduleId($schedule->id)->delete();

        return $this->sendSuccess( __('messages.flash.schedule_deleted'));
    }

    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function doctorScheduleList(Request $request)
    {
        $input = $request->all();

        $doctorSchedule = $this->scheduleRepository->getDoctorSchedule($input);

        return $this->sendResponse($doctorSchedule, 'successfully');
    }

    /**
     * @return BinaryFileResponse
     */
    public function schedulesExport()
    {
        $response = Excel::download(new ScheduleExport, getLoggedInUser()->full_name.'-schedules-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }
}
