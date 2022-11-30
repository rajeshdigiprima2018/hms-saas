<?php

namespace App\Http\Controllers;

use App\Exports\PharmacistExport;
use App\Http\Requests\CreatePharmacistRequest;
use App\Http\Requests\UpdatePharmacistRequest;
use App\Models\EmployeePayroll;
use App\Models\Pharmacist;
use App\Repositories\PharmacistRepository;
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

class PharmacistController extends AppBaseController
{
    /** @var PharmacistRepository */
    private $pharmacistRepository;

    public function __construct(PharmacistRepository $pharmacistRepo)
    {
        $this->pharmacistRepository = $pharmacistRepo;
    }

    /**
     * Display a listing of the Pharmacist.
     *
     * @param  Request  $request
     *
     * @throws Exception
     *
     * @return Factory|View
     */
    public function index(Request $request)
    {
        $data['statusArr'] = Pharmacist::STATUS_ARR;

        return view('pharmacists.index', $data);
    }

    /**
     * Show the form for creating a new Pharmacist.
     *
     * @return Factory|View
     */
    public function create()
    {
        $bloodGroup = getBloodGroups();

        return view('pharmacists.create', compact('bloodGroup'));
    }

    /**
     * Store a newly created Pharmacist in storage.
     *
     * @param  CreatePharmacistRequest  $request
     *
     * @return RedirectResponse|Redirector
     */
    public function store(CreatePharmacistRequest $request)
    {
        $input = $request->all();
        $input['status'] = isset($input['status']) ? 1 : 0;

        $this->pharmacistRepository->store($input);
        Flash::success(__('messages.flash.Pharmacist_saved'));

        return redirect(route('pharmacists.index'));
    }


    /**
     * @param Pharmacist $pharmacist
     *
     *
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function show(Pharmacist $pharmacist)
    {
        if (!canAccessRecord(Pharmacist::class, $pharmacist->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $payrolls = $pharmacist->payrolls;

        return view('pharmacists.show', compact('pharmacist', 'payrolls'));
    }


    /**
     * @param Pharmacist $pharmacist
     *
     *
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(Pharmacist $pharmacist)
    {
        if (!canAccessRecord(Pharmacist::class, $pharmacist->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $user = $pharmacist->user;
        $bloodGroup = getBloodGroups();

        return view('pharmacists.edit', compact('pharmacist', 'user', 'bloodGroup'));
    }

    /**
     * Update the specified Pharmacist in storage.
     *
     * @param  Pharmacist  $pharmacist
     * @param  UpdatePharmacistRequest  $request
     *
     * @return RedirectResponse|Redirector
     */
    public function update(Pharmacist $pharmacist, UpdatePharmacistRequest $request)
    {
        $input = $request->all();
        $input['status'] = isset($input['status']) ? 1 : 0;
        $this->pharmacistRepository->update($input, $pharmacist);

        Flash::success(__('messages.flash.Pharmacist_updated'));

        return redirect(route('pharmacists.index'));
    }

    /**
     * Remove the specified Pharmacist from storage.
     *
     * @param  Pharmacist  $pharmacist
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function destroy(Pharmacist $pharmacist)
    {
        if(!canAccessRecord(Pharmacist::class , $pharmacist->id)){
            return $this->sendError(__('messages.flash.pharmacist_not_found'));
        }
        
        $empPayRollResult = canDeletePayroll(EmployeePayroll::class, 'owner_id', $pharmacist->id,
            $pharmacist->user->owner_type);
        if ($empPayRollResult) {
            return $this->sendError(__('messages.flash.Pharmacist_cant_deleted'));
        }
        $pharmacist->user()->delete();
        $pharmacist->delete();
        $pharmacist->address()->delete();

        return $this->sendSuccess(__('messages.flash.Pharmacist_deleted'));
    }

    /**
     * @param  int  $id
     *
     * @return JsonResponse
     */
    public function activeDeactiveStatus($id)
    {
        $pharmacist = Pharmacist::findOrFail($id);
        $status = ! $pharmacist->user->status;
        $pharmacist->user()->update(['status' => $status]);

        return $this->sendSuccess(__('messages.common.status_updated_successfully'));
    }

    /**
     * @return BinaryFileResponse
     */
    public function pharmacistExport()
    {
        $response = Excel::download(new PharmacistExport, 'pharmacists-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }
}
