<?php

namespace App\Http\Controllers;

use App\Exports\AppointmentExport;
use App\Http\Requests\CreateAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Repositories\AppointmentRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class AppointmentController
 */
class AppointmentController extends AppBaseController
{
    /** @var AppointmentRepository */
    private $appointmentRepository;

    public function __construct(AppointmentRepository $appointmentRepo)
    {
        $this->appointmentRepository = $appointmentRepo;
    }

    /**
     * Display a listing of the appointment.
     *
     * @param  Request  $request
     *
     * @throws Exception
     *
     * @return Factory|View
     */
    public function index(Request $request)
    {
        $statusArr = Appointment::STATUS_ARR;

        return view('appointments.index', compact('statusArr'));
    }

    /**
     * Show the form for creating a new appointment.
     *
     * @return Factory|View
     */
    public function create()
    {
        $patients = $this->appointmentRepository->getPatients();
        $departments = $this->appointmentRepository->getDoctorDepartments();
        $statusArr = Appointment::STATUS_PENDING;

        return view('appointments.create', compact('patients', 'departments', 'statusArr'));
    }

    /**
     * Store a newly created appointment in storage.
     *
     * @param  CreateAppointmentRequest  $request
     *
     * @return JsonResponse
     */
    public function store(CreateAppointmentRequest $request)
    {
        $input = $request->all();
        $input['opd_date'] = $input['opd_date'].$input['time'];
        $input['is_completed'] = isset($input['status']) ? Appointment::STATUS_COMPLETED : Appointment::STATUS_PENDING;
        if ($request->user()->hasRole('Patient')) {
            $input['patient_id'] = $request->user()->owner_id;
        }
        $this->appointmentRepository->create($input);
        $this->appointmentRepository->createNotification($input);

        return $this->sendSuccess( __('messages.flash.appointment_saved'));
    }

    /**
     * Display the specified appointment.
     *
     * @param  Appointment  $appointment
     *
     * @return Factory|View|RedirectResponse
     */
    public function show(Appointment $appointment)
    {
        return view('appointments.show')->with('appointment', $appointment);
    }

    /**
     * Show the form for editing the specified appointment.
     *
     * @param Appointment $appointment
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse|Redirector|View
     */
    public function edit(Appointment $appointment)
    {
        $patients = $this->appointmentRepository->getPatients();
        $doctors = $this->appointmentRepository->getDoctors($appointment->department_id);
        $departments = $this->appointmentRepository->getDoctorDepartments();
        $statusArr = $appointment->is_completed;

        return view('appointments.edit', compact('appointment', 'patients', 'doctors', 'departments', 'statusArr'));
    }

    /**
     * Update the specified appointment in storage.
     *
     * @param  Appointment  $appointment
     * @param  UpdateAppointmentRequest  $request
     *
     * @return JsonResponse
     */
    public function update(Appointment $appointment, UpdateAppointmentRequest $request)
    {
        $input = $request->all();
        $input['opd_date'] = $input['opd_date'].$input['time'];
        $input['is_completed'] = isset($input['status']) ? Appointment::STATUS_COMPLETED : Appointment::STATUS_PENDING;
        if ($request->user()->hasRole('Patient')) {
            $input['patient_id'] = $request->user()->owner_id;
        }
        $appointment = $this->appointmentRepository->update($input, $appointment->id);

        return $this->sendSuccess( __('messages.flash.appointment_updated'));
    }

    /**
     * Remove the specified appointment from storage.
     *
     * @param  Appointment  $appointment
     *
     * @throws Exception
     *
     * @return RedirectResponse|Redirector|JsonResponse
     */
    public function destroy(Appointment $appointment)
    {
        $this->appointmentRepository->delete($appointment->id);

        return $this->sendSuccess( __('messages.flash.appointment_delete'));
    }

    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function getDoctors(Request $request)
    {
        $id = $request->get('id');

        $doctors = $this->appointmentRepository->getDoctors($id);

        return $this->sendResponse($doctors, __('messages.flash.retrieve'));
    }

    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function getBookingSlot(Request $request)
    {
        $inputs = $request->all();
        $data = $this->appointmentRepository->getBookingSlot($inputs);

        return $this->sendResponse($data,  __('messages.flash.retrieve'));
    }

    /**
     * @return BinaryFileResponse
     */
    public function appointmentExport()
    {
        $response = Excel::download(new AppointmentExport, 'appointments-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }

    /**
     * @param  Appointment  $appointment
     *
     * @return JsonResponse
     */
    public function status(Appointment $appointment)
    {
        if (getLoggedInUser()->hasRole('Doctor')) {
            $patientAppointmentHasDoctor = Appointment::whereId($appointment->id)->whereDoctorId(getLoggedInUser()->owner_id)->exists();
            if(!$patientAppointmentHasDoctor){
                return $this->sendError(__('messages.flash.appointment_not_found'));
            }
        }
        
        if(!canAccessRecord(Appointment::class , $appointment->id)){
            return $this->sendError(__('messages.flash.appointment_not_found'));
        }
        $isCompleted = ! $appointment->is_completed;
        $appointment->update(['is_completed' => $isCompleted]);

        return $this->sendSuccess( __('messages.common.status_updated_successfully'));
    }

    /** 
     * @param  Appointment  $appointment
     *
     * @return JsonResponse
     */
    public function cancelAppointment(Appointment $appointment)
    {
        if (getLoggedInUser()->hasRole('Doctor')) {
            $patientAppointmentHasDoctor = Appointment::whereId($appointment->id)->whereDoctorId(getLoggedInUser()->owner_id)->exists();
            if(!$patientAppointmentHasDoctor){
                return $this->sendError(__('messages.flash.appointment_not_found'));
            }
        }
        
        if(!canAccessRecord(Appointment::class , $appointment->id)){
            return $this->sendError(__('messages.flash.appointment_not_found'));
        }
        
        $appointment->update(['is_completed' => Appointment::STATUS_CANCELLED]);

        return $this->sendSuccess( __('messages.flash.appointment_cancel'));
    }
}
