<?php

namespace App\Http\Controllers\Patient;

use App\Exports\PrescriptionExport;
use App\Http\Controllers\Controller;
use App\Models\PatientAdmission;
use App\Models\Prescription;
use App\Models\User;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PrescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     *
     * @throws Exception
     *
     * @return Factory|View
     */
    public function index(Request $request)
    {
        $data['statusArr'] = Prescription::STATUS_ARR;

        return view('patients_prescription_list.index', $data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return Factory|View
     */
    public function show($id)
    {
        if(!canAccessRecord(Prescription::class , $id)){
            return Redirect::back();
        }

        if (getLoggedInUser()->hasRole('Patient')) {
            $insuranceHasPatient = Prescription::wherePatientId(getLoggedInUser()->owner_id)->whereId($id)->exists();
            if(!$insuranceHasPatient){
                return Redirect::back();
            }
        }
        
        $prescription = Prescription::findOrFail($id);

        return view('patients_prescription_list.show')->with('prescription', $prescription);
    }
    
    /**
     * @return BinaryFileResponse
     */
    public function prescriptionExport()
    {
        $response = Excel::download(new PrescriptionExport, 'prescriptions-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }
}
