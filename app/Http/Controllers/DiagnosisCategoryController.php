<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDiagnosisCategoryRequest;
use App\Http\Requests\UpdateDiagnosisCategoryRequest;
use App\Models\DiagnosisCategory;
use App\Models\PatientDiagnosisTest;
use App\Repositories\DiagnosisCategoryRepository;
use Exception;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class DiagnosisCategoryController extends AppBaseController
{
    /**
     * @var DiagnosisCategoryRepository
     */
    private $categoryRepository;

    public function __construct(DiagnosisCategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     *
     * @throws Exception
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        return view('diagnosis_categories.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateDiagnosisCategoryRequest  $request
     *
     * @return JsonResponse
     */
    public function store(CreateDiagnosisCategoryRequest $request)
    {
        $input = $request->all();
        $this->categoryRepository->create($input);

        return $this->sendSuccess( __('messages.flash.diagnosis_category_saved'));
    }


    /**
     * @param DiagnosisCategory $diagnosisCategory
     *
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(DiagnosisCategory $diagnosisCategory)
    {
        if (!canAccessRecord(DiagnosisCategory::class, $diagnosisCategory->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        return view('diagnosis_categories.show', compact('diagnosisCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  DiagnosisCategory  $diagnosisCategory
     *
     * @return JsonResponse
     */
    public function edit(DiagnosisCategory $diagnosisCategory)
    {
        if(!canAccessRecord(DiagnosisCategory::class , $diagnosisCategory->id)){
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }
        
        return $this->sendResponse($diagnosisCategory, __('messages.flash.diagnosis_category_retrieved'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateDiagnosisCategoryRequest  $request
     *
     * @param  DiagnosisCategory  $diagnosisCategory
     *
     * @return JsonResponse
     */
    public function update(UpdateDiagnosisCategoryRequest $request, DiagnosisCategory $diagnosisCategory)
    {
        $input = $request->all();
        $this->categoryRepository->update($input, $diagnosisCategory->id);

        return $this->sendSuccess( __('messages.flash.diagnosis_category_updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  DiagnosisCategory  $diagnosisCategory
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function destroy(DiagnosisCategory $diagnosisCategory)
    {
        if(!canAccessRecord(DiagnosisCategory::class , $diagnosisCategory->id)){
            return $this->sendError(__('messages.flash.diagnosis_category_not_found'));
        }
        
        $diagnosisCategoryModal = [
            PatientDiagnosisTest::class,
        ];
        $result = canDelete($diagnosisCategoryModal, 'category_id', $diagnosisCategory->id);
        if ($result) {
            return $this->sendError( __('messages.flash.diagnosis_category_cant_deleted'));
        }

        $diagnosisCategory->delete();

        return $this->sendSuccess( __('messages.flash.diagnosis_category_deleted'));
    }
}
