<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Invoice;
use App\Models\Setting;
use App\Repositories\InvoiceRepository;
use Barryvdh\DomPDF\Facade as PDF;
use DB;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Throwable;

class InvoiceController extends AppBaseController
{
    /** @var InvoiceRepository */
    private $invoiceRepository;

    public function __construct(InvoiceRepository $invoiceRepo)
    {
        $this->invoiceRepository = $invoiceRepo;
    }

    /**
     * Display a listing of the Invoice.
     *
     * @param  Request  $request
     *
     * @throws Exception
     *
     * @return Factory|View
     */
    public function index(Request $request)
    {
        $statusArr = Invoice::STATUS_ARR;

        return view('invoices.index')->with('statusArr', $statusArr);
    }

    /**
     * Show the form for creating a new Invoice.
     *
     * @return Factory|View
     */
    public function create()
    {
        $data = $this->invoiceRepository->getSyncList();

        return view('invoices.create')->with($data);
    }

    /**
     * Store a newly created Invoice in storage.
     *
     * @param  CreateInvoiceRequest  $request
     *
     * @throws Exception|Throwable
     *
     * @return JsonResponse
     */
    public function store(CreateInvoiceRequest $request)
    {
        try {
            DB::beginTransaction();
            $bill = $this->invoiceRepository->saveInvoice($request->all());
            $this->invoiceRepository->saveNotification($request->all());
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($bill, __('messages.flash.invoice_saved'));
    }


    /**
     * @param Invoice $invoice
     *
     *
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function show(Invoice $invoice)
    {
        if (!canAccessRecord(Invoice::class, $invoice->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $data['hospitalAddress'] = Setting::where('key', '=', 'hospital_address')->first()->value;
        $data['invoice'] = Invoice::with(['invoiceItems.account', 'patient.address'])->find($invoice->id);

        return view('invoices.show')->with($data);
    }


    /**
     * @param Invoice $invoice
     *
     *
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(Invoice $invoice)
    {
        if (!canAccessRecord(Invoice::class, $invoice->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $invoice->invoiceItems;
        $data = $this->invoiceRepository->getSyncList();
        $data['invoice'] = $invoice;

        return view('invoices.edit')->with($data);
    }

    /**
     * Update the specified Invoice in storage.
     *
     * @param  Invoice  $invoice
     * @param  UpdateInvoiceRequest  $request
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function update(Invoice $invoice, UpdateInvoiceRequest $request)
    {
        try {
            DB::beginTransaction();
            $bill = $this->invoiceRepository->updateInvoice($invoice->id, $request->all());
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($bill, __('messages.flash.invoice_updated'));
    }

    /**
     * Remove the specified Invoice from storage.
     *
     * @param  Invoice  $invoice
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function destroy(Invoice $invoice)
    {
        if(!canAccessRecord(Invoice::class , $invoice->id)){
            return $this->sendError(__('messages.flash.invoice_not_found'));
        }
        
        $this->invoiceRepository->delete($invoice->id);

        return $this->sendSuccess( __('messages.flash.invoice_deleted'));
    }

    /**
     * @param Invoice $invoice
     *
     * @return RedirectResponse|Redirector
     */
    public function convertToPdf(Invoice $invoice)
    {
        $invoice->invoiceItems;
        $data = $this->invoiceRepository->getSyncListForCreate($invoice->id);
        $data['invoice'] = $invoice;
        $data['currencySymbol'] = getCurrencySymbol();
        $pdf = PDF::loadView('invoices.invoice_pdf', $data);

        return $pdf->stream('invoice.pdf');
    }
}
