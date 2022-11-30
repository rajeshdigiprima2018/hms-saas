<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\AppBaseController;
use App\Models\PatientAdmission;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PatientAdmissionController extends AppBaseController
{
    /**
     * Display a listing of the PatientAdmission.
     *
     * @param  Request  $request
     *
     * @throws Exception
     *
     * @return Factory|View
     */
    public function index(Request $request)
    {
        $data['statusArr'] = PatientAdmission::STATUS_ARR;

        return view('employees.patient_admissions.index', $data);
    }

    /**
     * Display the specified PatientAdmission.
     *
     * @param  PatientAdmission  $patientAdmission
     *
     * @return Factory|View
     */
    public function show(PatientAdmission $patientAdmission)
    {
        if(!canAccessRecord(PatientAdmission::class , $patientAdmission->id)){
            return Redirect::back();
        }

        if (getLoggedInUser()->hasRole('Patient')) {
            if(getLoggedInUser()->owner_id != $patientAdmission->patient_id){
                return Redirect::back();
            }
        }
        
        return view('employees.patient_admissions.show')->with('patientAdmission', $patientAdmission);
    }
}
